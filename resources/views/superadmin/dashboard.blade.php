@php
    // Ensure variables exist to avoid undefined errors
    $stats = $stats ?? [];
    $recentUsers = $recentUsers ?? collect();
    $recentHelps = $recentHelps ?? collect();
    // Ensure chart data exists to avoid Blade parsing issues when using @json
    $userChart = $userChart ?? [
        'daily' => ['labels' => [], 'data' => []],
        'monthly' => ['labels' => [], 'data' => []],
        'yearly' => ['labels' => [], 'data' => []],
    ];
@endphp

<div class="space-y-6">
    <!-- Top Stat Cards (5 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5" style="gap: 20px;">
        <!-- 1. Total Pengguna -->
        <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:shadow-md transition-all duration-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-gray-500 truncate" title="Total Pengguna">Total Pengguna</div>
                <div class="text-xl font-bold text-gray-900 leading-tight my-0.5">{{ number_format($stats['total_users'] ?? 0) }}</div>
                <div class="text-xs text-gray-400 truncate">Semua akun</div>
            </div>
        </div>

        <!-- 2. Customer -->
        <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:shadow-md transition-all duration-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-gray-500 truncate" title="Customer">Customer</div>
                <div class="text-xl font-bold text-gray-900 leading-tight my-0.5">{{ number_format($stats['total_customers'] ?? 0) }}</div>
                <div class="text-xs text-gray-400 truncate">Total customer</div>
            </div>
        </div>

        <!-- 3. Total Kota -->
        <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:shadow-md transition-all duration-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-gray-500 truncate" title="Total Kota">Total Kota</div>
                <div class="text-xl font-bold text-gray-900 leading-tight my-0.5">{{ number_format($stats['total_cities'] ?? 0) }}</div>
                <div class="text-xs text-gray-400 truncate">Kota terdaftar</div>
            </div>
        </div>

        <!-- 4. Mitra -->
        <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:shadow-md transition-all duration-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-gray-500 truncate" title="Mitra">Mitra</div>
                <div class="text-xl font-bold text-gray-900 leading-tight my-0.5">{{ number_format($stats['total_mitras'] ?? 0) }}</div>
                <div class="text-xs text-gray-400 truncate">Mitra aktif</div>
            </div>
        </div>

        <!-- 5. Admin -->
        <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:shadow-md transition-all duration-200 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-semibold text-gray-500 truncate" title="Admin">Admin</div>
                <div class="text-xl font-bold text-gray-900 leading-tight my-0.5">{{ number_format($stats['total_admins'] ?? 0) }}</div>
                <div class="text-xs text-gray-400 truncate">Admin sistem</div>
            </div>
        </div>
    </div>

    <!-- Main Content Area: Chart -->
    <div class="mb-6">
        <!-- Chart Section -->
        <div class="w-full bg-white rounded-2xl shadow-xs border border-gray-200/80 p-6 flex flex-col">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Data Pengguna Terakhir</h2>
                    <p class="text-xs text-gray-500">Grafik pendaftaran pengguna</p>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <!-- Range Tabs -->
                    <div id="chartRangeTabs" role="tablist" class="inline-flex p-1 bg-gray-100 rounded-xl border border-gray-200/80 shadow-xs self-start sm:self-auto">
                        <button type="button" data-range="daily" class="chart-range-tab px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200">Harian</button>
                        <button type="button" data-range="monthly" class="chart-range-tab px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200">Bulanan</button>
                        <button type="button" data-range="yearly" class="chart-range-tab px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200">Tahunan</button>
                    </div>
                </div>
            </div>

            <div class="w-full relative transition-all duration-300" id="chartContainer" style="height: 280px;">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 3-Column Section: Aksi Cepat, Pengguna Terbaru, Permintaan Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Aksi Cepat -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center mb-5">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0 mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Aksi Cepat</h2>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Kelola User -->
                    <a href="{{ route('superadmin.users') }}" class="group bg-blue-50/60 hover:bg-blue-100/70 rounded-xl py-4 px-3 border border-blue-100 transition-all duration-200 hover:shadow-xs flex flex-col items-center text-center">
                        <div class="w-11 h-11 rounded-xl bg-white shadow-xs flex items-center justify-center mb-3.5 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-900 leading-tight">Kelola User</h3>
                        <p class="text-[10px] text-gray-500 mt-1">Semua pengguna</p>
                    </a>

                    <!-- Kelola Kota -->
                    <a href="{{ route('superadmin.cities') }}" class="group bg-emerald-50/60 hover:bg-emerald-100/70 rounded-xl py-4 px-3 border border-emerald-100 transition-all duration-200 hover:shadow-xs flex flex-col items-center text-center">
                        <div class="w-11 h-11 rounded-xl bg-white shadow-xs flex items-center justify-center mb-3.5 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-900 leading-tight">Kelola Kota</h3>
                        <p class="text-[10px] text-gray-500 mt-1">Kota layanan</p>
                    </a>

                    <!-- Pengaturan Bantuan -->
                    <a href="{{ route('superadmin.settings.help') }}" class="group bg-amber-50/60 hover:bg-amber-100/70 rounded-xl py-4 px-3 border border-amber-100 transition-all duration-200 hover:shadow-xs flex flex-col items-center text-center">
                        <div class="w-11 h-11 rounded-xl bg-white shadow-xs flex items-center justify-center mb-3.5 group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c.79 0 1.5.3 2.04.78L20 14v6a1 1 0 01-1 1h-6l-5.22-5.22A4 4 0 1112 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-900 leading-tight">Pengaturan</h3>
                        <p class="text-[10px] text-gray-500 mt-1">Tarif & fee admin</p>
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100">
                <a href="{{ route('superadmin.transactions.log') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center justify-between">
                    <span>Lihat Financial Report</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Pengguna Terbaru -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold flex-shrink-0 mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Pengguna Terbaru</h2>
                    </div>
                </div>

                @if($recentUsers->isEmpty())
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-full bg-gray-100 mx-auto flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Belum ada pengguna baru</p>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach($recentUsers->take(4) as $u)
                            <div class="flex items-center p-2 rounded-xl hover:bg-gray-50 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-xs shadow-xs mr-3">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-bold text-gray-900 truncate block">
                                            {{ $u->name }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 whitespace-nowrap">{{ $u->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-gray-400 truncate">{{ $u->email }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.2 text-[10px] font-semibold bg-blue-50 text-blue-700 rounded">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Permintaan Terbaru -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-6 hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center font-bold flex-shrink-0 mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Permintaan Terbaru</h2>
                    </div>
                    <a href="{{ route('superadmin.helps.approved') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Semua &rarr;</a>
                </div>

                @if($recentHelps->isEmpty())
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-full bg-gray-100 mx-auto flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Belum ada permintaan</p>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach($recentHelps->take(4) as $h)
                            <div class="p-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-xs font-bold text-gray-900 line-clamp-1 leading-snug">
                                        {{ $h->title }}
                                    </p>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold flex-shrink-0
                                        {{ $h->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($h->status === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ ucfirst($h->status) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1.5">
                                    <span>oleh {{ optional($h->user)->name ?? '—' }}</span>
                                    <span>{{ $h->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($userChart);
        const canvas = document.getElementById('usersChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let usersChart = null;

        function renderRange(range) {
            const chartContainer = document.getElementById('chartContainer');
            if (chartContainer) {
                chartContainer.style.opacity = '0.4';
                chartContainer.style.transform = 'translateY(8px) scale(0.99)';
            }
            
            setTimeout(() => {
                let maxTicks = 12;
                if (range === 'daily') maxTicks = 10;
                if (range === 'monthly') maxTicks = 12;
                if (range === 'yearly') maxTicks = 6;

                // Gradient background for bars
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.9)'); // blue-500
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

                const dataValues = (chartData[range] && chartData[range].data) ? chartData[range].data : [];
                const labelValues = (chartData[range] && chartData[range].labels) ? chartData[range].labels : [];

                const cfg = {
                    type: 'bar',
                    data: {
                        labels: labelValues,
                        datasets: [{
                            label: 'Pendaftaran',
                            data: dataValues,
                            backgroundColor: gradient,
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1.5,
                            hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                            borderRadius: 6,
                            maxBarThickness: 32
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 10,
                                cornerRadius: 8,
                                titleFont: { size: 12, weight: '700', family: 'Inter, sans-serif' },
                                bodyFont: { size: 12, family: 'Inter, sans-serif' },
                                callbacks: {
                                    label: function(ctx) {
                                        const v = ctx.raw ?? 0;
                                        return 'Pendaftaran: ' + Number(v).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { 
                                grid: { display: false },
                                ticks: { 
                                    font: { size: 11, family: 'Inter, sans-serif' },
                                    color: '#64748b',
                                    maxTicksLimit: maxTicks
                                }
                            },
                            y: { 
                                beginAtZero: true, 
                                grid: { color: 'rgba(226, 232, 240, 0.6)' },
                                ticks: { 
                                    precision: 0,
                                    font: { size: 11, family: 'Inter, sans-serif' },
                                    color: '#64748b',
                                    callback: function(value) { 
                                        return Number(value).toLocaleString('id-ID'); 
                                    } 
                                } 
                            }
                        },
                        animation: {
                            duration: 500,
                            easing: 'easeOutQuart'
                        }
                    }
                };

                if (usersChart) {
                    usersChart.destroy();
                }
                usersChart = new Chart(ctx, cfg);
                
                if (chartContainer) {
                    chartContainer.style.opacity = '1';
                    chartContainer.style.transform = 'translateY(0) scale(1)';
                }
            }, 100);
        }

        const tabs = document.querySelectorAll('.chart-range-tab');
        const validRanges = ['daily', 'monthly', 'yearly'];
        let initialRange = localStorage.getItem('superadmin.usersChart.range') || 'daily';
        if (!validRanges.includes(initialRange)) initialRange = 'daily';

        function setActiveTab(range) {
            tabs.forEach(t => {
                if (t.dataset.range === range) {
                    t.classList.add('bg-white', 'text-gray-900', 'shadow-xs');
                    t.classList.remove('text-gray-600', 'hover:text-gray-900');
                } else {
                    t.classList.remove('bg-white', 'text-gray-900', 'shadow-xs');
                    t.classList.add('text-gray-600', 'hover:text-gray-900');
                }
            });
        }

        if (tabs.length) {
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const r = tab.dataset.range;
                    if (!validRanges.includes(r)) return;
                    setActiveTab(r);
                    localStorage.setItem('superadmin.usersChart.range', r);
                    renderRange(r);
                });
            });
            setActiveTab(initialRange);
            renderRange(initialRange);
        } else {
            renderRange('daily');
        }
    });
</script>