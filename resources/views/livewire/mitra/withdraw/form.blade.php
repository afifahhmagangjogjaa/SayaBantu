@extends('layouts.mitra')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-md mx-auto">
            <!-- Header (consistent with other pages) -->
            <div class="px-5 pt-4 pb-8 relative overflow-hidden" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
                <div class="absolute top-0 right-0 w-28 h-28 bg-white/5 rounded-full -mr-12 -mt-12"></div>
                <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full -ml-8 -mb-8"></div>

                <div class="relative z-10 max-w-md mx-auto">
                    <div class="flex items-center justify-between text-white mb-4">
                        <button onclick="window.history.back()" aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <div class="text-center flex-1 px-2">
                            <h1 class="text-base font-semibold">Tarik Saldo</h1>
                            <p class="text-xs text-white/90 mt-0.5">Ajukan penarikan dana dengan mudah</p>
                        </div>

                        <a href="{{ route('mitra.withdraw.history') }}" class="p-2 hover:bg-white/20 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Curved separator (reduced) -->
                <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 56" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0,24 C360,56 1080,0 1440,32 L1440,56 L0,56 Z" fill="#f9fafb"></path>
                </svg>
            </div>

            <!-- Content -->
            <div class="bg-gray-50 -mt-6 px-5 pt-6 pb-6">
                <!-- Balance Card -->
                <div class="mb-5 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-90 mb-1">Saldo Tersedia</p>
                            <p class="text-2xl font-bold">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-white/20">
                        <p class="text-xs opacity-75">Minimum penarikan: <span class="font-semibold">Rp 10.000</span></p>
                    </div>
                </div>

        <!-- Status Messages -->
        @if(session('status'))
            <div class="mb-5 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 text-blue-800 rounded-lg shadow-sm flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium text-xs">{{ session('status') }}</span>
            </div>
        @endif

        @if($user->hasPendingOrProcessingWithdraws())
            <!-- Pending Withdrawal Notice -->
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-2">Pengajuan Sedang Diproses</h3>
                <p class="text-xs text-gray-600 mb-6 leading-relaxed">
                    Pengajuan tarik saldo Anda sedang diproses oleh admin. Mohon tunggu 1-5 hari kerja. 
                    Anda akan menerima notifikasi ketika status berubah.
                </p>
                <a href="{{ route('mitra.withdraw.history') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lihat Riwayat
                </a>
            </div>
        @else
            @if(($user->balance ?? 0) < 10000)
                <!-- Saldo Kurang Notice -->
                <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 shadow-2xs">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="font-bold text-xs text-amber-950">Saldo Belum Mencukupi</h4>
                            <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                Saldo Anda saat ini adalah <strong>Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</strong>. Minimal saldo untuk melakukan penarikan dana adalah <strong>Rp 10.000</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl shadow-2xs">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-bold text-red-800 text-xs mb-1">Gagal mengajukan penarikan:</p>
                            <ul class="list-disc pl-5 text-xs text-red-700 space-y-0.5 font-medium">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Withdrawal Form -->
            <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                <form id="withdraw-form" action="{{ route('mitra.withdraw.request') }}" method="POST">
                    @csrf
                    
                    <!-- Amount Input -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-gray-700">Jumlah Penarikan</label>
                            @if(($user->balance ?? 0) >= 10000)
                                <button type="button" id="btn-tarik-semua" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 underline">
                                    Tarik Semua (Rp {{ number_format($user->balance, 0, ',', '.') }})
                                </button>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-xs">Rp</span>
                            <input type="number" name="amount" id="amount-input" min="10000" max="{{ (int) ($user->balance ?? 0) }}" value="{{ old('amount') }}" required
                                @if(($user->balance ?? 0) < 10000) disabled @endif
                                class="pl-10 w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs font-semibold text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed" 
                                placeholder="{{ ($user->balance ?? 0) < 10000 ? 'Saldo kurang dari Rp 10.000' : 'Contoh: 50000' }}" />
                        </div>
                        <div id="amount-feedback" class="mt-1.5 text-xs text-gray-500">
                            Minimum penarikan adalah <span class="font-semibold">Rp 10.000</span>
                        </div>
                    </div>

                    <!-- Bank Code Input -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Bank / E-Wallet</label>
                        <select name="bank_code" required
                            @if(($user->balance ?? 0) < 10000) disabled @endif
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all bg-white disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                            <option value="">-- Pilih Bank / E-Wallet --</option>
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
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor Rekening / Akun E-Wallet</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" required
                            @if(($user->balance ?? 0) < 10000) disabled @endif
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-xs text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                            placeholder="Masukkan nomor rekening atau no HP e-wallet" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <a href="{{ route('mitra.withdraw.history') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-50 transition-all flex items-center justify-center">
                            Riwayat
                        </a>
                        <button type="submit" id="btn-submit-withdraw"
                            @if(($user->balance ?? 0) < 10000) disabled @endif
                            class="flex-1 px-4 py-2.5 text-xs font-bold rounded-xl shadow-sm transition-all text-white {{ ($user->balance ?? 0) < 10000 ? 'bg-gray-300 cursor-not-allowed text-gray-500' : 'bg-blue-600 hover:bg-blue-700' }}">
                            {{ ($user->balance ?? 0) < 10000 ? 'Saldo Tidak Mencukupi' : 'Ajukan Penarikan' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userBalance = {{ (int) ($user->balance ?? 0) }};
            const amountInput = document.getElementById('amount-input');
            const feedback = document.getElementById('amount-feedback');
            const submitBtn = document.getElementById('btn-submit-withdraw');
            const btnTarikSemua = document.getElementById('btn-tarik-semua');

            if (btnTarikSemua && amountInput) {
                btnTarikSemua.addEventListener('click', function() {
                    amountInput.value = userBalance;
                    validateAmount();
                });
            }

            function validateAmount() {
                if (!amountInput || !feedback) return;
                const val = parseInt(amountInput.value) || 0;

                if (userBalance < 10000) {
                    feedback.innerHTML = '<span class="text-amber-600 font-semibold">⚠️ Saldo tidak mencukupi (minimal Rp 10.000)</span>';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.className = 'flex-1 px-4 py-2.5 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-gray-300 cursor-not-allowed text-gray-500';
                        submitBtn.textContent = 'Saldo Tidak Mencukupi';
                    }
                    return false;
                }

                if (val > userBalance) {
                    feedback.innerHTML = `<span class="text-red-600 font-semibold">❌ Melebihi saldo tersedia (Maksimal Rp ${userBalance.toLocaleString('id-ID')})</span>`;
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.className = 'flex-1 px-4 py-2.5 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-red-300 cursor-not-allowed';
                        submitBtn.textContent = 'Nominal Melebihi Saldo';
                    }
                    return false;
                } else if (val > 0 && val < 10000) {
                    feedback.innerHTML = '<span class="text-amber-600 font-semibold">⚠️ Minimal penarikan adalah Rp 10.000</span>';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.className = 'flex-1 px-4 py-2.5 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-gray-300 cursor-not-allowed text-gray-500';
                        submitBtn.textContent = 'Minimal Rp 10.000';
                    }
                    return false;
                } else if (val >= 10000) {
                    feedback.innerHTML = `<span class="text-green-600 font-semibold">✓ Nominal valid: Rp ${val.toLocaleString('id-ID')}</span>`;
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.className = 'flex-1 px-4 py-2.5 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-blue-600 hover:bg-blue-700 cursor-pointer';
                        submitBtn.textContent = 'Ajukan Penarikan';
                    }
                    return true;
                } else {
                    feedback.innerHTML = 'Minimum penarikan adalah <span class="font-semibold">Rp 10.000</span>';
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.className = 'flex-1 px-4 py-2.5 text-xs font-bold rounded-xl shadow-sm transition-all text-white bg-blue-600 hover:bg-blue-700 cursor-pointer';
                        submitBtn.textContent = 'Ajukan Penarikan';
                    }
                    return true;
                }
            }

            if (amountInput) {
                amountInput.addEventListener('input', validateAmount);
                amountInput.addEventListener('change', validateAmount);
            }
        });
    </script>
    @endpush
@endsection