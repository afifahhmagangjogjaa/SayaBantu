<div class="min-h-screen bg-slate-50/60 pb-16">
    <!-- Header - Brand Blue with Wave Separator (Identik Sesuai Halaman Lain) -->
    <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white bg-[#0066FF]">
        <!-- Decorative circle overlays -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

        <div class="relative z-10 max-w-md mx-auto">
            <div class="flex items-center justify-between text-white mb-2">
                <a href="{{ route('profile') }}" wire:navigate aria-label="Kembali ke Profil"
                   class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer"
                   title="Kembali ke Profil">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">Alamat Saya</h1>
                    <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola daftar alamat pesanan Anda</p>
                </div>

                <div class="w-9"></div>
            </div>
        </div>

        <!-- Wave Separator (SVG) bergelombang -->
        <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
            <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
        </svg>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-md mx-auto px-4 pt-1 pb-24 space-y-3.5 relative z-10">
        <!-- Toast Flash Message -->
        @if(session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold px-4 py-3 rounded-2xl shadow-xs flex items-center justify-between gap-3 animate-fade-in">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Subtitle Info Bar -->
        <div class="flex items-center justify-between px-1 text-xs text-slate-500">
            <span class="font-bold text-slate-700 text-xs">Alamat Tersimpan ({{ $addresses->count() }})</span>
            <span class="text-[11px] text-slate-400">Maks. 10 Alamat</span>
        </div>

        <!-- Daftar Kartu Alamat (Shopee Clean & Modern Style) -->
        @forelse($addresses as $addr)
            @php
                $cleanName = $addr->clean_recipient_name;
                $formattedPhone = $addr->formatted_phone;
                $rawAddress = trim($addr->address);
                $regionString = $addr->region_string;
                $hasCity = $addr->city_name && str_contains(strtolower($rawAddress), strtolower($addr->city_name));
            @endphp
            <div wire:key="address-card-{{ $addr->id }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 sm:p-5 transition hover:border-slate-300 hover:shadow-xs">
                <!-- Baris 1: Nama, Kontak, dan Tombol Ubah (Pindah Halaman) -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-900 text-sm tracking-tight leading-snug">{{ $cleanName }}</span>
                            <span class="text-slate-300 font-light text-xs">|</span>
                            <span class="text-xs font-semibold text-slate-500 tracking-wide">{{ $formattedPhone }}</span>
                        </div>
                    </div>

                    <!-- Tombol Ubah (Pindah Halaman Penuh, Tanpa Modal Pop-up) -->
                    <a href="{{ route('customer.addresses.edit', $addr->id) }}" wire:navigate
                       class="text-xs font-bold text-[#0066FF] hover:text-[#0052CC] hover:bg-blue-50 px-2.5 py-1 rounded-lg transition active:scale-95 shrink-0 inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Ubah</span>
                    </a>
                </div>

                <!-- Baris 2: Alamat Lengkap & Wilayah -->
                <div class="mt-2 text-xs text-slate-700 leading-relaxed">
                    <p class="font-medium text-slate-800">{{ $rawAddress }}</p>
                    @if(!$hasCity && !empty($regionString))
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $regionString }}</p>
                    @endif

                    @if(!empty($addr->benchmark))
                        <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50/80 border border-amber-200/60 rounded-lg text-[11px] text-amber-800">
                            <span class="font-bold text-amber-600 shrink-0">📍 Patokan:</span>
                            <span class="text-slate-700">{{ $addr->benchmark }}</span>
                        </div>
                    @endif
                </div>

                <!-- Baris 3: Badges (Shopee Style: [Utama] [Rumah]) -->
                <div class="mt-3 flex items-center gap-1.5 flex-wrap">
                    @if($addr->is_primary)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 uppercase tracking-wide">
                            Utama
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold text-slate-600 bg-slate-100 border border-slate-200">
                        @if($addr->label === 'Rumah') 🏠 
                        @elseif($addr->label === 'Kantor') 🏢 
                        @elseif($addr->label === 'Kos') 🛏️ 
                        @elseif($addr->label === 'Apartemen') 🏬 
                        @else 📍 
                        @endif
                        {{ $addr->label }}
                    </span>
                </div>

                <!-- Baris 4: Footer Aksi Bawah (Alamat Utama & Hapus) -->
                <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                    @if($addr->is_primary)
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Alamat Utama Terpilih</span>
                        </div>
                    @else
                        <button type="button" 
                                wire:click="setAsPrimary({{ $addr->id }})"
                                wire:loading.attr="disabled"
                                class="text-xs font-semibold text-slate-500 hover:text-[#0066FF] transition cursor-pointer flex items-center gap-1.5 group disabled:opacity-50">
                            <span class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 group-hover:border-[#0066FF] transition inline-block shrink-0"></span>
                            <span wire:loading.remove wire:target="setAsPrimary({{ $addr->id }})">Atur sebagai Alamat Utama</span>
                            <span wire:loading wire:target="setAsPrimary({{ $addr->id }})" class="inline-flex items-center gap-1 text-[#0066FF]">
                                <svg class="animate-spin h-3.5 w-3.5 text-[#0066FF]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Mengubah...</span>
                            </span>
                        </button>
                    @endif

                    @if(!$addr->is_primary || $addresses->count() > 1)
                        <button type="button" wire:click="confirmDelete({{ $addr->id }})" 
                                wire:loading.attr="disabled"
                                class="text-xs font-medium text-slate-400 hover:text-rose-600 transition flex items-center gap-1 cursor-pointer py-1 px-1.5 rounded hover:bg-rose-50/50"
                                title="Hapus Alamat">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus</span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="bg-white rounded-3xl p-8 text-center border border-slate-200/80 shadow-xs">
                <div class="w-16 h-16 mx-auto mb-3.5 rounded-full bg-blue-50 text-[#0066FF] flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 mb-1">Belum Ada Alamat Tersimpan</h3>
                <p class="text-xs text-slate-500 max-w-xs mx-auto mb-5 leading-relaxed">
                    Tambahkan alamat rumah, kantor, atau lokasi lainnya untuk memudahkan pemesanan bantuan.
                </p>
                <a href="{{ route('customer.addresses.create') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold text-xs shadow-md shadow-blue-500/20 active:scale-95 transition cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Alamat Baru</span>
                </a>
            </div>
        @endforelse

        <!-- Tombol Tambah Alamat Baru (Pindah Halaman) -->
        @if($addresses->count() < 10)
            <div class="pt-2">
                <a href="{{ route('customer.addresses.create') }}" wire:navigate
                   class="w-full py-3 px-4 rounded-xl bg-[#0066FF] hover:bg-[#0052cc] text-white font-bold text-xs shadow-xs active:scale-[0.99] transition cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Alamat Baru</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Modal Konfirmasi Hapus Alamat -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 animate-fade-in">
            <div class="bg-white max-w-sm w-full rounded-2xl p-5 text-center shadow-2xl border border-slate-100 animate-scale-up">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900 mb-1">Hapus Alamat Ini?</h3>
                <p class="text-xs text-slate-500 leading-relaxed mb-4">
                    Alamat yang dihapus tidak dapat dipulihkan. Apakah Anda yakin ingin menghapusnya?
                </p>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="cancelDelete"
                            class="flex-1 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="delete"
                            class="flex-1 py-2.5 px-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
