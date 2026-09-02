@php
    $title = 'Manajemen Kategori';
    $breadcrumb = 'Super Admin / Manajemen Kategori';
@endphp

    <div>
        <!-- Header Summary Card & Action -->
        <div class="bg-white rounded-2xl border border-gray-200/80 p-3 sm:px-5 sm:py-3.5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50/80 border border-blue-100/80 flex items-center justify-center text-blue-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <span class="text-base sm:text-lg font-bold text-gray-900">
                    Jumlah kategori saat ini: <span class="text-blue-600 font-extrabold ml-1">{{ $categories->total() }}</span>
                </span>
            </div>
            <button wire:click="openCreateModal"
                class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition shadow-sm hover:shadow flex items-center justify-center space-x-2 self-start sm:self-auto cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kategori</span>
            </button>
        </div>
        <div wire:loading class="text-sm text-primary-700 mb-4">Memproses... Mohon tunggu.</div>

        <!-- Filter Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-5 mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
                <!-- Search Input with inline Icon -->
                <div class="relative w-full md:flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none" style="padding-left: 14px;">
                        <svg class="text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari nama atau deskripsi kategori..."
                        style="padding-left: 44px; padding-right: 16px; padding-top: 10px; padding-bottom: 10px; font-size: 14px;"
                        class="w-full bg-gray-50/60 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto flex-wrap sm:flex-nowrap">
                    <!-- Per Page Filter -->
                    <div class="relative min-w-[120px] flex-1 sm:flex-initial">
                        <select wire:model.live="perPage"
                            style="padding-left: 14px; padding-right: 36px; padding-top: 10px; padding-bottom: 10px; font-size: 14px;"
                            class="w-full bg-gray-50/60 hover:bg-white focus:bg-white border border-gray-300 rounded-xl text-gray-800 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition appearance-none">
                            <option value="10">10 Data</option>
                            <option value="25">25 Data</option>
                            <option value="50">50 Data</option>
                            <option value="100">100 Data</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none" style="padding-right: 14px;">
                            <svg class="text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-200">
                        <tr>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider w-10">No</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Icon</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Jumlah Bantuan</th>
                            <th class="px-2.5 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-2.5 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500">
                                    {{ $categories->firstItem() + $loop->index }}
                                </td>
                                <td class="px-2.5 py-3">
                                    <div class="flex items-center gap-3" style="gap: 12px;">
                                        <div class="w-8 h-8 flex-shrink-0 bg-primary-50 border border-primary-200 rounded-full flex items-center justify-center" style="width: 32px; height: 32px; min-width: 32px;">
                                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0" style="margin-left: 4px;">
                                            <div class="text-sm font-semibold text-gray-900 leading-tight truncate max-w-[160px]">{{ $category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2.5 py-3">
                                    <div class="text-xs text-gray-600 max-w-xs truncate">
                                        {{ $category->description ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-center">
                                    @if ($category->icon)
                                        <span class="text-lg">{{ $category->icon }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ number_format($category->helps_count) }} Bantuan
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full {{ $category->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-2.5 py-3 whitespace-nowrap text-center text-xs font-medium">
                                    <div class="inline-flex items-center justify-center gap-1.5 leading-none">
                                        {{-- Edit --}}
                                        <button wire:click="editCategory({{ $category->id }})"
                                            class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition"
                                            title="Edit Kategori">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        {{-- Toggle Status ON/OFF --}}
                                        <button type="button"
                                            x-data="{ on: {{ $category->is_active ? 'true' : 'false' }} }"
                                            @click="on = !on; $wire.toggleStatus({{ $category->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="toggleStatus({{ $category->id }})"
                                            class="inline-flex items-center gap-1.5 cursor-pointer focus:outline-none"
                                            title="{{ $category->is_active ? 'Aktif — Klik untuk nonaktifkan' : 'Nonaktif — Klik untuk aktifkan' }}">
                                            <span class="inline-flex items-center h-5 w-9 p-0.5 rounded-full transition-colors duration-200 ease-in-out"
                                                :class="on ? 'bg-emerald-500 justify-end' : 'bg-gray-300 justify-start'">
                                                <span class="h-4 w-4 rounded-full bg-white shadow ring-0"></span>
                                            </span>
                                        </button>
                                        {{-- Hapus --}}
                                        <button wire:click="confirmDelete({{ $category->id }})"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus Kategori">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium">Tidak ada data kategori</p>
                                        <p class="text-gray-400 text-sm mt-1">Tambah kategori baru untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($categories->hasPages())
                <div class="bg-gray-50 px-6 py-5 border-t border-gray-200">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

    {{-- Create / Edit Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-md overflow-hidden flex flex-col max-h-[90vh]">
                <div class="bg-primary-600 p-4 text-white flex justify-between items-center flex-shrink-0">
                    <div>
                        <h3 class="font-bold text-lg leading-tight">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
                        <p class="text-xs text-primary-100 mt-0.5">Lengkapi form untuk mengelola kategori bantuan</p>
                    </div>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="saveCategory" class="space-y-6">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Nama Kategori <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="name" class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Misal: Rumah Tangga">
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-700">Icon (Emoji/Teks) <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="text" wire:model="icon" class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Misal: 🧹">
                                @error('icon') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-700 block mb-2">Status Kategori <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-3">
                                    <button type="button" 
                                        wire:click="$set('is_active', {{ $is_active ? 'false' : 'true' }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-sm font-semibold {{ $is_active ? 'text-emerald-700' : 'text-gray-500' }}">
                                        {{ $is_active ? 'ON (Aktif)' : 'OFF (Nonaktif)' }}
                                    </span>
                                </div>
                                @error('is_active') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-700">Deskripsi Singkat <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <textarea wire:model="description" rows="3" class="w-full mt-1 px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Deskripsi tentang kategori ini..."></textarea>
                                @error('description') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-2 border-t flex items-center justify-end gap-3 mt-6">
                            <button type="button" wire:click.prevent="closeModal" class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg flex items-center gap-2 text-sm" wire:loading.attr="disabled">
                                <svg wire:loading class="w-4 h-4 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                <span>{{ $categoryId ? 'Simpan Perubahan' : 'Tambah Kategori' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm Delete Modal --}}
    @if($showConfirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-xl shadow-lg w-11/12 max-w-sm p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Kategori?</h3>
                <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus kategori ini? Data yang terkait dengan kategori ini mungkin akan terpengaruh.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" wire:click.prevent="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl text-sm hover:bg-gray-200 transition-colors flex-1">Batal</button>
                    <button wire:click="deleteCategory" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-xl text-sm hover:bg-red-700 transition-colors flex-1 shadow-sm shadow-red-200">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    </div>
</div>ndif

    </div>
</div>