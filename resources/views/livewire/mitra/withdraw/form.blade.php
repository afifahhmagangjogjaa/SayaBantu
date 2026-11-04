@php
    $isCustomer = ($user && $user->role === 'customer');
    $prefix = $prefix ?? ($isCustomer ? 'customer' : 'mitra');
    $dashRoute = $prefix . '.dashboard';
    $formRoute = $prefix . '.withdraw.form';
    $reqRoute = $prefix . '.withdraw.request';
    $histRoute = $prefix . '.withdraw.history';
    $bankRoute = $prefix . '.bank-accounts';
@endphp
@extends($isCustomer ? 'layouts.app' : 'layouts.mitra')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-20">
        <!-- Header - Brand Blue with Wave Separator -->
        <div class="relative bg-[#0066FF] text-white">
            <!-- Decorative circle overlays in safe container -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16"></div>
            </div>

            <div class="relative z-10 px-5 pt-6 pb-[92px]">
                <div class="flex items-center justify-between text-white">
                    <a href="{{ route($dashRoute) }}" wire:navigate aria-label="Kembali ke Dashboard" class="p-2 hover:bg-white/20 rounded-xl transition active:scale-95 cursor-pointer inline-flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="text-center flex-1 px-2">
                        <h1 class="text-lg font-black text-white tracking-tight leading-tight">Penarikan Saldo</h1>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">Ajukan penarikan dana ke rekening Anda</p>
                    </div>

                    <div class="w-9"></div>
                </div>
            </div>

            <!-- Wave Separator (SVG) bergelombang - Tepat di bawah header biru -->
            <svg class="absolute -bottom-1 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 28px; bottom: -2px;">
                <path d="M0,28 C360,76 1080,-4 1440,36 L1440,84 L0,84 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <!-- Saldo Mitra Card (Mengambang di Atas Wave - Pas di Tengah Garis Wave) -->
        <div class="px-5 -mt-[66px] relative z-10 mb-4">
            <div class="bg-gradient-to-r from-[#0052CC] to-[#0044AA] rounded-2xl p-5 text-white shadow-xl relative overflow-hidden border border-white/20">
                <!-- Decorative circle overlay matching Beranda -->
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>

                <div class="relative z-10 flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-100 mb-1">Saldo Tersedia</p>
                        <h2 class="text-2xl font-black text-white tracking-tight">
                            Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="text-white/95 mt-1">
                        <!-- Classical Greek Bank Temple Icon -->
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v2h20V7L12 2zm-8 8v8h3v-8H4zm6 0v8h4v-8h-4zm7 0v8h3v-8h-3zM2 20h20v2H2v-2z"/>
                        </svg>
                    </div>
                </div>

                <div class="relative z-10 mt-3 pt-3 border-t border-white/20 text-xs text-blue-100">
                    <span>Minimum penarikan: <strong class="text-white font-bold">Rp 10.000</strong></span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="px-5 pt-4 space-y-4">
            <!-- Status Messages -->
            @if(session('status') || session('success') || session('message'))
                @php $msg = session('success') ?? session('message') ?? session('status'); @endphp
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl shadow-2xs flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-bold text-xs">{{ $msg }}</span>
                </div>
            @endif

            @if($user->hasPendingOrProcessingWithdraws())
                <!-- Pending Withdrawal Notice -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 text-center">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900 mb-1.5">Pengajuan Sedang Diproses</h3>
                    <p class="text-xs text-slate-600 mb-6 leading-relaxed">
                        Pengajuan penarikan saldo Anda sedang diproses oleh admin. Mohon tunggu 1-5 hari kerja. 
                        Anda akan menerima notifikasi ketika status berubah.
                    </p>
                    <a href="{{ route($histRoute) }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold rounded-xl transition shadow-md active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Riwayat Penarikan
                    </a>
                </div>
            @else
                @if(!$user->hasVerifiedEmail())
                    <!-- Belum Verifikasi Email Notice -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-amber-950">Verifikasi Email Diperlukan</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Saldo belum dapat ditarik karena email Anda (<strong>{{ $user->email }}</strong>) belum diverifikasi. Silakan verifikasi email terlebih dahulu untuk melakukan penarikan saldo.
                                </p>
                                <form method="POST" action="{{ route('verification.send') }}" class="mt-3" onsubmit="event.preventDefault(); window.sendEmailVerification(this.querySelector('button'), '{{ route('verification.send') }}');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition active:scale-95 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Kirim Link Verifikasi Email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$user->hasTransactionPin())
                    <!-- Belum Atur PIN Transaksi Notice -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-amber-950">PIN Transaksi Belum Diatur</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Demi keamanan saldo Anda, buat PIN transaksi 6 digit terlebih dahulu untuk otorisasi penarikan dana.
                                </p>
                                <a href="{{ route($bankRoute) }}" wire:navigate class="inline-flex items-center gap-1.5 mt-2.5 px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition active:scale-95">
                                    <span>Atur PIN Transaksi 6 Digit &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($bankAccounts) && $bankAccounts->isEmpty())
                    <!-- Belum Ada Rekening Bank Notice -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl text-blue-900 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-[#0066FF] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-xs text-blue-950">Rekening Tujuan Belum Terdaftar</h4>
                                <p class="text-xs text-blue-800 mt-1 leading-relaxed">
                                    Tambahkan rekening bank atau e-wallet Anda terlebih dahulu agar penarikan saldo dapat diproses.
                                </p>
                                <a href="{{ route($bankRoute) }}" wire:navigate class="inline-flex items-center gap-1.5 mt-2.5 px-3.5 py-1.5 bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl text-xs font-bold shadow-xs transition active:scale-95">
                                    <span>+ Tambah Rekening / E-Wallet &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->isPinLocked())
                    <!-- PIN Sedang Terkunci Notice -->
                    <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-900 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-500/20 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-red-950">PIN Transaksi Terkunci</h4>
                                <p class="text-xs text-red-800 mt-1 leading-relaxed">
                                    PIN Anda terkunci sementara karena salah 3x berturut-turut. Coba lagi dalam <strong>{{ $user->getPinLockRemainingMinutes() }} menit</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(($user->balance ?? 0) < 10000)
                    <!-- Saldo Kurang Notice -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-amber-950">Saldo Belum Mencukupi</h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Saldo Anda saat ini adalah <strong>Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</strong>. Minimal saldo untuk melakukan penarikan saldo adalah <strong>Rp 10.000</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($limit24h) && !$limit24h['allowed'])
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl shadow-2xs mb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-amber-900 text-xs mb-0.5">Penarikan Dibatasi 1x dalam 24 Jam</p>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Anda telah mengajukan penarikan pada <strong>{{ $limit24h['latest_withdraw']?->created_at->format('d M Y, H:i') }} WIB</strong>. Pengajuan penarikan berikutnya dapat dilakukan pada <strong>{{ $limit24h['available_at']?->format('d M Y, H:i') }} WIB</strong> (sisa <strong>{{ $limit24h['remaining_text'] }}</strong>).
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 rounded-2xl shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-500/20 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-red-900 text-xs mb-1">Gagal mengajukan penarikan:</p>
                                <ul class="list-disc pl-4 text-xs text-red-700 space-y-0.5 font-medium">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @php
                    $is24hBlocked = isset($limit24h) && !$limit24h['allowed'];
                    $canWithdraw = ($user->balance ?? 0) >= 10000 
                        && $user->hasVerifiedEmail() 
                        && $user->hasTransactionPin() 
                        && isset($bankAccounts) 
                        && $bankAccounts->isNotEmpty() 
                        && !$user->isPinLocked()
                        && !$is24hBlocked;
                @endphp

                <!-- Withdrawal Form Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <form id="withdraw-form" action="{{ route($reqRoute) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Amount Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-800">Jumlah Penarikan <span class="text-red-500">*</span></label>
                                @if(($user->balance ?? 0) >= 10000 && $user->hasVerifiedEmail())
                                    <button type="button" id="btn-tarik-semua" class="text-[11px] font-bold text-[#0066FF] hover:text-[#0052CC] hover:underline cursor-pointer">
                                        Tarik Semua (Rp {{ number_format($user->balance, 0, ',', '.') }})
                                    </button>
                                @endif
                            </div>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-extrabold text-xs">Rp</span>
                                <input type="text" inputmode="numeric" name="amount" id="amount-input" 
                                    oninput="let raw = this.value.replace(/\D/g, ''); this.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';"
                                    value="{{ old('amount') ? number_format((int) preg_replace('/\D/', '', old('amount')), 0, ',', '.') : '' }}" required
                                    @if(!$canWithdraw) disabled @endif
                                    class="pl-10 w-full px-3.5 py-3 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed placeholder:font-normal placeholder:text-xs" 
                                    placeholder="{{ !$user->hasVerifiedEmail() ? 'Verifikasi email untuk penarikan saldo' : (($user->balance ?? 0) < 10000 ? 'Saldo kurang dari Rp 10.000' : 'Contoh: 50.000') }}" />
                            </div>
                            <div id="amount-feedback" class="mt-1.5 text-[11px] text-slate-500">
                                @if(!$user->hasVerifiedEmail())
                                    <span class="text-amber-600 font-medium">Verifikasi email diperlukan untuk penarikan saldo</span>
                                @elseif($is24hBlocked)
                                    <span class="text-amber-600 font-semibold">Penarikan dibatasi 1x per 24 jam (Sisa {{ $limit24h['remaining_text'] ?? '' }})</span>
                                @else
                                    Minimum penarikan adalah <span class="font-bold text-slate-700">Rp 10.000</span>
                                @endif
                            </div>
                        </div>

                        <!-- Bank Account Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-800">Rekening / E-Wallet Tujuan <span class="text-red-500">*</span></label>
                                <a href="{{ route($bankRoute) }}" wire:navigate class="text-[11px] font-bold text-[#0066FF] hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Kelola Rekening
                                </a>
                            </div>

                            @if(isset($bankAccounts) && $bankAccounts->count() > 0)
                                <div class="space-y-2">
                                    @foreach($bankAccounts as $acc)
                                        <label class="relative flex items-center justify-between p-3.5 rounded-2xl border cursor-pointer transition hover:bg-slate-50/80 has-[:checked]:border-[#0066FF] has-[:checked]:bg-blue-50/40 has-[:checked]:ring-1.5 has-[:checked]:ring-[#0066FF] border-slate-200 bg-white">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <input type="radio" name="bank_account_id" value="{{ $acc->id }}" 
                                                    data-bank-name="{{ $acc->bank_name }}"
                                                    data-account-number="{{ $acc->masked_account_number }}"
                                                    data-account-name="{{ $acc->account_name }}"
                                                    {{ (old('bank_account_id', $primaryAccount?->id) == $acc->id) ? 'checked' : '' }} required 
                                                    @if(!$canWithdraw) disabled @endif
                                                    class="text-[#0066FF] focus:ring-[#0066FF] w-4 h-4 shrink-0 cursor-pointer bank-radio">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $acc->is_primary ? 'bg-blue-100 text-[#0066FF]' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ strtoupper(substr($acc->bank_code, 0, 3)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-black text-slate-900 truncate">{{ $acc->bank_name }}</span>
                                                        @if($acc->is_primary)
                                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#0066FF] text-white shrink-0">Utama</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5 truncate">{{ $acc->masked_account_number }} a.n {{ $acc->account_name }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-slate-50 rounded-2xl text-center border border-dashed border-slate-200">
                                    <p class="text-xs text-slate-500 mb-2">Belum ada rekening atau e-wallet tersimpan</p>
                                    <a href="{{ route($bankRoute) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#0066FF] text-white rounded-xl text-xs font-bold shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Rekening Baru
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Hidden PIN Input yang diisi dari Modal PIN Terpisah -->
                        <input type="hidden" name="pin" id="hidden-pin-input" />

                        <!-- Action Buttons -->
                        <div class="flex gap-2.5 pt-2">
                            <a href="{{ route($histRoute) }}" wire:navigate class="px-4 py-3 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition active:scale-95 flex items-center justify-center">
                                Riwayat
                            </a>
                            <button type="button" id="btn-submit-withdraw"
                                @if(!$canWithdraw) disabled @endif
                                class="flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-md transition-all text-white inline-flex items-center justify-center gap-1.5 {{ !$canWithdraw ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-[#0066FF] hover:bg-[#0052CC] active:scale-95 cursor-pointer' }}">
                                @if(!$user->hasVerifiedEmail())
                                    Verifikasi Email Terlebih Dahulu
                                @elseif(!$user->hasTransactionPin())
                                    Atur PIN Terlebih Dahulu
                                @elseif(isset($bankAccounts) && $bankAccounts->isEmpty())
                                    Tambah Rekening Terlebih Dahulu
                                @elseif($user->isPinLocked())
                                    PIN Terkunci
                                @elseif(($user->balance ?? 0) < 10000)
                                    Saldo Tidak Mencukupi
                                @elseif($is24hBlocked)
                                    Batas 1x24 Jam (Sisa {{ $limit24h['remaining_text'] ?? '' }})
                                @else
                                    <span>Lanjutkan Penarikan</span>
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Konfirmasi & Masukkan PIN (Terpisah) -->
    <div id="withdraw-pin-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div id="withdraw-pin-overlay" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

        <!-- Modal Dialog Box -->
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100 z-10 transform transition-all animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-6 pb-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#0066FF] flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-base font-black text-slate-900 tracking-tight">Konfirmasi & Masukkan PIN</h3>
                <p class="text-xs text-slate-500 mt-1">Masukkan 6 digit PIN transaksi untuk otorisasi penarikan saldo</p>
            </div>

            <!-- Ringkasan Singkat Penarikan -->
            <div class="px-6">
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Nominal Penarikan</span>
                        <span id="modal-summary-amount" class="font-extrabold text-slate-900 text-sm">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Rekening Tujuan</span>
                        <span id="modal-summary-bank" class="font-bold text-slate-800 text-right truncate max-w-[170px]">-</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/60">
                        <span class="text-slate-500 font-medium">Biaya Admin</span>
                        <span class="font-bold text-emerald-600">Gratis (Rp 0)</span>
                    </div>
                </div>
            </div>

            <!-- PIN Input Field & Toggle -->
            <div class="p-6 pt-5">
                <div class="mb-2">
                    <div class="relative">
                        <input type="password" inputmode="numeric" maxlength="6" id="modal-pin-input"
                            placeholder="••••••"
                            class="w-full text-center tracking-[0.7em] text-2xl font-black px-10 py-3.5 border-2 border-slate-200 rounded-2xl focus:border-[#0066FF] focus:ring-4 focus:ring-[#0066FF]/15 transition outline-none placeholder:font-normal placeholder:tracking-normal placeholder:text-sm placeholder:text-slate-300" />
                        <button type="button" id="modal-pin-toggle" class="absolute right-3.5 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 transition cursor-pointer" title="Lihat/Sembunyikan PIN">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Indikator 6 Digit Bullets (Visual Feedback) -->
                    <div class="flex items-center justify-center gap-2.5 mt-3" id="pin-dots-container">
                        <span class="w-3 h-3 rounded-full border-2 border-slate-300 transition-all duration-150 pin-dot" data-idx="0"></span>
                        <span class="w-3 h-3 rounded-full border-2 border-slate-300 transition-all duration-150 pin-dot" data-idx="1"></span>
                        <span class="w-3 h-3 rounded-full border-2 border-slate-300 transition-all duration-150 pin-dot" data-idx="2"></span>
                        <span class="w-3 h-3 rounded-full border-2 border-slate-300 transition-all duration-150 pin-dot" data-idx="3"></span>
                        <span class="w-3 h-3 rounded-full border-2 border-slate-300 transition-all duration-150 pin-dot" data-idx="4"></span>
                        <span class="w-3 h-3 rounded-full border-2 border-slate-300 transition-all duration-150 pin-dot" data-idx="5"></span>
                    </div>

                    <!-- Error message container -->
                    <div id="modal-pin-error" class="text-center mt-2.5 min-h-[18px]">
                        @if($errors->has('pin'))
                            <p class="text-xs font-bold text-red-600 animate-pulse">{{ $errors->first('pin') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center text-[11px] mb-5 px-1">
                    <span class="text-slate-400">Pastikan 6 digit sesuai</span>
                    <a href="{{ route($bankRoute, ['action' => 'reset_pin']) }}" wire:navigate class="text-[#0066FF] font-bold hover:underline">Lupa PIN?</a>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" id="btn-close-pin-modal"
                        class="w-full py-3 px-4 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="btn-confirm-pin" disabled
                        class="w-full py-3 px-4 rounded-xl bg-slate-300 text-slate-500 text-xs font-bold transition-all disabled:cursor-not-allowed active:scale-95 cursor-pointer shadow-md shadow-blue-500/20">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            function initWithdrawForm() {
                const isEmailVerified = {{ $user->hasVerifiedEmail() ? 'true' : 'false' }};
                const hasPin = {{ $user->hasTransactionPin() ? 'true' : 'false' }};
                const isPinLocked = {{ $user->isPinLocked() ? 'true' : 'false' }};
                const hasBankAccounts = {{ (isset($bankAccounts) && $bankAccounts->isNotEmpty()) ? 'true' : 'false' }};
                const is24hLimited = {{ (isset($limit24h) && !$limit24h['allowed']) ? 'true' : 'false' }};
                const userBalance = {{ (int) ($user->balance ?? 0) }};
                const amountInput = document.getElementById('amount-input');
                const feedback = document.getElementById('amount-feedback');
                const submitBtn = document.getElementById('btn-submit-withdraw');
                const btnTarikSemua = document.getElementById('btn-tarik-semua');
                const withdrawForm = document.getElementById('withdraw-form');

                // Elements Modal PIN
                const pinModal = document.getElementById('withdraw-pin-modal');
                const pinOverlay = document.getElementById('withdraw-pin-overlay');
                const btnClosePinModal = document.getElementById('btn-close-pin-modal');
                const btnConfirmPin = document.getElementById('btn-confirm-pin');
                const modalPinInput = document.getElementById('modal-pin-input');
                const modalPinToggle = document.getElementById('modal-pin-toggle');
                const hiddenPinInput = document.getElementById('hidden-pin-input');
                const summaryAmount = document.getElementById('modal-summary-amount');
                const summaryBank = document.getElementById('modal-summary-bank');
                const pinDots = document.querySelectorAll('.pin-dot');

                if (btnTarikSemua && amountInput && isEmailVerified && hasPin && hasBankAccounts && !isPinLocked && !is24hLimited) {
                    btnTarikSemua.addEventListener('click', function() {
                        amountInput.value = userBalance ? userBalance.toLocaleString('id-ID') : '0';
                        validateAmount();
                    });
                }

                function validateAmount() {
                    if (!amountInput || !feedback) return false;

                    if (!isEmailVerified) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Anda harus memverifikasi email terlebih dahulu</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'Verifikasi Email Terlebih Dahulu';
                        }
                        return false;
                    }

                    if (!hasPin) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Atur PIN transaksi 6 digit terlebih dahulu</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'Atur PIN Terlebih Dahulu';
                        }
                        return false;
                    }

                    if (!hasBankAccounts) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Tambahkan rekening bank / e-wallet terlebih dahulu</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'Tambah Rekening Terlebih Dahulu';
                        }
                        return false;
                    }

                    if (isPinLocked) {
                        feedback.innerHTML = '<span class="text-red-600 font-semibold">PIN sedang terkunci karena 3x salah</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'PIN Terkunci';
                        }
                        return false;
                    }

                    if (is24hLimited) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Penarikan dibatasi 1x per 24 jam</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-amber-800 bg-amber-100 border border-amber-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'Batas 1x24 Jam (Sisa {{ $limit24h["remaining_text"] ?? "" }})';
                        }
                        return false;
                    }

                    const val = parseInt(amountInput.value.replace(/\D/g, '')) || 0;

                    if (userBalance < 10000) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Saldo tidak mencukupi (minimal Rp 10.000)</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'Saldo Tidak Mencukupi';
                        }
                        return false;
                    }

                    if (val > userBalance) {
                        feedback.innerHTML = `<span class="text-red-600 font-semibold">Melebihi saldo tersedia (Maksimal Rp ${userBalance.toLocaleString('id-ID')})</span>`;
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-red-400 cursor-not-allowed';
                            submitBtn.innerHTML = 'Nominal Melebihi Saldo';
                        }
                        return false;
                    } else if (val > 0 && val < 10000) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Minimal penarikan adalah Rp 10.000</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.innerHTML = 'Minimal Rp 10.000';
                        }
                        return false;
                    } else if (val >= 10000) {
                        feedback.innerHTML = `<span class="text-emerald-600 font-semibold">✓ Nominal valid: Rp ${val.toLocaleString('id-ID')}</span>`;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-md transition-all text-white bg-[#0066FF] hover:bg-[#0052CC] active:scale-95 cursor-pointer inline-flex items-center justify-center gap-1.5';
                            submitBtn.innerHTML = '<span>Lanjutkan Penarikan</span><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
                        }
                        return true;
                    } else {
                        feedback.innerHTML = 'Minimum penarikan adalah <span class="font-bold text-slate-700">Rp 10.000</span>';
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-md transition-all text-white bg-[#0066FF] hover:bg-[#0052CC] active:scale-95 cursor-pointer inline-flex items-center justify-center gap-1.5';
                            submitBtn.innerHTML = '<span>Lanjutkan Penarikan</span><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
                        }
                        return true;
                    }
                }

                if (amountInput) {
                    amountInput.addEventListener('input', function() {
                        let raw = this.value.replace(/\D/g, '');
                        this.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
                        validateAmount();
                    });
                    amountInput.addEventListener('change', validateAmount);

                    if (amountInput.value) {
                        let raw = amountInput.value.replace(/\D/g, '');
                        if (raw) {
                            amountInput.value = parseInt(raw, 10).toLocaleString('id-ID');
                        }
                        validateAmount();
                    }
                }

                // Fungsi Pembaruan Titik Visual PIN
                function updatePinDots(val) {
                    if (!pinDots) return;
                    pinDots.forEach((dot, idx) => {
                        if (idx < val.length) {
                            dot.className = 'w-3.5 h-3.5 rounded-full bg-[#0066FF] border-2 border-[#0066FF] scale-110 transition-all duration-150 pin-dot';
                        } else {
                            dot.className = 'w-3 h-3 rounded-full bg-transparent border-2 border-slate-300 scale-100 transition-all duration-150 pin-dot';
                        }
                    });
                }

                function openPinModal() {
                    if (!pinModal) return;
                    
                    // Isi data ringkasan ke dalam modal
                    if (summaryAmount && amountInput) {
                        summaryAmount.textContent = 'Rp ' + (amountInput.value || '0');
                    }
                    
                    const selectedBank = document.querySelector('input[name="bank_account_id"]:checked');
                    if (summaryBank && selectedBank) {
                        const bankName = selectedBank.dataset.bankName || 'Bank';
                        const accNumber = selectedBank.dataset.accountNumber || '';
                        const accName = selectedBank.dataset.accountName || '';
                        summaryBank.textContent = `${bankName} • ${accNumber}${accName ? ' (' + accName + ')' : ''}`;
                    }

                    if (modalPinInput) {
                        modalPinInput.value = '';
                        updatePinDots('');
                    }

                    if (btnConfirmPin) {
                        btnConfirmPin.disabled = true;
                        btnConfirmPin.className = 'w-full py-3 px-4 rounded-xl bg-slate-300 text-slate-500 text-xs font-bold transition-all disabled:cursor-not-allowed cursor-not-allowed';
                        btnConfirmPin.textContent = 'Konfirmasi';
                    }

                    pinModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        if (modalPinInput) modalPinInput.focus();
                    }, 120);
                }

                function closePinModal() {
                    if (!pinModal) return;
                    pinModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    if (modalPinInput) {
                        modalPinInput.value = '';
                        updatePinDots('');
                    }
                }

                function submitWithdrawWithPin() {
                    if (!modalPinInput || !withdrawForm) return;
                    const pin = modalPinInput.value.trim();
                    if (pin.length !== 6) return;

                    if (hiddenPinInput) {
                        hiddenPinInput.value = pin;
                    }

                    if (btnConfirmPin) {
                        btnConfirmPin.disabled = true;
                        btnConfirmPin.innerHTML = '<span class="inline-flex items-center gap-1.5 justify-center"><svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memverifikasi...</span>';
                    }

                    if (amountInput) {
                        amountInput.value = amountInput.value.replace(/\D/g, '');
                    }

                    withdrawForm.submit();
                }

                // Event Listener Tombol "Lanjutkan Penarikan"
                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (!validateAmount()) {
                            if (amountInput) amountInput.focus();
                            return;
                        }

                        const selectedBank = document.querySelector('input[name="bank_account_id"]:checked');
                        if (!selectedBank) {
                            if (feedback) {
                                feedback.innerHTML = '<span class="text-amber-600 font-semibold">Pilih salah satu rekening tujuan terlebih dahulu</span>';
                            }
                            return;
                        }

                        openPinModal();
                    });
                }

                // Event Listener di dalam Modal PIN
                if (btnClosePinModal) btnClosePinModal.addEventListener('click', closePinModal);
                if (pinOverlay) pinOverlay.addEventListener('click', closePinModal);

                if (modalPinToggle && modalPinInput) {
                    modalPinToggle.addEventListener('click', function() {
                        modalPinInput.type = modalPinInput.type === 'password' ? 'text' : 'password';
                    });
                }

                if (modalPinInput) {
                    modalPinInput.addEventListener('input', function() {
                        let val = this.value.replace(/\D/g, '').slice(0, 6);
                        this.value = val;
                        updatePinDots(val);

                        if (btnConfirmPin) {
                            if (val.length === 6) {
                                btnConfirmPin.disabled = false;
                                btnConfirmPin.className = 'w-full py-3 px-4 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all active:scale-95 cursor-pointer';
                                btnConfirmPin.textContent = 'Konfirmasi & Tarik Saldo';
                                const err = document.getElementById('modal-pin-error');
                                if (err) err.innerHTML = '';
                            } else {
                                btnConfirmPin.disabled = true;
                                btnConfirmPin.className = 'w-full py-3 px-4 rounded-xl bg-slate-300 text-slate-500 text-xs font-bold transition-all disabled:cursor-not-allowed cursor-not-allowed';
                                btnConfirmPin.textContent = 'Konfirmasi';
                            }
                        }
                    });

                    modalPinInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && this.value.length === 6) {
                            e.preventDefault();
                            submitWithdrawWithPin();
                        } else if (e.key === 'Escape') {
                            closePinModal();
                        }
                    });
                }

                if (btnConfirmPin) {
                    btnConfirmPin.addEventListener('click', function() {
                        submitWithdrawWithPin();
                    });
                }

                // Otomatis buka modal PIN jika ada error verifikasi PIN dari backend
                @if($errors->has('pin'))
                    setTimeout(function() {
                        openPinModal();
                    }, 150);
                @endif
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initWithdrawForm);
            } else {
                initWithdrawForm();
            }
        })();
    </script>
@endsection
