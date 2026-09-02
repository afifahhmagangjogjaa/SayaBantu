@php
    $title = 'Pengaturan Bantuan';
    $breadcrumb = 'Super Admin / Pengaturan / Bantuan';
@endphp

<div>
    <!-- Admin Fee Revenue Chart Section -->
    <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Chart Header -->
                <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-gray-50/80">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Pendapatan Biaya Admin</h2>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Grafik pendapatan dari biaya admin (tidak termasuk nominal pokok transaksi)</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <!-- Summary Cards (3 Cards) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-8" style="gap: 18px;">
                        <!-- 1. Total Biaya Admin -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-gray-500 truncate" title="Total Biaya Admin">Total Biaya Admin</div>
                                <div class="text-lg sm:text-xl font-bold text-gray-900 leading-tight my-0.5 truncate" title="Rp {{ number_format($totalAll ?? 0, 0, ',', '.') }}">
                                    Rp {{ number_format($totalAll ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-emerald-600 font-medium truncate">Sepanjang waktu</div>
                            </div>
                        </div>

                        <!-- 2. Biaya Admin Bulan Ini -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-gray-500 truncate" title="Biaya Admin Bulan Ini">Biaya Admin Bulan Ini</div>
                                <div class="text-lg sm:text-xl font-bold text-gray-900 leading-tight my-0.5 truncate" title="Rp {{ number_format($totalMonth ?? 0, 0, ',', '.') }}">
                                    Rp {{ number_format($totalMonth ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-blue-600 font-medium truncate">{{ now()->translatedFormat('F Y') }}</div>
                            </div>
                        </div>

                        <!-- 3. Rata-rata Admin Fee -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-gray-500 truncate" title="Rata-rata Admin Fee">Rata-rata Admin Fee</div>
                                <div class="text-lg sm:text-xl font-bold text-gray-900 leading-tight my-0.5 truncate" title="Rp {{ number_format($avgAdmin ?? 0, 0, ',', '.') }}">
                                    Rp {{ number_format($avgAdmin ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-amber-600 font-medium truncate">Per transaksi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Tabs -->
                    <div class="mb-6 overflow-x-auto">
                        <div id="adminFeeChartTabs" class="inline-flex bg-gray-100 rounded-xl p-1 min-w-max">
                            <button type="button" data-range="daily"
                                class="chart-range-tab px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 whitespace-nowrap">
                                Harian
                            </button>
                            <button type="button" data-range="monthly"
                                class="chart-range-tab px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 whitespace-nowrap">
                                Bulanan
                            </button>
                            <button type="button" data-range="yearly"
                                class="chart-range-tab px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-200 whitespace-nowrap">
                                Tahunan
                            </button>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div class="w-full transition-all duration-500 ease-out bg-gray-50/70 rounded-2xl p-4 sm:p-6 border border-gray-200"
                        id="adminFeeChartContainer">
                        <canvas id="adminFeeChart" height="140"></canvas>
                    </div>

                    <!-- Breakdown by Source -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Breakdown Sumber Biaya Admin</h3>
                                <p class="text-xs text-gray-500">Perbandingan perolehan fee dari order bantuan dan pengisian saldo</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Help (Bantuan) Breakdown -->
                            <div class="bg-gradient-to-br from-blue-50/80 to-blue-100/60 rounded-2xl p-5 border border-blue-200 shadow-xs">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm">Bantuan</h4>
                                            <p class="text-xs text-gray-500">Fee dari pembuatan bantuan</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 border border-blue-200 rounded-full text-xs font-bold">
                                        {{ $totalAll > 0 ? number_format(($breakdown['help']['total'] / $totalAll) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="space-y-2.5">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600 font-medium">Total Fee:</span>
                                        <span class="text-base font-bold text-blue-700">
                                            Rp {{ number_format($breakdown['help']['total'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600 font-medium">Jumlah Transaksi:</span>
                                        <span class="font-semibold text-gray-900">
                                            {{ number_format($breakdown['help']['count'] ?? 0, 0, ',', '.') }} bantuan
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs pt-2 border-t border-blue-200/80">
                                        <span class="text-gray-600 font-medium">Rata-rata Fee:</span>
                                        <span class="font-semibold text-gray-900">
                                            Rp {{ number_format($breakdown['help']['avg'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Top-up Breakdown -->
                            <div class="bg-gradient-to-br from-emerald-50/80 to-emerald-100/60 rounded-2xl p-5 border border-emerald-200 shadow-xs">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-sm">Top-up Saldo</h4>
                                            <p class="text-xs text-gray-500">Fee dari pengisian saldo</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full text-xs font-bold">
                                        {{ $totalAll > 0 ? number_format(($breakdown['topup']['total'] / $totalAll) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="space-y-2.5">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600 font-medium">Total Fee:</span>
                                        <span class="text-base font-bold text-emerald-700">
                                            Rp {{ number_format($breakdown['topup']['total'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-600 font-medium">Jumlah Transaksi:</span>
                                        <span class="font-semibold text-gray-900">
                                            {{ number_format($breakdown['topup']['count'] ?? 0, 0, ',', '.') }} top-up
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs pt-2 border-t border-emerald-200/80">
                                        <span class="text-gray-600 font-medium">Rata-rata Fee:</span>
                                        <span class="font-semibold text-gray-900">
                                            Rp {{ number_format($breakdown['topup']['avg'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Form Section (Combined Form) -->
        <form wire:submit.prevent="save">
            @if(session()->has('message'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-green-800 font-medium text-sm">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            <!-- Settings flash hook for JS (used to detect Livewire updates) -->
            <div id="settingsFlash" data-message="{{ session('message') ?? '' }}" style="display:none"></div>

            <!-- Konfigurasi Bantuan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-gray-50/80">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Konfigurasi Bantuan</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Atur nominal minimal pembuatan order dan biaya admin untuk sistem bantuan</p>
                </div>

                <div class="p-4 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nominal Minimal Bantuan</label>
                            <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                <input type="number" wire:model="min_help_nominal" placeholder="10000"
                                    style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                            </div>
                            @error('min_help_nominal')
                                <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                            <p class="text-xs text-gray-400 mt-2">Customer tidak bisa membuat bantuan dengan nominal di bawah nilai ini</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Biaya Admin Bantuan</label>
                            <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                <input type="number" wire:model="admin_fee" placeholder="0"
                                    style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                            </div>
                            @error('admin_fee')
                                <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                            <p class="text-xs text-gray-400 mt-2">Biaya tambahan yang dikenakan ke customer saat membuat order bantuan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top-Up Fees Settings Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">
                <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-gray-50/80">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Konfigurasi Biaya Admin Top-Up</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Atur biaya admin berdasarkan nominal transaksi top-up (3 tier bertingkat)</p>
                </div>

                <div class="p-4 sm:p-8 space-y-6">
                    <!-- Tier 1 Settings -->
                    <div class="border border-gray-200 rounded-2xl p-5 sm:p-6 bg-gray-50/60 hover:border-gray-300 transition-all">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 font-bold border border-blue-200 flex items-center justify-center text-sm shadow-xs">1</span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Tier 1 - Nominal Kecil</h3>
                                <p class="text-xs text-gray-500">Biaya tetap untuk nominal top-up di bawah batas tier 1</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Batas Maksimal Tier 1</label>
                                <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                    <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                    <input type="number" wire:model="tier1_limit" placeholder="50000"
                                        style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                                </div>
                                @error('tier1_limit')
                                    <div class="flex items-center gap-2 mt-1.5 text-red-600 text-xs">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-[11px] text-gray-400 mt-1.5">Nominal top-up di bawah nilai ini menggunakan biaya tier 1</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Biaya Admin Tier 1</label>
                                <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                    <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                    <input type="number" wire:model="tier1_fee" placeholder="5000"
                                        style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                                </div>
                                @error('tier1_fee')
                                    <div class="flex items-center gap-2 mt-1.5 text-red-600 text-xs">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-[11px] text-gray-400 mt-1.5">Biaya tetap untuk nominal di bawah Rp {{ number_format($tier1_limit ?? 50000, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tier 2 Settings -->
                    <div class="border border-gray-200 rounded-2xl p-5 sm:p-6 bg-gray-50/60 hover:border-gray-300 transition-all">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 font-bold border border-purple-200 flex items-center justify-center text-sm shadow-xs">2</span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Tier 2 - Nominal Menengah</h3>
                                <p class="text-xs text-gray-500">Biaya tetap untuk nominal top-up antara batas tier 1 dan tier 2</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Batas Maksimal Tier 2</label>
                                <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                    <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                    <input type="number" wire:model="tier2_limit" placeholder="100000"
                                        style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                                </div>
                                @error('tier2_limit')
                                    <div class="flex items-center gap-2 mt-1.5 text-red-600 text-xs">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-[11px] text-gray-400 mt-1.5">Nominal top-up di bawah nilai ini menggunakan biaya tier 2</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Biaya Admin Tier 2</label>
                                <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                    <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                    <input type="number" wire:model="tier2_fee" placeholder="7500"
                                        style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                                </div>
                                @error('tier2_fee')
                                    <div class="flex items-center gap-2 mt-1.5 text-red-600 text-xs">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-[11px] text-gray-400 mt-1.5">Biaya tetap untuk nominal Rp {{ number_format($tier1_limit ?? 50000, 0, ',', '.') }} - Rp {{ number_format($tier2_limit ?? 100000, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tier 3 Settings -->
                    <div class="border border-gray-200 rounded-2xl p-5 sm:p-6 bg-gray-50/60 hover:border-gray-300 transition-all">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 font-bold border border-amber-200 flex items-center justify-center text-sm shadow-xs">3</span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Tier 3 - Nominal Besar (Persentase)</h3>
                                <p class="text-xs text-gray-500">Biaya dihitung berdasarkan persentase nominal dengan batas maksimal</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Persentase Biaya Admin</label>
                                <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                    <input type="number" step="0.01" wire:model="tier3_percentage" placeholder="3"
                                        style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                                    <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-left: 8px; user-select: none;">%</span>
                                </div>
                                @error('tier3_percentage')
                                    <div class="flex items-center gap-2 mt-1.5 text-red-600 text-xs">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-[11px] text-gray-400 mt-1.5">Persentase dari nominal top-up (untuk nominal ≥ Rp {{ number_format($tier2_limit ?? 100000, 0, ',', '.') }})</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Biaya Maksimal Tier 3 (Cap)</label>
                                <div class="flex items-center rounded-xl shadow-xs border border-gray-300 bg-white" style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                                    <span style="color: #64748b; font-size: 14px; font-weight: 600; margin-right: 8px; user-select: none;">Rp</span>
                                    <input type="number" wire:model="tier3_max" placeholder="15000"
                                        style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                                </div>
                                @error('tier3_max')
                                    <div class="flex items-center gap-2 mt-1.5 text-red-600 text-xs">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <p class="text-[11px] text-gray-400 mt-1.5">Batas maksimal biaya admin untuk tier 3</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods (Banks) -->
                    <div class="border border-gray-200 rounded-2xl p-5 sm:p-6 bg-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Metode Pembayaran Top-Up (Transfer Bank)</h3>
                                <p class="text-xs text-gray-500">Atur daftar rekening bank yang akan ditampilkan pada proses top-up customer</p>
                            </div>
                            <button type="button" wire:click.prevent="addBank" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary-50 text-primary-700 border border-primary-200 hover:bg-primary-100 font-semibold rounded-xl text-xs transition-all shadow-xs self-start sm:self-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Bank</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @foreach($payment_banks as $i => $bank)
                                <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/70 hover:bg-gray-50 transition-all shadow-xs">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-center">
                                        <div class="lg:col-span-1">
                                            <label class="block text-[11px] font-bold text-gray-600 mb-1 uppercase tracking-wider">Kode</label>
                                            <input type="text" wire:model="payment_banks.{{ $i }}.code" placeholder="bca"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs font-semibold text-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition" />
                                        </div>

                                        <div class="lg:col-span-3">
                                            <label class="block text-[11px] font-bold text-gray-600 mb-1 uppercase tracking-wider">Nama Bank</label>
                                            <input type="text" wire:model="payment_banks.{{ $i }}.name" placeholder="Bank Central Asia (BCA)"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs font-semibold text-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition" />
                                        </div>

                                        <div class="lg:col-span-2">
                                            <label class="block text-[11px] font-bold text-gray-600 mb-1 uppercase tracking-wider">No. Rekening</label>
                                            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:model="payment_banks.{{ $i }}.account_number" placeholder="1234567890"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs font-semibold text-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition" />
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-6">
                                            <label class="block text-[11px] font-bold text-gray-600 mb-1 mt-1 uppercase tracking-wider">Nama Pemilik Rekening (a.n.)</label>
                                            <input type="text" wire:model="payment_banks.{{ $i }}.account_name" placeholder="PT Saya Bantu Indonesia"
                                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl text-xs font-semibold text-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition" />
                                        </div>

                                        <div class="sm:col-span-2 lg:col-span-6 flex items-center justify-between pt-2 border-t border-gray-200/80 mt-1">
                                            <div class="flex items-center gap-2">
                                                <label class="inline-flex items-center text-xs font-semibold text-gray-700 cursor-pointer">
                                                    <input type="checkbox" wire:model="payment_banks.{{ $i }}.enabled" class="rounded text-primary-600 focus:ring-primary-500 h-4 w-4 border-gray-300" />
                                                    <span class="ml-2">Aktifkan Rekening</span>
                                                </label>
                                            </div>
                                            <div>
                                                <button type="button" wire:click.prevent="removeBank({{ $i }})" 
                                                    class="inline-flex items-center gap-1 text-red-600 hover:text-red-700 hover:bg-red-50 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold shadow-md shadow-primary-500/20 hover:shadow-lg transition-all duration-200 w-full sm:w-auto cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Saved confirmation modal -->
        <div id="settingsSavedModal" class="fixed inset-0 z-50 flex items-center justify-center hidden transition-opacity duration-300">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="settingsSavedContent">
                <!-- Close Button -->
                <button id="settingsSavedClose" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Content -->
                <div class="p-6 text-center">
                    <!-- Success Icon with animation -->
                    <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 animate-bounce-once">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Berhasil Disimpan!</h3>
                    
                    <!-- Message -->
                    <p class="text-sm text-gray-600 leading-relaxed" id="settingsSavedMessage">
                        Perubahan telah diterapkan dan tersimpan.
                    </p>
                </div>
            </div>
        </div>

        <style>
            @keyframes bounce-once {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            .animate-bounce-once {
                animation: bounce-once 0.5s ease-in-out;
            }
            #settingsSavedModal:not(.hidden) #settingsSavedContent {
                transform: scale(1);
                opacity: 1;
            }
        </style>
    </div>

    <!-- Chart.js -->
@php
    $adminFeeChartJson = json_encode($adminFeeChart ?? ['daily' => ['labels' => [], 'data' => []], 'monthly' => ['labels' => [], 'data' => []], 'yearly' => ['labels' => [], 'data' => []]]);
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = {!! $adminFeeChartJson !!};

        const ctx = document.getElementById('adminFeeChart').getContext('2d');
        let adminChart = null;

        function renderRange(range) {
            const container = document.getElementById('adminFeeChartContainer');
            container.style.opacity = '0';
            container.style.transform = 'translateY(10px) scale(0.99)';

            setTimeout(() => {
                const labels = chartData[range].labels || [];
                const data = chartData[range].data || [];

                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(17, 24, 39, 0.9)');
                gradient.addColorStop(1, 'rgba(55, 65, 81, 0.7)');

                const cfg = {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan Biaya Admin',
                            data: data,
                            backgroundColor: gradient,
                            borderColor: 'rgba(17, 24, 39, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                padding: 12,
                                titleColor: '#fff',
                                bodyColor: '#e5e7eb',
                                borderColor: 'rgba(55, 65, 81, 0.3)',
                                borderWidth: 1,
                                cornerRadius: 6,
                                callbacks: {
                                    label: function (ctx) {
                                        const v = ctx.raw ?? ctx.parsed?.y ?? 0;
                                        return 'Rp ' + Number(v).toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 45,
                                    font: { size: 11 },
                                    color: '#6b7280'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (v) { return 'Rp ' + Number(v).toLocaleString(); },
                                    font: { size: 11 },
                                    color: '#6b7280'
                                },
                                grid: { color: 'rgba(156, 163, 175, 0.15)' }
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                };

                if (adminChart) adminChart.destroy();
                adminChart = new Chart(ctx, cfg);

                setTimeout(() => {
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0) scale(1)';
                }, 120);
            }, 100);
        }

        const tabs = document.querySelectorAll('.chart-range-tab');
        const valid = ['daily', 'monthly', 'yearly'];
        let initial = 'daily';
        const saved = localStorage.getItem('superadmin.adminFeeChart.range');
        if (saved && valid.includes(saved)) initial = saved;

        function setActive(r) {
            tabs.forEach(t => {
                if (t.dataset.range === r) {
                    t.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                    t.classList.remove('text-gray-600');
                } else {
                    t.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                    t.classList.add('text-gray-600');
                }
            });
        }

        if (tabs.length) {
            tabs.forEach(t => t.addEventListener('click', function () {
                const r = t.dataset.range;
                if (!valid.includes(r)) return;
                setActive(r);
                localStorage.setItem('superadmin.adminFeeChart.range', r);
                renderRange(r);
            }));
            setActive(initial);
            renderRange(initial);
        } else {
            renderRange('daily');
        }
    });
</script>
<script>
    let modalTimeout = null;
    
    function showSettingsSaved(message) {
        const modal = document.getElementById('settingsSavedModal');
        const msgEl = document.getElementById('settingsSavedMessage');
        if (!modal) return;
        if (!message) return;
        
        // Update message
        if (msgEl) msgEl.textContent = message;
        
        // Clear any existing timeout
        if (modalTimeout) {
            clearTimeout(modalTimeout);
            modalTimeout = null;
        }
        
        // Force hide first
        modal.classList.add('hidden');
        
        // Then show with slight delay
        setTimeout(() => {
            modal.classList.remove('hidden');
            // Auto hide after 3 seconds
            modalTimeout = setTimeout(() => {
                modal.classList.add('hidden');
                modalTimeout = null;
            }, 3000);
        }, 50);
    }

    // Setup Livewire listener
    document.addEventListener('livewire:init', () => {
        Livewire.on('settingsSaved', (event) => {
            const message = event[0]?.message || event.message || 'Pengaturan berhasil disimpan';
            showSettingsSaved(message);
        });
    });

    // Close button handler
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('settingsSavedClose');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                if (modalTimeout) {
                    clearTimeout(modalTimeout);
                    modalTimeout = null;
                }
                const modal = document.getElementById('settingsSavedModal');
                if (modal) modal.classList.add('hidden');
            });
        }
    });
</script>