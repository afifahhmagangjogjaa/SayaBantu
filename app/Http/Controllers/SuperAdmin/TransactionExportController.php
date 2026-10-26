<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransaction;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionExportController extends Controller
{
    /**
     * Export balance transactions to Excel (.xls HTML format).
     */
    public function export(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $from = $request->get('from');
        $to = $request->get('to');
        $cityId = $request->get('city_id');

        $query = BalanceTransaction::query();

        // Filter by Transaction Type
        if ($type && $type !== 'all') {
            if ($type === 'withdraw') {
                $query->where(function ($q) {
                    $q->where('type', 'withdraw')
                        ->orWhere(function ($sub) {
                            $sub->whereIn('type', ['deduction', 'withdraw_deduction'])
                                ->whereHas('user', fn ($u) => $u->where('role', 'mitra'));
                        });
                });
            } elseif ($type === 'topup') {
                $query->where('type', 'topup');
            } elseif ($type === 'other') {
                $query->whereNotIn('type', ['topup', 'withdraw']);
            } else {
                $query->where('type', $type);
            }
        }

        // Filter by Search Term
        if ($search) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('request_code', 'like', "%{$s}%")
                    ->orWhere('order_id', 'like', "%{$s}%")
                    ->orWhere('reference_id', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($qu) use ($s) {
                        $qu->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        // Filter by Date Range
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Filter by City
        $cityName = 'Semua-Wilayah';
        if ($cityId) {
            $query->whereHas('user', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });

            $selectedCity = City::find($cityId);
            if ($selectedCity) {
                $cityName = Str::slug($selectedCity->name);
            }
        }

        $transactions = $query->with(['user.city'])->orderBy('created_at', 'desc')->get();
        $filename = 'Laporan-Transaksi-'.$cityName.'-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($transactions, $cityName) {
            $this->renderExcelContent($transactions, $cityName);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Render the spreadsheet HTML stream.
     */
    protected function renderExcelContent($transactions, string $cityName): void
    {
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<style>
            body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #111827; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #0f172a; padding: 8px 10px; text-align: center; vertical-align: middle; }
            td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
            .text-center { text-align: center; }
            .text-left { text-align: left; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .bg-total { background-color: #f1f5f9; font-weight: bold; }
            .title-main { font-size: 16pt; font-weight: bold; text-align: center; color: #1e3a8a; }
            .title-sub { font-size: 10pt; color: #64748b; text-align: center; }
        </style></head><body>';

        echo '<table>';
        echo '<tr><td colspan="9" class="title-main" style="border:none; padding-top:10px; padding-bottom:4px;">LAPORAN DAFTAR TRANSAKSI ('.strtoupper(str_replace('-', ' ', $cityName)).')</td></tr>';
        echo '<tr><td colspan="9" class="title-sub" style="border:none; padding-bottom:15px;">Tanggal Cetak: '.now()->translatedFormat('d F Y, H:i').' WIB | Oleh: '.htmlspecialchars(auth()->user()->name ?? 'Super Admin').'</td></tr>';
        echo '</table>';

        echo '<table>';
        echo '<thead><tr>';
        echo '<th style="width: 45px;">No</th>';
        echo '<th style="width: 140px;">Waktu</th>';
        echo '<th style="width: 180px;">Nama User</th>';
        echo '<th style="width: 200px;">Email</th>';
        echo '<th style="width: 140px;">Wilayah / Kota</th>';
        echo '<th style="width: 130px;">Tipe</th>';
        echo '<th style="width: 130px;">Jumlah (Rp)</th>';
        echo '<th style="width: 120px;">Ref</th>';
        echo '<th style="width: 100px;">Status</th>';
        echo '</tr></thead><tbody>';

        $no = 1;
        $total = 0;

        foreach ($transactions as $t) {
            $userRole = optional($t->user)->role;
            if ($t->type === 'withdraw' || ($userRole === 'mitra' && in_array($t->type, ['deduction', 'withdraw_deduction']))) {
                $label = 'Withdraw';
            } elseif ($userRole === 'mitra' && ($t->type === 'topup' || $t->type === 'income')) {
                $label = 'Pendapatan Mitra';
            } elseif ($t->type === 'topup' || $t->type === 'deposit') {
                $label = 'Top Up';
            } elseif ($t->type === 'deduction') {
                $label = 'Pembayaran Bantuan';
            } else {
                $label = ucfirst($t->type);
            }

            $statusLabel = $t->status ?? 'completed';
            $total += (float) $t->amount;
            $rowBg = ($no % 2 === 0) ? 'style="background-color: #f8fafc;"' : '';
            $refCode = $t->request_code ?? $t->order_id ?? $t->reference_id ?? $t->reference ?? '-';
            $userCity = $t->user->city_name ?? '-';

            echo "<tr {$rowBg}>";
            echo '<td class="text-center">'.$no++.'</td>';
            echo '<td class="text-center">'.($t->created_at ? $t->created_at->format('d/m/Y H:i') : '-').'</td>';
            echo '<td class="text-left">'.htmlspecialchars(optional($t->user)->name ?? '-').'</td>';
            echo '<td class="text-left">'.htmlspecialchars(optional($t->user)->email ?? '-').'</td>';
            echo '<td class="text-center">'.htmlspecialchars($userCity).'</td>';
            echo '<td class="text-center">'.$label.'</td>';
            echo '<td class="text-right font-bold">'.number_format($t->amount, 0, ',', '.').'</td>';
            echo '<td class="text-center">'.htmlspecialchars($refCode).'</td>';
            echo '<td class="text-center">'.$statusLabel.'</td>';
            echo '</tr>';
        }

        echo '<tr>';
        echo '<td colspan="6" class="text-right font-bold bg-total">TOTAL KESELURUHAN</td>';
        echo '<td class="text-right font-bold bg-total">Rp '.number_format($total, 0, ',', '.').'</td>';
        echo '<td colspan="2" class="bg-total"></td>';
        echo '</tr>';

        echo '</tbody></table></body></html>';
    }
}
