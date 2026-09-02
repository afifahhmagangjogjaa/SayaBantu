<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnerActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $activities = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $selectedActivity = null;
        $recentActivities = collect();
        $suspicious = [
            'flag' => false,
            'reasons' => [],
        ];

        if ($activityId = $request->get('activity_id')) {
            $selectedActivity = PartnerActivity::with('user')->find($activityId);

            if ($selectedActivity) {
                $recentStart = $selectedActivity->created_at->copy()->subDay();
                $recentEnd = $selectedActivity->created_at->copy();

                $recentActivities = PartnerActivity::with('user')
                    ->where('user_id', $selectedActivity->user_id)
                    ->where('id', '<>', $selectedActivity->id)
                    ->whereBetween('created_at', [$recentStart, $recentEnd])
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();

                $suspicious = $this->detectSuspicious($selectedActivity, $recentActivities);
            }
        }

        $activityTypes = PartnerActivity::select('activity_type')
            ->distinct()
            ->orderBy('activity_type')
            ->pluck('activity_type');

        return view('admin.partners.activity', compact('activities', 'activityTypes', 'selectedActivity', 'recentActivities', 'suspicious'));
    }

    public function exportCsv(Request $request)
    {
        $filename = 'aktivitas_mitra_' . now()->format('Ymd_His') . '.csv';
        $activityLabels = $this->getActivityLabels();

        $callback = function () use ($request, $activityLabels) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM so Excel opens CSV without character encoding issues
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['No', 'Waktu (WIB)', 'User', 'Email', 'Role', 'Aktivitas', 'Deskripsi', 'IP Address', 'User Agent']);

            $no = 1;
            $this->buildFilteredQuery($request)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->chunk(500, function ($rows) use ($handle, &$no, $activityLabels) {
                    foreach ($rows as $row) {
                        $user = $row->user;
                        $role = 'Internal';
                        if ($user) {
                            if ($user->isMitra()) $role = 'Mitra';
                            elseif ($user->isCustomer()) $role = 'Customer';
                            elseif ($user->isAdmin()) $role = 'Admin';
                            elseif ($user->isSuperAdmin()) $role = 'Super Admin';
                        }

                        $actName = $activityLabels[$row->activity_type] ?? ucwords(str_replace('_', ' ', $row->activity_type));

                        fputcsv($handle, [
                            $no++,
                            optional($row->created_at)->format('Y-m-d H:i:s'),
                            $user?->name ?? 'User Terhapus',
                            $user?->email ?? '-',
                            $role,
                            $actName,
                            $row->description ?? '-',
                            $row->ip_address ?? '-',
                            $row->user_agent ?? '-',
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filename = 'aktivitas_mitra_' . now()->format('Ymd_His') . '.xls';
        $activityLabels = $this->getActivityLabels();

        $activities = $this->buildFilteredQuery($request)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Log Aktivitas</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>
            * { font-family: Calibri, Arial, sans-serif !important; }
            table { border-collapse: collapse; width: 100%; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
            .title { font-size: 15pt; font-weight: bold; text-align: center; color: #0f172a; font-family: Calibri, Arial, sans-serif; height: 35px; vertical-align: middle; }
            .subtitle { font-size: 10pt; text-align: center; color: #64748b; font-family: Calibri, Arial, sans-serif; height: 22px; vertical-align: middle; }
            th { background-color: #0284c7; color: #ffffff; font-weight: bold; border: 1px solid #0369a1; padding: 8px 10px; text-align: center; vertical-align: middle; font-size: 11pt; font-family: Calibri, Arial, sans-serif; }
            td { border: 1px solid #cbd5e1; padding: 6px 10px; vertical-align: top; font-size: 10.5pt; font-family: Calibri, Arial, sans-serif; }
            tr:nth-child(even) { background-color: #f8fafc; }
            .role-mitra { color: #0284c7; font-weight: bold; text-align: center; }
            .role-customer { color: #d97706; font-weight: bold; text-align: center; }
            .role-internal { color: #475569; text-align: center; }
            .text-center { text-align: center; }
        </style></head><body>';

        $html .= '<table>';
        $html .= '<tr><td colspan="8" class="title" style="border: none; text-align: center; font-size: 15pt; font-weight: bold; font-family: Calibri, Arial, sans-serif;">LAPORAN AKTIVITAS MITRA & PENGGUNA</td></tr>';
        $html .= '<tr><td colspan="8" style="border: none; height: 10px;"></td></tr>';

        $html .= '<thead><tr>';
        $html .= '<th style="width: 40px; text-align: center;">NO</th>';
        $html .= '<th style="width: 220px; text-align: center;">USER</th>';
        $html .= '<th style="width: 100px; text-align: center;">ROLE</th>';
        $html .= '<th style="width: 180px; text-align: center;">AKTIVITAS</th>';
        $html .= '<th style="width: 260px; text-align: center;">DESKRIPSI</th>';
        $html .= '<th style="width: 130px; text-align: center;">IP</th>';
        $html .= '<th style="width: 200px; text-align: center;">USER AGENT</th>';
        $html .= '<th style="width: 160px; text-align: center;">WAKTU</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($activities as $index => $row) {
            $user = $row->user;
            $role = 'Internal';
            $roleClass = 'role-internal';
            if ($user) {
                if ($user->isMitra()) {
                    $role = 'Mitra';
                    $roleClass = 'role-mitra';
                } elseif ($user->isCustomer()) {
                    $role = 'Customer';
                    $roleClass = 'role-customer';
                } elseif ($user->isAdmin()) {
                    $role = 'Admin';
                } elseif ($user->isSuperAdmin()) {
                    $role = 'Super Admin';
                }
            }

            $actName = $activityLabels[$row->activity_type] ?? ucwords(str_replace('_', ' ', $row->activity_type));

            $userDisplay = '<b>' . htmlspecialchars($user?->name ?? 'User Terhapus') . '</b>';
            if ($user && $user->email) {
                $userDisplay .= '<br/><span style="color: #64748b; font-size: 9pt;">' . htmlspecialchars($user->email) . '</span>';
            }

            $waktuDiff = $row->created_at ? $row->created_at->diffForHumans() : '-';
            $waktuFull = $row->created_at ? '(' . $row->created_at->format('Y-m-d H:i:s') . ' WIB)' : '';
            $waktuDisplay = '<b>' . htmlspecialchars($waktuDiff) . '</b><br/><span style="color: #64748b; font-size: 8.5pt;">' . htmlspecialchars($waktuFull) . '</span>';

            $html .= '<tr>';
            $html .= '<td style="text-align: center;">' . ($index + 1) . '</td>';
            $html .= '<td>' . $userDisplay . '</td>';
            $html .= '<td class="' . $roleClass . '" style="text-align: center;">' . $role . '</td>';
            $html .= '<td>' . htmlspecialchars($actName) . '</td>';
            $html .= '<td>' . htmlspecialchars($row->description ?? '-') . '</td>';
            $html .= '<td style="font-family: Consolas, monospace; text-align: center;">' . htmlspecialchars($row->ip_address ?? '-') . '</td>';
            $html .= '<td>' . htmlspecialchars($row->user_agent ?? '-') . '</td>';
            $html .= '<td style="text-align: center; white-space: nowrap;">' . $waktuDisplay . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPrint(Request $request)
    {
        $activities = $this->buildFilteredQuery($request)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.partners.activity_print', compact('activities'));
    }

    protected function buildFilteredQuery(Request $request)
    {
        $query = PartnerActivity::with('user');

        // Filter by admin's city if user is admin
        if (auth()->user() && auth()->user()->role === 'admin') {
            $admin = auth()->user();
            $cityIds = \App\Models\City::where('admin_id', $admin->id)
                ->pluck('id')
                ->merge($admin->managedCities()->pluck('cities.id'))
                ->push($admin->city_id)
                ->filter()
                ->unique();
                
            $query->whereHas('user', function ($q) use ($cityIds) {
                $q->whereIn('city_id', $cityIds);
            });
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            if ($type !== 'all') {
                $query->where('activity_type', $type);
            }
        }

        if ($start = $request->get('start_date')) {
            $query->whereDate('created_at', '>=', $start);
        }

        if ($end = $request->get('end_date')) {
            $query->whereDate('created_at', '<=', $end);
        }

        return $query;
    }

    protected function getActivityLabels(): array
    {
        return [
            'login' => 'Login Berhasil',
            'login_failed' => 'Login Gagal',
            'logout' => 'Logout',
            'take_help' => 'Ambil Bantuan',
            'help_started' => 'Mulai Kerjakan Bantuan',
            'help_completed' => 'Selesaikan Bantuan',
            'help_cancelled' => 'Batalkan Bantuan',
            'help_created' => 'Customer Membuat Bantuan',
            'help_reviewed' => 'Customer Menilai Bantuan',
            'profile_updated' => 'Update Data Diri',
            'ktp_reuploaded' => 'Upload Ulang KTP',
            'phone_changed' => 'Mengubah Nomor Telepon',
            'password_changed' => 'Mengubah Password',
            'balance_topup' => 'Top Up Saldo',
            'balance_withdraw' => 'Tarik Saldo',
            'balance_deducted' => 'Pengurangan Saldo',
            'security_bruteforce' => 'Banyak Login Gagal',
            'security_location_anomaly' => 'Lokasi Mencurigakan',
            'security_outdated_app' => 'Aplikasi Versi Lama',
            'ktp_verified' => 'KTP Diverifikasi',
            'ktp_rejected' => 'KTP Ditolak',
        ];
    }

    public function resetSessions($userId)
    {
        DB::table('sessions')->where('user_id', $userId)->delete();

        return back()->with('success', 'Sesi login mitra telah direset.');
    }

    public function resetPassword($userId)
    {
        $user = User::findOrFail($userId);

        $newPassword = Str::random(12);
        $user->password = bcrypt($newPassword);
        $user->save();

        return back()->with('success', 'Password mitra telah direset. Password baru: ' . $newPassword);
    }

    protected function detectSuspicious(PartnerActivity $selectedActivity, $recentActivities): array
    {
        $reasons = [];

        $all = collect([$selectedActivity])->merge($recentActivities);

        $failedLogins = $all->where('activity_type', 'login_failed')
            ->where('created_at', '>=', now()->subMinutes(10));
        if ($failedLogins->count() >= 5) {
            $reasons[] = 'Banyak login gagal dalam 10 menit terakhir.';
        }

        $ips = $all->pluck('ip_address')->filter()->unique();
        if ($ips->count() >= 3) {
            $reasons[] = 'IP address berubah beberapa kali dalam 24 jam.';
        }

        if ($selectedActivity->activity_type === 'login') {
            $hour = (int) $selectedActivity->created_at->format('H');
            if ($hour <= 4 || $hour >= 23) {
                $reasons[] = 'Login pada jam tidak wajar.';
            }
        }

        return [
            'flag' => !empty($reasons),
            'reasons' => $reasons,
        ];
    }
}
