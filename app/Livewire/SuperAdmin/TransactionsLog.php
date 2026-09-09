<?php
namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\BalanceTransaction;
use App\Models\WithdrawRequest;
use App\Models\City;

#[Layout('layouts.superadmin')]
class TransactionsLog extends Component
{
    use WithPagination;

    public $search = '';
    public $type = 'all'; // types: all, topup, withdraw, other
    public $perPage = 15;
    public $period = 'this_month'; // options: this_month, all_time, today, last_month, custom
    public $from = null;
    public $to = null;
    public $city_id = ''; // Filter wilayah / kota

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => 'all'],
        'period' => ['except' => 'this_month'],
        'from' => ['except' => ''],
        'to' => ['except' => ''],
        'city_id' => ['except' => ''],
    ];

    public function mount()
    {
        if (request()->has('period')) {
            $this->period = request('period');
        }

        if (request()->has('city_id')) {
            $this->city_id = request('city_id');
        }

        if ($this->period === 'this_month') {
            $this->from = now()->startOfMonth()->format('Y-m-d');
            $this->to = now()->format('Y-m-d');
        } elseif ($this->period === 'all_time') {
            $this->from = null;
            $this->to = null;
        } elseif ($this->period === 'today') {
            $this->from = now()->format('Y-m-d');
            $this->to = now()->format('Y-m-d');
        } elseif ($this->period === 'last_month') {
            $this->from = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->to = now()->subMonth()->endOfMonth()->format('Y-m-d');
        } else {
            if (request()->has('from')) $this->from = request('from');
            if (request()->has('to')) $this->to = request('to');
            if (!$this->from && !$this->to && $this->period !== 'all_time') {
                $this->from = now()->startOfMonth()->format('Y-m-d');
                $this->to = now()->format('Y-m-d');
                $this->period = 'this_month';
            }
        }
    }

    public function updatedCityId()
    {
        $this->resetPage();
    }

    public function updatedPeriod($val)
    {
        $this->resetPage();
        if ($val === 'this_month') {
            $this->from = now()->startOfMonth()->format('Y-m-d');
            $this->to = now()->format('Y-m-d');
        } elseif ($val === 'all_time') {
            $this->from = null;
            $this->to = null;
        } elseif ($val === 'today') {
            $this->from = now()->format('Y-m-d');
            $this->to = now()->format('Y-m-d');
        } elseif ($val === 'last_month') {
            $this->from = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->to = now()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif ($val === 'custom') {
            if (!$this->from) $this->from = now()->startOfMonth()->format('Y-m-d');
            if (!$this->to) $this->to = now()->format('Y-m-d');
        }
    }

    public function updatedFrom()
    {
        $this->period = 'custom';
        $this->resetPage();
    }

    public function updatedTo()
    {
        $this->period = 'custom';
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->type = 'all';
        $this->period = 'this_month';
        $this->city_id = '';
        $this->from = now()->startOfMonth()->format('Y-m-d');
        $this->to = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function exportExcel()
    {
        $cityName = 'Semua-Wilayah';
        if ($this->city_id) {
            $selectedCity = City::find($this->city_id);
            if ($selectedCity) {
                $cityName = \Illuminate\Support\Str::slug($selectedCity->name);
            }
        }

        $transactions = $this->getBaseQuery()->with(['user.city'])->orderBy('created_at', 'desc')->get();
        $filename = 'Laporan-Transaksi-' . $cityName . '-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($transactions, $cityName) {
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
            
            // Header Judul di Tengah (Center) dengan Colspan
            echo '<table>';
            echo '<tr><td colspan="9" class="title-main" style="border:none; padding-top:10px; padding-bottom:4px;">LAPORAN DAFTAR TRANSAKSI (' . strtoupper(str_replace('-', ' ', $cityName)) . ')</td></tr>';
            echo '<tr><td colspan="9" class="title-sub" style="border:none; padding-bottom:15px;">Tanggal Cetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB | Oleh: ' . htmlspecialchars(auth()->user()->name ?? 'Super Admin') . '</td></tr>';
            echo '</table>';

            echo '<table>';
            echo '<thead><tr>';
            echo '<th style="width: 45px;">No</th>';
            echo '<th style="width: 140px;">Waktu</th>';
            echo '<th style="width: 180px;">Nama User</th>';
            echo '<th style="width: 200px;">Email</th>';
            echo '<th style="width: 130px;">Wilayah / Kota</th>';
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
                echo '<td class="text-center">' . $no++ . '</td>';
                echo '<td class="text-center">' . ($t->created_at ? $t->created_at->format('d/m/Y H:i') : '-') . '</td>';
                echo '<td class="text-left">' . htmlspecialchars(optional($t->user)->name ?? '-') . '</td>';
                echo '<td class="text-left">' . htmlspecialchars(optional($t->user)->email ?? '-') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($userCity) . '</td>';
                echo '<td class="text-center">' . $label . '</td>';
                echo '<td class="text-right font-bold">' . number_format($t->amount, 0, ',', '.') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($refCode) . '</td>';
                echo '<td class="text-center">' . $statusLabel . '</td>';
                echo '</tr>';
            }

            echo '<tr>';
            echo '<td colspan="6" class="text-right font-bold bg-total">TOTAL KESELURUHAN</td>';
            echo '<td class="text-right font-bold bg-total">Rp ' . number_format($total, 0, ',', '.') . '</td>';
            echo '<td colspan="2" class="bg-total"></td>';
            echo '</tr>';

            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportPdf()
    {
        $this->js('window.print()');
    }

    private function getBaseQuery()
    {
        $query = BalanceTransaction::query();

        if ($this->type && $this->type !== 'all') {
            if ($this->type === 'withdraw') {
                $query->where(function ($q) {
                    $q->where('type', 'withdraw')
                      ->orWhere(function ($sub) {
                          $sub->whereIn('type', ['deduction', 'withdraw_deduction'])
                              ->whereHas('user', fn($u) => $u->where('role', 'mitra'));
                      });
                });
            } elseif ($this->type === 'topup') {
                $query->where('type', 'topup');
            } elseif ($this->type === 'other') {
                $query->whereNotIn('type', ['topup', 'withdraw']);
            } else {
                $query->where('type', $this->type);
            }
        }

        if ($this->search) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('request_code', 'like', "%{$s}%")
                    ->orWhere('order_id', 'like', "%{$s}%")
                    ->orWhere('reference_id', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($qu) use ($s) {
                        $qu->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        if ($this->from) {
            $query->whereDate('created_at', '>=', $this->from);
        }
        if ($this->to) {
            $query->whereDate('created_at', '<=', $this->to);
        }

        // Filter Wilayah Berdasarkan Kota User
        if ($this->city_id) {
            $query->whereHas('user', function ($q) {
                $q->where('city_id', $this->city_id);
            });
        }

        return $query;
    }

    public $showUserBalanceModal = false;
    public $selectedUserId = null;
    public $selectedUser = null;
    public $selectedUserBalance = 0;
    public $selectedUserStats = [];
    public $selectedUserRecentTransactions = [];

    public function viewUserBalance($userId)
    {
        $this->selectedUserId = $userId;
        $this->selectedUser = \App\Models\User::with(['city'])->find($userId);

        if ($this->selectedUser) {
            $this->selectedUserBalance = (float) $this->selectedUser->balance;
            $userTxQuery = BalanceTransaction::where('user_id', $userId);
            $isMitra = $this->selectedUser->role === 'mitra';

            if ($isMitra) {
                $totalIncome = (clone $userTxQuery)->whereIn('type', ['income', 'topup'])->where('status', 'completed')->sum('amount');
                $withdrawTx = (clone $userTxQuery)->whereIn('type', ['withdraw', 'withdraw_deduction', 'deduction'])->where('status', 'completed')->sum('amount');
                $withdrawReq = \App\Models\WithdrawRequest::where('user_id', $userId)->whereIn('status', ['success', 'completed', 'approved'])->sum('amount');
                $totalWithdraw = max((float)$withdrawTx, (float)$withdrawReq);

                $this->selectedUserStats = [
                    'is_mitra' => true,
                    'total_income' => $totalIncome,
                    'total_withdraw' => $totalWithdraw,
                    'total_tx' => (clone $userTxQuery)->count(),
                ];
            } else {
                $totalTopup = (clone $userTxQuery)->where('type', 'topup')->where('status', 'completed')->sum('amount');
                $totalPayment = (clone $userTxQuery)->where('type', 'deduction')->where('status', 'completed')->sum('amount');

                $this->selectedUserStats = [
                    'is_mitra' => false,
                    'total_topup' => $totalTopup,
                    'total_payment' => $totalPayment,
                    'total_tx' => (clone $userTxQuery)->count(),
                ];
            }

            $this->selectedUserRecentTransactions = (clone $userTxQuery)
                ->orderByDesc('created_at')
                ->take(6)
                ->get();

            $this->showUserBalanceModal = true;
        }
    }

    public function closeUserBalanceModal()
    {
        $this->showUserBalanceModal = false;
        $this->selectedUserId = null;
        $this->selectedUser = null;
        $this->selectedUserStats = [];
        $this->selectedUserRecentTransactions = [];
    }

    public function render()
    {
        $query = $this->getBaseQuery()->with(['user.city']);
        $transactions = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        // Summary statistics (ikut terfilter sesuai wilayah yang dipilih)
        $summaryQuery = $this->getBaseQuery();
        
        $totalRevenue = $summaryQuery->clone()
            ->where('type', 'topup')
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'mitra');
            })
            ->where('status', 'completed')
            ->sum('amount');
        
        $totalWithdrawTx = $summaryQuery->clone()
            ->where('type', 'withdraw')
            ->where('status', 'completed')
            ->sum('amount');

        $withdrawReqQuery = WithdrawRequest::whereIn('status', ['success', 'completed', 'approved', 'pending', 'processing']);
        if ($this->from) $withdrawReqQuery->whereDate('created_at', '>=', $this->from);
        if ($this->to) $withdrawReqQuery->whereDate('created_at', '<=', $this->to);
        if ($this->city_id) {
            $withdrawReqQuery->whereHas('user', function ($q) {
                $q->where('city_id', $this->city_id);
            });
        }
        $totalWithdraw = max($totalWithdrawTx, (float) $withdrawReqQuery->sum('amount'));

        $totalTransactions = $summaryQuery->clone()->count();
        $netCashflow = (float) $totalRevenue - (float) $totalWithdraw;

        // Ambil daftar kota untuk dropdown filter
        $cities = City::orderBy('name', 'asc')->get();

        return view('superadmin.transactions-log', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'totalWithdraw' => $totalWithdraw,
            'totalTransactions' => $totalTransactions,
            'netCashflow' => $netCashflow,
            'cities' => $cities,
        ]);
    }
}