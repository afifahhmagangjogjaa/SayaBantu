<div class="min-h-screen bg-white"
    x-data
    x-init="
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                p => {
                    $wire.setCoordinates(p.coords.latitude, p.coords.longitude);
                },
                err => { console.log('Geolocation init info:', err.message); },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }
    "
>
    <style>
        :root{
            --brand-500: #0ea5a4;
            --brand-600: #08979a;
            --muted-600: #6b7280;
        }

        .card-shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card-shadow-hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .focus-ring:focus { outline: none; box-shadow: 0 0 0 3px rgba(14,165,164,0.2); }
        
        /* BRImo-style decorative pattern */
        .header-pattern {
            position: relative;
            overflow: hidden;
        }
        
        .header-pattern::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header-pattern::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden header-pattern" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <button onclick="window.history.back()" aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="text-center flex-1">
                        <h1 class="text-lg font-bold">Semua Bantuan</h1>
                        <p class="text-xs text-white/90 mt-0.5">Cari bantuan yang tersedia</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('components.notification-icon', ['route' => route('mitra.notifications.index')])
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="relative mt-3">
                    <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari nama, lokasi, atau deskripsi..."
                        class="w-full pl-10 pr-10 py-2.5 rounded-2xl bg-white/95 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-white/80 focus:shadow-md outline-none transition text-xs font-medium">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if(!empty($search))
                        <button type="button" wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Curved separator (SVG) to create non-flat divider into content -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-6 min-h-[60vh]">
            <!-- Modern Sort & Filter Header -->
            <div class="mb-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-[#0098e7] flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-gray-800">Urutkan Bantuan</span>
                        <span class="text-[10px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                            {{ $helps->total() ?? count($helps) }} tersedia
                        </span>
                    </div>

                    <!-- Sleek Custom Dropdown Filter -->
                    <div class="relative inline-flex items-center">
                        <select wire:model.live="sortBy" 
                            x-on:change="if($event.target.value === 'nearby' && navigator.geolocation){ navigator.geolocation.getCurrentPosition(p => { $wire.setCoordinates(p.coords.latitude, p.coords.longitude); }); }" 
                            class="appearance-none pl-3.5 pr-8 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-xs font-semibold text-gray-700 shadow-2xs hover:border-[#0098e7]/60 focus:border-[#0098e7] focus:ring-2 focus:ring-blue-100 outline-none transition cursor-pointer">
                            <option value="latest">✨ Terbaru</option>
                            <option value="nearby">📍 Terdekat (Maks. {{ (int) ($maxRadius ?? 10) }} km)</option>
                            <option value="price_high">💰 Harga Tertinggi</option>
                            <option value="price_low">🏷️ Harga Terendah</option>
                            <option value="oldest">⏳ Terlama</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="space-y-4">
                {{-- Flash Messages --}}
                @if (session()->has('error') || session()->has('message') || session()->has('success'))
                    <div>
                        @if (session()->has('error'))
                            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3.5 text-xs flex items-start gap-2.5 shadow-2xs">
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1 font-medium leading-relaxed">{{ session('error') }}</div>
                            </div>
                        @endif
                        @if (session()->has('success') || session()->has('message'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3.5 text-xs flex items-start gap-2.5 shadow-2xs">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <div class="flex-1 font-medium leading-relaxed">{{ session('success') ?? session('message') }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- KTP Verification Warning Banner --}}
                @auth
                    @if(!auth()->user()->verified)
                        @php
                            $hasKtpUploaded = !empty(auth()->user()->ktp_photo);
                        @endphp
                        <div class="rounded-2xl p-3.5 text-xs flex items-center justify-between gap-3 bg-white"
                             style="border: 1.5px solid {{ $hasKtpUploaded ? '#3b82f6' : '#f59e0b' }}; box-shadow: 0 4px 14px {{ $hasKtpUploaded ? 'rgba(59, 130, 246, 0.12)' : 'rgba(245, 158, 11, 0.12)' }};">
                            <div class="flex items-center gap-3 min-w-0">
                                <div style="background: linear-gradient(135deg, {{ $hasKtpUploaded ? '#3b82f6 0%, #1d4ed8 100%' : '#f59e0b 0%, #d97706 100%' }}); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(59, 130, 246, 0.35);">
                                    <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $hasKtpUploaded ? 'Verifikasi KTP Sedang Diproses' : 'Verifikasi KTP Diperlukan' }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $hasKtpUploaded ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-amber-100 text-amber-900 border border-amber-200' }}">
                                            {{ $hasKtpUploaded ? 'Menunggu Admin' : 'Wajib' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-600 truncate mt-0.5">
                                        {{ $hasKtpUploaded ? 'Dokumen KTP Anda sedang ditinjau. Anda dapat mengambil order setelah disetujui.' : 'Unggah KTP & Selfie terlebih dahulu untuk dapat mengambil pesanan bantuan.' }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('profile.settings.verification') }}"
                               class="flex-shrink-0 font-bold px-3 py-1.5 rounded-xl transition text-[11px] text-white hover:opacity-95 active:scale-95 shadow-xs"
                               style="background: linear-gradient(135deg, #0098e7, #0077cc); box-shadow: 0 2px 6px rgba(0, 152, 231, 0.35);">
                                {{ $hasKtpUploaded ? 'Cek Status' : 'Verifikasi' }}
                            </a>
                        </div>
                    @endif
                @endauth

                @if(!empty($needsCity))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                        <div class="font-semibold">Atur kota Anda terlebih dahulu</div>
                        <div class="mt-1">Untuk melihat bantuan yang tersedia di daerah Anda, silakan tentukan Kota/Kabupaten Anda di halaman profil.</div>
                        <div class="mt-3">
                            <a href="{{ route('mitra.profile') }}" class="inline-block px-3 py-2 bg-yellow-600 text-white rounded-lg text-xs">Buka Profil</a>
                        </div>
                    </div>
                @endif
                {{-- List based on filter --}}
                @forelse($helps as $help)
                    @php
                        $isUrgent = $help->isUrgent();
                        $isCancelledByMe = auth()->check() && $help->wasCancelledByMitra(auth()->id());
                    @endphp
                    <div class="bg-white rounded-xl p-3.5 shadow-sm hover:shadow-md transition-all border border-gray-100 {{ $isCancelledByMe ? 'border-l-4 border-l-amber-500 bg-amber-50/20' : ($isUrgent ? 'border-l-4 border-l-red-500' : '') }}">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                @if($help->photo)
                                    <img src="{{ asset('storage/' . $help->photo) }}" alt="{{ $help->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-lg">
                                        {{ $help->category?->icon ?? '📦' }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                                        <h3 class="font-semibold text-sm text-gray-900 line-clamp-1">{{ $help->title }}</h3>
                                        @if($isUrgent)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider flex-shrink-0">⚡ Urgent</span>
                                            @if($help->auto_cancel_at && $help->status === 'menunggu_mitra')
                                                @php
                                                    $secsLeft = (int) now()->diffInSeconds($help->auto_cancel_at, false);
                                                    $minsLeft = (int) ceil($secsLeft / 60);
                                                @endphp
                                                @if($secsLeft > 0)
                                                    <span x-data="{
                                                        target: new Date('{{ $help->auto_cancel_at->toIso8601String() }}').getTime(),
                                                        label: '{{ $minsLeft > 1 ? 'Sisa ' . $minsLeft . ' mnt' : 'Sisa < 1 mnt' }}',
                                                        init() {
                                                            this.update();
                                                            setInterval(() => this.update(), 5000);
                                                        },
                                                        update() {
                                                            const diff = this.target - Date.now();
                                                            if (diff <= 0) {
                                                                this.label = 'Waktu habis';
                                                                if (window.Livewire) { $wire.$refresh(); }
                                                                return;
                                                            }
                                                            const m = Math.ceil(diff / 60000);
                                                            this.label = m > 1 ? `Sisa ${m} mnt` : 'Sisa < 1 mnt';
                                                        }
                                                    }" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200/80 flex-shrink-0">
                                                        ⏳ <span class="ml-0.5" x-text="label">{{ $minsLeft > 1 ? 'Sisa ' . $minsLeft . ' mnt' : 'Sisa < 1 mnt' }}</span>
                                                    </span>
                                                @endif
                                            @endif
                                        @endif
                                        @if($isCancelledByMe)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200/80 flex-shrink-0">⚠️ Pernah Dibatalkan</span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold whitespace-nowrap" style="color: #0098e7;">Rp {{ number_format($help->amount, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex items-center gap-2 mb-2">
                                    @if($help->status === 'menunggu_mitra')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(255, 159, 67, 0.08); color:#ff8a00; border:1px solid rgba(255,159,67,0.12);">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(107, 114, 128, 0.08); color:#6b7280;">
                                            {{ ucfirst(str_replace('_',' ', $help->status)) }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ optional($help->created_at)->diffForHumans() }}</span>
                                </div>

                                <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ Str::limit($help->description, 100) }}</p>

                                @php
                                    $schedDate = $help->scheduled_at ?? $help->created_at;
                                @endphp
                                @if($schedDate)
                                    <div class="text-xs {{ $isUrgent ? 'text-red-600 font-semibold' : 'text-gray-500' }} mb-2">
                                        {{ $isUrgent ? '⚡ ' : '📅 ' }}{{ \Carbon\Carbon::parse($schedDate)->translatedFormat('d M Y, H:i') }}
                                    </div>
                                @endif

                                <!-- Lokasi & Radius Jarak -->
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-3 flex-wrap">
                                    <span class="inline-flex items-center gap-1 font-medium text-gray-700 whitespace-nowrap">
                                        <span>📍</span>
                                        <span>{{ $help->city->name ?? '-' }}</span>
                                    </span>
                                    @if(isset($help->distance))
                                        <span class="text-gray-300">•</span>
                                        <span class="inline-flex items-center font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full whitespace-nowrap text-[11px]">
                                            {{ number_format($help->distance, 1) }} km dari Anda
                                        </span>
                                    @endif
                                </div>

                                @if($isCancelledByMe)
                                    <div class="mb-3 p-2 bg-amber-50 border border-amber-200/80 rounded-lg text-amber-800 text-[11px] flex items-center gap-1.5 font-medium leading-tight">
                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Anda tidak bisa mengambil bantuan ini karena sudah pernah dibatalkan</span>
                                    </div>
                                @endif
                                
                                <!-- Tombol Aksi -->
                                <div class="flex items-center justify-end gap-2 pt-2.5 border-t border-gray-100">
                                    @php 
                                        $schedLabel = $schedDate ? \Carbon\Carbon::parse($schedDate)->translatedFormat('d M Y, H:i') : '-' ;
                                        $catLabel = $help->category ? $help->category->name : 'Lainnya';
                                        $cityLabel = $help->city->name ?? '-';
                                        $distLabel = isset($help->distance) ? number_format($help->distance, 1) . ' km' : '';
                                    @endphp
                                    @if(is_null($help->mitra_id))
                                        <button type="button" onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}', {{ $isUrgent ? 'true' : 'false' }}, '{{ addslashes($cityLabel) }}', '{{ addslashes($distLabel) }}', {{ $isCancelledByMe ? 'true' : 'false' }})" class="px-3.5 py-1.5 bg-gray-100 text-gray-700 font-medium rounded-lg text-xs hover:bg-gray-200 transition">Lihat</button>
                                        @if($isCancelledByMe)
                                            <button type="button" disabled class="px-3.5 py-1.5 bg-gray-100 text-gray-400 font-medium rounded-lg text-xs cursor-not-allowed">Pernah Dibatalkan</button>
                                        @else
                                            <button type="button" onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}', {{ $isUrgent ? 'true' : 'false' }}, '{{ addslashes($cityLabel) }}', '{{ addslashes($distLabel) }}', false)" class="px-3.5 py-1.5 bg-blue-500 text-white font-medium rounded-lg text-xs hover:bg-blue-600 transition shadow-xs">Ambil</button>
                                        @endif
                                    @else
                                        <span class="px-3 py-1.5 bg-gray-50 text-gray-400 rounded-md text-xs">Diambil</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-sm font-semibold text-gray-700">{{ $search ? 'Tidak ada bantuan ditemukan' : 'Belum ada bantuan dalam radius ' . (int) ($maxRadius ?? 10) . ' km' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $search ? 'Coba cari dengan kata kunci lain' : 'Cek kembali nanti untuk bantuan baru di sekitar Anda' }}
                        </p>
                        @if(!empty($search))
                            <button type="button" wire:click="$set('search', '')" class="inline-flex items-center gap-1.5 mt-3.5 px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition shadow-2xs">
                                Reset Pencarian
                            </button>
                        @endif
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($helps->hasPages())
                    <div class="mt-6">
                        {{ $helps->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Modal Preview Bantuan (Compact Bottom Sheet Style) -->
    <div id="helpPreviewModal" class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 hidden" onclick="closePreviewModal()">
        <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl max-h-[85vh] overflow-y-auto flex flex-col relative" onclick="event.stopPropagation()">
            <!-- Drag Handle Bar -->
            <div class="pt-2.5 pb-1 flex justify-center">
                <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
            </div>

            <!-- Modal Header -->
            <div class="px-4 py-2 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Preview Bantuan</h3>
                <button type="button" onclick="closePreviewModal()" class="p-1 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4 space-y-2.5">
                <div id="previewUrgentBadge" class="hidden p-2 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-bold text-center">
                    BANTUAN MENDESAK (URGENT)
                </div>

                <!-- Judul & Nominal -->
                <div class="bg-gray-50/80 border border-gray-100 rounded-xl p-3 flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block">Judul Bantuan</span>
                        <p id="previewTitle" class="text-sm font-bold text-gray-900 line-clamp-2 mt-0.5 leading-snug">-</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block">Upah Anda</span>
                        <div id="previewAmount" class="inline-flex items-center bg-emerald-50 text-emerald-700 border border-emerald-200/80 px-2 py-0.5 rounded-lg font-extrabold text-xs mt-0.5 whitespace-nowrap">
                            Rp 0
                        </div>
                    </div>
                </div>

                <!-- Info Details List -->
                <div class="bg-white border border-gray-100 rounded-xl p-3 space-y-2 text-xs shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Kategori</span>
                        <span id="previewCategory" class="font-semibold text-gray-800 text-right">-</span>
                    </div>
                    <div class="flex items-center justify-between pt-1.5 border-t border-gray-100">
                        <span class="text-gray-500 font-medium">Jadwal</span>
                        <span id="previewScheduled" class="font-semibold text-gray-800 text-right">-</span>
                    </div>
                    <div class="flex items-center justify-between pt-1.5 border-t border-gray-100">
                        <span class="text-gray-500 font-medium">Lokasi & Jarak</span>
                        <div class="flex items-center gap-1.5 font-semibold text-gray-800 justify-end">
                            <span id="previewCity">-</span>
                            <span id="previewDistanceDot" class="text-gray-300 hidden">•</span>
                            <span id="previewDistance" class="hidden text-blue-600 bg-blue-50 border border-blue-100 px-1.5 py-0.5 rounded text-[10px] whitespace-nowrap"></span>
                        </div>
                    </div>
                </div>

                <!-- Notice -->
                <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-2.5 text-blue-800">
                    <p class="text-[11px] leading-relaxed text-blue-700">
                        <strong class="font-semibold text-blue-900">Info Terbatas:</strong> Deskripsi lengkap, peta, dan kontak customer akan terbuka setelah bantuan diambil.
                    </p>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-4 pb-4 pt-1 bg-white border-t border-gray-100">
                @if(auth()->check() && !auth()->user()->isProfileComplete())
                    <div class="mb-2.5 p-2.5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Biodata Belum Lengkap</span>
                        </p>
                        <p class="text-[11px] text-red-700 leading-relaxed">
                            Harap lengkapi biodata profil Anda terlebih dahulu sebelum mengambil bantuan.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->verified)
                    <div class="mb-2.5 p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>KTP Belum Terverifikasi</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Akun dan KTP sedang dalam verifikasi Admin. Belum dapat mengambil bantuan.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->canTakeMoreOrders())
                    <div class="mb-2.5 p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Batas 2 Bantuan Tercapai</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Akun belum verifikasi email dan mencapai batas maksimal 2 bantuan.
                        </p>
                    </div>
                @endif
                <div id="previewCancelledByMeAlert" class="hidden mb-2.5 p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                    <p class="font-bold flex items-center gap-1.5 mb-0.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Bantuan Pernah Dibatalkan</span>
                    </p>
                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        Anda tidak bisa mengambil bantuan ini karena sudah pernah dibatalkan.
                    </p>
                </div>
                <div class="flex gap-2.5">
                    <button type="button" onclick="closePreviewModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 px-4 rounded-xl font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="previewCancelledTakeBtn" disabled class="hidden flex-1 bg-gray-200 text-gray-400 py-2.5 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                        Tidak Dapat Diambil
                    </button>
                    @if(auth()->check() && !auth()->user()->isProfileComplete())
                        <a href="{{ route('mitra.profile.edit') }}" class="flex-1 bg-red-600 text-white py-2.5 px-4 rounded-xl font-bold hover:bg-red-700 transition text-center text-xs flex items-center justify-center">
                            Lengkapi Biodata
                        </a>
                    @elseif(auth()->check() && !auth()->user()->verified)
                        <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 py-2.5 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                            Menunggu Verifikasi KTP
                        </button>
                    @elseif(auth()->check() && !auth()->user()->canTakeMoreOrders())
                        <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 py-2.5 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                            Batas Order Tercapai
                        </button>
                    @else
                        <button type="button" id="previewTakeBtn" onclick="takeHelpFromModal()" class="flex-1 bg-primary-500 hover:bg-primary-600 active:bg-primary-700 text-white py-2.5 px-4 rounded-xl font-bold text-xs transition shadow-md shadow-primary-500/20 cursor-pointer text-center">
                            Ambil Bantuan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentHelpId = null;

        function showHelpPreview(helpId, title, amount, scheduled, category, isUrgent = false, city = '', distance = '', isCancelledByMe = false) {
            currentHelpId = helpId;
            const urgentBadge = document.getElementById('previewUrgentBadge');
            if (urgentBadge) {
                if (isUrgent) {
                    urgentBadge.classList.remove('hidden');
                } else {
                    urgentBadge.classList.add('hidden');
                }
            }
            
            const cancelledAlert = document.getElementById('previewCancelledByMeAlert');
            const cancelledBtn = document.getElementById('previewCancelledTakeBtn');
            const takeBtn = document.getElementById('previewTakeBtn');
            if (isCancelledByMe) {
                if (cancelledAlert) cancelledAlert.classList.remove('hidden');
                if (cancelledBtn) cancelledBtn.classList.remove('hidden');
                if (takeBtn) takeBtn.classList.add('hidden');
            } else {
                if (cancelledAlert) cancelledAlert.classList.add('hidden');
                if (cancelledBtn) cancelledBtn.classList.add('hidden');
                if (takeBtn) takeBtn.classList.remove('hidden');
            }
            document.getElementById('previewTitle').textContent = title;
            const cleanCategory = (category || 'Lainnya').replace(/^[\p{Extended_Pictographic}\uFE0F\u200D\s]+/u, '').trim();
            document.getElementById('previewCategory').textContent = cleanCategory || 'Lainnya';
            document.getElementById('previewAmount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            const schedEl = document.getElementById('previewScheduled');
            if (schedEl) {
                if (scheduled && scheduled.length) {
                    schedEl.textContent = scheduled;
                } else {
                    // fallback: fetch latest help data
                    fetch('/helps/' + helpId + '/json', { credentials: 'same-origin' })
                        .then(r => r.ok ? r.json() : Promise.reject(r))
                        .then(data => {
                            schedEl.textContent = data.scheduled_at ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(data.scheduled_at)) : '-';
                        }).catch(() => { schedEl.textContent = '-'; });
                }
            }

            const cityEl = document.getElementById('previewCity');
            const distEl = document.getElementById('previewDistance');
            const distDot = document.getElementById('previewDistanceDot');
            if (cityEl) {
                cityEl.textContent = city ? city : '-';
            }
            if (distEl) {
                if (distance && distance.trim() !== '') {
                    distEl.textContent = distance + ' dari Anda';
                    distEl.classList.remove('hidden');
                    if (distDot) distDot.classList.remove('hidden');
                } else {
                    distEl.classList.add('hidden');
                    if (distDot) distDot.classList.add('hidden');
                }
            }

            document.getElementById('helpPreviewModal').classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('helpPreviewModal').classList.add('hidden');
            currentHelpId = null;
        }

        function takeHelpFromModal() {
            if (!currentHelpId) return;

            const btn = document.querySelector('[onclick="takeHelpFromModal()"]');
            const originalText = btn ? btn.textContent : 'Ambil Bantuan';

            if (btn) {
                btn.textContent = 'Memproses...';
                btn.disabled = true;
            }

            let taken = false;
            const completeTake = (lat = null, lng = null) => {
                if (taken) return;
                taken = true;
                clearTimeout(fallbackTimer);

                if (lat && lng) {
                    console.log('📍 Mengambil bantuan dengan lokasi GPS:', { lat, lng });
                    @this.takeHelp(currentHelpId, lat, lng);
                } else {
                    console.log('📍 Mengambil bantuan (koordinat default dari profil mitra)');
                    @this.takeHelp(currentHelpId);
                }

                closePreviewModal();

                if (btn) {
                    btn.textContent = originalText;
                    btn.disabled = false;
                }
            };

            // Batas maksimal tunggu deteksi GPS adalah 1.8 detik agar tidak pernah stuck/macet
            const fallbackTimer = setTimeout(() => {
                console.log('⏱️ Waktu deteksi GPS selesai, langsung mengambil bantuan...');
                completeTake();
            }, 1800);

            // Coba ambil lokasi secara cepat jika browser mendukung
            if (navigator.geolocation) {
                try {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            completeTake(position.coords.latitude, position.coords.longitude);
                        },
                        (error) => {
                            console.warn('⚠️ Lokasi GPS tidak dapat diakses, mengambil bantuan langsung:', error.message);
                            completeTake();
                        },
                        {
                            enableHighAccuracy: false,
                            timeout: 1500,
                            maximumAge: 300000 // gunakan cache lokasi 5 menit terakhir jika ada
                        }
                    );
                } catch (e) {
                    completeTake();
                }
            } else {
                completeTake();
            }
        }

        // Close modal when clicking outside
        document.getElementById('helpPreviewModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePreviewModal();
            }
        });

        // Listen for help-taken event from Livewire
        window.addEventListener('help-taken', function(event) {
            // event.detail may contain the helpId depending on how Livewire dispatched it.
            var helpId = event?.detail?.helpId ?? event?.detail ?? null;

            if (!helpId) {
                // fallback: reload the page
                window.location.reload();
                return;
            }

            // Template URL with placeholder id, generated by Laravel route helper
            var detailUrlTemplate = @json(route('mitra.helps.detail', ['id' => 'REPLACE_ID']));

            // Replace placeholder with actual id and redirect to detail page
            window.location.href = detailUrlTemplate.replace('REPLACE_ID', helpId);
        });
    </script>
</div>