<div class="min-h-screen bg-gray-100"
    x-data
    x-init="
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                p => {
                    $wire.setCoordinates(p.coords.latitude, p.coords.longitude);
                },
                err => {},
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
    </style>

    <!-- Top Profile Bar matching Gambar 1 (Mitra) -->
    <div class="px-5 pt-4 pb-3 bg-white border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-blue-50 border-2 border-[#0066FF]/20 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                <x-user-avatar :user="auth()->user()" class="h-full w-full text-[#0066FF] font-bold text-sm" />
            </div>
            <div>
                <h1 class="text-sm font-extrabold text-gray-900 leading-tight">{{ optional(auth()->user())->name ?? 'Budi Santoso' }}</h1>
                <div class="mt-0.5">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Mitra Aktif
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @include('components.notification-icon', ['route' => route('mitra.notifications.index'), 'class' => 'p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition relative'])
        </div>
    </div>

    <!-- Saldo Mitra Card (Gambar 1) -->
    <div class="px-5 pt-4">
        <div class="bg-gradient-to-r from-[#0066FF] to-[#0052CC] rounded-2xl p-4 text-white shadow-md relative overflow-hidden">
            <!-- Decorative circle overlay -->
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5 text-blue-100 text-xs font-semibold">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Saldo Mitra</span>
                </div>

                <a href="{{ route('mitra.withdraw.form') }}" wire:navigate
                   class="bg-white hover:bg-slate-50 text-[#0066FF] active:scale-95 font-bold text-xs px-3.5 py-1.5 rounded-xl transition shadow-xs">
                    Tarik Saldo
                </a>
            </div>

            <div class="flex items-baseline justify-between mt-1">
                <h2 class="text-2xl font-black tracking-tight text-white">
                    Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                </h2>
                <a href="{{ route('mitra.withdraw.history') }}" wire:navigate class="text-[11px] text-blue-100 hover:text-white underline font-medium">
                    Riwayat Transaksi →
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Kotak Metrik Statistik Cepat (Gambar 1) -->
    <div class="px-5 pt-3">
        <div class="grid grid-cols-2 min-[380px]:grid-cols-4 gap-2">
            <!-- 1. Order Selesai -->
            <div class="bg-white border border-gray-200/90 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center shadow-2xs">
                <div class="w-7 h-7 rounded-xl bg-blue-50 text-[#0066FF] flex items-center justify-center mb-1 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-sm font-black text-gray-900 leading-none">{{ $completedCount ?? 0 }}</span>
                <span class="text-[10px] text-gray-500 font-semibold mt-1 leading-tight">Order Selesai</span>
            </div>

            <!-- 2. Order Aktif -->
            <div class="bg-white border border-gray-200/90 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center shadow-2xs">
                <div class="w-7 h-7 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-1 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-sm font-black text-gray-900 leading-none">{{ $inProgressCount ?? 0 }}</span>
                <span class="text-[10px] text-gray-500 font-semibold mt-1 leading-tight">Order Aktif</span>
            </div>

            <!-- 3. Rating -->
            <div class="bg-white border border-gray-200/90 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center shadow-2xs">
                <div class="w-7 h-7 rounded-xl bg-yellow-50 text-amber-500 flex items-center justify-center mb-1 text-xs">
                    ⭐
                </div>
                <span class="text-sm font-black text-gray-900 leading-none">{{ $ratingAvg ?? 4.8 }}</span>
                <span class="text-[10px] text-gray-500 font-semibold mt-1 leading-tight">Rating</span>
            </div>

            <!-- 4. Total Penghasilan -->
            <div class="bg-white border border-gray-200/90 rounded-2xl p-2.5 flex flex-col items-center justify-center text-center shadow-2xs">
                <div class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-1 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[11px] min-[380px]:text-xs font-black text-gray-900 leading-none whitespace-nowrap">
                    @if($totalEarnings >= 1000000)
                        Rp {{ number_format($totalEarnings / 1000000, 1) }}jt
                    @else
                        Rp {{ number_format($totalEarnings / 1000, 0) }}rb
                    @endif
                </span>
                <span class="text-[10px] text-gray-500 font-semibold mt-1 leading-tight">Penghasilan</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-5 pt-3 pb-6">

        <!-- GPS Tracker Component - Tampil untuk bantuan aktif -->

        @foreach($helps as $help)
            @if(in_array($help->status, ['taken', 'partner_on_the_way', 'partner_arrived']) && $help->mitra_id === auth()->id())
                <div class="mb-3">
                    <livewire:mitra.gps-tracker :helpId="$help->id" :key="'gps-'.$help->id" />
                </div>
            @endif
        @endforeach

        <!-- Card Notifikasi Bertahap (If-Else): Biodata -> KTP -> Email (Hilang 1 per 1 jika sudah terpenuhi) -->
        @php
            $dashUser = auth()->user();
            $needsBiodata = $dashUser && !$dashUser->isProfileComplete();
            $needsKtp = $dashUser && !(bool)$dashUser->verified;
            $hasKtpUploaded = $dashUser && (!empty($dashUser->ktp_photo) && !empty($dashUser->selfie_photo));
            $needsEmail = $dashUser && !$dashUser->hasVerifiedEmail();
        @endphp

        {{-- Flash Messages --}}
        @if (session()->has('error') || session()->has('message') || session()->has('success'))
            <div class="mb-4">
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
                        <p class="text-[11px] text-gray-600 truncate mt-0.5">Lengkapi profil Anda untuk mengambil order bantuan</p>
                    </div>
                </div>
                <a href="{{ route('mitra.profile.edit') }}" wire:navigate 
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
                            {{ $hasKtpUploaded ? 'Dokumen KTP Anda sedang ditinjau oleh Admin' : 'Upload KTP & Selfie untuk verifikasi akun mitra' }}
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
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">Maks. 2 Order & Belum Bisa Tarik Saldo</span>
                        </div>
                        <p class="text-[11px] text-gray-600 truncate mt-0.5">Verifikasi untuk ambil order tanpa batas & tarik saldo</p>
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
            $mitraBanners = json_decode((string) \App\Models\AppSetting::get('banner_mitra', '[]'), true) ?: [];
        @endphp

        @if(!empty($mitraBanners) && count($mitraBanners))
            <div class="mb-5">
                <div x-data="bannerSlider({{ count($mitraBanners) }})"
                    @touchstart="touchStart($event)"
                    @touchmove="touchMove($event)"
                    @touchend="touchEnd($event)"
                    @mousedown="mouseDown($event)"
                    @mousemove="mouseMove($event)"
                    @mouseup="mouseUp($event)"
                    @mouseleave="mouseLeave($event)"
                    @mouseenter="stopAutoPlay()"
                    class="relative rounded-2xl overflow-hidden shadow-md select-none group touch-pan-y {{ count($mitraBanners) > 1 ? 'cursor-grab active:cursor-grabbing' : '' }} bg-gray-100">

                    <!-- Slides Track -->
                    <div class="relative h-36 sm:h-40 overflow-hidden">
                        <div class="flex h-full will-change-transform"
                            :style="'transform: translateX(calc(-' + (current * 100) + '% + ' + dragOffset + 'px)); transition: ' + (isDragging ? 'none' : 'transform 500ms cubic-bezier(0.25, 1, 0.5, 1)')">
                            @foreach($mitraBanners as $b)
                                <div class="flex-shrink-0 w-full h-full select-none">
                                    <img src="{{ asset('storage/' . $b) }}"
                                        alt="Banner Mitra"
                                        draggable="false"
                                        class="w-full h-full object-cover pointer-events-none select-none" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if(count($mitraBanners) > 1)
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
                            @foreach($mitraBanners as $i => $b)
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

        <!-- Order Terdekat Section (Gambar 1) -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-extrabold text-gray-900">Order Terdekat</h2>
                <a href="{{ route('mitra.helps.all') }}" wire:navigate class="text-xs font-bold text-[#0066FF] hover:underline">
                    Lihat Semua &gt;
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($recommendedHelps as $help)
                    @php 
                        $schedDate = $help->scheduled_at ?? $help->created_at;
                        $timeStr = $schedDate ? \Carbon\Carbon::parse($schedDate)->format('H:i') : '09:00';
                        $schedLabel = $schedDate ? \Carbon\Carbon::parse($schedDate)->translatedFormat('d M Y, H:i') : '-' ;
                        $catName = $help->category ? $help->category->name : 'Jasa Serabutan';
                        $isUrgent = $help->isUrgent();
                        $addressStr = $help->location ?? $help->full_address ?? ($help->city->name ?? 'Yogyakarta');
                        $cityLabel = $help->city->name ?? '-';
                        $distLabel = isset($help->distance) ? number_format($help->distance, 1) . ' km' : '';
                        $isCancelledByMe = auth()->check() && $help->wasCancelledByMitra(auth()->id());
                    @endphp
                    <div onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catName) }}', {{ $isUrgent ? 'true' : 'false' }}, '{{ addslashes($cityLabel) }}', '{{ addslashes($distLabel) }}', {{ $isCancelledByMe ? 'true' : 'false' }})"
                        class="bg-white border border-gray-200/90 rounded-2xl p-3.5 shadow-2xs hover:shadow-sm hover:border-[#0066FF]/30 transition flex items-center gap-3 cursor-pointer group">
                        <!-- Category Icon (Colored Circle) -->
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition-transform
                            {{ str_contains(strtolower($catName), 'bersih') ? 'bg-blue-50 text-blue-600' :
                               (str_contains(strtolower($catName), 'pindah') ? 'bg-indigo-50 text-indigo-600' :
                               (str_contains(strtolower($catName), 'tukang') || str_contains(strtolower($catName), 'bangun') ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600')) }}">
                            {{ $help->category?->icon ?? '🛠️' }}
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="font-extrabold text-xs text-gray-900 group-hover:text-[#0066FF] truncate transition-colors">{{ $help->title }}</h3>
                            <p class="text-[11px] text-gray-500 truncate mt-0.5 flex items-center gap-1">
                                <span>📍</span>
                                <span>{{ Str::limit($addressStr, 35) }}</span>
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-black text-gray-900">Rp {{ number_format($help->amount, 0, ',', '.') }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="text-[10px] text-gray-500 font-medium">⭐ 4.8 | Hari ini, {{ $timeStr }}</span>
                            </div>
                        </div>

                        <!-- Action Button Ambil (Green pill button matching Gambar 1) -->
                        <div class="flex-shrink-0" onclick="event.stopPropagation()">
                            @if($isCancelledByMe)
                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full">Dibatalkan</span>
                            @else
                                <button type="button"
                                    onclick="event.stopPropagation()"
                                    wire:click.stop="takeHelp({{ $help->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="takeHelp({{ $help->id }})"
                                    class="bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold text-xs px-4 py-1.5 rounded-full shadow-xs transition flex items-center gap-1 cursor-pointer">
                                    <span wire:loading.remove wire:target="takeHelp({{ $help->id }})">Ambil</span>
                                    <span wire:loading wire:target="takeHelp({{ $help->id }})">...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <x-empty-state 
                        icon="clipboard" 
                        title="Belum Ada Order Terdekat" 
                        :description="'Belum ada permintaan bantuan dalam radius ' . (int) ($maxRadius ?? 10) . ' km saat ini.'" 
                    />
                @endforelse
            </div>
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
                        <a href="{{ route('mitra.profile.edit') }}" wire:navigate class="flex-1 bg-red-600 text-white py-2.5 px-4 rounded-xl font-bold hover:bg-red-700 transition text-center text-xs flex items-center justify-center">
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

            const btn = document.getElementById('previewTakeBtn') || document.querySelector('[onclick="takeHelpFromModal()"]');
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
                    @this.takeHelp(currentHelpId, lat, lng);
                } else {
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
            // Reload page to show updated list
            window.location.reload();
        });
    </script>
</div>
