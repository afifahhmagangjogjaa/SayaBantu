@php
    $title = 'Pengaturan Banner';
    $breadcrumb = 'Super Admin / Pengaturan / Banner';
@endphp

<div>
    <!-- Notification Toast / Alert -->
    @if (session('message'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 shadow-sm transition-all">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('message') }}</div>
            <button type="button" @click="$el.closest('div').remove()" class="text-emerald-500 hover:text-emerald-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-2xl text-blue-800 shadow-sm transition-all">
            <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('info') }}</div>
            <button type="button" @click="$el.closest('div').remove()" class="text-blue-500 hover:text-blue-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800 shadow-sm transition-all">
            <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            <button type="button" @click="$el.closest('div').remove()" class="text-red-500 hover:text-red-700 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Top Section: Customer & Mitra Banners (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- 1. Customer Banner Section -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Banner Customer</h2>
                            <p class="text-xs text-gray-500">Tampil di dashboard akun Customer</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full">
                        {{ count($customerBanners) }} Banner
                    </span>
                </div>

                <!-- Gallery Customer -->
                <div class="mb-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @forelse($customerBanners as $i => $b)
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50 group">
                                <img src="{{ asset('storage/' . $b) }}" alt="banner-{{ $i }}" class="w-full h-24 sm:h-28 object-cover group-hover:scale-105 transition-transform duration-200">
                                <button wire:click="removeCustomer({{ $i }})" wire:confirm="Yakin ingin menghapus banner customer ini?" type="button"
                                    class="absolute top-2 right-2 w-7 h-7 bg-white/95 hover:bg-red-500 text-gray-700 hover:text-white rounded-md flex items-center justify-center shadow-md transition-all cursor-pointer"
                                    title="Hapus Banner">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="col-span-full py-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                                Belum ada banner untuk customer.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Upload Area Customer -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Upload Banner Customer</label>

                <div x-data="{
                    isDragging: false,
                    async handleFiles(files) {
                        if (!files || !files.length) return;
                        const compressedList = [];
                        for (let i = 0; i < files.length; i++) {
                            const comp = await window.compressImageFile(files[i]);
                            compressedList.push(comp);
                        }
                        $wire.uploadMultiple('customerUploads', compressedList);
                    }
                }">
                    <label for="customer-file-input"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                        :class="isDragging ? 'border-primary-500 bg-primary-50/40' : 'border-gray-300 bg-gray-50/60 hover:bg-gray-50 hover:border-primary-400'"
                        class="relative border-2 border-dashed rounded-xl p-5 text-center transition-all cursor-pointer block">
                        
                        <input type="file" id="customer-file-input" @change="handleFiles($event.target.files); $event.target.value = ''" accept="image/*" multiple class="hidden" />
                        
                        <div class="space-y-1.5">
                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="text-xs text-gray-600">
                                <span class="font-bold text-primary-600 hover:text-primary-700">Klik untuk upload</span>
                                <span> atau drag & drop</span>
                            </div>
                            <p class="text-[11px] text-gray-400">PNG, JPG, JPEG hingga 10MB</p>
                        </div>
                    </label>
                </div>

                <div wire:loading wire:target="customerUploads" class="text-xs text-primary-600 font-semibold flex items-center gap-2">
                    <svg class="animate-spin h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses file banner customer...
                </div>

                @error('customerUploads.*') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

                @if (!empty($customerUploads))
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 pt-2">
                        @foreach ($customerUploads as $idx => $upload)
                            <div class="relative rounded-lg overflow-hidden border-2 border-primary-300 shadow-xs">
                                <img src="{{ $upload->temporaryUrl() }}" alt="preview-{{ $idx }}" class="w-full h-20 object-cover">
                                <button type="button" wire:click="removeCustomerUpload({{ $idx }})" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-0.5 shadow cursor-pointer" title="Hapus">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center gap-2 pt-1">
                    <button type="button" wire:click.prevent="saveCustomer" wire:loading.attr="disabled" wire:target="customerUploads, saveCustomer"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-primary-500/20 transition cursor-pointer">
                        <span wire:loading.remove wire:target="saveCustomer">Unggah & Simpan</span>
                        <span wire:loading wire:target="saveCustomer">Menyimpan...</span>
                    </button>
                    @if (!empty($customerUploads))
                        <button type="button" wire:click.prevent="$set('customerUploads', [])"
                            class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Mitra Banner Section -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Banner Mitra</h2>
                            <p class="text-xs text-gray-500">Tampil di dashboard akun Mitra</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full">
                        {{ count($mitraBanners) }} Banner
                    </span>
                </div>

                <!-- Gallery Mitra -->
                <div class="mb-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @forelse($mitraBanners as $i => $b)
                            <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50 group">
                                <img src="{{ asset('storage/' . $b) }}" alt="banner-mitra-{{ $i }}" class="w-full h-24 sm:h-28 object-cover group-hover:scale-105 transition-transform duration-200">
                                <button wire:click="removeMitra({{ $i }})" wire:confirm="Yakin ingin menghapus banner mitra ini?" type="button"
                                    class="absolute top-2 right-2 w-7 h-7 bg-white/95 hover:bg-red-500 text-gray-700 hover:text-white rounded-md flex items-center justify-center shadow-md transition-all cursor-pointer"
                                    title="Hapus Banner">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="col-span-full py-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                                Belum ada banner untuk mitra.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Upload Area Mitra -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Upload Banner Mitra</label>

                <div x-data="{
                    isDragging: false,
                    async handleFiles(files) {
                        if (!files || !files.length) return;
                        const compressedList = [];
                        for (let i = 0; i < files.length; i++) {
                            const comp = await window.compressImageFile(files[i]);
                            compressedList.push(comp);
                        }
                        $wire.uploadMultiple('mitraUploads', compressedList);
                    }
                }">
                    <label for="mitra-file-input"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                        :class="isDragging ? 'border-primary-500 bg-primary-50/40' : 'border-gray-300 bg-gray-50/60 hover:bg-gray-50 hover:border-primary-400'"
                        class="relative border-2 border-dashed rounded-xl p-5 text-center transition-all cursor-pointer block">
                        
                        <input type="file" id="mitra-file-input" @change="handleFiles($event.target.files); $event.target.value = ''" accept="image/*" multiple class="hidden" />
                        
                        <div class="space-y-1.5">
                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="text-xs text-gray-600">
                                <span class="font-bold text-primary-600 hover:text-primary-700">Klik untuk upload</span>
                                <span> atau drag & drop</span>
                            </div>
                            <p class="text-[11px] text-gray-400">PNG, JPG, JPEG hingga 10MB</p>
                        </div>
                    </label>
                </div>

                <div wire:loading wire:target="mitraUploads" class="text-xs text-primary-600 font-semibold flex items-center gap-2">
                    <svg class="animate-spin h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses file banner mitra...
                </div>

                @error('mitraUploads.*') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

                @if (!empty($mitraUploads))
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 pt-2">
                        @foreach ($mitraUploads as $idx => $upload)
                            <div class="relative rounded-lg overflow-hidden border-2 border-primary-300 shadow-xs">
                                <img src="{{ $upload->temporaryUrl() }}" alt="preview-mitra-{{ $idx }}" class="w-full h-20 object-cover">
                                <button type="button" wire:click="removeMitraUpload({{ $idx }})" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-0.5 shadow cursor-pointer" title="Hapus">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center gap-2 pt-1">
                    <button type="button" wire:click.prevent="saveMitra" wire:loading.attr="disabled" wire:target="mitraUploads, saveMitra"
                        class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-primary-500/20 transition cursor-pointer">
                        <span wire:loading.remove wire:target="saveMitra">Unggah & Simpan</span>
                        <span wire:loading wire:target="saveMitra">Menyimpan...</span>
                    </button>
                    @if (!empty($mitraUploads))
                        <button type="button" wire:click.prevent="$set('mitraUploads', [])"
                            class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section: Home/Beranda Banner (Full Width, aligned perfectly) -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Banner Beranda (Landing Page)</h2>
                    <p class="text-xs text-gray-500">Tampil di halaman depan utama SayaBantu</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full">
                {{ count($homeBanners) }} Banner
            </span>
        </div>

        <!-- Gallery Home -->
        <div class="mb-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                @forelse($homeBanners as $i => $b)
                    <div class="relative rounded-xl overflow-hidden border border-gray-200 bg-gray-50 group">
                        <img src="{{ asset('storage/' . $b) }}" alt="banner-home-{{ $i }}" class="w-full h-24 sm:h-28 object-cover group-hover:scale-105 transition-transform duration-200">
                        <button wire:click="removeHome({{ $i }})" wire:confirm="Yakin ingin menghapus banner beranda ini?" type="button"
                            class="absolute top-2 right-2 w-7 h-7 bg-white/95 hover:bg-red-500 text-gray-700 hover:text-white rounded-md flex items-center justify-center shadow-md transition-all cursor-pointer"
                            title="Hapus Banner">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @empty
                    <div class="col-span-full py-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                        Belum ada banner untuk beranda.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Upload Area Home -->
        <div class="space-y-4 pt-4 border-t border-gray-100">
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Upload Banner Beranda</label>

            <div x-data="{
                isDragging: false,
                async handleFiles(files) {
                    if (!files || !files.length) return;
                    const compressedList = [];
                    for (let i = 0; i < files.length; i++) {
                        const comp = await window.compressImageFile(files[i]);
                        compressedList.push(comp);
                    }
                    $wire.uploadMultiple('homeUploads', compressedList);
                }
            }">
                <label for="home-file-input"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                    :class="isDragging ? 'border-primary-500 bg-primary-50/40' : 'border-gray-300 bg-gray-50/60 hover:bg-gray-50 hover:border-primary-400'"
                    class="relative border-2 border-dashed rounded-xl p-5 text-center transition-all cursor-pointer block">
                    
                    <input type="file" id="home-file-input" @change="handleFiles($event.target.files); $event.target.value = ''" accept="image/*" multiple class="hidden" />
                    
                    <div class="space-y-1.5">
                        <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="text-xs text-gray-600">
                            <span class="font-bold text-primary-600 hover:text-primary-700">Klik untuk upload</span>
                            <span> atau drag & drop</span>
                        </div>
                        <p class="text-[11px] text-gray-400">PNG, JPG, JPEG hingga 10MB</p>
                    </div>
                </label>
            </div>

            <div wire:loading wire:target="homeUploads" class="text-xs text-primary-600 font-semibold flex items-center gap-2">
                <svg class="animate-spin h-3.5 w-3.5 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses file banner beranda...
            </div>

            @error('homeUploads.*') <div class="text-xs text-red-600">{{ $message }}</div> @enderror

            @if (!empty($homeUploads))
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 pt-2">
                    @foreach ($homeUploads as $idx => $upload)
                        <div class="relative rounded-lg overflow-hidden border-2 border-primary-300 shadow-xs">
                            <img src="{{ $upload->temporaryUrl() }}" alt="preview-home-{{ $idx }}" class="w-full h-20 object-cover">
                            <button type="button" wire:click="removeHomeUpload({{ $idx }})" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full p-0.5 shadow cursor-pointer" title="Hapus">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center gap-2 pt-1">
                <button type="button" wire:click.prevent="saveHome" wire:loading.attr="disabled" wire:target="homeUploads, saveHome"
                    class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-md shadow-primary-500/20 transition cursor-pointer">
                    <span wire:loading.remove wire:target="saveHome">Unggah & Simpan</span>
                    <span wire:loading wire:target="saveHome">Menyimpan...</span>
                </button>
                @if (!empty($homeUploads))
                    <button type="button" wire:click.prevent="$set('homeUploads', [])"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                        Batal
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Section: Real-time Live Previews (2 Columns) -->
    <div class="pt-2">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900">Preview Tampilan Dashboard</h2>
            <p class="text-xs text-gray-500">Lihat simulasi banner di dashboard Customer dan Mitra secara real-time</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Preview Customer -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-3.5 text-white">
                    <h3 class="text-sm font-bold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Dashboard Customer
                    </h3>
                </div>
                <div class="p-5">
                    <div id="customer-preview" class="h-48 sm:h-52 bg-gray-100 rounded-xl overflow-hidden shadow-inner border border-gray-200 relative group">
                        @if(!empty($customerBanners) && count($customerBanners))
                            <div id="customerSlider" class="w-full h-full overflow-hidden">
                                <div class="customer-slides flex h-full will-change-transform" style="transition: transform 700ms cubic-bezier(.2,.9,.2,1);">
                                    @foreach($customerBanners as $b)
                                        <div class="flex-shrink-0 w-full h-full">
                                            <img src="{{ asset('storage/' . $b) }}" alt="preview-customer" class="w-full h-full object-cover" />
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" data-role="prev" data-target="customer"
                                    class="absolute left-2.5 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow hover:bg-white opacity-0 group-hover:opacity-100 transition-all hover:scale-110 cursor-pointer z-20">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <button type="button" data-role="next" data-target="customer"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow hover:bg-white opacity-0 group-hover:opacity-100 transition-all hover:scale-110 cursor-pointer z-20">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                </button>
                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                                    @foreach($customerBanners as $index => $b)
                                        <div class="customer-dot w-2 h-2 rounded-full bg-white/60 transition-all duration-300" data-index="{{ $index }}"></div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-center px-6">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500 font-semibold">Tidak ada banner Customer</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Sistem akan menampilkan placeholder default</p>
                            </div>
                        @endif
                    </div>
                    <p class="text-[11px] text-gray-500 mt-3 text-center">Banner berganti otomatis setiap 3.5 detik. Hover untuk jeda animasi.</p>
                </div>
            </div>

            <!-- Preview Mitra -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3.5 text-white">
                    <h3 class="text-sm font-bold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Dashboard Mitra
                    </h3>
                </div>
                <div class="p-5">
                    <div id="mitra-preview" class="h-48 sm:h-52 bg-gray-100 rounded-xl overflow-hidden shadow-inner border border-gray-200 relative group">
                        @if(!empty($mitraBanners) && count($mitraBanners))
                            <div id="mitraSlider" class="w-full h-full overflow-hidden">
                                <div class="mitra-slides flex h-full will-change-transform" style="transition: transform 700ms cubic-bezier(.2,.9,.2,1);">
                                    @foreach($mitraBanners as $b)
                                        <div class="flex-shrink-0 w-full h-full">
                                            <img src="{{ asset('storage/' . $b) }}" alt="preview-mitra" class="w-full h-full object-cover" />
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" data-role="prev" data-target="mitra"
                                    class="absolute left-2.5 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow hover:bg-white opacity-0 group-hover:opacity-100 transition-all hover:scale-110 cursor-pointer z-20">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <button type="button" data-role="next" data-target="mitra"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm rounded-full p-2 shadow hover:bg-white opacity-0 group-hover:opacity-100 transition-all hover:scale-110 cursor-pointer z-20">
                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                </button>
                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                                    @foreach($mitraBanners as $index => $b)
                                        <div class="mitra-dot w-2 h-2 rounded-full bg-white/60 transition-all duration-300" data-index="{{ $index }}"></div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-center px-6">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs text-gray-500 font-semibold">Tidak ada banner Mitra</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Sistem akan menampilkan placeholder default</p>
                            </div>
                        @endif
                    </div>
                    <p class="text-[11px] text-gray-500 mt-3 text-center">Banner berganti otomatis setiap 3.5 detik. Hover untuk jeda animasi.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Global client-side image compression helper to avoid PHP 2M upload limit
        window.compressImageFile = async function(file) {
            if (!file || !file.type || !file.type.startsWith('image/') || file.type === 'image/svg+xml') {
                return file;
            }
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const MAX_WIDTH = 1920;
                        const MAX_HEIGHT = 1080;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height = Math.round((height * MAX_WIDTH) / width);
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width = Math.round((width * MAX_HEIGHT) / height);
                                height = MAX_HEIGHT;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                resolve(file);
                                return;
                            }
                            const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                            const compressedFile = new File([blob], cleanName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(compressedFile);
                        }, 'image/jpeg', 0.85);
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        };

        (function () {
            let activeIntervals = {};

            function initSimpleSlider(prefix) {
                if (activeIntervals[prefix]) {
                    clearInterval(activeIntervals[prefix]);
                    delete activeIntervals[prefix];
                }

                const sliderId = prefix + 'Slider';
                const sliderEl = document.getElementById(sliderId);
                if (!sliderEl) return;
                const slidesWrapper = sliderEl.querySelector('.' + prefix + '-slides');
                if (!slidesWrapper) return;
                const total = slidesWrapper.children.length || 0;
                if (total <= 0) return;

                let idx = parseInt(slidesWrapper.dataset.idx || '0', 10) || 0;
                if (idx >= total) idx = 0;

                const dots = sliderEl.querySelectorAll('.' + prefix + '-dot');

                function updateDots() {
                    dots.forEach((dot, i) => {
                        if (i === idx) {
                            dot.classList.add('bg-white', 'w-6');
                            dot.classList.remove('bg-white/60', 'w-2');
                        } else {
                            dot.classList.add('bg-white/60', 'w-2');
                            dot.classList.remove('bg-white', 'w-6');
                        }
                    });
                }

                function goTo(i) {
                    idx = (i + total) % total;
                    slidesWrapper.style.transform = 'translateX(' + (-idx * 100) + '%)';
                    slidesWrapper.dataset.idx = String(idx);
                    updateDots();
                }

                function next() { goTo(idx + 1); }
                function prev() { goTo(idx - 1); }

                updateDots();

                if (total > 1) {
                    activeIntervals[prefix] = setInterval(next, 3500);

                    const container = document.getElementById(prefix + '-preview');
                    if (container) {
                        container.onmouseenter = function () {
                            if (activeIntervals[prefix]) clearInterval(activeIntervals[prefix]);
                        };
                        container.onmouseleave = function () {
                            if (activeIntervals[prefix]) clearInterval(activeIntervals[prefix]);
                            activeIntervals[prefix] = setInterval(next, 3500);
                        };
                    }
                }

                const prevBtn = sliderEl.querySelector('[data-role="prev"]');
                const nextBtn = sliderEl.querySelector('[data-role="next"]');
                if (prevBtn) {
                    prevBtn.onclick = function (ev) {
                        ev.stopPropagation();
                        prev();
                        if (activeIntervals[prefix]) {
                            clearInterval(activeIntervals[prefix]);
                            activeIntervals[prefix] = setInterval(next, 3500);
                        }
                    };
                }
                if (nextBtn) {
                    nextBtn.onclick = function (ev) {
                        ev.stopPropagation();
                        next();
                        if (activeIntervals[prefix]) {
                            clearInterval(activeIntervals[prefix]);
                            activeIntervals[prefix] = setInterval(next, 3500);
                        }
                    };
                }
            }

            window.initBannerSliders = function () {
                initSimpleSlider('customer');
                initSimpleSlider('mitra');
            };

            document.addEventListener('DOMContentLoaded', window.initBannerSliders);
            document.addEventListener('livewire:navigated', window.initBannerSliders);

            if (window.Livewire) {
                Livewire.hook('morph.updated', function () {
                    setTimeout(window.initBannerSliders, 50);
                });
                Livewire.on('bannersSaved', function () {
                    setTimeout(window.initBannerSliders, 100);
                });
            }
        })();
    </script>
</div>