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
            th:nth-child(2), td:nth-child(2) { width: 14% !important; }
            th:nth-child(3), td:nth-child(3) { width: 18% !important; }
            th:nth-child(4), td:nth-child(4) { width: 12% !important; text-align: center !important; } /* Kolom Kota */
            th:nth-child(5), td:nth-child(5) { width: 12% !important; text-align: center !important; } /* Tipe */
            th:nth-child(6), td:nth-child(6) { width: 13% !important; text-align: right !important; }  /* Jumlah */
            th:nth-child(7), td:nth-child(7) { width: 14% !important; text-align: center !important; font-size: 9px !important; word-break: break-all !important; }
            th:nth-child(8), td:nth-child(8) { width: 13% !important; text-align: center !important; } /* Status */

            td:nth-child(3) .user-name-print,
            td:nth-child(3) div.text-xs {
                font-size: 11px !important;
                font-weight: 700 !important;
                color: #0f172a !important;
                line-height: 1.25 !important;
            }

            td:nth-child(3) .user-email-print,
            td:nth-child(3) div.text-\[10px\] {
                font-size: 8.5px !important;
                color: #64748b !important;
                line-height: 1.2 !important;
            }

            td:nth-child(8) span {
                font-size: 9.5px !important;
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
        @php
            $currentCityName = 'SEMUA WILAYAH';
            if (!empty($city_id)) {
                $foundCity = $cities->firstWhere('id', $city_id);
                if ($foundCity) {
                    $currentCityName = strtoupper($foundCity->name);
                }
            }
        @endphp
        <h1 class="text-2xl font-black tracking-wider text-black uppercase">LAPORAN DAFTAR TRANSAKSI ({{ $currentCityName }})</h1>
        <p class="text-xs text-gray-600 mt-1">
            Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Oleh: {{ auth()->user()->name ?? 'Super Admin' }}
            | Periode: {{ $from && $to ? \Carbon\Carbon::parse($from)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($to)->translatedFormat('d M Y') : 'Keseluruhan Transaksi (Semua Waktu)' }}
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2.5 text-xs font-semibold shadow-xs no-print">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-2.5 text-xs font-semibold shadow-xs no-print">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="summary-cards-section grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Top Up Card -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Total Top Up</p>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Withdraw Card -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Total Withdraw</p>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Rp {{ number_format($totalWithdraw, 0, ',', '.') }}</h3>
                </div>
                <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Transactions Card -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Total Transaksi</p>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">{{ number_format($totalTransactions) }}</h3>
                </div>
                <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Arus Kas Bersih Card -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Arus Kas Bersih</p>
                    <h3 class="text-lg sm:text-xl font-bold {{ $netCashflow >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-0.5">
                        Rp {{ number_format($netCashflow, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <a href="{{ route('superadmin.transactions.export.excel', ['type' => $type, 'search' => $search, 'from' => $from, 'to' => $to, 'city_id' => $city_id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors cursor-pointer shadow-sm">
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
                    <button type="button" wire:click="openDeleteModal" class="inline-flex items-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors cursor-pointer shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Riwayat
                    </button>
                </div>
            </div>

            <div class="filter-form-section flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[220px]">
                    <input wire:model.live.debounce.400ms="search" type="text" placeholder="Cari user, email atau ref..."
                        class="border border-gray-300 rounded-lg px-4 py-2.5 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm" />
                </div>

                <!-- Dropdown Filter Wilayah / Kota -->
                <select wire:model.live="city_id" class="border border-gray-300 rounded-lg pl-3.5 pr-9 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer font-medium text-gray-700">
                    <option value="">Semua Wilayah</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="type" class="border border-gray-300 rounded-lg pl-3.5 pr-9 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer">
                    <option value="all">Semua Tipe</option>
                    <option value="topup">Topup</option>
                    <option value="withdraw">Withdraw</option>
                    <option value="other">Lainnya</option>
                </select>

                <select wire:model.live="period" class="border border-gray-300 rounded-lg pl-3.5 pr-9 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer font-medium text-gray-700">
                    <option value="this_month">Bulan Ini</option>
                    <option value="all_time">Keseluruhan Transaksi</option>
                    <option value="today">Hari Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="custom">Pilih Rentang Tanggal</option>
                </select>

                <select wire:model.live="perPage" class="border border-gray-300 rounded-lg pl-3.5 pr-9 py-2.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer">
                    <option value="10">10 / halaman</option>
                    <option value="15">15 / halaman</option>
                    <option value="30">30 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>

                <button wire:click="resetFilters" type="button" class="border border-gray-300 rounded-lg px-3.5 py-2.5 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm cursor-pointer flex items-center gap-1.5 text-gray-700" title="Reset Filter">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span class="hidden sm:inline">Reset</span>
                </button>
            </div>

            @if($period === 'custom' || ($from && $to && !in_array($period, ['this_month', 'all_time', 'today', 'last_month'])))
                <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2.5 bg-slate-50/70 p-2.5 rounded-lg border border-dashed border-slate-200">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Rentang Tanggal:</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <input wire:model.live="from" type="date" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()" class="border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-xs cursor-pointer" title="Dari Tanggal" />
                        <span class="text-gray-400 text-xs font-semibold">s/d</span>
                        <input wire:model.live="to" type="date" max="{{ date('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()" class="border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-xs cursor-pointer" title="Sampai Tanggal" />
                    </div>
                </div>
            @endif
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-200">
                    <tr>
                        <th class="px-3.5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 45px;">No</th>
                        <th class="px-3.5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 140px;">Waktu</th>
                        <th class="px-5 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-3.5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 120px;">Kota / Wilayah</th>
                        <th class="px-3.5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 130px;">Tipe</th>
                        <th class="px-3.5 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 130px;">Jumlah</th>
                        <th class="px-3.5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 110px;">Ref</th>
                        <th class="px-3.5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap" style="width: 100px;">Status</th>
                        <th class="px-3.5 py-3.5 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap no-print" style="width: 75px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($transactions as $index => $t)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-3.5 py-3.5 text-xs text-center text-gray-500">
                                {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-3.5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                                {{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->translatedFormat('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($t->user)
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-2xs mr-4 no-print">
                                            {{ strtoupper(substr($t->user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs sm:text-sm font-semibold text-gray-900 leading-snug truncate max-w-[190px] user-name-print">{{ $t->user->name }}</div>
                                            <div class="text-[11px] text-gray-500 truncate max-w-[190px] user-email-print mt-0.5">{{ $t->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400">-</div>
                                @endif
                            </td>
                            <!-- Kolom Kota/Wilayah -->
                            <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                <span class="text-xs font-medium text-gray-700 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-md">
                                    {{ $t->user->city_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
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
                            <td class="px-3.5 py-3.5 text-xs text-right font-bold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                <code class="text-[11px] bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 text-gray-700 font-mono">{{ $t->request_code ?? $t->order_id ?? $t->reference_id ?? $t->reference ?? '-' }}</code>
                            </td>
                            <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-50 text-green-700 border border-green-200">
                                    {{ $t->status ?? 'ok' }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3.5 text-center whitespace-nowrap no-print">
                                <div class="flex items-center justify-center gap-1">
                                    @if($t->user)
                                        <button type="button" wire:click="viewUserBalance({{ $t->user->id }})" wire:loading.attr="disabled"
                                            class="p-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-semibold transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                            title="Lihat Detail Saldo & Info User ({{ $t->user->name }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @endif
                                    <button type="button" 
                                        wire:click="deleteTransaction({{ $t->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus data transaksi ini?"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center" 
                                        title="Hapus Data Transaksi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
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

    <!-- Modal Detail Saldo User (Balanced & Spacious Layout) -->
    @if ($showUserBalanceModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto no-print">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Dark Backdrop Overlay -->
                <div class="fixed inset-0 transition-opacity bg-black/75 backdrop-blur-sm" style="background-color: rgba(15, 23, 42, 0.80); backdrop-filter: blur(4px);" wire:click="closeUserBalanceModal"></div>

                <div class="relative inline-block w-full max-w-xl p-6 sm:p-7 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-gray-100">
                    <!-- Header Modal -->
                    <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-2xl {{ $selectedUser->role === 'mitra' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }} font-bold text-base flex items-center justify-center flex-shrink-0 shadow-xs mr-4">
                                {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <h3 class="text-base font-bold text-gray-900 leading-snug">{{ $selectedUser->name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $selectedUser->role === 'mitra' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $selectedUser->role === 'mitra' ? 'Mitra Relawan' : 'Customer' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2.5 mt-1 text-xs text-gray-500 flex-wrap">
                                    <span>{{ $selectedUser->email }}</span>
                                    @if($selectedUser->phone)
                                        <span class="flex items-center gap-1"><span class="text-gray-300">•</span> 📞 {{ $selectedUser->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button type="button" wire:click="closeUserBalanceModal" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-400 hover:text-gray-700 flex items-center justify-center transition cursor-pointer" title="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Saldo & Metrics Section -->
                    <div class="space-y-4 my-5">
                        <!-- Main Balance Card -->
                        <div class="p-5 sm:p-6 rounded-2xl shadow-sm text-white" style="background: {{ $selectedUser->role === 'mitra' ? 'linear-gradient(135deg, #059669 0%, #0d9488 100%)' : 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)' }};">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-white/90 uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span>Saldo Aktif Saat Ini</span>
                                </div>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-white/20 text-white font-medium">Realtime</span>
                            </div>
                            <h4 class="text-3xl font-black mt-2.5 text-white tracking-tight">Rp {{ number_format($selectedUserBalance, 0, ',', '.') }}</h4>
                            <p class="text-xs text-white/80 mt-2">
                                {{ $selectedUser->role === 'mitra' ? 'Saldo aktif relawan yang siap untuk ditarik (withdraw).' : 'Saldo aktif customer yang siap digunakan untuk pesanan bantuan.' }}
                            </p>
                        </div>

                        <!-- 3 Stats Cards (Row) -->
                        @if($selectedUser->role === 'mitra')
                            <div class="grid grid-cols-3 gap-3">
                                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-center">
                                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Pendapatan</span>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 mt-1">Rp {{ number_format($selectedUserStats['total_income'] ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 text-center">
                                    <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">Withdraw</span>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 mt-1">Rp {{ number_format($selectedUserStats['total_withdraw'] ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 text-center">
                                    <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block">Transaksi</span>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 mt-1">{{ number_format($selectedUserStats['total_tx'] ?? 0) }}x</div>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-3">
                                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-center">
                                    <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Top Up</span>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 mt-1">Rp {{ number_format($selectedUserStats['total_topup'] ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100 text-center">
                                    <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Pembayaran</span>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 mt-1">Rp {{ number_format($selectedUserStats['total_payment'] ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 text-center">
                                    <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block">Transaksi</span>
                                    <div class="text-xs sm:text-sm font-black text-gray-900 mt-1">{{ number_format($selectedUserStats['total_tx'] ?? 0) }}x</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL HAPUS RIWAYAT BERDASARKAN PERIODE -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs transition-opacity no-print">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-5 border border-gray-100 animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Header Modal -->
                <div class="flex items-start justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Hapus Riwayat Transaksi</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih periode bulanan atau tahunan</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeDeleteModal" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Pilihan Tipe Hapus (Tabs) -->
                <div class="flex rounded-xl bg-gray-100 p-1 gap-1">
                    <button type="button" wire:click="$set('deleteMode', 'monthly')"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $deleteMode === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                        Bulanan
                    </button>
                    <button type="button" wire:click="$set('deleteMode', 'yearly')"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $deleteMode === 'yearly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                        Tahunan
                    </button>
                    <button type="button" wire:click="$set('deleteMode', 'all')"
                        class="flex-1 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $deleteMode === 'all' ? 'bg-rose-600 text-white shadow-sm' : 'text-gray-500 hover:text-rose-600' }}">
                        Hapus Semua
                    </button>
                </div>

                @if(!empty($city_id))
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-2.5 flex gap-2 items-center text-xs text-blue-800 font-medium">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>Filter aktif wilayah: <strong>{{ optional($cities->firstWhere('id', $city_id))->name }}</strong></span>
                    </div>
                @endif

                <!-- Konten Sesuai Mode -->
                @if($deleteMode === 'monthly')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                            <select wire:model.live="deleteYear" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 cursor-pointer">
                                @foreach($availableYears ?? [date('Y')] as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Bulan</label>
                            <select wire:model.live="deleteMonth" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 cursor-pointer">
                                <option value="1">01 - Januari</option>
                                <option value="2">02 - Februari</option>
                                <option value="3">03 - Maret</option>
                                <option value="4">04 - April</option>
                                <option value="5">05 - Mei</option>
                                <option value="6">06 - Juni</option>
                                <option value="7">07 - Juli</option>
                                <option value="8">08 - Agustus</option>
                                <option value="9">09 - September</option>
                                <option value="10">10 - Oktober</option>
                                <option value="11">11 - November</option>
                                <option value="12">12 - Desember</option>
                            </select>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Hanya data pada bulan dan tahun terpilih yang akan dihapus.
                            </p>
                        </div>

                        <button type="button"
                            wire:click="executeDelete"
                            wire:confirm="Apakah Anda yakin ingin menghapus data transaksi riwayat bulan terpilih? Tindakan ini tidak dapat dibatalkan."
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Riwayat Bulan Terpilih</span>
                        </button>
                    </div>

                @elseif($deleteMode === 'yearly')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Tahun</label>
                            <select wire:model.live="deleteYear" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 cursor-pointer">
                                @foreach($availableYears ?? [date('Y')] as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex gap-2.5 items-start">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Seluruh riwayat transaksi sepanjang tahun <strong>{{ $deleteYear }}</strong> (Januari - Desember) akan dihapus.
                            </p>
                        </div>

                        <button type="button"
                            wire:click="executeDelete"
                            wire:confirm="Apakah Anda yakin ingin menghapus SELURUH data transaksi pada tahun {{ $deleteYear }}? Tindakan ini tidak dapat dibatalkan."
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Seluruh Tahun {{ $deleteYear }}</span>
                        </button>
                    </div>

                @else
                    <div class="space-y-4">
                        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 flex gap-3 items-start">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="text-xs font-bold text-rose-900 uppercase tracking-wider mb-1">Peringatan Ekstrem</h4>
                                <p class="text-xs text-rose-800 leading-relaxed">
                                    Tindakan ini akan menghapus <strong>SEMUA</strong> riwayat transaksi yang ada dalam sistem tanpa batasan waktu. Data yang dihapus tidak dapat dipulihkan kembali.
                                </p>
                            </div>
                        </div>

                        <button type="button"
                            wire:click="executeDelete"
                            wire:confirm="PERINGATAN EKSTREM: Apakah Anda BENAR-BENAR yakin ingin menghapus SEMUA data riwayat transaksi tanpa terkecuali?"
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Semua Riwayat Sekarang</span>
                        </button>
                    </div>
                @endif

                <!-- Footer Cancel -->
                <div class="border-t border-gray-100 pt-3 flex items-center justify-end">
                    <button type="button" wire:click="closeDeleteModal" class="px-4 py-2 text-xs text-gray-600 hover:text-gray-800 font-medium hover:bg-gray-100 rounded-lg transition cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>