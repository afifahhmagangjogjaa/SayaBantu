@php
    $currentRoute = request()->route()?->getName() ?? '';
    
    $routeMeta = [
        'superadmin.dashboard' => [
            'title' => 'Dashboard',
            'subtitle' => 'Ringkasan statistik dan aktivitas sistem Sayabantu hari ini'
        ],
        'superadmin.users' => [
            'title' => 'Kelola Pengguna',
            'subtitle' => 'Kelola semua pengguna dalam sistem'
        ],
        'superadmin.ratings.index' => [
            'title' => 'Rating & Ulasan',
            'subtitle' => 'Pantau feedback, evaluasi, dan rating dari customer dan mitra'
        ],
        'superadmin.cities' => [
            'title' => 'Manajemen Kota',
            'subtitle' => 'Kelola kota layanan sayabantu'
        ],
        'superadmin.admin.users' => [
            'title' => 'Manajemen Admin',
            'subtitle' => 'Kelola admin dalam sistem'
        ],
        'superadmin.settings.help' => [
            'title' => 'Pengaturan Bantuan',
            'subtitle' => 'Kelola nominal minimal dan biaya admin untuk sistem bantuan'
        ],
        'superadmin.settings.banners' => [
            'title' => 'Pengaturan Banner',
            'subtitle' => 'Kelola banner yang tampil di dashboard Customer, Mitra, dan halaman Beranda'
        ],
        'superadmin.transactions.log' => [
            'title' => 'Financial Report',
            'subtitle' => 'Laporan daftar transaksi, top up, dan mutasi saldo sistem'
        ],
        'superadmin.withdraws.index' => [
            'title' => 'Manajemen Withdraw',
            'subtitle' => 'Kelola dan verifikasi permintaan tarik saldo (withdraw) dari mitra'
        ],
        'superadmin.topup.approvals' => [
            'title' => 'Approval Top-Up',
            'subtitle' => 'Verifikasi dan approve request penambahan saldo dari semua customer'
        ],
        'superadmin.activity.logs' => [
            'title' => 'Activity Logs',
            'subtitle' => 'Log riwayat aktivitas dan peristiwa penting dalam sistem'
        ],
        'superadmin.notifications' => [
            'title' => 'Notifikasi',
            'subtitle' => 'Daftar notifikasi dan pemberitahuan sistem'
        ],
        'superadmin.categories' => [
            'title' => 'Manajemen Kategori',
            'subtitle' => 'Kelola semua kategori bantuan dalam sistem'
        ],
        'superadmin.helps.approved' => [
            'title' => 'Permintaan Terbaru',
            'subtitle' => 'Daftar seluruh bantuan yang baru masuk'
        ],
    ];

    $detectedTitle = $routeMeta[$currentRoute]['title'] ?? null;
    $detectedSubtitle = $routeMeta[$currentRoute]['subtitle'] ?? null;

    if (!$detectedTitle) {
        if (request()->routeIs('superadmin.users*')) {
            $detectedTitle = 'Kelola Pengguna';
            $detectedSubtitle = 'Kelola semua pengguna dalam sistem';
        } elseif (request()->routeIs('superadmin.ratings*')) {
            $detectedTitle = 'Rating & Ulasan';
            $detectedSubtitle = 'Pantau feedback, evaluasi, dan rating dari customer dan mitra';
        } elseif (request()->routeIs('superadmin.cities*')) {
            $detectedTitle = 'Manajemen Kota';
            $detectedSubtitle = 'Kelola kota layanan sayabantu';
        } elseif (request()->routeIs('superadmin.admin*')) {
            $detectedTitle = 'Manajemen Admin';
            $detectedSubtitle = 'Kelola admin dalam sistem';
        } elseif (request()->routeIs('superadmin.settings.help*')) {
            $detectedTitle = 'Pengaturan Bantuan';
            $detectedSubtitle = 'Kelola nominal minimal dan biaya admin untuk sistem bantuan';
        } elseif (request()->routeIs('superadmin.settings.banners*')) {
            $detectedTitle = 'Pengaturan Banner';
            $detectedSubtitle = 'Kelola banner yang tampil di dashboard Customer, Mitra, dan halaman Beranda';
        } elseif (request()->routeIs('superadmin.transactions*')) {
            $detectedTitle = 'Financial Report';
            $detectedSubtitle = 'Laporan daftar transaksi, top up, dan mutasi saldo sistem';
        } elseif (request()->routeIs('superadmin.withdraws*')) {
            $detectedTitle = 'Manajemen Withdraw';
            $detectedSubtitle = 'Kelola dan verifikasi permintaan tarik saldo (withdraw) dari mitra';
        } elseif (request()->routeIs('superadmin.topup*')) {
            $detectedTitle = 'Approval Top-Up';
            $detectedSubtitle = 'Verifikasi dan approve request penambahan saldo dari semua customer';
        } elseif (request()->routeIs('superadmin.activity*')) {
            $detectedTitle = 'Activity Logs';
            $detectedSubtitle = 'Log riwayat aktivitas dan peristiwa penting dalam sistem';
        }
    }

    $pageTitle = (!empty($title) && $title !== 'Super Admin Panel') ? $title : ($detectedTitle ?? 'Dashboard');
    $pageSubtitle = (!empty($subtitle)) ? $subtitle : ($detectedSubtitle ?? 'Kelola sistem Sayabantu');
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
</head>

<body class="antialiased" x-data="{ showLogoutModal: false }" @open-logout-modal.window="showLogoutModal = true">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg fixed h-full flex flex-col z-50">
            <div id="superadmin-sidebar-header" class="h-[96px] px-6 flex flex-col justify-center border-b border-gray-200 flex-shrink-0">
                <h1 class="text-2xl font-bold text-primary-600">sayabantu</h1>
                <p class="text-xs text-gray-500 mt-1">Super Admin Panel</p>
            </div>

            <nav class="p-4 flex-1 overflow-y-auto" id="superadmin-sidebar-nav">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.dashboard') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen Data</p>
                </div>

                <a href="{{ route('superadmin.admin.users') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.admin.users*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                    </svg>
                    Manajemen Admin
                </a>

                <a href="{{ route('superadmin.users') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.users*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Kelola Pengguna
                </a>

                <a href="{{ route('superadmin.cities') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.cities*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Manajemen Kota
                </a>

                <a href="{{ route('superadmin.categories') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.categories*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Manajemen Kategori
                </a>

                <a href="{{ route('superadmin.helps.approved') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.helps.approved*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Permintaan Bantuan
                </a>

                 <a href="{{ route('superadmin.ratings.index') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.ratings*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Rating & Ulasan
                </a>

                <a href="{{ route('superadmin.settings.help') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.settings.help*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan Bantuan
                </a>

                <a href="{{ route('superadmin.settings.banners') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.settings.banners*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Pengaturan Banner
                </a>

                <a href="{{ route('superadmin.transactions.log') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.transactions.log*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Financial Report
                </a>

                 <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Keuangan   </p>
                </div>

                <a href="{{ route('superadmin.withdraws.index') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.withdraws*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Manajemen Withdraw
                </a>

                <a href="{{ route('superadmin.topup.approvals') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.topup.approvals*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Approval Top-Up
                </a>

                <a href="{{ route('superadmin.activity.logs') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.activity.logs*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Activity Logs
                </a>



                <!-- Subscriptions (Langganan Mitra) removed -->


                <!-- Moderasi Bantuan link removed -->

                <!-- Verifikasi KTP link removed -->
            </nav>

            <div class="w-64 p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Super Admin</p>
                        </div>
                    </div>
                    <button 
                        @click="$dispatch('open-logout-modal')" 
                        type="button" 
                        class="text-gray-400 hover:text-red-600 transition" 
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
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight" style="font-weight: 800; letter-spacing: -0.025em;">{{ $pageTitle }}</h2>
                            <p class="text-sm sm:text-base text-gray-600 mt-1 font-medium">
                                {{ $pageSubtitle }}
                            </p>
                        </div>

                        <!-- Right Side Actions -->
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <!-- Hari dan Tanggal Badge -->
                            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 h-9 px-3.5 rounded-xl border border-gray-200 shadow-2xs transition">
                                <svg class="w-4 h-4 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="whitespace-nowrap">{{ now()->locale('id')->translatedFormat('l, d M Y') }}</span>
                            </div>

                            <!-- Notifications -->
                            <livewire:super-admin.notification-dropdown />

                            <!-- User Profile -->
                            <div class="flex items-center space-x-3 border-l border-gray-200 pl-3 sm:pl-4">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">Super Admin</p>
                                </div>
                                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-semibold shadow-xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="p-6">
                @if(isset($slot))
                    {{ $slot }}
                @endif

                @yield('content')
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
        
        <!-- Background overlay -->
        <div 
            x-show="showLogoutModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="showLogoutModal = false">
        </div>

        <!-- Modal panel -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                x-show="showLogoutModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all sm:w-full sm:max-w-lg"
                @click.stop>
                
                <div class="bg-white px-6 pt-6 pb-4">
                    <!-- Icon -->
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="mt-4 text-center">
                        <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                            Konfirmasi Logout
                        </h3>
                        <div class="mt-3">
                            <p class="text-base text-gray-600">
                                Apakah Anda yakin ingin keluar dari panel Super Admin?
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Anda harus login kembali untuk mengakses panel ini.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row gap-3 sm:gap-3">
                    <button 
                        type="button"
                        @click="showLogoutModal = false"
                        class="flex-1 inline-flex justify-center items-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-base font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button 
                            type="submit"
                            class="w-full inline-flex justify-center items-center rounded-xl bg-red-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-red-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Sync Sidebar Header Height to match Navbar perfectly
            function syncSidebarHeader() {
                const navbar = document.querySelector('main > nav');
                const sidebarHeader = document.getElementById('superadmin-sidebar-header');
                if (navbar && sidebarHeader) {
                    sidebarHeader.style.height = navbar.offsetHeight + 'px';
                }
            }
            syncSidebarHeader();
            window.addEventListener('resize', syncSidebarHeader);
            window.addEventListener('load', syncSidebarHeader);

            // 2. Sidebar Scroll Preservation
            const sidebarNav = document.getElementById('superadmin-sidebar-nav');
            if (sidebarNav) {
                // Restore previous scroll position
                const savedScroll = sessionStorage.getItem('superadmin_sidebar_scroll');
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
                    sessionStorage.setItem('superadmin_sidebar_scroll', sidebarNav.scrollTop);
                }, { passive: true });
            }
        });
    </script>
</body>

</html>






