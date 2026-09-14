<div class="min-h-screen bg-gray-100">
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

    <!-- Header Section - BRImo Style -->
    <div class="px-5 pt-5 pb-20 relative overflow-hidden header-pattern" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
        <!-- Decorative circles (like BRImo) -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
        
        <div class="relative z-10">
            <!-- Top Bar -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    @php
                        $__avatar = optional(auth()->user())->profile_photo ?? optional(auth()->user())->photo ?? optional(auth()->user())->profile_photo_path ?? null;
                    @endphp
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden ring-2 ring-white/30">
                        <img src="{{ $__avatar ? asset('storage/' . $__avatar) : asset('images/avatar-placeholder.svg') }}" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-xs text-white/80">Selamat datang</p>
                        <h1 class="text-sm font-bold text-white">{{ optional(auth()->user())->name ?? 'Mitra' }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('mitra.chat') }}" class="relative bg-white/10 backdrop-blur-sm p-2 rounded-lg hover:bg-white/20 transition text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-5 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z" />
                        </svg>
                        @if(!empty($unreadChatCount) && $unreadChatCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-3 w-3 items-center justify-center">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        @endif
                    </a>
                    @include('components.notification-icon', ['route' => route('mitra.notifications.index'), 'class' => 'bg-white/10 backdrop-blur-sm p-2 rounded-lg hover:bg-white/20 transition'])
                </div>
            </div>

            <!-- Header Content - Info Section -->
            <div class="mt-4 mb-2">
                <h2 class="text-white text-lg font-bold mb-1">Dashboard Mitra</h2>
                <p class="text-white/80 text-sm leading-relaxed">Kelola pekerjaan bantuan dan pendapatan Anda dengan mudah</p>
                
                <!-- Quick Stats -->
                {{-- <div class="flex items-center gap-4 mt-4">
                    <div class="flex items-center gap-2 text-white/90">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-medium">{{ $availableHelpsCount }} Tersedia</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-medium">Saldo Aktif</span>
                    </div>
                    <div class="flex items-center gap-2 text-white/90">
                        <a href="{{ route('mitra.chat') }}" class="inline-flex items-center gap-2 text-white/90 hover:text-white/100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-5 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z" />
                            </svg>
                            <span class="text-xs font-medium">Chat</span>
                        </a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <br>

    <!-- Small account info bar placed above the balance card (over header) -->
    <div class="px-5 -mt-10 relative z-20">
        <div class="max-w-full">
            <div class="inline-flex items-center gap-3 bg-white/10 backdrop-hblur-sm text-white text-sm px-3 py-1 rounded-full shadow-sm">
                <svg class="w-4 h-4 text-white/90" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6c0 4.5 6 10 6 10s6-5.5 6-10a6 6 0 00-6-6z"/></svg>
                <span>{{ optional(optional(auth()->user())->city)->name ?? (auth()->user()->city ?? '-') }}</span>
                <span class="opacity-60">•</span>
                <span>Member sejak {{ optional(auth()->user())->created_at ? optional(auth()->user())->created_at->format('M Y') : '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Balance Card - BRImo Style (overlapping header) -->
    <div class="px-5 -mt-20 relative z-20 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-xl">
            <div class="flex items-start justify-between mb-2">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 mb-1">Total Saldo</p>
                    

                    <div class="flex items-center gap-2" x-data="{ show: false }">
                        <h2 class="text-2xl font-bold text-gray-900" x-show="show">
                            Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                        </h2>
                        <h2 class="text-2xl font-bold text-gray-900" x-show="!show">Rp ••••••</h2>
                        <button @click="show = !show" class="p-1.5 hover:bg-gray-100 rounded-lg transition" title="Tampilkan / Sembunyikan Saldo">
                            <!-- Saat Saldo Tersembunyi (Slashed Eye) -->
                            <svg class="w-4 h-4 text-gray-500" x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <!-- Saat Saldo Terlihat (Open Eye) -->
                            <svg class="w-4 h-4 text-[#0098e7]" x-show="show" x-cloak style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-1.5">
                        <a href="{{ route('mitra.withdraw.history') }}" class="text-[11px] font-semibold flex items-center gap-1 w-max" style="color: #0098e7;">
                            Lihat riwayat transaksi
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                <a href="{{ route('mitra.withdraw.form') }}" class="text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-md inline-flex items-center gap-1.5" style="background: #0098e7;" title="{{ auth()->check() && !auth()->user()->hasVerifiedEmail() ? 'Perlu verifikasi email untuk tarik saldo' : 'Tarik Saldo' }}">
                    @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
                        <svg class="w-3.5 h-3.5 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @endif
                    <span>Tarik Saldo</span>
                </a>
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

        @if($needsBiodata)
            {{-- 1. LENGKAPI BIODATA DULU --}}
            <div class="rounded-2xl p-3.5 text-xs mb-5 flex items-center justify-between gap-3 bg-white"
                 style="border: 1.5px solid #0098e7; box-shadow: 0 4px 14px rgba(0, 152, 231, 0.15);">
                <div class="flex items-center gap-3 min-w-0">
                    <div style="background: linear-gradient(135deg, #0098e7 0%, #0077cc 100%); width: 38px; height: 38px; min-width: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0, 152, 231, 0.35);">
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
                <a href="{{ route('mitra.profile.edit') }}" 
                   class="flex-shrink-0 font-bold px-3.5 py-1.5 rounded-xl transition text-[11px] text-white hover:opacity-95 active:scale-95 cursor-pointer shadow-xs"
                   style="background: linear-gradient(to right, #0098e7, #0077cc); box-shadow: 0 2px 6px rgba(0, 152, 231, 0.35);">
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
                <a href="{{ route('profile.settings.verification') }}" 
                   class="flex-shrink-0 font-bold px-3.5 py-1.5 rounded-xl transition text-[11px] hover:opacity-95 active:scale-95 cursor-pointer shadow-xs {{ $hasKtpUploaded ? 'text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100' : 'text-white' }}"
                   @if(!$hasKtpUploaded) style="background: linear-gradient(135deg, #0098e7 0%, #0077cc 100%); box-shadow: 0 2px 6px rgba(0, 152, 231, 0.35);" @endif>
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
                <div class="rounded-xl overflow-hidden shadow-md">
                    <div class="relative h-36 overflow-hidden">
                        <div class="flex h-full will-change-transform mitra-banner-slides"
                            style="transition: transform 700ms cubic-bezier(.2,.9,.2,1);">
                            @foreach($mitraBanners as $b)
                                <div class="flex-shrink-0 w-full h-full">
                                    <img src="{{ asset('storage/' . $b) }}" alt="Banner" class="w-full h-full object-cover" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-5">
                <div id="promo-banner" class="rounded-xl overflow-hidden shadow-md">
                    <div class="relative h-36 overflow-hidden" style="background: linear-gradient(to right, #0098e7, #0077cc);">
                        <div id="promo-track" class="flex h-full transition-transform duration-700 ease-in-out"></div>
                    </div>
                </div>
                <div id="promo-dots" class="flex justify-center mt-3 gap-2">
                    <button data-dot="0" class="w-2 h-2 rounded-full transition-all" style="background: #0098e7;"></button>
                    <button data-dot="1" class="w-2 h-2 rounded-full bg-gray-300 transition-all"></button>
                    <button data-dot="2" class="w-2 h-2 rounded-full bg-gray-300 transition-all"></button>
                </div>
            </div>
        @endif

        <!-- Bantuan Tersedia Section -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-gray-900">Bantuan Tersedia</h2>
                <a href="{{ route('mitra.helps.all') }}" class="text-xs font-semibold" style="color: #0098e7;">Lihat Semua →</a>
            </div>

            <div class="space-y-3">
                <div wire:loading class="space-y-3">
                    @for($i=0;$i<3;$i++)
                        <div class="bg-white rounded-xl p-3 shadow-sm animate-pulse">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                                <div class="flex-1">
                                    <div class="h-3 bg-gray-200 rounded w-3/5 mb-2"></div>
                                    <div class="h-2.5 bg-gray-200 rounded w-4/5"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                @forelse($recommendedHelps as $help)
                    @php 
                        $schedLabel = $help->scheduled_at ? \Carbon\Carbon::parse($help->scheduled_at)->translatedFormat('d M Y, H:i') : '' ;
                        $catLabel = $help->category ? ($help->category->icon . ' ' . $help->category->name) : '📦 Lainnya';
                    @endphp
                    <button type="button" onclick="showHelpPreview({{ $help->id }}, '{{ addslashes($help->title) }}', {{ $help->amount }}, '{{ addslashes($schedLabel) }}', '{{ addslashes($catLabel) }}')"
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
                                    <h3 class="font-semibold text-sm text-gray-900 line-clamp-1">{{ $help->title }}</h3>
                                    <span class="text-xs font-bold whitespace-nowrap" style="color: #0098e7;">Rp {{ number_format($help->amount, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-gray-600 line-clamp-1 mb-1.5">{{ Str::limit($help->description, 60) }}</p>
                                @if($help->scheduled_at)
                                    <div class="text-xs text-gray-500 mb-1">📅 {{ \Carbon\Carbon::parse($help->scheduled_at)->translatedFormat('d M Y, H:i') }}</div>
                                @endif
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500">📍 {{ $help->city->name ?? '-' }}</span>
                                    <span class="text-xs text-gray-400">{{ $help->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-10 bg-white rounded-xl shadow-sm">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-sm font-semibold text-gray-700">Belum ada bantuan tersedia</p>
                        <p class="text-xs text-gray-500 mt-1">Cek kembali nanti untuk bantuan baru</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>


    @if(empty($mitraBanners) || !count($mitraBanners))
        <script>
            (function () {
                const banners = [
                    { title: 'Promo Spesial', desc: 'Dapatkan bonus saldo dan insentif khusus.', bgCss: 'linear-gradient(135deg,#6366f1,#4f46e5)' },
                    { title: 'Insentif Mitra', desc: 'Selesaikan lebih banyak bantuan, dapatkan insentif.', bgCss: 'linear-gradient(135deg,#10b981,#059669)' },
                    { title: 'Badge Aktif', desc: 'Selesaikan 5 bantuan dan dapatkan badge Mitra Aktif.', bgCss: 'linear-gradient(135deg,#f59e0b,#f97316)' }
                ];

                const track = document.getElementById('promo-track');
                const dotsContainer = document.getElementById('promo-dots');
                const dots = dotsContainer ? Array.from(dotsContainer.querySelectorAll('button')) : [];
                let idx = 0;
                let timer = null;

                // build slides (create DOM nodes and use inline background to avoid Tailwind purge issues)
                if (track) {
                    track.innerHTML = '';
                    const frag = document.createDocumentFragment();
                    banners.forEach(b => {
                        const slide = document.createElement('div');
                        slide.className = 'w-full flex-shrink-0 p-6 flex items-center justify-center text-white';
                        slide.style.background = b.bgCss;
                        const inner = document.createElement('div');
                        inner.className = 'text-center';
                        const title = document.createElement('div');
                        title.className = 'font-extrabold text-xl mb-1 tracking-tight';
                        title.textContent = b.title;
                        const desc = document.createElement('div');
                        desc.className = 'text-sm opacity-90 font-medium';
                        desc.textContent = b.desc;
                        inner.appendChild(title);
                        inner.appendChild(desc);
                        slide.appendChild(inner);
                        frag.appendChild(slide);
                    });
                    track.appendChild(frag);
                }

                function update() {
                    if (track) {
                        const percent = (idx * 100) / banners.length;
                        track.style.transform = `translateX(${-percent}%)`;
                    }
                    if (dots.length) {
                        dots.forEach((d, k) => {
                            d.classList.toggle('bg-primary-600', k === idx);
                            d.classList.toggle('bg-gray-300', k !== idx);
                        });
                    }
                }

                function go(i) {
                    idx = (i + banners.length) % banners.length;
                    update();
                }

                function resetTimer() {
                    if (timer) clearInterval(timer);
                    timer = setInterval(() => go(idx + 1), 4200);
                }

                // dot clicks
                if (dotsContainer) {
                    dotsContainer.addEventListener('click', function (e) {
                        const dot = e.target.closest('button[data-dot]');
                        if (!dot) return;
                        const i = parseInt(dot.dataset.dot);
                        go(i);
                        resetTimer();
                    });
                }

                // init
                if (track) {
                    // ensure track has width for transform to work correctly
                    track.style.width = `${banners.length * 100}%`;
                    Array.from(track.children).forEach(child => child.style.width = `${100 / banners.length}%`);
                    update();
                    resetTimer();
                }
            })();
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initBannerSlider(wrapperSelector) {
                const wrapper = document.querySelector(wrapperSelector);
                if (!wrapper) return;
                const container = wrapper.parentElement; // expected visible container
                const slides = Array.from(wrapper.children || []);
                if (!slides.length || slides.length <= 1) return;

                function setup() {
                    const cw = container.clientWidth || container.getBoundingClientRect().width;
                    wrapper.style.width = (cw * slides.length) + 'px';
                    wrapper.style.display = 'flex';
                    wrapper.style.transition = 'transform 700ms cubic-bezier(.2,.9,.2,1)';
                    slides.forEach(s => {
                        s.style.width = cw + 'px';
                        s.style.flex = '0 0 auto';
                    });
                }

                let idx = 0;
                let timer = null;

                function go(i) {
                    idx = (i + slides.length) % slides.length;
                    const shift = -(idx * (container.clientWidth || container.getBoundingClientRect().width));
                    wrapper.style.transform = 'translateX(' + shift + 'px)';
                }

                setup();
                window.addEventListener('resize', setup);

                timer = setInterval(function () { go(idx + 1); }, 3500);

                container.addEventListener('mouseenter', function () { if (timer) clearInterval(timer); });
                container.addEventListener('mouseleave', function () { if (timer) clearInterval(timer); timer = setInterval(function () { go(idx + 1); }, 3500); });
            }

            try { initBannerSlider('.mitra-banner-slides'); } catch (e) { console.warn('mitra slider init', e); }
            try { initBannerSlider('.customer-banner-slides'); } catch (e) { /* ignore */ }
        });
    </script>

    <!-- Modal Preview Bantuan (Bottom Sheet Style) -->
    <div id="helpPreviewModal" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 hidden pb-16">
        <div class="bg-white rounded-t-3xl w-full max-w-md shadow-2xl max-h-[75vh] overflow-y-auto" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b px-5 py-4 rounded-t-3xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Preview Bantuan</h3>
                    <button type="button" onclick="closePreviewModal()" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="p-5 pb-6">
                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Judul Bantuan</p>
                    <p id="previewTitle" class="text-base font-bold text-gray-900">-</p>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Kategori</p>
                    <div id="previewCategory" class="text-sm text-gray-700">-</div>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Nominal untuk Mitra</p>
                    <div id="previewAmount" class="inline-block bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-bold text-sm">
                        💰 Rp 0
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Jadwal Permintaan</p>
                    <div id="previewScheduled" class="text-sm text-gray-700">-</div>
                </div>

                <!-- Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-xs font-semibold text-blue-800 mb-1">🔒 Informasi Terbatas</p>
                    <p class="text-xs text-blue-700">
                        Deskripsi, alamat lengkap, lokasi di peta, foto, dan kontak customer akan ditampilkan setelah Anda mengambil bantuan ini.
                    </p>
                </div>
            </div>

            <!-- Sticky footer -->
            <div class="sticky bottom-0 bg-white border-t pt-4 px-5 pb-5">
                @if(auth()->check() && !auth()->user()->isProfileComplete())
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Biodata Belum Lengkap</span>
                        </p>
                        <p class="text-[11px] text-red-700 leading-relaxed">
                            Harap lengkapi data profil dan dokumen KTP Anda terlebih dahulu sebelum dapat mengambil bantuan.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->verified)
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>KTP Belum Terverifikasi</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Akun dan KTP Anda sedang dalam proses verifikasi oleh Admin. Anda belum dapat mengambil bantuan hingga akun terverifikasi.
                        </p>
                    </div>
                @elseif(auth()->check() && !auth()->user()->canTakeMoreOrders())
                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs">
                        <p class="font-bold flex items-center gap-1.5 mb-1">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Batas 2 Bantuan Tercapai</span>
                        </p>
                        <p class="text-[11px] text-amber-700 leading-relaxed">
                            Akun Anda belum verifikasi email dan telah mencapai batas maksimal 2 bantuan. Silakan verifikasi email Anda terlebih dahulu untuk dapat mengambil bantuan kembali tanpa batas.
                        </p>
                    </div>
                @endif
                <div class="flex gap-3">
                    <button type="button" onclick="closePreviewModal()" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl font-bold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    @if(auth()->check() && !auth()->user()->isProfileComplete())
                        <a href="{{ route('mitra.profile.edit') }}" class="flex-1 bg-red-600 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-red-700 transition text-center text-xs flex items-center justify-center">
                            Lengkapi Biodata
                        </a>
                    @elseif(auth()->check() && !auth()->user()->verified)
                        <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 px-4 py-2.5 rounded-xl font-bold cursor-not-allowed text-xs">
                            Menunggu Verifikasi KTP
                        </button>
                    @elseif(auth()->check() && !auth()->user()->canTakeMoreOrders())
                        <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 px-4 py-2.5 rounded-xl font-bold cursor-not-allowed text-xs">
                            Batas Order Tercapai
                        </button>
                    @else
                        <button type="button" id="previewTakeBtn" onclick="takeHelpFromModal()" class="flex-1 bg-primary-500 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-primary-600 transition">
                            Ambil Bantuan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentHelpId = null;

        function showHelpPreview(helpId, title, amount, scheduled, category) {
            currentHelpId = helpId;
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewCategory').textContent = category || '📦 Lainnya';
            document.getElementById('previewAmount').textContent = '💰 Rp ' + amount.toLocaleString('id-ID');
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
            document.getElementById('helpPreviewModal').classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('helpPreviewModal').classList.add('hidden');
            currentHelpId = null;
        }

        function takeHelpFromModal() {
            if (!currentHelpId) return;

            // Fungsi untuk reset button
            const resetButton = (btn, originalText) => {
                btn.textContent = originalText;
                btn.disabled = false;
            };
            
            // Fungsi untuk ambil bantuan dengan/tanpa lokasi
            const takeBantuanWithLocation = (lat = null, lng = null) => {
                if (lat && lng) {
                    console.log('📍 Mengambil bantuan dengan lokasi:', { lat, lng });
                    @this.takeHelp(currentHelpId, lat, lng);
                } else {
                    console.log('📍 Mengambil bantuan tanpa lokasi GPS');
                    @this.takeHelp(currentHelpId);
                }
                closePreviewModal();
            };
            
            // Fungsi fallback: Coba gunakan IP-based location
            const tryIPBasedLocation = (btn, originalText) => {
                console.log('🌐 Mencoba IP-based location...');
                btn.textContent = 'Mendeteksi lokasi dari IP...';
                
                // Gunakan ipapi.co untuk mendapatkan koordinat dari IP
                fetch('https://ipapi.co/json/', { timeout: 3000 })
                    .then(response => response.json())
                    .then(data => {
                        if (data.latitude && data.longitude) {
                            console.log('✅ IP-based location berhasil:', data);
                            takeBantuanWithLocation(data.latitude, data.longitude);
                            resetButton(btn, originalText);
                        } else {
                            throw new Error('Invalid location data');
                        }
                    })
                    .catch(error => {
                        console.error('❌ IP-based location gagal:', error);
                        // Tetap tanyakan apakah mau ambil tanpa lokasi
                        if (confirm('📍 Lokasi tidak dapat dideteksi.\n\n✅ Ambil bantuan tanpa GPS tracking?\n\n(Lokasi dapat diupdate nanti saat Anda mulai bergerak)')) {
                            takeBantuanWithLocation();
                        }
                        resetButton(btn, originalText);
                    });
            };

            // Request GPS permission dan ambil lokasi
            if (navigator.geolocation) {
                // Show loading on button
                const btn = document.getElementById('previewTakeBtn');
                const originalText = btn.textContent;
                btn.textContent = 'Mengambil GPS...';
                btn.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        
                        console.log('✅ GPS Location obtained:', { lat, lng, accuracy: accuracy + 'm' });
                        
                        // Call Livewire method dengan GPS coordinates
                        takeBantuanWithLocation(lat, lng);
                        
                        // Reset button
                        resetButton(btn, originalText);
                    },
                    (error) => {
                        console.error('❌ GPS Error:', error);
                        
                        // Error codes:
                        // 1 = PERMISSION_DENIED
                        // 2 = POSITION_UNAVAILABLE (no GPS hardware atau signal)
                        // 3 = TIMEOUT
                        
                        if (error.code === 2) {
                            // GPS tidak tersedia (laptop/desktop tanpa GPS)
                            console.log('💻 Device tidak memiliki GPS, mencoba IP-based location...');
                            tryIPBasedLocation(btn, originalText);
                        } else if (error.code === 1) {
                            // User menolak permission
                            if (confirm('📍 Akses lokasi ditolak.\n\n🌐 Coba deteksi lokasi dari IP address?\n\n(Akurasi lebih rendah tetapi cukup untuk tracking)')) {
                                tryIPBasedLocation(btn, originalText);
                            } else if (confirm('✅ Ambil bantuan tanpa GPS tracking?\n\n(Lokasi dapat diupdate nanti)')) {
                                takeBantuanWithLocation();
                                resetButton(btn, originalText);
                            } else {
                                resetButton(btn, originalText);
                            }
                        } else {
                            // Timeout atau error lain
                            if (confirm('⏱️ GPS timeout atau error.\n\n🌐 Coba deteksi lokasi dari IP address?')) {
                                tryIPBasedLocation(btn, originalText);
                            } else if (confirm('✅ Ambil bantuan tanpa GPS tracking?')) {
                                takeBantuanWithLocation();
                                resetButton(btn, originalText);
                            } else {
                                resetButton(btn, originalText);
                            }
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 8000, // 8 detik timeout (lebih lama untuk laptop)
                        maximumAge: 0
                    }
                );
            } else {
                // Browser tidak support GPS (browser lama)
                const btn = document.getElementById('previewTakeBtn');
                const originalText = btn.textContent;
                console.warn('⚠️ Browser tidak support Geolocation API');
                
                if (confirm('🌐 Browser tidak mendukung GPS.\n\nCoba deteksi lokasi dari IP address?')) {
                    btn.disabled = true;
                    tryIPBasedLocation(btn, originalText);
                } else if (confirm('✅ Ambil bantuan tanpa GPS tracking?')) {
                    takeBantuanWithLocation();
                } else {
                    // User cancel
                }
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