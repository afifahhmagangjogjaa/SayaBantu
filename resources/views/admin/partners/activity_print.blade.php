<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Log Aktivitas - SayaBantu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #0f172a;
        }

        @page {
            size: landscape;
            margin: 8mm 6mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                overflow: visible !important;
            }

            .print-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            table {
                width: 100% !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            thead {
                display: table-header-group !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 text-xs min-h-screen">
    @php
        $activityLabels = [
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

        $detectDevice = function ($userAgent) {
            if (!$userAgent) return 'Unknown Device';
            $ua = strtolower($userAgent);
            if (str_contains($ua, 'android') || str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) return 'Mobile';
            if (str_contains($ua, 'windows')) return 'Windows';
            if (str_contains($ua, 'mac os') || str_contains($ua, 'macintosh')) return 'MacOS';
            if (str_contains($ua, 'linux')) return 'Linux';
            return 'Desktop';
        };
    @endphp

    <!-- Floating Print Control Bar for Screen View -->
    <div class="no-print bg-white border-b border-gray-200 px-6 py-3 sticky top-0 z-50 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <span class="font-bold text-sm text-gray-800">Pratinjau Dokumen Cetak / PDF</span>
            <span class="text-gray-400 text-xs font-mono">({{ $activities->count() }} Data Aktivitas)</span>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition">
                Tutup
            </button>
        </div>
    </div>

    <div class="print-wrapper max-w-7xl mx-auto p-4 sm:p-6 bg-white my-3 sm:my-5 rounded-xl shadow-sm border border-gray-200">
        <!-- Judul Laporan (Center - Bold Capslock Saja) -->
        <div class="text-center pb-3 mb-3 border-b-2 border-gray-900">
            <h1 class="text-base sm:text-lg font-extrabold text-gray-900 tracking-wider uppercase">LAPORAN AKTIVITAS MITRA & PENGGUNA</h1>
        </div>

        <!-- Table Log Aktivitas -->
        <table class="w-full text-left border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100 text-gray-800 border-b border-gray-300 text-[10px] font-bold uppercase tracking-wider">
                    <th class="border border-gray-300 px-2 py-2 text-center w-8">NO</th>
                    <th class="border border-gray-300 px-3 py-2 text-center w-40">USER</th>
                    <th class="border border-gray-300 px-2 py-2 text-center w-20">ROLE</th>
                    <th class="border border-gray-300 px-2.5 py-2 text-center w-36">AKTIVITAS</th>
                    <th class="border border-gray-300 px-3 py-2 text-center">DESKRIPSI</th>
                    <th class="border border-gray-300 px-2 py-2 text-center w-24">IP</th>
                    <th class="border border-gray-300 px-2 py-2 text-center w-32 max-w-[130px]">USER AGENT</th>
                    <th class="border border-gray-300 px-2.5 py-2 text-center w-36">WAKTU</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($activities as $index => $a)
                    @php
                        $role = 'Internal';
                        $roleBadgeClass = 'bg-gray-100 text-gray-700 border-gray-300';
                        if ($a->user) {
                            if ($a->user->isMitra()) {
                                $role = 'Mitra';
                                $roleBadgeClass = 'bg-blue-50 text-blue-800 border-blue-200';
                            } elseif ($a->user->isCustomer()) {
                                $role = 'Customer';
                                $roleBadgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                            } elseif ($a->user->isAdmin()) {
                                $role = 'Admin';
                            } elseif ($a->user->isSuperAdmin()) {
                                $role = 'Super Admin';
                            }
                        }

                        $actName = $activityLabels[$a->activity_type] ?? ucwords(str_replace('_', ' ', $a->activity_type));
                        $device = $detectDevice($a->user_agent);
                    @endphp
                    <tr class="{{ $index % 2 === 1 ? 'bg-gray-50/60' : 'bg-white' }} text-[10.5px]">
                        <td class="border border-gray-300 px-2 py-2 text-center text-gray-500 font-medium align-middle">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-3 py-2 align-middle">
                            <div class="font-bold text-gray-900 leading-snug">{{ $a->user->name ?? 'User Terhapus' }}</div>
                            <div class="text-gray-500 text-[9.5px] leading-tight">{{ $a->user->email ?? '-' }}</div>
                        </td>
                        <td class="border border-gray-300 px-2 py-2 text-center whitespace-nowrap align-middle">
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9.5px] font-semibold border {{ $roleBadgeClass }}">
                                {{ $role }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-2.5 py-2 font-semibold text-gray-800 text-center align-middle">
                            {{ $actName }}
                        </td>
                        <td class="border border-gray-300 px-3 py-2 text-gray-700 align-middle leading-relaxed">
                            {{ $a->description ?? '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-2 font-mono text-[9.5px] text-gray-700 text-center whitespace-nowrap align-middle">
                            {{ $a->ip_address ?? '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-2 text-[10px] w-32 max-w-[130px] break-all align-middle">
                            <div class="font-bold text-gray-900 text-center">{{ $device }}</div>
                            <div class="text-[8px] text-gray-400 mt-0.5 leading-tight break-all text-center">{{ $a->user_agent ?? '-' }}</div>
                        </td>
                        <td class="border border-gray-300 px-2.5 py-2 text-center whitespace-nowrap text-gray-700 align-middle">
                            <div class="font-semibold text-gray-900">{{ $a->created_at->diffForHumans() }}</div>
                            <div class="text-gray-500 text-[9.5px]">({{ $a->created_at->format('Y-m-d H:i:s') }} WIB)</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                            Tidak ada data aktivitas yang sesuai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 400);
        });
    </script>
</body>

</html>
