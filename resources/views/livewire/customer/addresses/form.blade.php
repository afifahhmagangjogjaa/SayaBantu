<div class="min-h-screen bg-slate-50/60 pb-20">
    <!-- Header - Brand Blue with Wave Separator (Identik Sesuai Halaman Lain) -->
    <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white bg-[#0066FF]">
        <!-- Decorative circle overlays -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

        <div class="relative z-10 max-w-md mx-auto">
            <div class="flex items-center justify-between text-white mb-2">
                <a href="{{ route('customer.addresses') }}" wire:navigate aria-label="Kembali ke Daftar Alamat"
                   class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer"
                   title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">
                        {{ $isEdit ? 'Ubah Alamat' : 'Tambah Alamat Baru' }}
                    </h1>
                    <p class="text-xs text-blue-100 font-medium mt-0.5">
                        {{ $isEdit ? 'Perbarui informasi alamat pesanan Anda' : 'Lengkapi detail alamat untuk pemesanan bantuan' }}
                    </p>
                </div>

                <div class="w-9 h-9"></div>
            </div>
        </div>

        <!-- Wave Separator (SVG) bergelombang -->
        <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
            <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
        </svg>
    </div>

    <!-- Main Content Form -->
    <div class="max-w-md mx-auto px-4 pt-1 space-y-4 relative z-10">
        <form wire:submit.prevent="save" class="space-y-4">
            <!-- Card 1: Kontak Penerima -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 sm:p-5 space-y-3.5">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#0066FF] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-900">Kontak Penerima</h2>
                        <p class="text-[11px] text-slate-400">Kontak yang dapat dihubungi mitra saat bertugas</p>
                    </div>
                </div>

                <!-- Nama Lengkap / Kontak -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Lengkap / Kontak
                    </label>
                    <input type="text" wire:model="recipient_name" required placeholder="Contoh: Dewi Rahayu"
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition @error('recipient_name') border-rose-400 @enderror">
                    @error('recipient_name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nomor Telepon (HP) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nomor Telepon (HP)
                    </label>
                    <div class="relative">
                        <input type="tel" wire:model="phone" required placeholder="Contoh: 081234567890" maxlength="13"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition @error('phone') border-rose-400 @enderror">
                    </div>
                    @error('phone') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Card 2: Wilayah & Alamat Lengkap -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 sm:p-5 space-y-3.5">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-900">Alamat Lengkap</h2>
                        <p class="text-[11px] text-slate-400">Pastikan titik jalan dan wilayah akurat</p>
                    </div>
                </div>

                <!-- Kota / Kabupaten & Provinsi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-searchable-city-select :cities="$cities" model="city_id" label="Kota / Kabupaten" placeholder="Cari & pilih kota / kabupaten..." :syncProvince="true" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Provinsi
                        </label>
                        <input type="text" wire:model="province" placeholder="Provinsi..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition">
                    </div>
                </div>

                <!-- Kecamatan & Kelurahan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Kecamatan
                        </label>
                        <input type="text" wire:model="district" placeholder="Nama kecamatan..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Kelurahan / Desa
                        </label>
                        <input type="text" wire:model="village" placeholder="Nama kelurahan/desa..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition">
                    </div>
                </div>

                <!-- Nama Jalan, Gedung, No. Rumah / RT RW -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Detail Jalan, Gedung, No. Rumah / RT RW
                    </label>
                    <textarea wire:model="address" rows="3" required
                              placeholder="Contoh: Jl. Malioboro No. 45, RT 02 / RW 05..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition @error('address') border-rose-400 @enderror"></textarea>
                    @error('address') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Detail Patokan & Kode Pos -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Patokan Lokasi (Opsional)
                        </label>
                        <input type="text" wire:model="benchmark" placeholder="Contoh: Depan Masjid Al-Ikhlas, Pagar Biru..."
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition">
                        <p class="text-[11px] text-slate-400 mt-1">Membantu mitra menemukan lokasi lebih cepat.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Kode Pos
                        </label>
                        <input type="text" wire:model="postal_code" placeholder="55xxx" maxlength="6"
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium transition">
                    </div>
                </div>
            </div>

            <!-- Card 3: Label & Pengaturan Alamat -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 sm:p-5 space-y-3.5">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-900">Label & Pengaturan</h2>
                        <p class="text-[11px] text-slate-400">Kategorisasi alamat untuk kemudahan memilih</p>
                    </div>
                </div>

                <!-- Pilihan Cepat Label Alamat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Label Alamat
                    </label>
                    <div class="grid grid-cols-4 gap-2 mb-2">
                        @foreach(['Rumah' => '🏠 Rumah', 'Kantor' => '🏢 Kantor', 'Kos' => '🛏️ Kos', 'Lainnya' => '📍 Lainnya'] as $key => $title)
                            <button type="button" wire:click="setQuickLabel('{{ $key }}')"
                                    class="py-2.5 px-2 text-xs font-bold rounded-xl border text-center transition cursor-pointer {{ ($label === $key || ($key === 'Lainnya' && !in_array($label, ['Rumah', 'Kantor', 'Kos']))) ? 'bg-blue-50 border-[#0066FF] text-[#0066FF] shadow-2xs ring-1 ring-[#0066FF]/20' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                                {{ $title }}
                            </button>
                        @endforeach
                    </div>

                    @if(!in_array($label, ['Rumah', 'Kantor', 'Kos']) || !empty($customLabel))
                        <div class="mt-2 animate-fade-in">
                            <input type="text" wire:model="customLabel" placeholder="Nama label khusus (mis: Toko, Gudang, Villa)..."
                                   class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:bg-white focus:border-[#0066FF] focus:ring-1 focus:ring-[#0066FF] font-medium">
                        </div>
                    @endif
                    @error('label') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Switch Atur sebagai Alamat Utama -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Jadikan Alamat Utama</span>
                        <span class="text-[11px] text-slate-400">Otomatis dipilih saat membuat pesanan bantuan</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_primary" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0066FF]"></div>
                    </label>
                </div>
            </div>

            <!-- Submit Button & Batal -->
            <div class="pt-2 pb-8 space-y-2.5">
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3.5 px-6 rounded-2xl bg-[#0066FF] hover:bg-[#0052CC] text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition active:scale-[0.99] cursor-pointer text-center flex items-center justify-center gap-2">
                    <span wire:loading.remove>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Alamat Baru' }}</span>
                    <span wire:loading class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>

                <a href="{{ route('customer.addresses') }}" wire:navigate
                   class="block w-full py-3 px-6 rounded-2xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition active:scale-[0.99] cursor-pointer text-center bg-white shadow-2xs">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

