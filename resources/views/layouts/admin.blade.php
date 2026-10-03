@php
    $currentRoute = request()->route()?->getName() ?? '';
    
    $routeMeta = [
        'admin.dashboard' => [
            'title' => 'Dashboard',
            'subtitle' => 'Ringkasan statistik dan aktivitas layanan'
        ],
        'admin.customers' => [
            'title' => 'Manajemen Customer',
            'subtitle' => 'Kelola seluruh data customer kota Anda'
        ],
        'admin.mitra' => [
            'title' => 'Manajemen Mitra',
            'subtitle' => 'Kelola seluruh data mitra kota Anda'
        ],
        'admin.verifications' => [
            'title' => 'Verifikasi KTP',
            'subtitle' => 'Kelola dan verifikasi identitas KTP mitra'
        ],
        'admin.users.index' => [
            'title' => 'Kelola Pengguna',
            'subtitle' => 'Daftar pengguna dengan role Mitra dan Customer'
        ],
        'admin.users.show' => [
            'title' => 'Detail Pengguna',
            'subtitle' => 'Informasi lengkap data akun pengguna'
        ],
        'admin.partners.activity' => [
            'title' => 'Aktivitas Mitra',
            'subtitle' => 'Pantau riwayat dan log aktivitas mitra di lapangan'
        ],
        'admin.partners.report' => [
            'title' => 'Manajemen Laporan Aduan',
            'subtitle' => 'Daftar keluhan dan aduan dari pengguna'
        ],
        'admin.partners.reports' => [
            'title' => 'Manajemen Laporan Aduan',
            'subtitle' => 'Daftar keluhan dan aduan dari pengguna'
        ],
        'admin.partners.reports.show' => [
            'title' => 'Detail Laporan Aduan',
            'subtitle' => 'Informasi lengkap tindak lanjut aduan'
        ],
        'admin.partners.blocked' => [
            'title' => 'Blokir Mitra',
            'subtitle' => 'Kelola dan monitor daftar mitra yang diblokir'
        ],
        'admin.withdraws.index' => [
            'title' => 'Manajemen Withdraw',
            'subtitle' => 'Daftar permintaan tarik saldo terbaru dari mitra'
        ],
        'admin.withdraws.show' => [
            'title' => 'Detail Withdraw',
            'subtitle' => 'Informasi permintaan tarik saldo dari mitra'
        ],
        'admin.topup.approvals' => [
            'title' => 'Approval Top-Up Saldo',
            'subtitle' => 'Monitoring request top-up saldo dari customer'
        ],
        'admin.helps' => [
            'title' => 'Manajemen Bantuan',
            'subtitle' => 'Daftar permintaan bantuan layanan'
        ],
        'admin.helps.approved' => [
            'title' => 'Bantuan Disetujui',
            'subtitle' => 'Menampilkan bantuan dengan status disetujui'
        ],
        'admin.ratings.index' => [
            'title' => 'Rating & Ulasan',
            'subtitle' => 'Rekap performa dan riwayat ulasan pengguna'
        ],
        'admin.password' => [
            'title' => 'Profil & Keamanan',
            'subtitle' => 'Kelola informasi profil dan perbarui kata sandi akun Admin'
        ],
    ];

    $detectedTitle = $routeMeta[$currentRoute]['title'] ?? null;
    $detectedSubtitle = $routeMeta[$currentRoute]['subtitle'] ?? null;

    if (!$detectedTitle) {
        if (request()->routeIs('admin.verifications*')) {
            $detectedTitle = 'Verifikasi KTP';
            $detectedSubtitle = 'Kelola dan verifikasi identitas KTP mitra';
        } elseif (request()->routeIs('admin.users*')) {
            $detectedTitle = 'Kelola Pengguna';
            $detectedSubtitle = 'Daftar pengguna dengan role Mitra dan Customer';
        } elseif (request()->routeIs('admin.partners.activity*')) {
            $detectedTitle = 'Aktivitas Mitra';
            $detectedSubtitle = 'Pantau riwayat dan log aktivitas mitra di lapangan';
        } elseif (request()->routeIs('admin.partners.report*')) {
            $detectedTitle = 'Manajemen Laporan Aduan';
            $detectedSubtitle = 'Daftar keluhan dan aduan dari pengguna';
        } elseif (request()->routeIs('admin.partners.blocked*')) {
            $detectedTitle = 'Blokir Mitra';
            $detectedSubtitle = 'Kelola dan monitor daftar mitra yang diblokir';
        } elseif (request()->routeIs('admin.withdraws*')) {
            $detectedTitle = 'Manajemen Withdraw';
            $detectedSubtitle = 'Daftar permintaan tarik saldo terbaru dari mitra';
        } elseif (request()->routeIs('admin.topup*')) {
            $detectedTitle = 'Approval Top-Up Saldo';
            $detectedSubtitle = 'Monitoring request top-up saldo dari customer';
        } elseif (request()->routeIs('admin.helps*')) {
            $detectedTitle = 'Manajemen Bantuan';
            $detectedSubtitle = 'Daftar permintaan bantuan layanan';
        } elseif (request()->routeIs('admin.password*')) {
            $detectedTitle = 'Profil & Keamanan';
            $detectedSubtitle = 'Kelola informasi profil dan perbarui kata sandi akun Admin';
        }
    }

    $pageTitle = (!empty($title) && $title !== 'Admin Panel') ? $title : ($detectedTitle ?? 'Dashboard');
    $pageSubtitle = (!empty($subtitle)) ? $subtitle : ($detectedSubtitle ?? 'Kelola layanan Sayabantu');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} - sayabantu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }

        /* Seluruh elemen bold, semibold, dan heading di panel Admin diubah ke font-medium (500) */
        .font-bold,
        .font-semibold,
        .font-extrabold,
        .font-black,
        strong,
        b,
        h1, h2, h3, h4, h5, h6,
        [style*="font-weight: 700"],
        [style*="font-weight: 600"],
        [style*="font-weight: 800"],
        [style*="font-weight: bold"],
        [style*="font-weight:bold"] {
            font-weight: 500 !important;
        }
    </style>
</head>

<body class="antialiased" x-data="{ showLogoutModal: false }" @open-logout-modal.window="showLogoutModal = true">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg fixed h-full flex flex-col z-50">
            <div id="admin-sidebar-header" class="h-[96px] px-6 flex flex-col justify-center border-b border-gray-200 flex-shrink-0">
                <h1 class="text-2xl font-bold text-primary-600">sayabantu</h1>
                <p class="text-xs text-gray-500 mt-1">Admin Panel</p>
            </div>

            <nav class="p-4 flex-1 overflow-y-auto" id="admin-sidebar-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.dashboard') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>

                <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Moderasi</p>
                </div>

                <a href="{{ route('admin.verifications') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.verifications*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Verifikasi KTP
                </a>

                <a href="{{ route('admin.customers') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.customers*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Manajemen Customer
                </a>

                <a href="{{ route('admin.mitra') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.mitra*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Manajemen Mitra
                </a>


                <a href="{{ route('admin.partners.activity') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.partners.activity') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Aktivitas Mitra
                </a>

                @php
                    $pendingReportsQuery = \App\Models\PartnerReport::where('status', 'pending');
                    if (auth()->check() && auth()->user()->role === 'admin') {
                        $adminCityIds = auth()->user()->getAdminCityIds();
                        if (!empty($adminCityIds)) {
                            $pendingReportsQuery->where(function($q) use ($adminCityIds) {
                                $q->whereHas('reportedHelp', function($hq) use ($adminCityIds) {
                                    $hq->whereIn('city_id', $adminCityIds);
                                })->orWhereHas('reporter', function($rq) use ($adminCityIds) {
                                    $rq->whereIn('city_id', $adminCityIds);
                                })->orWhereHas('reportedUser', function($uq) use ($adminCityIds) {
                                    $uq->whereIn('city_id', $adminCityIds);
                                });
                            });
                        }
                    }
                    $pendingReportsCount = $pendingReportsQuery->count();
                @endphp

                <a href="{{ route('admin.partners.report') }}"
                    class="flex items-center justify-between mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.partners.report') || request()->routeIs('admin.partners.reports.*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Manajemen Laporan Aduan</span>
                    </div>
                    @if($pendingReportsCount > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full shadow-sm animate-pulse">
                            {{ $pendingReportsCount > 99 ? '99+' : $pendingReportsCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.partners.blocked') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.partners.blocked') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    Blokir Mitra
                </a>

                <a href="{{ route('admin.helps') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.helps') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen Bantuan
                </a>

                 <a href="{{ route('admin.ratings.index') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.ratings*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Rating & Ulasan
                </a>

                <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Keuangan</p>
                </div>

                <a href="{{ route('admin.withdraws.index') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.withdraws.*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Manajemen Withdraw
                </a>

                <a href="{{ route('admin.topup.approvals') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('admin.topup.approvals*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 7h.01M7 11h.01M7 15h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Manajemen Approval
                </a>

            </nav>

            <div class="w-64 p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.password') }}" class="flex items-center group flex-1 min-w-0 mr-2 p-1.5 -ml-1.5 rounded-xl hover:bg-gray-50 transition cursor-pointer" title="Kelola Profil & Ubah Password">
                        <div style="background-color: #0077cc; color: #ffffff;"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition flex-shrink-0 shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">Admin</p>
                        </div>
                    </a>
                    <button 
                        @click="$dispatch('open-logout-modal')" 
                        type="button" 
                        class="text-gray-400 hover:text-red-600 transition p-1.5 rounded-lg hover:bg-gray-50 flex-shrink-0 cursor-pointer" 
                        title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 overflow-y-auto min-h-screen">
            <!-- Navbar -->
            <nav class="bg-white shadow-md sticky top-0 z-40">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Page Title & Subtitle -->
                        <div>
                            <h2 class="text-lg sm:text-xl font-medium text-gray-900">
                                @hasSection('page-title')
                                    @yield('page-title')
                                @else
                                    {{ $pageTitle }}
                                @endif
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                                @hasSection('page-description')
                                    @yield('page-description')
                                @else
                                    {{ $pageSubtitle }}
                                @endif
                            </p>
                        </div>

                        <!-- Right Side Actions -->
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <!-- Tombol Refresh -->
                            <div class="flex items-center">
                                <button onclick="location.reload()" type="button" class="inline-flex items-center gap-2 h-9 px-3.5 bg-gray-50 hover:bg-gray-100 hover:text-primary-600 border border-gray-200 text-xs font-medium text-gray-700 rounded-xl transition shadow-2xs cursor-pointer focus:outline-none" title="Refresh Halaman">
                                    <svg class="w-4 h-4 text-gray-600 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Refresh</span>
                                </button>
                            </div>

                            <!-- Notifications -->
                            <livewire:admin.notifications />

                            <!-- User Profile Link -->
                            <div class="flex items-center border-l border-gray-200 pl-3 sm:pl-4">
                                <a href="{{ route('admin.password') }}" 
                                   class="flex items-center space-x-2.5 group cursor-pointer focus:outline-none" 
                                   title="Profil Admin & Keamanan">
                                    <div class="text-right hidden sm:block">
                                        <p class="text-sm font-medium text-gray-900 group-hover:text-primary-600 transition">{{ auth()->user()->name ?? 'Admin' }}</p>
                                        <p class="text-xs text-gray-500">Admin {{ auth()->user()->city->name ?? '' }}</p>
                                    </div>
                                    <div style="background-color: #0077cc; color: #ffffff;" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-xs transition group-hover:ring-2 group-hover:ring-primary-500">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="p-6">
                @hasSection('content')
                    @yield('content')
                @elseif(isset($slot))
                    {{ $slot }}
                @endif
            </div>
        </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <div 
        x-show="showLogoutModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true">
        
        <!-- Backdrop with Blur -->
        <div 
            x-show="showLogoutModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
            @click="showLogoutModal = false">
        </div>

        <!-- Modal panel -->
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div 
                x-show="showLogoutModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-sm border border-gray-100"
                @click.stop>
                
                <!-- Close Button (Top Right) -->
                <button 
                    type="button" 
                    @click="showLogoutModal = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition cursor-pointer"
                    title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="p-6 sm:p-7 text-center">
                    <!-- Icon -->
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 shadow-xs mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title" style="font-weight: 700 !important;">
                        Konfirmasi Logout
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed">
                        Apakah Anda yakin ingin keluar dari panel Admin? Anda harus login kembali untuk mengakses panel ini.
                    </p>

                    <!-- Actions -->
                    <div class="mt-6 flex items-center gap-3">
                        <button 
                            type="button"
                            @click="showLogoutModal = false"
                            class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-medium transition shadow-2xs cursor-pointer">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('admin.logout') }}" class="flex-1 m-0">
                            @csrf
                            <button 
                                type="submit"
                                class="w-full inline-flex justify-center items-center gap-1.5 py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium transition shadow-xs hover:shadow-md cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Ya, Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('modals')

    <!-- Modal Notifikasi Akun Admin Diblokir / Dinonaktifkan -->
    @php
        $isAdminAccountDisabled = auth()->check() && in_array(auth()->user()->status, ['blocked', 'inactive']);
        $isAdminInactive = auth()->check() && auth()->user()->status === 'inactive';
    @endphp
    <div id="admin-blocked-account-modal" class="{{ $isAdminAccountDisabled ? '' : 'hidden' }}" style="{{ $isAdminAccountDisabled ? 'display: flex !important;' : '' }} position: fixed; inset: 0; background-color: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9999999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100 transform transition-all animate-bounce-in">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <h3 id="admin-blocked-account-title" class="text-lg font-bold text-gray-900 mb-2">
                {{ $isAdminInactive ? 'Akun Admin Dinonaktifkan' : 'Akun Admin Telah Diblokir' }}
            </h3>
            <p id="admin-blocked-account-message" class="text-xs sm:text-sm text-gray-600 mb-6 leading-relaxed">
                {{ $isAdminInactive 
                    ? 'Akun Admin Anda telah dinonaktifkan oleh Super Admin. Anda tidak dapat melanjutkan aktivitas di panel admin. Silakan klik tombol di bawah untuk keluar.' 
                    : 'Akses akun Admin Anda telah diblokir oleh Super Admin. Silakan hubungi Super Admin untuk informasi lebih lanjut.' }}
            </p>
            <a href="{{ route('admin.logout') }}" 
               onclick="this.style.pointerEvents='none'; this.innerHTML='<span>Mengeluarkan...</span>';"
               class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold rounded-xl text-sm shadow-lg shadow-red-200 transition-all flex items-center justify-center gap-2 cursor-pointer text-center">
                <span>OK, Keluar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        let adminBlockedModalShown = false;

        function triggerAdminBlockedModal(isInactive = null) {
            const modal = document.getElementById('admin-blocked-account-modal');
            if (!modal) return;

            adminBlockedModalShown = true;

            const updateTexts = (inactive) => {
                const title = document.getElementById('admin-blocked-account-title');
                const msg = document.getElementById('admin-blocked-account-message');
                if (inactive) {
                    if (title) title.innerText = 'Akun Admin Dinonaktifkan';
                    if (msg) msg.innerText = 'Akun Admin Anda telah dinonaktifkan oleh Super Admin. Anda tidak dapat melanjutkan aktivitas di panel admin. Silakan klik tombol di bawah untuk keluar.';
                } else {
                    if (title) title.innerText = 'Akun Admin Telah Diblokir';
                    if (msg) msg.innerText = 'Akses akun Admin Anda telah diblokir oleh Super Admin. Silakan hubungi Super Admin untuk informasi lebih lanjut.';
                }
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            };

            if (isInactive === true || isInactive === false) {
                updateTexts(isInactive);
            } else {
                fetch("{{ route('account.status.check') }}", {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => {
                    if (res.status === 401 || res.status === 403) {
                        return { should_logout: true, is_inactive: true };
                    }
                    return res.json();
                })
                .then(data => {
                    const inactive = !!(data && (data.status === 'inactive' || data.is_inactive));
                    updateTexts(inactive);
                })
                .catch(() => {
                    updateTexts(true);
                });
            }
        }

        function checkAdminAccountStatus() {
            if (adminBlockedModalShown) return;
            fetch("{{ route('account.status.check') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (res.status === 401 || res.status === 403) {
                    triggerAdminBlockedModal(true);
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if (data && (data.should_logout || data.is_blocked || data.is_inactive || data.status === 'blocked' || data.status === 'inactive')) {
                    const isInactive = (data.status === 'inactive' || data.is_inactive);
                    triggerAdminBlockedModal(isInactive);
                }
            })
            .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', () => {
            @if(auth()->check() && in_array(auth()->user()->status, ['blocked', 'inactive']))
                triggerAdminBlockedModal({{ auth()->user()->status === 'inactive' ? 'true' : 'false' }});
            @else
                checkAdminAccountStatus();
            @endif
        });

        // Periodic check every 2.5s and on window focus/tab change
        setInterval(checkAdminAccountStatus, 2500);
        window.addEventListener('focus', checkAdminAccountStatus);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkAdminAccountStatus();
        });

        // Intercept Livewire errors (401 / 403)
        document.addEventListener('livewire:init', () => {
            if (typeof Livewire !== 'undefined' && Livewire.hook) {
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, content, preventDefault }) => {
                        if (status === 403 || status === 401) {
                            if (typeof preventDefault === 'function') preventDefault();
                            let isInactive = true;
                            try {
                                if (content) {
                                    const parsed = typeof content === 'string' ? JSON.parse(content) : content;
                                    if (parsed && (parsed.status || parsed.is_inactive !== undefined)) {
                                        isInactive = !!(parsed.status === 'inactive' || parsed.is_inactive);
                                    }
                                }
                            } catch(e) {}
                            triggerAdminBlockedModal(isInactive);
                        }
                    });
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Sync Sidebar Header Height to match Navbar perfectly
            function syncSidebarHeader() {
                const navbar = document.querySelector('main > nav');
                const sidebarHeader = document.getElementById('admin-sidebar-header');
                if (navbar && sidebarHeader) {
                    sidebarHeader.style.height = navbar.offsetHeight + 'px';
                }
            }
            syncSidebarHeader();
            window.addEventListener('resize', syncSidebarHeader);
            window.addEventListener('load', syncSidebarHeader);

            // 2. Sidebar Scroll Preservation
            const sidebarNav = document.getElementById('admin-sidebar-nav');
            if (sidebarNav) {
                // Restore previous scroll position
                const savedScroll = sessionStorage.getItem('admin_sidebar_scroll');
                if (savedScroll !== null) {
                    sidebarNav.scrollTop = parseInt(savedScroll, 10);
                } else {
                    const activeItem = sidebarNav.querySelector('.bg-primary-600');
                    if (activeItem) {
                        activeItem.scrollIntoView({ block: 'nearest', behavior: 'instant' });
                    }
                }

                // Save scroll position on scroll
                sidebarNav.addEventListener('scroll', function() {
                    sessionStorage.setItem('admin_sidebar_scroll', sidebarNav.scrollTop);
                }, { passive: true });
            }
        });

        // Cegah popup browser "This page has expired" saat sesi habis
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419) {
                        preventDefault();
                        window.location.reload();
                    }
                });
            });
        });
    </script>
</body>

</html>






