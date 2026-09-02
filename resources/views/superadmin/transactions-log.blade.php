<div>
    <style>
        @page {
            size: A4 portrait;
            margin: 0; /* Menghilangkan teks tanggal dan judul URL default browser */
        }

        @media print {
            /* Sembunyikan elemen web UI */
            aside, nav, header, footer, .no-print,
            .summary-cards-section,
            .filter-form-section,
            .pagination-container,
            .export-btn-group,
            button {
                display: none !important;
            }

            html, body {
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 8mm 6mm !important;
                background: white !important;
                color: #111827 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                font-size: 10px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            main, .min-h-screen, .px-6.py-8 {
                min-height: 0 !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
                width: 100% !important;
            }

            .main-table-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .p-4, .p-5, .p-6, .border-b {
                padding: 0 !important;
                border: none !important;
            }

            .print-header {
                display: block !important;
                text-align: center !important;
                margin: 0 0 14px 0 !important;
                padding-bottom: 8px !important;
                border-bottom: 2px solid #000000 !important;
            }

            .print-header h1 {
                font-size: 19px !important;
                font-weight: 900 !important;
                color: #000000 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                margin: 0 0 4px 0 !important;
                text-align: center !important;
            }

            .print-header p {
                font-size: 10px !important;
                color: #4b5563 !important;
                margin: 0 !important;
                text-align: center !important;
            }

            table {
                width: 100% !important;
                max-width: 100% !important;
                border-collapse: collapse !important;
                table-layout: fixed !important;
                page-break-inside: auto !important;
                margin-top: 6px !important;
            }

            thead {
                display: table-header-group !important;
            }

            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            th, td {
                border: 1px solid #cbd5e1 !important;
                padding: 6px 4px !important;
                font-size: 10px !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
            }

            th {
                background-color: #f3f4f6 !important;
                color: #111827 !important;
                font-weight: bold !important;
                text-align: center !important;
                font-size: 10.5px !important;
            }

            td {
                color: #111827 !important;
                vertical-align: middle !important;
            }

            /* Proporsi Lebar Kolom Print (Total 100% Pas A4) */
            th:nth-child(1), td:nth-child(1) { width: 4% !important; text-align: center !important; }
            th:nth-child(2), td:nth-child(2) { width: 15% !important; }
            th:nth-child(3), td:nth-child(3) { width: 20% !important; }
            th:nth-child(4), td:nth-child(4) { width: 14% !important; text-align: center !important; }
            th:nth-child(5), td:nth-child(5) { width: 13% !important; text-align: right !important; }
            th:nth-child(6), td:nth-child(6) { width: 16% !important; text-align: center !important; font-size: 9px !important; word-break: break-all !important; }
            th:nth-child(7), td:nth-child(7) { width: 18% !important; text-align: center !important; }

            td:nth-child(3) .user-name-print,
            td:nth-child(3) div.text-xs {
                font-size: 11.5px !important;
                font-weight: 700 !important;
                color: #0f172a !important;
                line-height: 1.25 !important;
            }

            td:nth-child(3) .user-email-print,
            td:nth-child(3) div.text-\[10px\] {
                font-size: 9px !important;
                color: #64748b !important;
                line-height: 1.2 !important;
            }

            td:nth-child(7) span {
                font-size: 10px !important;
                padding: 2px 6px !important;
                white-space: normal !important;
                word-break: break-word !important;
                display: inline-block !important;
                font-weight: 600 !important;
            }
        }

        .print-header {
            display: none;
        }
    </style>

    <!-- Print Header (Hanya muncul saat PDF/Print: Langsung Judul Center) -->
    <div class="print-header">
        <h1 class="text-2xl font-black tracking-wider text-black uppercase">LAPORAN DAFTAR TRANSAKSI</h1>
        <p class="text-xs text-gray-600 mt-1">
            Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Oleh: {{ auth()->user()->name ?? 'Super Admin' }}
            @if($from || $to)
                | Periode: {{ $from ? \Carbon\Carbon::parse($from)->translatedFormat('d M Y') : 'Awal' }} s/d {{ $to ? \Carbon\Carbon::parse($to)->translatedFormat('d M Y') : 'Sekarang' }}
            @endif
        </p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards-section grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Top Up Card -->
        <div class="bg-white rounded-2xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Top Up</p>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Withdraw Card -->
        <div class="bg-white rounded-2xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Withdraw</p>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalWithdraw, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Transactions Card -->
        <div class="bg-white rounded-2xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Transaksi</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalTransactions) }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Arus Kas Bersih Card -->
        <div class="bg-white rounded-2xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Arus Kas Bersih</p>
                    <h3 class="text-2xl font-bold {{ $netCashflow >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        Rp {{ number_format($netCashflow, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Section -->
    <div class="main-table-card bg-white rounded-2xl shadow">
        <!-- Filter Section -->
        <div class="p-6 border-b border-gray-100 no-print">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Transaksi</h2>
                <div class="export-btn-group flex items-center gap-2">
                    <a href="{{ route('superadmin.transactions.export.excel', ['type' => $type, 'search' => $search, 'from' => $from, 'to' => $to]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors cursor-pointer shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Excel
                    </a>
                    <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors cursor-pointer shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        PDF
                    </button>
                </div>
            </div>

            <div class="filter-form-section flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[280px]">
                    <input wire:model.live.debounce.400ms="search" type="text" placeholder="Cari user, email atau ref"
                        class="border border-gray-300 rounded-lg px-4 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm" />
                </div>

                <select wire:model.live="type" class="border border-gray-300 rounded-lg pl-3.5 pr-9 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer">
                    <option value="all">Semua Tipe</option>
                    <option value="topup">Topup</option>
                    <option value="withdraw">Withdraw</option>
                    <option value="other">Lainnya</option>
                </select>

                <input wire:model.live="from" type="date" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()" class="border border-gray-300 rounded-lg px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer" />
                <input wire:model.live="to" type="date" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()" class="border border-gray-300 rounded-lg px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer" />

                <select wire:model.live="perPage" class="border border-gray-300 rounded-lg pl-3.5 pr-9 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer">
                    <option value="10">10 / halaman</option>
                    <option value="15">15 / halaman</option>
                    <option value="30">30 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>

                <button wire:click="resetFilters" type="button" class="border border-gray-300 rounded-lg px-3.5 py-2.5 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer flex items-center gap-1 text-gray-700" title="Reset Filter">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span class="hidden sm:inline">Reset</span>
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200">
                    <tr>
                        <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 40px;">No</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 130px;">Waktu</th>
                        <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 120px;">Tipe</th>
                        <th class="px-2.5 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 120px;">Jumlah</th>
                        <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 100px;">Ref</th>
                        <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($transactions as $index => $t)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-2.5 py-3 text-xs text-center text-gray-500">
                                {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-2.5 py-3 text-xs text-gray-600 whitespace-nowrap">
                                {{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->translatedFormat('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-2.5 py-3">
                                @if($t->user)
                                    <div class="flex items-center gap-3" style="gap: 10px;">
                                        <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center flex-shrink-0 no-print" style="width: 28px; height: 28px; min-width: 28px;">
                                            {{ strtoupper(substr($t->user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0" style="margin-left: 2px;">
                                            <div class="text-xs font-semibold text-gray-900 leading-tight truncate max-w-[160px] user-name-print">{{ $t->user->name }}</div>
                                            <div class="text-[10px] text-gray-500 truncate max-w-[160px] user-email-print">{{ $t->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400">-</div>
                                @endif
                            </td>
                            <td class="px-2.5 py-3 text-center whitespace-nowrap">
                                @php
                                    $userRole = optional($t->user)->role;
                                    if ($t->type === 'withdraw' || ($userRole === 'mitra' && in_array($t->type, ['deduction', 'withdraw_deduction']))) {
                                        $label = 'Withdraw';
                                        $badgeStyle = 'bg-amber-50 text-amber-700 border border-amber-200';
                                    } elseif ($userRole === 'mitra' && ($t->type === 'topup' || $t->type === 'income')) {
                                        $label = 'Pendapatan Mitra';
                                        $badgeStyle = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                    } elseif ($t->type === 'topup' || $t->type === 'deposit') {
                                        $label = 'Topup';
                                        $badgeStyle = 'bg-blue-50 text-blue-700 border border-blue-100';
                                    } elseif ($t->type === 'deduction') {
                                        $label = 'Pembayaran Bantuan';
                                        $badgeStyle = 'bg-rose-50 text-rose-700 border border-rose-100';
                                    } else {
                                        $label = ucfirst($t->type);
                                        $badgeStyle = 'bg-gray-50 text-gray-700 border border-gray-200';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $badgeStyle }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-2.5 py-3 text-xs text-right font-bold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-2.5 py-3 text-center whitespace-nowrap">
                                <code class="text-[11px] bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-700 font-mono">{{ $t->request_code ?? $t->order_id ?? $t->reference_id ?? $t->reference ?? '-' }}</code>
                            </td>
                            <td class="px-2.5 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-50 text-green-700 border border-green-200">
                                    {{ $t->status ?? 'ok' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500 font-medium">Tidak ada transaksi</p>
                                    <p class="text-xs text-gray-400 mt-1">Data transaksi akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-container px-6 py-4 border-t border-gray-100 bg-gray-50 no-print">
            {{ $transactions->links('vendor.pagination.superadmin') }}
        </div>
    </div>
</div>