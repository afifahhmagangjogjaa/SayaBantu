<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('superadmin.dashboard') }}" wire:navigate class="hover:text-[#0066FF] transition">Super Admin</a>
                <span>/</span>
                <span class="text-slate-400">Pengaturan</span>
                <span>/</span>
                <span class="text-slate-700 font-medium">Saluran Bank & E-Wallet</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Pengaturan Bank & E-Wallet</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola daftar bank dan dompet digital yang aktif digunakan untuk penarikan saldo mitra.</p>
        </div>

        <div>
            <button type="button" wire:click="openAddModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0066FF] hover:bg-[#0052cc] active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-md shadow-blue-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Bank Baru</span>
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 font-medium leading-relaxed">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Saluran</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-gray-900">{{ $totalCount }}</span>
                <span class="text-xs text-slate-400">bank & e-wallet</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-200/80 bg-emerald-50/20 p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Status Aktif (Tampil)</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-emerald-700">{{ $activeCount }}</span>
                <span class="text-xs text-emerald-600">dapat dipilih mitra</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status Nonaktif</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-600">{{ $inactiveCount }}</span>
                <span class="text-xs text-slate-400">disembunyikan dari mitra</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative w-full sm:w-80">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama atau kode bank..."
                   class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition outline-none" />
        </div>

        <!-- Category Filter Tabs -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-bold w-full sm:w-auto">
            <button type="button" wire:click="$set('categoryFilter', 'all')"
                    class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg transition {{ $categoryFilter === 'all' ? 'bg-white text-gray-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}">
                Semua ({{ $totalCount }})
            </button>
            <button type="button" wire:click="$set('categoryFilter', 'bank')"
                    class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg transition {{ $categoryFilter === 'bank' ? 'bg-white text-[#0066FF] shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}">
                🏦 Bank
            </button>
            <button type="button" wire:click="$set('categoryFilter', 'ewallet')"
                    class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg transition {{ $categoryFilter === 'ewallet' ? 'bg-white text-emerald-700 shadow-2xs' : 'text-slate-500 hover:text-slate-900' }}">
                📱 E-Wallet
            </button>
        </div>
    </div>

    <!-- Bank Channels List Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-5">Kode</th>
                        <th class="py-3 px-4">Nama Bank / Saluran</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Urutan</th>
                        <th class="py-3 px-4 text-center">Status Tampil</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($channels as $channel)
                        <tr class="hover:bg-slate-50/60 transition {{ !$channel->is_active ? 'opacity-60 bg-slate-50/30' : '' }}">
                            <!-- Code Badge -->
                            <td class="py-3.5 px-5">
                                <span class="font-mono font-black text-xs px-2.5 py-1 rounded-lg {{ $channel->is_active ? 'bg-blue-50 text-[#0066FF] border border-blue-200/80' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $channel->code }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="py-3.5 px-4 font-bold text-gray-900 text-sm">
                                <div class="flex items-center gap-2">
                                    <span>{{ $channel->category === 'ewallet' ? '📱' : '🏦' }}</span>
                                    <span>{{ $channel->name }}</span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-4">
                                @if($channel->category === 'ewallet')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        E-Wallet
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Bank Transfer
                                    </span>
                                @endif
                            </td>

                            <!-- Order -->
                            <td class="py-3.5 px-4 text-center text-slate-500 font-mono font-semibold">
                                {{ $channel->order }}
                            </td>

                            <!-- Toggle Status Switch -->
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" wire:click="toggleStatus({{ $channel->id }})"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $channel->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"
                                        title="{{ $channel->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $channel->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                                <div class="text-[10px] font-bold mt-1 {{ $channel->is_active ? 'text-emerald-700' : 'text-slate-400' }}">
                                    {{ $channel->is_active ? 'Aktif' : 'Nonaktif' }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" wire:click="openEditModal({{ $channel->id }})"
                                            class="p-2 rounded-xl text-slate-500 hover:text-[#0066FF] hover:bg-blue-50 transition cursor-pointer"
                                            title="Edit bank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $channel->id }})"
                                            class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                            title="Hapus bank">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                <div class="text-3xl mb-2">🔍</div>
                                <p class="font-bold text-gray-700">Tidak ada saluran bank yang ditemukan</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Coba gunakan kata kunci pencarian lain atau klik tombol "Tambah Bank Baru".</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Tambah / Edit Bank -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4" x-data>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <!-- Modal Header -->
                <div class="p-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">{{ $isEditing ? 'Edit Saluran Bank' : 'Tambah Bank Baru' }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Atur detail informasi bank untuk transaksi penarikan</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit="save" class="p-5 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Bank / E-Wallet</label>
                        <input type="text" wire:model="code" placeholder="Contoh: BCA, BRI, DANA"
                               class="w-full uppercase font-mono px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 outline-none transition" />
                        @error('code') <span class="text-rose-600 text-[11px] font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Bank / E-Wallet</label>
                        <input type="text" wire:model="name" placeholder="Contoh: BCA (Bank Central Asia)"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 outline-none transition" />
                        @error('name') <span class="text-rose-600 text-[11px] font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kategori</label>
                            <select wire:model="category"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 outline-none transition">
                                <option value="bank">Bank Transfer</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Urut</label>
                            <input type="number" wire:model="order" min="0" placeholder="0"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 outline-none transition" />
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="w-4 h-4 text-[#0066FF] rounded border-slate-300 focus:ring-[#0066FF]" />
                            <span class="font-bold text-slate-800 text-xs">Aktifkan untuk penarikan dana</span>
                        </label>
                        <p class="text-[11px] text-slate-400 pl-6.5 mt-0.5">Jika dicentang, mitra bisa memilih bank ini saat penarikan.</p>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 bg-[#0066FF] hover:bg-[#0052cc] text-white font-bold rounded-xl text-xs transition shadow-md shadow-blue-500/20 cursor-pointer">
                            Simpan Bank
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4" x-data>
            <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200 p-6 text-center">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl mx-auto mb-3 shadow-2xs">
                    🗑️
                </div>
                <h3 class="font-bold text-base text-gray-900">Hapus Saluran Bank?</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Apakah Anda yakin ingin menghapus bank <strong>"{{ $deletingName }}"</strong> dari daftar saluran penarikan?
                </p>
                <div class="mt-6 flex items-center gap-2.5">
                    <button type="button" wire:click="cancelDelete"
                            class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="delete"
                            class="flex-1 py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-rose-600/20 cursor-pointer">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

