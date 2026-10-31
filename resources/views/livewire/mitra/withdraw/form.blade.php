@extends('layouts.mitra')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-20">
        <!-- Header - Brand Blue with Wave Separator -->
        <div class="px-5 pt-6 pb-20 relative overflow-hidden text-white bg-[#0066FF]">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <a href="{{ route('mitra.dashboard') }}" wire:navigate aria-label="Kembali ke Dashboard" class="p-2 hover:bg-white/20 rounded-xl transition active:scale-95 cursor-pointer inline-flex items-center">
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

            <!-- Wave Separator (SVG) bergelombang -->
            <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <!-- Saldo Mitra Card (Overlapping Wavy Header) -->
        <div class="px-5 -mt-14 relative z-10 mb-5">
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
        <div class="px-5 space-y-4">
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
                    <a href="{{ route('mitra.withdraw.history') }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold rounded-xl transition shadow-md active:scale-95">
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

                <!-- Withdrawal Form Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <form id="withdraw-form" action="{{ route('mitra.withdraw.request') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Amount Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-800">Jumlah Penarikan</label>
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
                                    @if(($user->balance ?? 0) < 10000 || !$user->hasVerifiedEmail()) disabled @endif
                                    class="pl-10 w-full px-3.5 py-3 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed placeholder:font-normal placeholder:text-xs" 
                                    placeholder="{{ !$user->hasVerifiedEmail() ? 'Verifikasi email untuk penarikan saldo' : (($user->balance ?? 0) < 10000 ? 'Saldo kurang dari Rp 10.000' : 'Contoh: 50.000') }}" />
                            </div>
                            <div id="amount-feedback" class="mt-1.5 text-[11px] text-slate-500">
                                @if(!$user->hasVerifiedEmail())
                                    <span class="text-amber-600 font-medium">Verifikasi email diperlukan untuk penarikan saldo</span>
                                @else
                                    Minimum penarikan adalah <span class="font-bold text-slate-700">Rp 10.000</span>
                                @endif
                            </div>
                        </div>

                        <!-- Bank Code Input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1.5">Bank / E-Wallet Tujuan</label>
                            <select name="bank_code" required
                                @if(($user->balance ?? 0) < 10000 || !$user->hasVerifiedEmail()) disabled @endif
                                class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all bg-white disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">
                                <option value="">Pilih Bank / E-Wallet</option>
                                <optgroup label="Bank Populer">
                                    <option value="BCA" {{ old('bank_code') === 'BCA' ? 'selected' : '' }}>BCA (Bank Central Asia)</option>
                                    <option value="BRI" {{ old('bank_code') === 'BRI' ? 'selected' : '' }}>BRI (Bank Rakyat Indonesia)</option>
                                    <option value="BNI" {{ old('bank_code') === 'BNI' ? 'selected' : '' }}>BNI (Bank Negara Indonesia)</option>
                                    <option value="Mandiri" {{ old('bank_code') === 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                                    <option value="BSI" {{ old('bank_code') === 'BSI' ? 'selected' : '' }}>BSI (Bank Syariah Indonesia)</option>
                                </optgroup>
                                <optgroup label="Bank Lainnya">
                                    <option value="CIMB Niaga" {{ old('bank_code') === 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                                    <option value="Permata" {{ old('bank_code') === 'Permata' ? 'selected' : '' }}>Bank Permata</option>
                                    <option value="Danamon" {{ old('bank_code') === 'Danamon' ? 'selected' : '' }}>Bank Danamon</option>
                                    <option value="BTN" {{ old('bank_code') === 'BTN' ? 'selected' : '' }}>Bank BTN</option>
                                    <option value="Panin" {{ old('bank_code') === 'Panin' ? 'selected' : '' }}>Bank Panin</option>
                                    <option value="OCBC NISP" {{ old('bank_code') === 'OCBC NISP' ? 'selected' : '' }}>OCBC NISP</option>
                                    <option value="BTPN / Jenius" {{ old('bank_code') === 'BTPN / Jenius' ? 'selected' : '' }}>BTPN / Jenius</option>
                                    <option value="Bank Mega" {{ old('bank_code') === 'Bank Mega' ? 'selected' : '' }}>Bank Mega</option>
                                    <option value="Bank Jago" {{ old('bank_code') === 'Bank Jago' ? 'selected' : '' }}>Bank Jago</option>
                                    <option value="SeaBank" {{ old('bank_code') === 'SeaBank' ? 'selected' : '' }}>SeaBank</option>
                                    <option value="Allo Bank" {{ old('bank_code') === 'Allo Bank' ? 'selected' : '' }}>Allo Bank</option>
                                </optgroup>
                                <optgroup label="E-Wallet">
                                    <option value="DANA" {{ old('bank_code') === 'DANA' ? 'selected' : '' }}>DANA</option>
                                    <option value="OVO" {{ old('bank_code') === 'OVO' ? 'selected' : '' }}>OVO</option>
                                    <option value="GoPay" {{ old('bank_code') === 'GoPay' ? 'selected' : '' }}>GoPay</option>
                                    <option value="ShopeePay" {{ old('bank_code') === 'ShopeePay' ? 'selected' : '' }}>ShopeePay</option>
                                    <option value="LinkAja" {{ old('bank_code') === 'LinkAja' ? 'selected' : '' }}>LinkAja</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Account Number Input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1.5">Nomor Rekening / No. HP E-Wallet</label>
                            <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" required
                                inputmode="numeric"
                                pattern="[0-9]*"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                @if(($user->balance ?? 0) < 10000 || !$user->hasVerifiedEmail()) disabled @endif
                                class="w-full px-3.5 py-3 border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition-all disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed placeholder:text-slate-400"
                                placeholder="Masukkan nomor rekening atau nomor HP e-wallet" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2.5 pt-2">
                            <a href="{{ route('mitra.withdraw.history') }}" wire:navigate class="px-4 py-3 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition active:scale-95 flex items-center justify-center">
                                Riwayat
                            </a>
                            <button type="submit" id="btn-submit-withdraw"
                                @if(($user->balance ?? 0) < 10000 || !$user->hasVerifiedEmail()) disabled @endif
                                class="flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-md transition-all text-white {{ (($user->balance ?? 0) < 10000 || !$user->hasVerifiedEmail()) ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-[#0066FF] hover:bg-[#0052CC] active:scale-95' }}">
                                @if(!$user->hasVerifiedEmail())
                                    Verifikasi Email Terlebih Dahulu
                                @elseif(($user->balance ?? 0) < 10000)
                                    Saldo Tidak Mencukupi
                                @else
                                    Ajukan Penarikan
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        (function() {
            function initWithdrawForm() {
                const isEmailVerified = {{ $user->hasVerifiedEmail() ? 'true' : 'false' }};
                const userBalance = {{ (int) ($user->balance ?? 0) }};
                const amountInput = document.getElementById('amount-input');
                const feedback = document.getElementById('amount-feedback');
                const submitBtn = document.getElementById('btn-submit-withdraw');
                const btnTarikSemua = document.getElementById('btn-tarik-semua');

                if (btnTarikSemua && amountInput && isEmailVerified) {
                    btnTarikSemua.addEventListener('click', function() {
                        amountInput.value = userBalance ? userBalance.toLocaleString('id-ID') : '0';
                        validateAmount();
                    });
                }

                function validateAmount() {
                    if (!amountInput || !feedback) return;

                    if (!isEmailVerified) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Anda harus memverifikasi email terlebih dahulu</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.textContent = 'Verifikasi Email Terlebih Dahulu';
                        }
                        return false;
                    }

                    const val = parseInt(amountInput.value.replace(/\D/g, '')) || 0;

                    if (userBalance < 10000) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Saldo tidak mencukupi (minimal Rp 10.000)</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.textContent = 'Saldo Tidak Mencukupi';
                        }
                        return false;
                    }

                    if (val > userBalance) {
                        feedback.innerHTML = `<span class="text-red-600 font-semibold">Melebihi saldo tersedia (Maksimal Rp ${userBalance.toLocaleString('id-ID')})</span>`;
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-red-400 cursor-not-allowed';
                            submitBtn.textContent = 'Nominal Melebihi Saldo';
                        }
                        return false;
                    } else if (val > 0 && val < 10000) {
                        feedback.innerHTML = '<span class="text-amber-600 font-semibold">Minimal penarikan adalah Rp 10.000</span>';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-slate-500 bg-slate-300 cursor-not-allowed';
                            submitBtn.textContent = 'Minimal Rp 10.000';
                        }
                        return false;
                    } else if (val >= 10000) {
                        feedback.innerHTML = `<span class="text-emerald-600 font-semibold">✓ Nominal valid: Rp ${val.toLocaleString('id-ID')}</span>`;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-md transition-all text-white bg-[#0066FF] hover:bg-[#0052CC] active:scale-95 cursor-pointer';
                            submitBtn.textContent = 'Ajukan Penarikan';
                        }
                        return true;
                    } else {
                        feedback.innerHTML = 'Minimum penarikan adalah <span class="font-bold text-slate-700">Rp 10.000</span>';
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.className = 'flex-1 px-4 py-3 text-xs font-bold rounded-xl shadow-md transition-all text-white bg-[#0066FF] hover:bg-[#0052CC] active:scale-95 cursor-pointer';
                            submitBtn.textContent = 'Ajukan Penarikan';
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

                const withdrawForm = document.getElementById('withdraw-form');
                const accountInput = document.getElementById('account_number') || document.querySelector('input[name="account_number"]');

                if (accountInput) {
                    accountInput.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                    accountInput.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const pasteData = (e.clipboardData || window.clipboardData).getData('text');
                        const cleanDigits = pasteData.replace(/[^0-9]/g, '');
                        document.execCommand('insertText', false, cleanDigits);
                    });
                }

                if (withdrawForm) {
                    withdrawForm.addEventListener('submit', function() {
                        if (amountInput) {
                            amountInput.value = amountInput.value.replace(/\D/g, '');
                        }
                        if (accountInput) {
                            accountInput.value = accountInput.value.replace(/[^0-9]/g, '');
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initWithdrawForm);
            } else {
                initWithdrawForm();
            }
        })();
    </script>
@endsection
