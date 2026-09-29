@php
    $title = 'Fee & Pendapatan Platform';
    $breadcrumb = 'Super Admin / Pengaturan / Bantuan / Fee & Pendapatan';
@endphp

<div>
    <!-- Admin Fee Revenue Chart Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Chart Header -->
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-gray-50/80">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg sm:text-xl font-medium text-gray-900">Pendapatan Platform</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Grafik perolehan pendapatan platform (fee operasional bantuan dan biaya admin top-up)</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <!-- Summary Cards (3 Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-8" style="gap: 16px;">
                    <!-- 1. Total Fee Platform Bantuan -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-medium text-gray-500 truncate" title="Total Fee Platform Bantuan">Total Fee Platform</div>
                            <div class="text-lg sm:text-xl font-medium text-gray-900 leading-tight my-0.5 truncate" title="Rp {{ number_format($helpTotalFee ?? 0, 0, ',', '.') }}">
                                Rp {{ number_format($helpTotalFee ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-blue-600 font-medium truncate">Customer + Mitra</div>
                        </div>
                    </div>

                    <!-- 2. Pendapatan Bulan Ini -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-medium text-gray-500 truncate" title="Fee Platform Bulan Ini">Fee Bulan Ini</div>
                            <div class="text-lg sm:text-xl font-medium text-gray-900 leading-tight my-0.5 truncate" title="Rp {{ number_format($helpFeeMonth ?? 0, 0, ',', '.') }}">
                                Rp {{ number_format($helpFeeMonth ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-indigo-600 font-medium truncate">{{ now()->translatedFormat('F Y') }}</div>
                        </div>
                    </div>

                    <!-- 3. Rata-rata Fee / Bantuan -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-medium text-gray-500 truncate" title="Rata-rata Fee per Bantuan">Rata-rata Fee / Bantuan</div>
                            <div class="text-lg sm:text-xl font-medium text-gray-900 leading-tight my-0.5 truncate" title="Rp {{ number_format($avgHelpFee ?? 0, 0, ',', '.') }}">
                                Rp {{ number_format($avgHelpFee ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-amber-600 font-medium truncate">Customer + Mitra</div>
                        </div>
                    </div>
                </div>

                <!-- Full-Width Chart: Fee Platform Bantuan -->
                <div class="bg-gray-50/60 rounded-2xl p-4 sm:p-6 border border-gray-200" id="platformFeeChartContainer">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs sm:text-sm font-medium text-gray-900">Grafik Pendapatan Fee Platform (Bantuan)</div>
                                <p class="text-[11px] text-gray-400">Akumulasi bagi hasil biaya layanan customer dan fee platform mitra</p>
                            </div>
                        </div>
                        <div class="inline-flex p-1 bg-gray-200/60 rounded-xl border border-gray-200/80 shadow-2xs self-start sm:self-auto">
                            <button type="button" data-range="daily" class="platform-range-tab px-3 py-1 text-xs font-medium rounded-lg transition-all duration-200">Harian</button>
                            <button type="button" data-range="monthly" class="platform-range-tab px-3 py-1 text-xs font-medium rounded-lg transition-all duration-200">Bulanan</button>
                            <button type="button" data-range="yearly" class="platform-range-tab px-3 py-1 text-xs font-medium rounded-lg transition-all duration-200">Tahunan</button>
                        </div>
                    </div>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="platformFeeChart"></canvas>
                    </div>
                </div>

                <!-- Breakdown Section: Komponen Fee Bantuan Platform -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-medium text-gray-900">Breakdown Komponen Fee Bantuan</h3>
                            <p class="text-xs text-gray-500">Rincian perolehan fee operasional berdasarkan persentase layanan customer dan mitra</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Fee Layanan Customer -->
                        <div class="bg-gradient-to-br from-blue-50/80 to-blue-100/60 rounded-2xl p-5 border border-blue-200 shadow-xs">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm">Fee Layanan Customer</h4>
                                        <p class="text-xs text-gray-500">Dikenakan di atas order customer</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-800 border border-blue-200 rounded-full text-xs font-medium">
                                    {{ $helpBreakdown['customer']['percent'] ?? 0 }}%
                                </span>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600 font-medium">Total Perolehan:</span>
                                    <span class="text-base font-medium text-blue-700">
                                         Rp {{ number_format($helpBreakdown['customer']['total'] ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-xs pt-2 border-t border-blue-200/80">
                                    <span class="text-gray-600 font-medium">Keterangan:</span>
                                    <span class="font-medium text-gray-700">
                                        Persentase setting saat ini: {{ (float) $customer_service_fee_percent }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Fee Platform Mitra -->
                        <div class="bg-gradient-to-br from-emerald-50/80 to-emerald-100/60 rounded-2xl p-5 border border-emerald-200 shadow-xs">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm">Fee Platform Mitra</h4>
                                        <p class="text-xs text-gray-500">Dipotong dari hasil komisi mitra</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-full text-xs font-medium">
                                    {{ $helpBreakdown['mitra']['percent'] ?? 0 }}%
                                </span>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600 font-medium">Total Perolehan:</span>
                                    <span class="text-base font-medium text-emerald-700">
                                         Rp {{ number_format($helpBreakdown['mitra']['total'] ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-xs pt-2 border-t border-emerald-200/80">
                                    <span class="text-gray-600 font-medium">Keterangan:</span>
                                    <span class="font-medium text-gray-700">
                                        Persentase setting saat ini: {{ (float) $mitra_platform_fee_percent }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pengaturan Fee & Pendapatan -->
    <form wire:submit.prevent="save">
        @if(session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-800 font-medium text-sm">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        <div id="settingsFlash" data-message="{{ session('message') ?? '' }}" style="display:none"></div>

        <!-- Fee Operasional Platform -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="border-b border-gray-200 px-4 sm:px-8 py-5 bg-emerald-50/60 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-medium shadow-sm" style="background-color: #059669; color: #ffffff;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: #ffffff;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-medium text-gray-900">Fee Bagi Hasil Platform</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pengaturan persentase bagi hasil operasional layanan bantuan dari customer dan mitra</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded-lg border border-emerald-200 hidden sm:inline-block">Tarif Platform</span>
            </div>

            <div class="p-4 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fee Customer -->
                    <div class="p-5 bg-blue-50/60 rounded-2xl border border-blue-100">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-xs font-medium text-blue-950 uppercase tracking-wider">Biaya Layanan Customer (%)</label>
                            <span class="text-xs font-medium px-2 py-0.5 bg-blue-200 text-blue-800 rounded">Customer</span>
                        </div>
                        <div class="flex items-center rounded-xl shadow-xs border border-blue-200 bg-white" style="display: flex; align-items: center; border: 1px solid #bfdbfe; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                            <input type="number" step="0.1" min="0" max="100" wire:model="customer_service_fee_percent" placeholder="10"
                                style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                            <span style="color: #64748b; font-size: 14px; font-weight: 500; margin-left: 8px; user-select: none;">%</span>
                        </div>
                        @error('customer_service_fee_percent')
                            <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                        <p class="text-xs text-blue-700 mt-2.5">Dikenakan sebagai biaya layanan kepada customer di atas nominal bantuan (default 10%).</p>
                    </div>

                    <!-- Fee Mitra -->
                    <div class="p-5 bg-emerald-50/60 rounded-2xl border border-emerald-100">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-xs font-medium text-emerald-950 uppercase tracking-wider">Biaya Platform Mitra (%)</label>
                            <span class="text-xs font-medium px-2 py-0.5 bg-emerald-200 text-emerald-800 rounded">Mitra</span>
                        </div>
                        <div class="flex items-center rounded-xl shadow-xs border border-emerald-200 bg-white" style="display: flex; align-items: center; border: 1px solid #a7f3d0; border-radius: 12px; background: #fff; padding-left: 14px; padding-right: 14px;">
                            <input type="number" step="0.1" min="0" max="100" wire:model="mitra_platform_fee_percent" placeholder="10"
                                style="border: none; outline: none; padding: 10px 0; width: 100%; font-size: 14px; font-weight: 500; color: #0f172a; background: transparent;" />
                            <span style="color: #64748b; font-size: 14px; font-weight: 500; margin-left: 8px; user-select: none;">%</span>
                        </div>
                        @error('mitra_platform_fee_percent')
                            <div class="flex items-center gap-2 mt-2 text-red-600 text-xs font-medium">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                        <p class="text-xs text-emerald-700 mt-2.5">Dipotong dari hasil pendapatan mitra saat pesanan bantuan berhasil diselesaikan (default 10%).</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium shadow-md shadow-primary-500/20 hover:shadow-lg transition-all duration-200 w-full sm:w-auto cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan Fee
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Saved confirmation modal -->
    <div id="settingsSavedModal" class="fixed inset-0 z-50 flex items-center justify-center hidden transition-opacity duration-300">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="settingsSavedContent">
            <button id="settingsSavedClose" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 animate-bounce-once">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Berhasil Disimpan!</h3>
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

<!-- Chart.js for Platform Fee -->
@php
    $platformFeeChartJson = json_encode($platformFeeChart ?? ['daily' => ['labels' => [], 'data' => []], 'monthly' => ['labels' => [], 'data' => []], 'yearly' => ['labels' => [], 'data' => []]]);
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const platformData = {!! $platformFeeChartJson !!};
        const platformCtx = document.getElementById('platformFeeChart')?.getContext('2d');
        let platformChartInstance = null;

        function renderPlatformChart(range) {
            if (!platformCtx) return;
            const container = document.getElementById('platformFeeChartContainer');
            if (container) {
                container.style.opacity = '0.3';
                container.style.transition = 'opacity 150ms ease';
            }

            setTimeout(() => {
                const labels = platformData[range]?.labels || [];
                const data = platformData[range]?.data || [];

                const gradient = platformCtx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, 'rgba(37, 99, 235, 0.85)');
                gradient.addColorStop(1, 'rgba(96, 165, 250, 0.35)');

                const cfg = {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Fee Platform',
                            data: data,
                            backgroundColor: gradient,
                            borderColor: 'rgba(37, 99, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                padding: 10,
                                titleColor: '#fff',
                                bodyColor: '#e2e8f0',
                                borderColor: 'rgba(51, 65, 85, 0.4)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (context) {
                                        const v = context.raw ?? context.parsed?.y ?? 0;
                                        return 'Fee Platform: Rp ' + Number(v).toLocaleString('id-ID');
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
                                    font: { size: 10 },
                                    color: '#64748b'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (v) { 
                                        if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + 'M';
                                        if (v >= 1000) return 'Rp ' + (v / 1000).toFixed(0) + 'k';
                                        return 'Rp ' + Number(v).toLocaleString('id-ID'); 
                                    },
                                    font: { size: 10 },
                                    color: '#64748b'
                                },
                                grid: { color: 'rgba(226, 232, 240, 0.6)' }
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                };

                if (platformChartInstance) platformChartInstance.destroy();
                platformChartInstance = new Chart(platformCtx, cfg);

                if (container) container.style.opacity = '1';
            }, 50);
        }

        const platformTabs = document.querySelectorAll('.platform-range-tab');
        function setPlatformActive(r) {
            platformTabs.forEach(t => {
                if (t.dataset.range === r) {
                    t.classList.add('bg-white', 'text-blue-700', 'shadow-xs');
                    t.classList.remove('text-gray-600');
                } else {
                    t.classList.remove('bg-white', 'text-blue-700', 'shadow-xs');
                    t.classList.add('text-gray-600');
                }
            });
        }
        platformTabs.forEach(t => t.addEventListener('click', function () {
            const r = t.dataset.range;
            setPlatformActive(r);
            renderPlatformChart(r);
        }));

        setPlatformActive('daily');
        renderPlatformChart('daily');
    });
</script>
<script>
    let modalTimeout = null;

    function showSettingsSaved(message) {
        const modal = document.getElementById('settingsSavedModal');
        const msgEl = document.getElementById('settingsSavedMessage');
        if (!modal) return;
        if (!message) return;

        if (msgEl) msgEl.textContent = message;

        if (modalTimeout) {
            clearTimeout(modalTimeout);
            modalTimeout = null;
        }

        modal.classList.add('hidden');

        setTimeout(() => {
            modal.classList.remove('hidden');
            modalTimeout = setTimeout(() => {
                modal.classList.add('hidden');
                modalTimeout = null;
            }, 3000);
        }, 50);
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('settingsSaved', (event) => {
            const message = event[0]?.message || event.message || 'Pengaturan berhasil disimpan';
            showSettingsSaved(message);
        });
    });

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