<div class="min-h-screen bg-slate-50 pb-20">
    <style>
        .header-pattern {
            position: relative;
            overflow: hidden;
        }
        .header-pattern::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .header-pattern::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
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
        <!-- Header - SayaBantu Modern Gradient with Integrated Pill Tabs -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden header-pattern text-white" style="background: linear-gradient(135deg, #0066FF 0%, #0052cc 100%);">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-36 h-36 bg-white/10 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/10 rounded-full -ml-12 -mb-12 pointer-events-none"></div>

            <div class="relative z-10">
                <!-- Top Row: Title, Subtitle, Notification -->
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <h1 class="text-xl font-black text-white tracking-tight leading-tight">Daftar Order</h1>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola & ambil pesanan bantuan terdekat</p>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('components.notification-icon', ['route' => route('mitra.notifications.index'), 'class' => 'p-2 rounded-xl text-white hover:bg-white/20 transition relative'])
                    </div>
                </div>

                <!-- Modern Filter Tabs Inside Header -->
                <div class="relative w-full overflow-hidden"
                    x-data="{
                        centerTab(btn) {
                            if (!btn || !this.$refs.tabsContainer) return;
                            const container = this.$refs.tabsContainer;
                            const targetLeft = btn.offsetLeft - (container.clientWidth / 2) + (btn.clientWidth / 2);
                            container.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
                        },
                        init() {
                            this.$nextTick(() => {
                                const active = this.$refs.tabsContainer?.querySelector('.tab-active');
                                if (active) { this.centerTab(active); }
                            });
                        }
                    }">
                    <div x-ref="tabsContainer"
                        class="flex items-center gap-2 overflow-x-auto hide-scrollbar py-1 px-0.5 scroll-smooth"
                        style="overscroll-behavior-x: contain; -webkit-overflow-scrolling: touch;">

                        <!-- Tab: Semua -->
                        <button type="button" wire:click="setTab('semua')" @click="centerTab($el)"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all focus:outline-none flex items-center gap-1.5 {{ $tab === 'semua' ? 'tab-active bg-white text-[#0066FF] shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                            <span>Semua</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'semua' ? 'bg-blue-100 text-[#0066FF]' : 'bg-white/25 text-white' }}">{{ $counts['semua'] ?? 0 }}</span>
                        </button>

                        <!-- Tab: Tersedia -->
                        <button type="button" wire:click="setTab('tersedia')" @click="centerTab($el)"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all focus:outline-none flex items-center gap-1.5 {{ $tab === 'tersedia' ? 'tab-active bg-white text-[#0066FF] shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                            <span>Tersedia</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'tersedia' ? 'bg-blue-100 text-[#0066FF]' : 'bg-white/25 text-white' }}">{{ $counts['tersedia'] ?? 0 }}</span>
                        </button>

                        <!-- Tab: Dalam Proses -->
                        <button type="button" wire:click="setTab('proses')" @click="centerTab($el)"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all focus:outline-none flex items-center gap-1.5 {{ $tab === 'proses' ? 'tab-active bg-white text-[#0066FF] shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                            <span>Dalam Proses</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'proses' ? 'bg-blue-100 text-[#0066FF]' : 'bg-white/25 text-white' }}">{{ $counts['proses'] ?? 0 }}</span>
                        </button>

                        <!-- Tab: Selesai -->
                        <button type="button" wire:click="setTab('selesai')" @click="centerTab($el)"
                            class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-all focus:outline-none flex items-center gap-1.5 {{ $tab === 'selesai' ? 'tab-active bg-white text-[#0066FF] shadow-md' : 'bg-white/20 text-white hover:bg-white/30' }}">
                            <span>Selesai</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'selesai' ? 'bg-blue-100 text-[#0066FF]' : 'bg-white/25 text-white' }}">{{ $counts['selesai'] ?? 0 }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Wave Separator (SVG) - Blends seamlessly into page background -->
            <svg class="absolute -bottom-1 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px; bottom: -2px;">
                <path d="M0,28 C360,76 1080,-4 1440,36 L1440,84 L0,84 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <!-- Search Bar & Sort Row -->
        <div class="px-5 pt-3 pb-2">
            <div class="flex items-center gap-2">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari pesanan, lokasi, kategori..."
                        class="w-full pl-9 pr-8 py-2.5 text-xs bg-white border border-gray-200/90 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] shadow-2xs transition placeholder:text-gray-400 font-medium" />
                    @if(!empty($search))
                        <button type="button" wire:click="$set('search', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-100 transition" title="Hapus pencarian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <!-- Sort Selector -->
                <div class="shrink-0">
                    <select wire:model.live="sortBy" class="bg-white border border-gray-200/90 text-gray-700 text-xs font-semibold py-2.5 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] shadow-2xs cursor-pointer">
                        <option value="latest">Terbaru</option>
                        <option value="nearby">📍 Terdekat</option>
                        <option value="price_high">💰 Upah Tertinggi</option>
                        <option value="price_low">Upah Terendah</option>
                        <option value="oldest">Terlama</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Main Content List -->
        <div class="px-5 pt-2 pb-8">
            <div class="space-y-3.5">
                {{-- Flash Messages --}}
                @if (session()->has('error') || session()->has('message') || session()->has('success'))
                    <div>
                        @if (session()->has('error'))
                            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3 text-xs flex items-start gap-2.5 shadow-2xs">
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1 font-medium leading-relaxed">{{ session('error') }}</div>
                            </div>
                        @endif
                        @if (session()->has('success') || session()->has('message'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3 text-xs flex items-start gap-2.5 shadow-2xs">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <div class="flex-1 font-medium leading-relaxed">{{ session('success') ?? session('message') }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Email Verification Warning Banner --}}
                @auth
                    @if(!auth()->user()->hasVerifiedEmail())
                        <div class="rounded-2xl p-3.5 text-xs flex items-center justify-between gap-3 bg-white border border-amber-200/80 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.25);">
                                    <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5 flex-wrap">
                                        <span>Verifikasi Email Diperlukan</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">
                                            Wajib
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5">
                                        Verifikasi alamat email Anda terlebih dahulu untuk dapat mengambil pesanan bantuan.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('verification.notice') }}" wire:navigate
                               class="flex-shrink-0 font-bold px-3 py-1.5 rounded-xl transition text-[11px] text-white hover:opacity-95 active:scale-95 shadow-xs bg-amber-500 hover:bg-amber-600">
                                Verifikasi
                            </a>
                        </div>
                    @endif
                @endauth

                {{-- KTP Verification Warning Banner --}}
                @auth
                    @if(!auth()->user()->verified)
                        @php
                            $hasKtpUploaded = !empty(auth()->user()->ktp_photo);
                        @endphp
                        <div class="rounded-2xl p-3.5 text-xs flex items-center justify-between gap-3 bg-white border border-amber-200/80 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div style="background: linear-gradient(135deg, {{ $hasKtpUploaded ? '#0066FF 0%, #0052cc 100%' : '#f59e0b 0%, #d97706 100%' }}); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0, 102, 255, 0.25);">
                                    <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $hasKtpUploaded ? 'Verifikasi KTP Sedang Ditinjau' : 'Verifikasi KTP Diperlukan' }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $hasKtpUploaded ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-900' }}">
                                            {{ $hasKtpUploaded ? 'Menunggu Admin' : 'Wajib' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5">
                                        {{ $hasKtpUploaded ? 'Dokumen KTP sedang diperiksa. Anda bisa ambil order setelah disetujui.' : 'Unggah KTP & Selfie terlebih dahulu untuk dapat mengambil pesanan.' }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('profile.settings.verification') }}" wire:navigate
                               class="flex-shrink-0 font-bold px-3 py-1.5 rounded-xl transition text-[11px] text-white hover:opacity-95 active:scale-95 shadow-xs"
                               style="background: linear-gradient(135deg, #0066FF, #0052cc);">
                                {{ $hasKtpUploaded ? 'Cek Status' : 'Verifikasi' }}
                            </a>
                        </div>
                    @endif
                @endauth

                {{-- Needs City Notice --}}
                @if(!empty($needsCity))
                    <div class="bg-amber-50 border border-amber-200/90 rounded-2xl p-4 text-xs text-amber-900 shadow-xs">
                        <div class="font-bold flex items-center gap-1.5 text-sm">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Atur Wilayah Kota Anda</span>
                        </div>
                        <p class="mt-1 text-amber-800 leading-relaxed">Untuk menampilkan pesanan yang tersedia di daerah Anda, silakan tentukan Kota/Kabupaten Anda di profil.</p>
                        <div class="mt-3">
                            <a href="{{ route('mitra.profile') }}" wire:navigate class="inline-block px-3.5 py-1.5 bg-[#0066FF] text-white font-bold rounded-xl text-xs hover:bg-[#0052cc] transition shadow-xs">Buka Profil</a>
                        </div>
                    </div>
                @endif

                {{-- Order Cards List --}}
                @forelse($helps as $help)
                    @php
                        $isUrgent = $help->isUrgent();
                        $isCancelledByMe = auth()->check() && $help->wasCancelledByMitra(auth()->id());
                        $schedDate = $help->scheduled_at ?? $help->created_at;
                        $schedLabel = $schedDate ? \Carbon\Carbon::parse($schedDate)->translatedFormat('d M Y, H:i') : '-';
                        $catLabel = $help->category ? $help->category->name : 'Lainnya';
                        $cityLabel = $help->city->name ?? '-';
                        $distLabel = isset($help->distance) ? number_format($help->distance, 1) . ' km' : '';

                        // Determine status type cleanly
                        $isExpired = $help->isExpired();
                        $isAvailable = ($help->status === 'menunggu_mitra' && is_null($help->mitra_id) && ! $isExpired);
                        $isCompleted = in_array($help->status, \App\Enums\HelpStatus::expand(\App\Enums\HelpStatus::SELESAI));
                        $isMyOrder = auth()->check() && ($help->mitra_id == auth()->id()) && ! $isCompleted;
                    @endphp

                    <div class="bg-white rounded-2xl p-4 shadow-xs hover:shadow-md transition-all duration-200 border border-slate-200/80 relative overflow-hidden">
                        @if($isUrgent)
                            <!-- Subtle Red Accent Top Bar for Prioritas/Urgent -->
                            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-rose-400"></div>
                        @endif

                        <!-- Top Header Row: Category on Left, Status Badge on Right -->
                        <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
                            <!-- Left: Category & Urgent Badges -->
                            <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                <div class="flex items-center gap-1 text-xs font-bold text-gray-700">
                                    <span class="text-sm">{{ $help->category?->icon ?? '📦' }}</span>
                                    <span class="truncate max-w-[130px]">{{ $catLabel }}</span>
                                </div>

                                @if($isUrgent)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-50 text-red-600 border border-red-200/80">
                                        <span>⚡ Prioritas</span>
                                    </span>
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
                                            }" class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                                                ⏳ <span class="ml-0.5" x-text="label">{{ $minsLeft > 1 ? 'Sisa ' . $minsLeft . ' mnt' : 'Sisa < 1 mnt' }}</span>
                                            </span>
                                        @endif
                                    @endif
                                @endif

                                @if($isCancelledByMe)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        Pernah Dibatalkan
                                    </span>
                                @endif
                            </div>

                            <!-- Right: Status Badge -->
                            <div class="shrink-0">
                                @if($isExpired)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        Waktu Habis
                                    </span>
                                @elseif($isAvailable)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        Tersedia
                                    </span>
                                @elseif($isCompleted)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Selesai
                                    </span>
                                @elseif($isMyOrder)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-[#0066FF] border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#0066FF] animate-pulse"></span>
                                        Sedang Berjalan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">
                                        {{ ucfirst(str_replace('_', ' ', $help->status)) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Body: Thumbnail & Details -->
                        <div class="py-3 flex items-start gap-3">
                            <!-- Thumbnail / Category Icon -->
                            <div class="w-13 h-13 rounded-2xl overflow-hidden bg-slate-50 flex-shrink-0 border border-slate-200/80 relative shadow-2xs">
                                @if($help->photo)
                                    <img src="{{ asset('storage/' . $help->photo) }}" alt="{{ $help->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl bg-gradient-to-br from-blue-50/70 to-slate-100">
                                        {{ $help->category?->icon ?? '📦' }}
                                    </div>
                                @endif
                            </div>

                            <!-- Title, Customer & Schedule -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-sm text-gray-900 line-clamp-1 leading-snug">{{ $help->title }}</h3>

                                <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mt-0.5">
                                    <span>Pemohon:</span>
                                    <span class="font-semibold text-gray-700 truncate max-w-[120px]">{{ $help->user?->name ?? 'Pelanggan' }}</span>
                                    <span>•</span>
                                    <span class="shrink-0">{{ optional($help->created_at)->diffForHumans() }}</span>
                                </div>

                                <!-- Location, Distance & Schedule inline info -->
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-1 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-gray-700 font-medium text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span>{{ $cityLabel }}</span>
                                    </span>

                                    @if(isset($help->distance))
                                        <span class="text-gray-300">•</span>
                                        <span class="text-[11px] text-blue-600 font-bold">
                                            {{ number_format($help->distance, 1) }} km
                                        </span>
                                    @endif

                                    @if($schedDate)
                                        <span class="text-gray-300">•</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-500 font-medium">
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ \Carbon\Carbon::parse($schedDate)->translatedFormat('d M, H:i') }} WIB</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Description (Clean Subtle Preview) -->
                                @if(!empty($help->description))
                                    <p class="text-xs text-gray-500 line-clamp-1 mt-1.5 italic font-normal">
                                        "{{ Str::limit($help->description, 80) }}"
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer: Price on Left, Action Button on Right -->
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-3">
                            <!-- Left: Price -->
                            <div>
                                <span class="text-base font-black text-[#0066FF] tracking-tight">Rp {{ number_format($help->amount, 0, ',', '.') }}</span>
                            </div>

                            <!-- Right: Action Buttons -->
                            <div class="flex items-center gap-2">
                                @if($isExpired)
                                    <button type="button" disabled class="px-3.5 py-1.5 bg-gray-100 text-gray-400 font-bold rounded-xl text-xs cursor-not-allowed">
                                        Waktu Habis
                                    </button>
                                @elseif($isAvailable)
                                    <button type="button"
                                        onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}', {{ $isUrgent ? 'true' : 'false' }}, '{{ addslashes($cityLabel) }}', '{{ addslashes($distLabel) }}', {{ $isCancelledByMe ? 'true' : 'false' }}, {{ $isExpired ? 'true' : 'false' }})"
                                        class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold rounded-xl text-xs transition cursor-pointer">
                                        Detail
                                    </button>

                                    @if($isCancelledByMe)
                                        <button type="button" disabled class="px-3.5 py-1.5 bg-gray-100 text-gray-400 font-bold rounded-xl text-xs cursor-not-allowed">
                                            Dibatalkan
                                        </button>
                                    @else
                                        <button type="button"
                                            onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}', {{ $isUrgent ? 'true' : 'false' }}, '{{ addslashes($cityLabel) }}', '{{ addslashes($distLabel) }}', false, false)"
                                            class="px-4 py-1.5 bg-[#0066FF] hover:bg-[#0052cc] active:scale-95 text-white font-bold rounded-xl text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                            <span>Ambil Order</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    @endif
                                @elseif($isCompleted)
                                    <a href="{{ route('mitra.helps.detail', $help->id) }}" wire:navigate
                                       class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
                                        <span>Lihat Riwayat</span>
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @elseif($isMyOrder)
                                    <a href="{{ route('mitra.helps.detail', $help->id) }}" wire:navigate
                                       class="px-4 py-1.5 bg-[#0066FF] hover:bg-[#0052cc] active:scale-95 text-white font-bold rounded-xl text-xs transition shadow-xs flex items-center gap-1.5">
                                        <span>Buka Detail & Chat</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @else
                                    <a href="{{ route('mitra.helps.detail', $help->id) }}" wire:navigate
                                       class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold rounded-xl text-xs transition">
                                        Lihat
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl p-8 border border-slate-200/80 text-center shadow-xs mt-2">
                        <div class="w-16 h-16 mx-auto mb-3.5 bg-blue-50 text-[#0066FF] rounded-2xl flex items-center justify-center text-2xl shadow-2xs">
                            @if(!empty($search))
                                🔍
                            @else
                                📋
                            @endif
                        </div>
                        <h3 class="font-black text-sm text-gray-900 mb-1">
                            {{ !empty($search) ? 'Tidak Ada Pesanan Ditemukan' : 'Belum Ada Pesanan Tersedia' }}
                        </h3>
                        <p class="text-xs text-gray-500 max-w-xs mx-auto leading-relaxed">
                            {{ !empty($search) ? 'Coba gunakan kata kunci lain atau bersihkan kotak pencarian.' : 'Saat ini belum ada pesanan dalam radius ' . (int) ($maxRadius ?? 10) . ' km dari lokasi Anda. Silakan cek kembali beberapa saat lagi.' }}
                        </p>
                        @if(!empty($search))
                            <button type="button" wire:click="$set('search', '')" class="mt-4 px-4 py-2 bg-[#0066FF] text-white rounded-xl text-xs font-bold hover:bg-[#0052cc] transition shadow-xs">
                                Reset Pencarian
                            </button>
                        @else
                            <button type="button" wire:click="$refresh" class="mt-4 px-4 py-2 bg-slate-100 text-gray-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                🔄 Segarkan Halaman
                            </button>
                        @endif
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($helps->hasPages())
                    <div class="mt-5">
                        {{ $helps->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Preview Bantuan (Compact Bottom Sheet Style) -->
    <div id="helpPreviewModal" class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 hidden transition-opacity" onclick="closePreviewModal()">
        <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl max-h-[85vh] overflow-y-auto flex flex-col relative" onclick="event.stopPropagation()">
            <!-- Drag Handle Bar -->
            <div class="pt-3 pb-1 flex justify-center">
                <div class="w-12 h-1.5 bg-gray-200 rounded-full"></div>
            </div>

            <!-- Modal Header -->
            <div class="px-5 py-3 flex items-center justify-between border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Detail Pesanan</h3>
                    <p class="text-[11px] text-gray-400">Tinjau informasi sebelum mengambil pesanan</p>
                </div>
                <button type="button" onclick="closePreviewModal()" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-5 space-y-3">
                <div id="previewUrgentBadge" class="hidden p-2.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-extrabold text-center tracking-wide shadow-2xs">
                    ⚡ PESANAN PRIORITAS / URGENT
                </div>

                <!-- Judul & Nominal -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block">Judul Bantuan</span>
                        <p id="previewTitle" class="text-sm font-bold text-gray-900 line-clamp-2 mt-0.5 leading-snug">-</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <div id="previewAmount" class="inline-flex items-center bg-blue-50 text-[#0066FF] border border-blue-200 px-2.5 py-1 rounded-xl font-black text-sm mt-0.5 whitespace-nowrap shadow-2xs">
                            Rp 0
                        </div>
                    </div>
                </div>

                <!-- Info Details List -->
                <div class="bg-white border border-gray-200/80 rounded-2xl p-3.5 space-y-2.5 text-xs shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Kategori</span>
                        <span id="previewCategory" class="font-bold text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-gray-500 font-medium">Jadwal Pengerjaan</span>
                        <span id="previewScheduled" class="font-bold text-gray-900 text-right">-</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="text-gray-500 font-medium">Wilayah & Jarak</span>
                        <div class="flex items-center gap-1.5 font-bold text-gray-900 justify-end">
                            <span id="previewCity">-</span>
                            <span id="previewDistanceDot" class="text-gray-300 hidden">•</span>
                            <span id="previewDistance" class="hidden text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md text-[10px] whitespace-nowrap"></span>
                        </div>
                    </div>
                </div>

                <!-- Privacy Notice -->
                <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-3 text-blue-900 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[11px] leading-relaxed text-blue-800">
                        <strong class="font-bold text-blue-900">Perlindungan Privasi:</strong> Alamat lengkap, nomor telepon, dan chat customer akan otomatis terbuka setelah Anda mengambil pesanan ini.
                    </p>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-5 pb-5 pt-2 bg-white border-t border-gray-100">
                @if(auth()->check() && !auth()->user()->isProfileComplete())
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Biodata Profil Belum Lengkap</span>
                        </p>
                        <p class="text-[11px] text-red-700 leading-relaxed">
                            Harap lengkapi biodata profil Anda terlebih dahulu sebelum mengambil bantuan.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->verified)
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>KTP Belum Terverifikasi</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Akun dan KTP Anda masih dalam peninjauan Admin. Belum dapat mengambil pesanan.
                        </p>
                    </div>
                @elseif(auth()->check() && auth()->user()->hasActiveOngoingOrder())
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Masih Ada Order Aktif Berjalan</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Selesaikan order yang sedang berjalan terlebih dahulu sebelum mengambil order baru.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->hasVerifiedEmail())
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Email Belum Terverifikasi</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Silakan verifikasi alamat email Anda terlebih dahulu untuk dapat mengambil pesanan bantuan.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->canTakeMoreOrders())
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Tidak Dapat Mengambil Order</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Anda belum dapat mengambil pesanan saat ini.
                        </p>
                    </div>
                @endif

                <div id="previewCancelledByMeAlert" class="hidden mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                    <p class="font-bold flex items-center gap-1.5 mb-0.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Bantuan Pernah Dibatalkan</span>
                    </p>
                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        Anda tidak bisa mengambil bantuan ini karena sudah pernah Anda batalkan.
                    </p>
                </div>

                <div id="previewExpiredAlert" class="hidden mb-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs">
                    <p class="font-bold flex items-center gap-1.5 mb-0.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Batas Waktu Telah Habis</span>
                    </p>
                    <p class="text-[11px] text-rose-700 leading-relaxed">
                        Batas waktu pencarian mitra untuk bantuan ini sudah habis atau bantuan sudah kadaluarsa.
                    </p>
                </div>

                <div class="flex gap-2.5">
                    <button type="button" onclick="closePreviewModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-gray-700 py-3 px-4 rounded-xl font-bold text-xs transition cursor-pointer">
                        Kembali
                    </button>
                    <button type="button" id="previewCancelledTakeBtn" disabled class="hidden flex-1 bg-gray-200 text-gray-400 py-3 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                        Tidak Dapat Diambil
                    </button>
                    @if(auth()->check() && !auth()->user()->isProfileComplete())
                        <a href="{{ route('mitra.profile.edit') }}" wire:navigate class="flex-1 bg-red-600 text-white py-3 px-4 rounded-xl font-bold hover:bg-red-700 transition text-center text-xs flex items-center justify-center shadow-xs">
                            Lengkapi Biodata
                        </a>
                    @elseif(auth()->check() && !auth()->user()->verified)
                        <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 py-3 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                            Menunggu Verifikasi KTP
                        </button>
                    @elseif(auth()->check() && auth()->user()->hasActiveOngoingOrder())
                        <button type="button" disabled class="flex-1 bg-amber-100 text-amber-900 border border-amber-200 py-3 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                            Selesaikan Order Aktif
                        </button>
                    @elseif(auth()->check() && !auth()->user()->hasVerifiedEmail())
                        <a href="{{ route('verification.notice') }}" wire:navigate class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-3 px-4 rounded-xl font-bold text-center text-xs flex items-center justify-center shadow-xs">
                            Verifikasi Email
                        </a>
                    @elseif(auth()->check() && !auth()->user()->canTakeMoreOrders())
                        <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 py-3 px-4 rounded-xl font-bold cursor-not-allowed text-xs">
                            Tidak Dapat Diambil
                        </button>
                    @else
                        <button type="button" id="previewTakeBtn" onclick="takeHelpFromModal()" class="flex-1 bg-[#0066FF] hover:bg-[#0052cc] active:scale-95 text-white py-3 px-4 rounded-xl font-bold text-xs transition shadow-md shadow-blue-500/25 cursor-pointer text-center flex items-center justify-center gap-1.5">
                            <span>Ambil Bantuan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentHelpId = null;

        function showHelpPreview(helpId, title, amount, scheduled, category, isUrgent = false, city = '', distance = '', isCancelledByMe = false, isExpired = false) {
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
            const expiredAlert = document.getElementById('previewExpiredAlert');
            const cancelledBtn = document.getElementById('previewCancelledTakeBtn');
            const takeBtn = document.getElementById('previewTakeBtn');

            if (isExpired) {
                if (expiredAlert) expiredAlert.classList.remove('hidden');
                if (cancelledAlert) cancelledAlert.classList.add('hidden');
                if (cancelledBtn) {
                    cancelledBtn.textContent = 'Waktu Habis';
                    cancelledBtn.classList.remove('hidden');
                }
                if (takeBtn) takeBtn.classList.add('hidden');
            } else if (isCancelledByMe) {
                if (expiredAlert) expiredAlert.classList.add('hidden');
                if (cancelledAlert) cancelledAlert.classList.remove('hidden');
                if (cancelledBtn) {
                    cancelledBtn.textContent = 'Tidak Dapat Diambil';
                    cancelledBtn.classList.remove('hidden');
                }
                if (takeBtn) takeBtn.classList.add('hidden');
            } else {
                if (expiredAlert) expiredAlert.classList.add('hidden');
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
            const originalText = btn ? btn.innerHTML : 'Ambil Bantuan';

            if (btn) {
                btn.innerHTML = '<span>Memproses...</span>';
                btn.disabled = true;
            }

            @this.takeHelp(currentHelpId);

            closePreviewModal();

            if (btn) {
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
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
            var helpId = event?.detail?.helpId ?? event?.detail ?? null;

            if (!helpId) {
                window.location.reload();
                return;
            }

            var detailUrlTemplate = @json(route('mitra.helps.detail', ['id' => 'REPLACE_ID']));
            window.location.href = detailUrlTemplate.replace('REPLACE_ID', helpId);
        });
    </script>
</div>
