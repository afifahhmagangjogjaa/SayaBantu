<div>
    <!-- Header Summary & Action -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Admin Wilayah</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Kelola data administrator, kredensial login, dan kota penugasan wewenang operasional.
                </p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-sm font-semibold shadow-md shadow-primary-500/20 hover:shadow-lg hover:shadow-primary-500/30 transition-all active:scale-98 cursor-pointer self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Admin Baru</span>
            </button>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">
            <!-- Stat 1: Total Admin -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Admin</span>
                    <h3 class="text-2xl font-black text-gray-900 mt-1">{{ $users->total() }}</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Staf pengelola terdaftar</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 border border-primary-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <!-- Stat 2: Admin Aktif -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status Aktif</span>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1">
                        {{ \App\Models\User::where('role', 'admin')->where('status', 'active')->count() }}
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Dapat login & bertugas</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Stat 3: Kota Terkelola -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Cakupan Kota</span>
                    <h3 class="text-2xl font-black text-indigo-600 mt-1">
                        {{ \Illuminate\Support\Facades\DB::table('admin_city')->distinct('city_id')->count('city_id') }}
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Kota memiliki penanggung jawab</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 p-4 mb-5">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            <!-- Search Input -->
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari berdasarkan nama admin, email, atau no. HP..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50/70 hover:bg-white focus:bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition">
            </div>

            <!-- Per Page Filter -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-xs text-gray-500 font-medium whitespace-nowrap hidden sm:inline">Tampilkan:</span>
                <select wire:model.live="perPage"
                    class="w-full md:w-auto pl-3.5 pr-9 py-2.5 bg-gray-50/70 hover:bg-white focus:bg-white border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition cursor-pointer">
                    <option value="10">10 Baris</option>
                    <option value="25">25 Baris</option>
                    <option value="50">50 Baris</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200/80 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Administrator</th>
                        <th class="py-3.5 px-4">Kontak (HP)</th>
                        <th class="py-3.5 px-4">Kota Basis</th>
                        <th class="py-3.5 px-4">Kota Wewenang</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr wire:key="admin-user-row-{{ $user->id }}" class="hover:bg-blue-50/20 transition-colors">
                            <td class="py-3.5 px-4 text-center text-xs text-gray-400 font-mono">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-gray-900 truncate" title="{{ $user->name }}">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 truncate flex items-center gap-1 mt-0.5" title="{{ $user->email }}">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-gray-700 whitespace-nowrap">
                                @if($user->phone)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100/80 text-gray-800">
                                        <span>{{ $user->phone }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-gray-700 whitespace-nowrap">
                                <span class="font-medium text-gray-900">{{ $user->city_name ?? '-' }}</span>
                                @if($user->province)
                                    <span class="block text-[11px] text-gray-400 mt-0.5">{{ $user->province }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->managedCities && $user->managedCities->count() > 0)
                                    <div class="flex flex-wrap items-center gap-1.5 max-w-xs">
                                        @foreach($user->managedCities->take(2) as $managedCity)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200/60">
                                                📍 {{ $managedCity->name }}
                                            </span>
                                        @endforeach
                                        @if($user->managedCities->count() > 2)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600" title="{{ $user->managedCities->pluck('name')->join(', ') }}">
                                                +{{ $user->managedCities->count() - 2 }} kota
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php
                                    $isAdminActive = ($user->status === 'active');
                                @endphp
                                <div class="inline-flex items-center gap-2">
                                    <button type="button"
                                        wire:key="admin-user-toggle-{{ $user->id }}-{{ $user->status }}"
                                        wire:click="confirmDeactivate({{ $user->id }})"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center h-5 w-9 p-0.5 rounded-full transition-colors duration-200 ease-in-out cursor-pointer focus:outline-none {{ $isAdminActive ? 'bg-emerald-500 justify-end' : 'bg-gray-300 justify-start' }}"
                                        title="{{ $isAdminActive ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                        <span class="h-4 w-4 rounded-full bg-white shadow-xs"></span>
                                    </button>
                                    <span class="text-[11px] font-semibold {{ $isAdminActive ? 'text-emerald-700' : 'text-gray-400' }}">
                                        {{ $isAdminActive ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1 bg-gray-50 border border-gray-200/70 p-1 rounded-xl">
                                    {{-- Detail --}}
                                    <button wire:click="viewUser({{ $user->id }})"
                                        class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-white rounded-lg transition-all"
                                        title="Lihat Detail Admin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    {{-- Edit --}}
                                    <button wire:click="editUser({{ $user->id }})"
                                        class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-white rounded-lg transition-all"
                                        title="Edit Admin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    {{-- Hapus --}}
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                        class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-white rounded-lg transition-all"
                                        title="Hapus Admin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-800">Tidak Ada Data Admin</h4>
                                    <p class="text-xs text-gray-500 mt-1">Belum ada admin yang cocok dengan pencarian atau filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL TAMBAH / EDIT ADMIN (PREMIUM REDESIGNED)                             -->
    <!-- ========================================================================= -->
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-gray-950/60 backdrop-blur-xs transition-opacity animate-fade-in"
            x-data="{ showPass: false, citySearch: '' }">
            <div class="w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white flex-shrink-0">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-primary-500/20 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 tracking-tight">
                                {{ $showEditModal ? 'Edit Data Admin Wilayah' : 'Tambah Admin Wilayah Baru' }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $showEditModal ? 'Perbarui informasi kredensial dan kota penugasan wewenang.' : 'Lengkapi kredensial akun dan tentukan kota yang dikelola admin.' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click.prevent="closeModal"
                        class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable Form) -->
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                    <form id="adminForm" wire:submit.prevent="saveUser" class="space-y-5">
                        
                        <!-- CARD 1: INFORMASI KREDENSIAL AKUN -->
                        <div class="bg-gray-50/70 border border-gray-200/80 rounded-2xl p-4 sm:p-5">
                            <div class="flex items-center gap-2 mb-3.5">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center">1</span>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700">Kredensial & Kontak Login</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Nama Lengkap Admin
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <input type="text" wire:model="name" placeholder="Contoh: Hendra Wijaya"
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition font-medium">
                                    </div>
                                    @error('name') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email Login -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Email Login Resmi
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="email" wire:model="email" placeholder="admin.wilayah@sayabantu.com"
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition font-medium">
                                    </div>
                                    @error('email') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Nomor HP / WA -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Nomor WhatsApp / HP
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input type="text" wire:model="phone" placeholder="08xxxxxxxxxx" maxlength="13"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)" inputmode="numeric"
                                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition font-medium font-mono">
                                    </div>
                                    @error('phone') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Kata Sandi (Password)
                                        @if($showEditModal)
                                            <span class="text-gray-400 font-normal">(Kosongkan jika tidak diubah)</span>
                                        @endif
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input :type="showPass ? 'text' : 'password'" wire:model="password"
                                            placeholder="{{ $showEditModal ? 'Isi untuk mengganti sandi' : 'Minimal 8 karakter' }}"
                                            class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition font-medium">
                                        <button type="button" @click="showPass = !showPass"
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showPass" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- CARD 2: PENUGASAN WILAYAH & WEWENANG KERJA -->
                        <div class="bg-blue-50/30 border border-blue-100 rounded-2xl p-4 sm:p-5">
                            <div class="flex items-center gap-2 mb-3.5">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">2</span>
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-800">Penugasan & Wewenang Wilayah</h4>
                                    <p class="text-[11px] text-gray-500">Admin akan bertanggung jawab atas mitra, customer, dan transaksi di kota-kota yang dipilih.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <!-- Kota Basis & Provinsi -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                            Kota Basis Operasional
                                        </label>
                                        <select wire:model.live="city_id"
                                            class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition cursor-pointer">
                                            <option value="">Pilih Kota Basis Utama</option>
                                            @foreach($cities as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->province ?? 'Indonesia' }})</option>
                                            @endforeach
                                        </select>
                                        @error('city_id') <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                            Provinsi Domisili Basis
                                        </label>
                                        <div class="px-3.5 py-2.5 bg-gray-100/80 border border-gray-200 rounded-xl text-sm text-gray-700 font-medium">
                                            {{ $province ?: 'Otomatis terisi saat kota dipilih' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Kota yang Dikelola (Interactive Checklist) -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-xs font-semibold text-gray-800 flex items-center gap-2">
                                            <span>Kota Wewenang yang Dikelola</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                                {{ count($managed_city_ids) }} Kota Dipilih
                                            </span>
                                        </label>
                                    </div>

                                    <!-- City Filter Bar & Card List -->
                                    <div class="bg-white rounded-2xl border border-gray-200 p-3 shadow-2xs">
                                        <div class="relative mb-2.5">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <input type="text" x-model="citySearch" placeholder="Ketik nama kota untuk memfilter pilihan..."
                                                class="w-full pl-8 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-primary-500 transition">
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-52 overflow-y-auto pr-1">
                                            @forelse($availableCities as $c)
                                                <label x-show="!citySearch || '{{ strtolower($c->name) }}'.includes(citySearch.toLowerCase()) || '{{ strtolower($c->province ?? '') }}'.includes(citySearch.toLowerCase())"
                                                    class="flex items-start gap-2.5 p-2.5 rounded-xl border transition-all cursor-pointer select-none text-left
                                                        {{ in_array($c->id, $managed_city_ids) ? 'border-primary-500 bg-primary-50/60 ring-1 ring-primary-200' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50' }}">
                                                    <input type="checkbox" wire:model.live="managed_city_ids" value="{{ $c->id }}"
                                                        class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500 mt-0.5 cursor-pointer">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-xs font-bold text-gray-900 leading-tight">
                                                            {{ $c->name }}
                                                        </div>
                                                        <div class="text-[10px] text-gray-500 mt-0.5">
                                                            {{ $c->province ?? 'Indonesia' }}
                                                        </div>
                                                    </div>
                                                </label>
                                            @empty
                                                <div class="col-span-full py-4 text-center text-xs text-gray-400">
                                                    Belum ada data kota tersedia di sistem.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    @error('managed_city_ids') <span class="text-xs text-red-600 mt-1.5 block font-medium">{{ $message }}</span> @enderror
                                </div>

                                <!-- Alamat Kantor / Catatan Posko (Opsional) -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                        Alamat Kantor / Catatan Posko Wilayah <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <x-smart-address-input 
                                        wire:model="address" 
                                        id="admin_office_address" 
                                        rows="2" 
                                        placeholder="Ketik alamat kantor posko, gedung, atau nama jalan..." 
                                    />
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 animate-shake">
                                <div class="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold text-xs">!</div>
                                <div>
                                    <h5 class="text-xs font-bold text-red-900">Periksa Kembali Formulir</h5>
                                    <p class="text-xs text-red-700 mt-0.5">Pastikan semua kolom telah diisi dengan benar.</p>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
                    <span class="text-xs text-gray-400 hidden sm:inline">Admin otomatis berstatus Aktif & Terverifikasi.</span>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <button type="button" wire:click.prevent="closeModal"
                            class="px-4 py-2.5 border border-gray-300 rounded-xl text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" form="adminForm" wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-xs sm:text-sm font-semibold shadow-md shadow-primary-500/20 hover:shadow-lg transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                            <svg wire:loading wire:target="saveUser" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="saveUser">{{ $showEditModal ? 'Perbarui Data Admin' : 'Simpan Admin Baru' }}</span>
                            <span wire:loading wire:target="saveUser">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL DETAIL ADMIN (PREMIUM REDESIGNED)                                   -->
    <!-- ========================================================================= -->
    @if($showViewModal && $selectedUser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-gray-950/60 backdrop-blur-xs transition-opacity animate-fade-in">
            <div class="w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col">
                <!-- Header Card Banner -->
                <div class="p-6 bg-gradient-to-br from-primary-600 via-indigo-600 to-blue-700 text-white relative">
                    <button type="button" wire:click.prevent="closeModal"
                        class="absolute top-4 right-4 p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 text-white font-black text-2xl flex items-center justify-center shadow-lg">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold tracking-tight text-white">{{ $selectedUser->name }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white border border-white/30">
                                    Admin Wilayah
                                </span>
                            </div>
                            <p class="text-xs text-white/80 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $selectedUser->email }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <!-- Status & Kontak Info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Status Akun</span>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $selectedUser->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                <span class="text-sm font-bold {{ $selectedUser->status === 'active' ? 'text-emerald-700' : 'text-gray-600' }}">
                                    {{ $selectedUser->status === 'active' ? 'Aktif Bertugas' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Nomor HP / WhatsApp</span>
                            <div class="mt-1 text-sm font-bold text-gray-900 font-mono">
                                {{ $selectedUser->phone ?: '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- Kota Basis -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Kota Basis Operasional</span>
                        <div class="mt-1.5 flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900">📍 {{ $selectedUser->city_name ?: '-' }}</span>
                            <span class="text-xs text-gray-500 font-medium">{{ $selectedUser->province ?: '' }}</span>
                        </div>
                    </div>

                    <!-- Kota yang Dikelola -->
                    <div class="p-4 bg-blue-50/40 rounded-2xl border border-blue-100">
                        <span class="text-[11px] font-bold text-blue-900 uppercase tracking-wider block mb-2">
                            Kota Wewenang yang Dikelola
                        </span>
                        @if($selectedUser->managedCities && $selectedUser->managedCities->count() > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($selectedUser->managedCities as $city)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-white text-blue-700 border border-blue-200 shadow-2xs">
                                        📍 {{ $city->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Belum ada kota yang ditugaskan.</p>
                        @endif
                    </div>

                    <!-- Alamat / Catatan Posko -->
                    @if($selectedUser->address)
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">Alamat / Catatan Posko</span>
                            <p class="text-xs text-gray-700 mt-1 leading-relaxed">{{ $selectedUser->address }}</p>
                        </div>
                    @endif

                    <div class="pt-2 text-center">
                        <span class="text-[11px] text-gray-400">Terdaftar sejak {{ optional($selectedUser->created_at)->format('d F Y, H:i') }} WIB</span>
                    </div>
                </div>

                <div class="p-4 bg-gray-50/80 border-t border-gray-100 flex justify-end">
                    <button type="button" wire:click.prevent="closeModal"
                        class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm Delete Modal --}}
    @if($showConfirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 border border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">Konfirmasi Hapus Admin</h3>
                <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun admin ini? Akses login dan wewenang kota admin ini akan dicabut permanen.
                </p>
                <div class="flex items-center justify-end gap-2.5">
                    <button type="button" wire:click.prevent="closeModal"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-semibold text-gray-700 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click.prevent="deleteUser"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-xl text-xs font-semibold text-white shadow-sm transition cursor-pointer">
                        Ya, Hapus Admin
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm Deactivate Modal --}}
    @if($showConfirmDeactivate)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 border border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">Nonaktifkan Admin</h3>
                <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                    Anda yakin ingin menonaktifkan admin <span class="font-bold text-gray-800">{{ $confirmingDeactivateName }}</span>? Sesi login admin akan segera dihentikan.
                </p>
                <div class="flex items-center justify-end gap-2.5">
                    <button type="button" wire:click.prevent="closeDeactivateModal"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-semibold text-gray-700 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click.prevent="executeDeactivateAdmin"
                        class="px-4 py-2 bg-amber-600 hover:bg-amber-700 rounded-xl text-xs font-semibold text-white shadow-sm transition cursor-pointer">
                        Ya, Nonaktifkan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
