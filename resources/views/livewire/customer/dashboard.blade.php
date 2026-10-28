<div class="min-h-screen bg-gray-100" wire:poll.30s.visible>
    <style>
        :root{
            --brand-500: #0ea5a4;
            --brand-600: #08979a;
            --muted-600: #6b7280;
        }
        .card-shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .card-shadow-hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .focus-ring:focus{ outline: none; box-shadow: 0 0 0 3px rgba(14,165,164,0.2); }
        
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
            pointer-events: none;
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
            pointer-events: none;
        }

        .search-dropdown {
            position: fixed;
            top: auto;
            left: 50%;
            transform: translateX(-50%);
            width: min(384px, calc(100vw - 2rem));
            z-index: 9999;
            margin-top: 0;
        }
    </style>

    <!-- Top Header Bar - Blue gradient matching mockup -->
    <div class="px-5 pt-6 pb-6 sticky top-0 z-30 header-pattern" style="background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%);">
        <!-- Decorative circles -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12 pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/40 flex items-center justify-center overflow-hidden shadow-md">
                    <x-user-avatar :user="auth()->user()" class="h-full w-full text-white font-bold" />
                </div>
                <div>
                    <p class="text-[11px] text-blue-100 font-medium">Selamat datang 👋</p>
                    <h1 class="text-base font-extrabold text-white leading-tight">Halo, {{ Str::words(trim(preg_replace('/\s*\((?:Mitra|Customer)[^)]*\)|\s*(?:Mitra|Customer)\b/i', '', optional(auth()->user())->name ?? 'Customer')), 2, '') }}!</h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @include('components.notification-icon', ['route' => route('customer.notifications.index'), 'class' => 'p-2.5 rounded-full bg-white/20 hover:bg-white/30 text-white transition backdrop-blur-sm'])
            </div>
        </div>

        <!-- Search Bar with live category search -->
        <div class="relative z-50 mt-3.5" 
             x-data="{ 
                focused: false,
                dropdownTop: 0,
                updatePos() {
                    const el = this.$el.querySelector('input');
                    if (el) {
                        const rect = el.getBoundingClientRect();
                        this.dropdownTop = rect.bottom + 8;
                    }
                }
             }"
             @focusin="focused = true; updatePos()"
             @focusout="setTimeout(() => { focused = false }, 200)">
            <div class="flex items-center gap-2.5 w-full bg-white/95 hover:bg-white transition-colors px-4 py-2.5 rounded-full shadow-md">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari kategori jasa..."
                    class="flex-1 bg-transparent text-xs text-gray-700 placeholder-gray-400 outline-none border-none focus:ring-0"
                    autocomplete="off"
                />
                @if($search)
                    <button wire:click="clearSearch" class="text-gray-400 hover:text-gray-600 transition" title="Hapus pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Dropdown hasil pencarian - pakai fixed positioning agar keluar dari sticky header --}}
            @if($showSearchResults)
                <div class="search-dropdown bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
                     :style="'top: ' + dropdownTop + 'px'"
                     x-show="focused || {{ $showSearchResults ? 'true' : 'false' }}"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    @if($searchResults->isEmpty())
                        <div class="px-4 py-6 text-center">
                            <div class="text-3xl mb-2">🔍</div>
                            <p class="text-sm font-bold text-gray-700">Kategori tidak ditemukan</p>
                            <p class="text-xs text-gray-400 mt-1">Coba kata kunci lain seperti "rumah", "kesehatan"</p>
                        </div>
                    @else
                        <div class="max-h-72 overflow-y-auto">
                            <p class="px-4 pt-3 pb-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kategori Jasa</p>
                            @foreach($searchResults as $cat)
                                @php
                                    $catStyles = [
                                        1 => 'bg-blue-50 text-blue-600',
                                        2 => 'bg-rose-50 text-rose-600',
                                        3 => 'bg-emerald-50 text-emerald-600',
                                        4 => 'bg-amber-50 text-amber-600',
                                        5 => 'bg-indigo-50 text-indigo-600',
                                        6 => 'bg-purple-50 text-purple-600',
                                        7 => 'bg-orange-50 text-orange-600',
                                        8 => 'bg-sky-50 text-sky-600',
                                    ];
                                    $catStyle = $catStyles[$cat->id] ?? 'bg-gray-50 text-gray-600';
                                @endphp
                                <a href="{{ route('customer.helps.create', ['category_id' => $cat->id]) }}" wire:navigate
                                   class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50/60 transition group border-b border-gray-50 last:border-0"
                                   wire:click="clearSearch">
                                    <div class="w-10 h-10 rounded-xl {{ $catStyle }} flex items-center justify-center text-lg shrink-0">
                                        {{ $cat->icon ?? '📦' }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800 group-hover:text-[#0066FF] transition">{{ $cat->name }}</p>
                                        @if($cat->description)
                                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $cat->description }}</p>
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-[#0066FF] shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                        <div class="border-t border-gray-100 px-4 py-3 bg-gray-50/50">
                            <a href="{{ route('customer.helps.create') }}" wire:navigate wire:click="clearSearch"
                               class="flex items-center gap-2 text-xs font-bold text-[#0066FF] hover:underline">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Buat pesanan baru tanpa kategori
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Wave Separator (SVG) bergelombang -->
        <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 20px;">
            <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
        </svg>
    </div>

    <!-- Main Content Body -->
    <div class="px-5 pt-4 pb-6">
        <!-- Hero Banner matching Gambar 2 -->
        <div class="relative overflow-hidden rounded-2xl p-5 mb-5 shadow-lg text-white"
             style="background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%);">
            <!-- Decorative circle -->
            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 flex items-center justify-between gap-3">
                <div class="flex-1 max-w-[65%]">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-bold text-white mb-2">
                        sayabantu.com 🤝
                    </span>
                    <h2 class="text-sm font-extrabold leading-snug">
                        Butuh bantuan untuk pekerjaan kecil hingga besar?
                    </h2>
                    <p class="text-[11px] text-blue-100 mt-1 line-clamp-2">
                        Pesan jasa serabutan cepat, mudah, dan terpercaya kapan saja.
                    </p>
                    <a href="{{ route('customer.helps.create') }}" wire:navigate
                       class="mt-3.5 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white text-[#0066FF] hover:bg-blue-50 font-bold text-xs rounded-full shadow-md active:scale-95 transition">
                        <span>Pesan Sekarang</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <div class="w-24 h-24 shrink-0 flex items-center justify-center">
                    <img src="{{ asset('images/sayabantu-mascot.jpg') }}" alt="Mascot SayaBantu" class="w-full h-full object-contain rounded-xl drop-shadow-md">
                </div>
            </div>
        </div>

        <!-- Saldo Customer Quick Card matching Gambar 2 -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                        <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Saldo Saya</span>
                    </div>

                    <div class="flex items-center gap-2 mt-1" x-data="{ show: false }">
                        <h2 class="text-xl font-black text-gray-900 tracking-tight" x-show="show">
                            Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                        </h2>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight" x-show="!show">Rp ••••••</h2>
                        <button @click="show = !show" class="p-1 hover:bg-gray-100 rounded-lg transition text-gray-400 hover:text-gray-700" title="Tampilkan / Sembunyikan Saldo">
                            <svg class="w-4 h-4" x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <svg class="w-4 h-4 text-[#0066FF]" x-show="show" x-cloak style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('customer.topup') }}" wire:navigate
                       class="px-3.5 py-1.5 rounded-full text-xs font-bold text-white bg-[#0066FF] hover:bg-blue-700 transition shadow-xs flex items-center gap-1 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Top Up</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kategori Jasa Grid matching Gambar 2 -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-extrabold text-gray-900">Kategori Jasa</h3>
                <a href="{{ route('customer.helps.create') }}" wire:navigate class="text-xs font-bold text-[#0066FF] hover:underline">
                    Buat Jasa +
                </a>
            </div>

            <div class="grid grid-cols-3 min-[380px]:grid-cols-4 gap-2 min-[380px]:gap-2.5">
                @php
                    $categoryStyles = [
                        1 => ['bg' => 'bg-blue-50 text-blue-600 border-blue-100', 'label' => 'Rumah Tangga'],
                        2 => ['bg' => 'bg-rose-50 text-rose-600 border-rose-100', 'label' => 'Bencana'],
                        3 => ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'label' => 'Kesehatan'],
                        4 => ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'label' => 'Pendidikan'],
                        5 => ['bg' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'label' => 'Transportasi'],
                        6 => ['bg' => 'bg-purple-50 text-purple-600 border-purple-100', 'label' => 'Sosial'],
                        7 => ['bg' => 'bg-orange-50 text-orange-600 border-orange-100', 'label' => 'Pangan'],
                        8 => ['bg' => 'bg-sky-50 text-sky-600 border-sky-100', 'label' => 'Lainnya'],
                    ];
                @endphp

                @foreach($categories as $cat)
                    @php
                        $style = $categoryStyles[$cat->id] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-100', 'label' => $cat->name];
                    @endphp
                    <a href="{{ route('customer.helps.create', ['category_id' => $cat->id]) }}" wire:navigate
                       class="flex flex-col items-center justify-center p-2.5 rounded-2xl bg-white border border-gray-100 hover:border-[#0066FF]/30 hover:shadow-xs transition group cursor-pointer text-center">
                        <div class="w-12 h-12 rounded-2xl {{ $style['bg'] }} border flex items-center justify-center text-xl shadow-2xs group-hover:scale-110 transition-transform">
                            {{ $cat->icon ?? '📦' }}
                        </div>
                        <span class="text-[11px] font-semibold text-gray-800 mt-1.5 w-full text-center leading-tight line-clamp-2 group-hover:text-[#0066FF]">
                            {{ $cat->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Card Notifikasi Bertahap (If-Else): Biodata -> KTP -> Email (Hilang 1 per 1 jika sudah terpenuhi) -->
        @php
            $dashUser = auth()->user();
            $needsBiodata = $dashUser && !$dashUser->isProfileComplete();
            $needsKtp = $dashUser && !(bool)$dashUser->verified;
            $hasKtpUploaded = $dashUser && (!empty($dashUser->ktp_photo) && !empty($dashUser->selfie_photo));
            $needsEmail = $dashUser && !$dashUser->hasVerifiedEmail();
        @endphp

        @if($needsBiodata)
            {{-- 1. LENGKAPI BIODATA DULU --}}
            <div class="rounded-2xl p-3.5 text-xs mb-5 flex items-center justify-between gap-3 bg-white"
                 style="border: 1.5px solid #0066FF; box-shadow: 0 4px 14px rgba(0, 102, 255, 0.15);">
                <div class="flex items-center gap-3 min-w-0">
                    <div style="background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0, 102, 255, 0.35);">
                        <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5 flex-wrap">
                            <span>Lengkapi Biodata</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">Wajib</span>
                        </div>
                        <p class="text-[11px] text-gray-600 truncate mt-0.5">Lengkapi profil Anda untuk membuat bantuan</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" wire:navigate 
                   class="flex-shrink-0 font-bold px-3.5 py-1.5 rounded-xl transition text-[11px] text-white hover:opacity-95 active:scale-95 cursor-pointer shadow-xs"
                   style="background: linear-gradient(to right, #0066FF, #0052cc); box-shadow: 0 2px 6px rgba(0, 102, 255, 0.35);">
                    Lengkapi
                </a>
            </div>

        @elseif($needsKtp)
            {{-- 2. JIKA BIODATA SUDAH LENGKAP, BARU NUNGGU / VERIF KTP --}}
            <div class="rounded-2xl p-3.5 text-xs mb-5 flex items-center justify-between gap-3 bg-white"
                 style="border: 1.5px solid {{ $hasKtpUploaded ? '#3b82f6' : '#f59e0b' }}; box-shadow: 0 4px 14px {{ $hasKtpUploaded ? 'rgba(59, 130, 246, 0.15)' : 'rgba(245, 158, 11, 0.15)' }};">
                <div class="flex items-center gap-3 min-w-0">
                    <div style="background: linear-gradient(135deg, {{ $hasKtpUploaded ? '#3b82f6 0%, #1d4ed8 100%' : '#0ea5e9 0%, #0284c7 100%' }}); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(59, 130, 246, 0.35);">
                        <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5 flex-wrap">
                            <span>{{ $hasKtpUploaded ? 'Verifikasi KTP' : 'Upload Dokumen KTP' }}</span>
                            @if($hasKtpUploaded)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Menunggu Konfirmasi</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">Wajib</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-600 truncate mt-0.5">
                            {{ $hasKtpUploaded ? 'Dokumen KTP Anda sedang ditinjau oleh Admin' : 'Upload KTP & Selfie untuk verifikasi akun' }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('profile.settings.verification') }}" wire:navigate 
                   class="flex-shrink-0 font-bold px-3.5 py-1.5 rounded-xl transition text-[11px] hover:opacity-95 active:scale-95 cursor-pointer shadow-xs {{ $hasKtpUploaded ? 'text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100' : 'text-white' }}"
                   @if(!$hasKtpUploaded) style="background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%); box-shadow: 0 2px 6px rgba(0, 102, 255, 0.35);" @endif>
                    {{ $hasKtpUploaded ? 'Cek Status' : 'Upload' }}
                </a>
            </div>

        @elseif($needsEmail)
            {{-- 3. JIKA KTP SUDAH DIVERIFIKASI, BARU EMAIL --}}
            <div class="rounded-2xl p-3.5 text-xs mb-5 flex items-center justify-between gap-3 bg-white"
                 style="border: 1.5px solid #f59e0b; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.15);">
                <div class="flex items-center gap-3 min-w-0">
                    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35);">
                        <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5 flex-wrap">
                            <span>Verifikasi Email</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">Maks. 2 Bantuan</span>
                        </div>
                        <p class="text-[11px] text-gray-600 truncate mt-0.5">Verifikasi untuk nikmati bantuan tanpa batas</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('verification.send') }}" class="flex-shrink-0" onsubmit="event.preventDefault(); window.sendEmailVerification(this.querySelector('button'), '{{ route('verification.send') }}');">
                    @csrf
                    <button type="submit" class="font-bold px-3.5 py-1.5 rounded-xl transition text-[11px] text-white hover:opacity-95 active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); box-shadow: 0 2px 6px rgba(234, 88, 12, 0.35);">
                        Verifikasi
                    </button>
                </form>
            </div>
        @endif

        <!-- Banner Section -->
        @php
            $customerBanners = json_decode((string) \App\Models\AppSetting::get('banner_customer', '[]'), true) ?: [];
        @endphp

        @if(!empty($customerBanners) && count($customerBanners))
            <div class="mb-5">
                <div x-data="bannerSlider({{ count($customerBanners) }})"
                    @touchstart="touchStart($event)"
                    @touchmove="touchMove($event)"
                    @touchend="touchEnd($event)"
                    @mousedown="mouseDown($event)"
                    @mousemove="mouseMove($event)"
                    @mouseup="mouseUp($event)"
                    @mouseleave="mouseLeave($event)"
                    @mouseenter="stopAutoPlay()"
                    class="relative rounded-2xl overflow-hidden shadow-md select-none group touch-pan-y {{ count($customerBanners) > 1 ? 'cursor-grab active:cursor-grabbing' : '' }} bg-gray-100">

                    <!-- Slides Track -->
                    <div class="relative h-36 sm:h-40 overflow-hidden">
                        <div class="flex h-full will-change-transform"
                            :style="'transform: translateX(calc(-' + (current * 100) + '% + ' + dragOffset + 'px)); transition: ' + (isDragging ? 'none' : 'transform 500ms cubic-bezier(0.25, 1, 0.5, 1)')">
                            @foreach($customerBanners as $b)
                                <div class="flex-shrink-0 w-full h-full select-none">
                                    <img src="{{ asset('storage/' . $b) }}"
                                        alt="Banner Customer"
                                        draggable="false"
                                        class="w-full h-full object-cover pointer-events-none select-none" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if(count($customerBanners) > 1)
                        <!-- Tombol Panah Kiri (Prev) -->
                        <button type="button"
                            @click.stop="prev()"
                            @mousedown.stop
                            @touchstart.stop
                            class="absolute left-2.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-black/40 hover:bg-black/70 active:scale-90 text-white flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-200 cursor-pointer select-none"
                            title="Banner Sebelumnya"
                            aria-label="Banner Sebelumnya">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <!-- Tombol Panah Kanan (Next) -->
                        <button type="button"
                            @click.stop="next()"
                            @mousedown.stop
                            @touchstart.stop
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-black/40 hover:bg-black/70 active:scale-90 text-white flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-200 cursor-pointer select-none"
                            title="Banner Selanjutnya"
                            aria-label="Banner Selanjutnya">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Indikator Dots -->
                        <div class="absolute bottom-2.5 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-20 select-none">
                            @foreach($customerBanners as $i => $b)
                                <button type="button"
                                    @click.stop="goTo({{ $i }})"
                                    @mousedown.stop
                                    @touchstart.stop
                                    :class="current === {{ $i }} ? 'w-5 bg-white shadow-md' : 'w-2 bg-white/60 hover:bg-white/90'"
                                    class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                    title="Slide {{ $i + 1 }}"
                                    aria-label="Slide {{ $i + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Pesanan Saya Section matching Gambar 2 -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-extrabold text-gray-900">Pesanan Saya</h2>
                <a href="{{ route('customer.helps.index') }}" wire:navigate class="text-xs font-bold text-[#0066FF] hover:underline">Lihat Semua →</a>
            </div>

            <div class="space-y-3">
                @if($activeTab !== 'history')
                    @forelse($availableHelps as $help)
                        <a href="{{ route('customer.helps.detail', $help->id) }}" wire:navigate
                            class="block w-full text-left bg-white rounded-xl p-3.5 shadow-sm hover:shadow-md transition-all">
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
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <h3 class="font-semibold text-sm text-gray-900 line-clamp-1">{{ $help->title }}</h3>
                                            @if($help->isUrgent())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider flex-shrink-0">⚡ Urgent</span>
                                                @if($help->auto_cancel_at && in_array($help->status, ['menunggu_pembayaran', 'menunggu_mitra', 'mencari_mitra']))
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
                                        </div>
                                        <span class="text-xs font-bold whitespace-nowrap" style="color: #0066FF;">Rp {{ number_format($help->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="mb-1.5 flex items-center gap-1.5 flex-wrap">
                                        @if(in_array($help->status, ['menunggu_pembayaran', 'menunggu_mitra', 'mencari_mitra']))
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Mitra</span>
                                        @elseif($help->isMemperolehMitra())
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Mitra Ditemukan</span>
                                        @elseif($help->status === 'partner_on_the_way')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">Mitra Menuju Lokasi</span>
                                        @elseif(in_array($help->status, ['partner_arrived', 'in_progress', 'sedang_diproses']))
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Sedang Dikerjakan</span>
                                        @elseif($help->status === 'waiting_customer_confirmation')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">Menunggu Konfirmasi</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-50 text-gray-700 border border-gray-200">{{ ucfirst(str_replace('_', ' ', $help->status)) }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600 line-clamp-1 mb-1.5">{{ $help->description }}</p>
                                    @php
                                        $displayDate = $help->scheduled_at ?? $help->created_at;
                                    @endphp
                                    @if($displayDate)
                                        <div class="text-xs text-gray-500 mb-1">📅 {{ \Carbon\Carbon::parse($displayDate)->translatedFormat('d M Y, H:i') }}</div>
                                    @endif
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-500">📍 {{ $help->city->name ?? '-' }}</span>
                                        <span class="text-xs text-gray-400">{{ $help->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <x-empty-state 
                            icon="clipboard" 
                            title="Belum Ada Permintaan Aktif" 
                            description="Anda belum memiliki permintaan bantuan yang sedang berjalan atau menunggu mitra." 
                            actionText="Buat Bantuan Baru" 
                            :actionUrl="route('customer.helps.create')" 
                        />
                    @endforelse
                @endif

                @if(method_exists($availableHelps, 'links'))
                    <div class="mt-4">{{ $availableHelps->links() }}</div>
                @endif
            </div>
        </div>
    </div>

        <!-- Transaction History Component -->
        @if($activeTab === 'history')
            @livewire('customer.balance-transaction-history')
        @endif

        <!-- Help Detail Modal (bottom-sheet style like helps index) -->
        @if($selectedHelpData)
            <div class="fixed inset-0 z-50 flex items-end justify-center" style="background: rgba(0,0,0,0.5);" wire:click="closeHelp">
                <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl max-h-[85vh] overflow-y-auto hide-scrollbar" wire:click.stop>
                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white border-b px-5 py-4 rounded-t-3xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Detail Permintaan</h3>
                            <button type="button" wire:click="closeHelp" class="p-2 hover:bg-gray-100 rounded-full transition">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-5 pb-6">
                        @if(data_get($selectedHelpData, 'photo'))
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . data_get($selectedHelpData, 'photo')) }}" 
                                    alt="Foto bantuan" class="w-full h-48 object-cover rounded-2xl">
                            </div>
                        @endif

                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h2 class="text-xl font-bold text-gray-900">{{ data_get($selectedHelpData, 'title') }}</h2>
                            @if(data_get($selectedHelpData, 'is_urgent') || data_get($selectedHelpData, 'help_type') === 'urgent')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider flex-shrink-0">⚡ Urgent</span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between mb-4 pb-4 border-b">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nominal Bantuan</p>
                                <p class="text-2xl font-bold" style="color: var(--brand-600)">
                                    Rp {{ number_format(data_get($selectedHelpData, 'amount', 0), 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-1">Status</p>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                                    style="background: rgba(14,165,164,0.1); color: var(--brand-600)">
                                    {{ ucfirst(data_get($selectedHelpData, 'status')) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4">
                            @if(data_get($selectedHelpData, 'category_name'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Kategori</p>
                                    <p class="text-sm text-gray-700">
                                        {{ data_get($selectedHelpData, 'category_icon') }} {{ data_get($selectedHelpData, 'category_name') }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1">Deskripsi</p>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ data_get($selectedHelpData, 'description') }}</p>
                            </div>

                            @if(data_get($selectedHelpData, 'location'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Lokasi</p>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-700">{{ data_get($selectedHelpData, 'location') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if(data_get($selectedHelpData, 'full_address'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Alamat Lengkap</p>
                                    <p class="text-sm text-gray-700">{{ data_get($selectedHelpData, 'full_address') }}</p>
                                </div>
                                <div class="mt-3">
                                    <div id="detail-map" class="w-full h-48 rounded-xl overflow-hidden mb-4"></div>
                                </div>
                            @endif

                            @if(data_get($selectedHelpData, 'equipment_provided'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Peralatan Disediakan</p>
                                    <p class="text-sm text-gray-700">{{ data_get($selectedHelpData, 'equipment_provided') }}</p>
                                </div>
                            @endif

                            @if(data_get($selectedHelpData, 'city_name'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Kota</p>
                                    <p class="text-sm text-gray-700">{{ data_get($selectedHelpData, 'city_name') }}</p>
                                </div>
                            @endif

                            @if(data_get($selectedHelpData, 'scheduled_at'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Jadwal Permintaan</p>
                                    <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse(data_get($selectedHelpData, 'scheduled_at'))->translatedFormat('d M Y, H:i') }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-1">Dibuat</p>
                                <p class="text-sm text-gray-700">{{ data_get($selectedHelpData, 'created_at_human') }}</p>
                            </div>
                        </div>

                    </div>

                    <!-- Sticky footer (close button) -->
                    <div class="sticky bottom-0 bg-white border-t pt-4 px-5 pb-5">
                        <button type="button" wire:click="closeHelp" 
                            class="w-full px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    if (typeof window.bannerSlider !== 'function') {
        window.bannerSlider = function (totalSlides = 1, autoPlayMs = 4000) {
            return {
                current: 0,
                total: totalSlides,
                timer: null,
                startX: 0,
                startY: 0,
                dragOffset: 0,
                isDragging: false,
                isSwipingHorizontal: false,

                init() {
                    if (this.total > 1) {
                        this.startAutoPlay();
                    }
                },

                next() {
                    if (this.total <= 1) return;
                    this.current = (this.current + 1) % this.total;
                    this.resetAutoPlay();
                },

                prev() {
                    if (this.total <= 1) return;
                    this.current = (this.current - 1 + this.total) % this.total;
                    this.resetAutoPlay();
                },

                goTo(idx) {
                    this.current = idx;
                    this.resetAutoPlay();
                },

                startAutoPlay() {
                    if (this.total <= 1) return;
                    this.stopAutoPlay();
                    this.timer = setInterval(() => {
                        this.next();
                    }, autoPlayMs);
                },

                stopAutoPlay() {
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                },

                resetAutoPlay() {
                    this.stopAutoPlay();
                    if (this.total > 1) {
                        this.startAutoPlay();
                    }
                },

                touchStart(e) {
                    if (this.total <= 1) return;
                    this.stopAutoPlay();
                    const touch = e.touches ? e.touches[0] : e;
                    this.startX = touch.clientX;
                    this.startY = touch.clientY;
                    this.dragOffset = 0;
                    this.isDragging = true;
                    this.isSwipingHorizontal = false;
                },

                touchMove(e) {
                    if (!this.isDragging || this.total <= 1) return;
                    const touch = e.touches ? e.touches[0] : e;
                    const diffX = touch.clientX - this.startX;
                    const diffY = touch.clientY - this.startY;

                    if (!this.isSwipingHorizontal) {
                        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 8) {
                            this.isSwipingHorizontal = true;
                        }
                    }

                    if (this.isSwipingHorizontal) {
                        if (e.cancelable) e.preventDefault();
                        this.dragOffset = diffX;
                    }
                },

                touchEnd() {
                    if (!this.isDragging || this.total <= 1) return;
                    this.isDragging = false;
                    const threshold = 40;
                    if (this.dragOffset < -threshold) {
                        this.next();
                    } else if (this.dragOffset > threshold) {
                        this.prev();
                    }
                    this.dragOffset = 0;
                    this.isSwipingHorizontal = false;
                    this.startAutoPlay();
                },

                mouseDown(e) {
                    if (this.total <= 1 || e.button !== 0) return;
                    this.stopAutoPlay();
                    this.startX = e.clientX;
                    this.startY = e.clientY;
                    this.dragOffset = 0;
                    this.isDragging = true;
                },

                mouseMove(e) {
                    if (!this.isDragging || this.total <= 1) return;
                    const diffX = e.clientX - this.startX;
                    this.dragOffset = diffX;
                },

                mouseUp() {
                    if (!this.isDragging || this.total <= 1) return;
                    this.isDragging = false;
                    const threshold = 40;
                    if (this.dragOffset < -threshold) {
                        this.next();
                    } else if (this.dragOffset > threshold) {
                        this.prev();
                    }
                    this.dragOffset = 0;
                    this.startAutoPlay();
                },

                mouseLeave() {
                    if (this.isDragging) {
                        this.mouseUp();
                    } else {
                        this.startAutoPlay();
                    }
                }
            };
        };
    }
</script>
