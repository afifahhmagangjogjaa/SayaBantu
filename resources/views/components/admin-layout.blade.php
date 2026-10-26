<style>
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

<div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg fixed h-full overflow-y-auto">
        <!-- Logo/Brand -->
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-primary-600">sayabantu</h1>
            <p class="text-xs text-gray-500 mt-1">
                {{ auth()->user()->role === 'super_admin' ? 'Super Admin Panel' : 'Admin Panel' }}
            </p>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('admin.dashboard') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->role === 'super_admin')
                <div class="mt-6 mb-2">
                    <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Super Admin</p>
                </div>

                <a href="{{ route('superadmin.users') }}"
                    class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('superadmin.users*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen User
                </a>

                <a href="{{ route('superadmin.cities') }}"
                    class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('superadmin.cities*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Manajemen Kota
                </a>

                <a href="{{ route('superadmin.categories') }}"
                    class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('superadmin.categories*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Manajemen Kategori
                </a>
            @endif

            <div class="mt-6 mb-2">
                <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Moderasi</p>
            </div>

            <a href="{{ route('admin.helps') }}"
                class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('admin.helps') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Moderasi Bantuan
            </a>

            <a href="{{ route('admin.verifications') }}"
                class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('admin.verifications') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Verifikasi KTP
            </a>

            <div class="mt-6 mb-2">
                <p class="px-4 text-xs font-medium text-gray-400 uppercase tracking-wider">Keuangan</p>
            </div>

            <a href="{{ route('admin.withdraws.index') }}"
                class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('admin.withdraws.*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Manajemen Withdraw
            </a>

            <a href="{{ route('admin.topup.approvals') }}"
                class="flex items-center px-4 py-3 mb-2 {{ request()->routeIs('admin.topup.*') ? 'text-white bg-primary-600' : 'text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                </svg>
                Approval Top-Up
            </a>
        </nav>

        <!-- User Profile & Logout removed, moved to navbar dropdown -->
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 overflow-y-auto min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg sm:text-xl font-medium text-gray-900">{{ $title ?? 'Admin Panel' }}</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ $subtitle ?? '' }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        {{ $headerActions ?? '' }}

                        <!-- User Avatar Dropdown -->
                        <div class="relative flex items-center border-l border-gray-200 pl-4" x-data="{ open: false }" @click.outside="open = false">
                            <button
                                @click="open = !open"
                                type="button"
                                class="flex items-center space-x-2.5 group cursor-pointer focus:outline-none"
                                title="Menu Profil">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-medium text-gray-900 group-hover:text-primary-600 transition">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm shadow-xs transition"
                                    :class="open ? 'ring-2 ring-primary-500' : 'group-hover:ring-2 group-hover:ring-primary-500'">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute right-0 top-12 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                                style="transform-origin: top right;">

                                <!-- Header info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                </div>

                                <!-- Lihat Profil -->
                                <a href="{{ auth()->user()->role === 'super_admin' ? route('superadmin.password') : route('admin.password') }}"
                                   @click="open = false"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary-600 transition group">
                                    <div class="w-8 h-8 rounded-xl bg-gray-50 group-hover:bg-primary-50 flex items-center justify-center transition">
                                        <svg class="w-4 h-4 text-gray-600 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Lihat Profil</span>
                                </a>

                                <!-- Divider -->
                                <div class="my-1 border-t border-gray-100"></div>

                                <!-- Keluar -->
                                <form method="POST" action="{{ auth()->user()->role === 'super_admin' ? route('logout') : route('admin.logout') }}" class="m-0">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition group text-left">
                                        <div class="w-8 h-8 rounded-xl bg-gray-50 group-hover:bg-red-100 flex items-center justify-center transition">
                                            <svg class="w-4 h-4 text-gray-500 group-hover:text-red-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                        </div>
                                        <span class="font-medium">Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8">
            {{ $slot }}
        </div>
    </main>
</div>