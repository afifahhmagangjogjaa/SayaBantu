@php
    $currentRoute = request()->route()?->getName() ?? '';
    
    $routeMeta = [
        'superadmin.dashboard' => [
            'title' => 'Dashboard',
            'subtitle' => 'Ringkasan statistik dan aktivitas sistem Sayabantu hari ini'
        ],
        'superadmin.customers' => [
            'title' => 'Manajemen Customer',
            'subtitle' => 'Kelola seluruh data, verifikasi, dan status akun customer'
        ],
        'superadmin.mitra' => [
            'title' => 'Manajemen Mitra',
            'subtitle' => 'Kelola seluruh data, verifikasi, dan status akun mitra'
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
            'subtitle' => 'Kelola nominal minimal, radius mitra, dan biaya platform'
        ],
        'superadmin.pengaturan.bantuan.fee-pendapatan' => [
            'title' => 'Fee & Pendapatan Platform',
            'subtitle' => 'Grafik perolehan pendapatan, breakdown sumber fee, dan pengaturan bagi hasil'
        ],
        'superadmin.pengaturan.bantuan.biaya-topup' => [
            'title' => 'Biaya Admin Top-Up & Bank',
            'subtitle' => 'Atur biaya admin top-up bertingkat dan daftar rekening bank penerima'
        ],
        'superadmin.pengaturan.bantuan.tarif-radius' => [
            'title' => 'Tarif & Radius Bantuan',
            'subtitle' => 'Nominal minimal bantuan standar & urgent, serta batas jangkauan mitra'
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
        } elseif (request()->routeIs('superadmin.settings.help*') || request()->routeIs('superadmin.pengaturan.bantuan*')) {
            $detectedTitle = 'Pengaturan Bantuan';
            $detectedSubtitle = 'Kelola nominal minimal, radius mitra, dan biaya platform';
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
    <style>
        [x-cloak] { display: none !important; }

        /* Seluruh elemen bold, semibold, dan heading di panel Super Admin diubah ke font-medium (500) */
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
            <div id="superadmin-sidebar-header" class="h-[96px] px-6 flex flex-col justify-center border-b border-gray-200 flex-shrink-0">
                <h1 class="text-2xl font-bold text-primary-600">sayabantu</h1>
                <p class="text-xs text-gray-500 mt-1">Super Admin Panel</p>
            </div>

            <nav class="p-4 flex-1 overflow-y-auto" id="superadmin-sidebar-nav">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.dashboard') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Manajemen Data</p>
                </div>

                <a href="{{ route('superadmin.admin.users') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.admin.users*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11c1.657 0 3-1.343 3-3S17.657 5 16 5s-3 1.343-3 3 1.343 3 3 3zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                    </svg>
                    Manajemen Admin
                </a>

                <a href="{{ route('superadmin.customers') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.customers*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Manajemen Customer
                </a>

                <a href="{{ route('superadmin.mitra') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.mitra*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Manajemen Mitra
                </a>

                <a href="{{ route('superadmin.cities') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.cities*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Manajemen Kota
                </a>

                <a href="{{ route('superadmin.categories') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.categories*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Manajemen Kategori
                </a>

                <a href="{{ route('superadmin.helps.approved') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.helps.approved*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Permintaan Bantuan
                </a>

                 <a href="{{ route('superadmin.ratings.index') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.ratings*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Rating & Ulasan
                </a>

                @php
                    $isHelpSettingsActive = request()->routeIs('superadmin.settings.help*') || request()->routeIs('superadmin.pengaturan.bantuan*');
                @endphp
                <div x-data="{ open: {{ $isHelpSettingsActive ? 'true' : 'false' }} }" class="mb-2">
                    <button type="button" @click="open = !open"
                        class="w-[calc(100%-1.5rem)] flex items-center mx-3 px-3 py-2.5 text-sm font-medium leading-tight rounded-lg transition {{ $isHelpSettingsActive ? 'text-primary-700 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0 {{ $isHelpSettingsActive ? 'text-primary-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="whitespace-nowrap">Pengaturan Bantuan</span>
                    </button>

                    <div x-show="open" x-cloak class="mt-1 space-y-1 pl-9 pr-3">
                        <a href="{{ route('superadmin.pengaturan.bantuan.fee-pendapatan') }}"
                            class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('superadmin.pengaturan.bantuan.fee-pendapatan') ? 'text-primary-700 bg-primary-100/90 font-medium' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <span class="w-4 h-4 mr-3 flex items-center justify-center flex-shrink-0 {{ request()->routeIs('superadmin.pengaturan.bantuan.fee-pendapatan') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </span>
                            <span class="whitespace-nowrap">Fee & Pendapatan</span>
                        </a>

                        <a href="{{ route('superadmin.pengaturan.bantuan.biaya-topup') }}"
                            class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('superadmin.pengaturan.bantuan.biaya-topup') ? 'text-primary-700 bg-primary-100/90 font-medium' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <span class="w-4 h-4 mr-3 flex items-center justify-center flex-shrink-0 {{ request()->routeIs('superadmin.pengaturan.bantuan.biaya-topup') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </span>
                            <span class="whitespace-nowrap">Biaya Top-Up & Bank</span>
                        </a>

                        <a href="{{ route('superadmin.pengaturan.bantuan.tarif-radius') }}"
                            class="group flex items-center px-3 py-2 text-xs font-medium rounded-lg transition {{ request()->routeIs('superadmin.pengaturan.bantuan.tarif-radius') ? 'text-primary-700 bg-primary-100/90 font-medium' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <span class="w-4 h-4 mr-3 flex items-center justify-center flex-shrink-0 {{ request()->routeIs('superadmin.pengaturan.bantuan.tarif-radius') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span class="whitespace-nowrap">Tarif & Radius</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('superadmin.settings.banners') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.settings.banners*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Pengaturan Banner
                </a>

                 <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Keuangan</p>
                </div>

                <a href="{{ route('superadmin.transactions.log') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.transactions.log*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Financial Report
                </a>

                <a href="{{ route('superadmin.withdraws.index') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.withdraws*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Manajemen Withdraw
                </a>

                <a href="{{ route('superadmin.topup.approvals') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.topup.approvals*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Approval Top-Up
                </a>

                <a href="{{ route('superadmin.activity.logs') }}"
                    class="flex items-center mx-3 px-3 py-2.5 mb-2 text-sm font-medium leading-tight {{ request()->routeIs('superadmin.activity.logs*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-medium">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
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
                            <h2 class="text-lg sm:text-xl font-medium text-gray-900">{{ $pageTitle }}</h2>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                                {{ $pageSubtitle }}
                            </p>
                        </div>

                        <!-- Right Side Actions -->
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <!-- Hari dan Tanggal Badge -->
                            <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 h-9 px-3.5 rounded-xl border border-gray-200 shadow-2xs transition">
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
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">Super Admin</p>
                                </div>
                                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-medium shadow-xs">
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
                        Apakah Anda yakin ingin keluar dari panel Super Admin? Anda harus login kembali untuk mengakses panel ini.
                    </p>

                    <!-- Actions -->
                    <div class="mt-6 flex items-center gap-3">
                        <button 
                            type="button"
                            @click="showLogoutModal = false"
                            class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-medium transition shadow-2xs cursor-pointer">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1 m-0">
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






