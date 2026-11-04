<div class="min-h-screen bg-slate-50/70 pb-20">
    <!-- Header - Brand Blue with Wave Separator -->
    <div class="relative bg-[#0066FF] text-white">
        <!-- Decorative circle overlays in safe container -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16"></div>
        </div>

        <div class="relative z-10 px-5 pt-6 pb-12">
            <div class="flex items-center justify-between text-white">
                <a href="{{ ($user && $user->role === 'customer') ? route('customer.dashboard') : route('mitra.settings') }}" wire:navigate aria-label="Kembali"
                    class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">Rekening & PIN</h1>
                    <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola rekening penarikan & keamanan PIN</p>
                </div>

                <a href="{{ ($user && $user->role === 'customer') ? route('customer.withdraw.form') : route('mitra.withdraw.form') }}" wire:navigate aria-label="Tarik Saldo"
                    class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer" title="Tarik Saldo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Wave Separator (SVG) bergelombang -->
        <svg class="absolute -bottom-1 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 28px; bottom: -2px;">
            <path d="M0,28 C360,76 1080,-4 1440,36 L1440,84 L0,84 Z" fill="#f8fafc"></path>
        </svg>
    </div>

    <div class="max-w-md mx-auto px-5 pt-3 space-y-4">
        <!-- Notification Alert -->
        @if($feedbackMessage)
            <div class="p-4 rounded-2xl shadow-2xs flex items-start gap-3 transition-all duration-300 {{ $feedbackType === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-blue-50 border border-blue-200 text-blue-900' }}">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 {{ $feedbackType === 'success' ? 'bg-emerald-500/20 text-emerald-600' : 'bg-blue-500/20 text-[#0066FF]' }}">
                    @if($feedbackType === 'success')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-xs leading-relaxed">{{ $feedbackMessage }}</p>
                </div>
                <button type="button" wire:click="$set('feedbackMessage', null)" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- 1. Security PIN Card -->
        <div class="bg-white rounded-3xl p-5 shadow-2xs border border-slate-200/80 relative overflow-hidden">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 {{ $user->hasTransactionPin() ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-extrabold text-slate-900">PIN Transaksi</h2>
                            @if($user->hasTransactionPin())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    Belum Diatur
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                            {{ $user->hasTransactionPin() ? '6 digit PIN untuk otorisasi penarikan dana' : 'Buat PIN 6 digit untuk mengamankan saldo Anda' }}
                        </p>
                    </div>
                </div>

                @if($user->hasTransactionPin())
                    <button type="button" wire:click="openPinModal('change')"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition active:scale-95 cursor-pointer shrink-0">
                        Ubah PIN
                    </button>
                @else
                    <button type="button" wire:click="openPinModal('setup')"
                        class="px-3.5 py-1.5 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold shadow-md shadow-blue-500/20 transition active:scale-95 cursor-pointer shrink-0">
                        Buat PIN
                    </button>
                @endif
            </div>

            @if($user->isPinLocked())
                <div class="mt-3.5 p-3 rounded-2xl bg-red-50 border border-red-200 flex items-center justify-between gap-2.5 text-xs text-red-800 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>PIN terkunci karena 3x salah (sisa {{ $user->getPinLockRemainingMinutes() }} mnt).</span>
                    </div>
                    <button type="button" wire:click="openPinModal('reset')" class="text-xs font-bold text-red-700 hover:text-red-900 underline whitespace-nowrap cursor-pointer">
                        Reset PIN
                    </button>
                </div>
            @endif
        </div>

        <!-- 2. Bank Accounts Section Header -->
        <div class="flex items-center justify-between pt-1">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Rekening & E-Wallet</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Daftar akun tujuan pencairan saldo yang terdaftar</p>
            </div>
            <button type="button" wire:click="openAddModal"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-500/20 transition active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Akun</span>
            </button>
        </div>

        <!-- 3. List of Saved Accounts -->
        @if($accounts->count() > 0)
            <div class="space-y-3">
                @foreach($accounts as $acc)
                    <div class="bg-white rounded-3xl p-5 shadow-2xs border {{ $acc->is_primary ? 'border-blue-400 bg-gradient-to-br from-blue-50/30 to-white ring-1 ring-blue-500/20' : 'border-slate-200/80' }} transition relative overflow-hidden">
                        <!-- Top Row: Bank Badge & Primary Status -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xs shrink-0 {{ $acc->is_primary ? 'bg-[#0066FF] text-white shadow-md shadow-blue-500/25' : 'bg-slate-100 text-slate-700' }}">
                                    {{ strtoupper(substr($acc->bank_code, 0, 3)) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 leading-tight">{{ $acc->bank_name }}</h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">{{ $acc->account_name }}</p>
                                </div>
                            </div>

                            @if($acc->is_primary)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#0066FF] text-white shadow-2xs">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Rekening Utama
                                </span>
                            @else
                                <button type="button" wire:click="setPrimary({{ $acc->id }})"
                                    class="text-[11px] font-bold text-[#0066FF] hover:text-[#0052CC] hover:underline cursor-pointer">
                                    Jadikan Utama
                                </button>
                            @endif
                        </div>

                        <!-- Account Number Row -->
                        <div class="bg-slate-50 rounded-2xl px-3.5 py-2.5 flex items-center justify-between border border-slate-100">
                            <span class="text-xs font-mono font-extrabold text-slate-800 tracking-wider">
                                {{ $acc->masked_account_number }}
                            </span>
                            <button type="button" wire:click="confirmDelete({{ $acc->id }})"
                                class="text-slate-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition cursor-pointer" title="Hapus Akun">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-3xl p-8 text-center shadow-2xs border border-slate-200/80">
                <div class="w-16 h-16 bg-blue-50 text-[#0066FF] rounded-3xl flex items-center justify-center mx-auto mb-3.5 border border-blue-100 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900 mb-1">Belum Ada Rekening Tersimpan</h3>
                <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                    Tambahkan rekening bank atau e-wallet Anda untuk mempermudah penarikan saldo tanpa harus input ulang setiap kali withdraw.
                </p>
                <button type="button" wire:click="openAddModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold rounded-2xl shadow-md shadow-blue-500/25 transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Rekening Sekarang</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Modal 1: Tambah Rekening Bank / E-Wallet -->
    @if($showAddModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all my-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#0066FF] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Tambah Akun Penarikan</h3>
                    </div>
                    <button type="button" wire:click="closeAddModal" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="saveAccount" class="space-y-3.5">
                    <!-- Bank Channel Select -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Bank / E-Wallet <span class="text-red-500">*</span></label>
                        <select wire:model="bank_channel_id" class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition bg-white text-slate-800">
                            <option value="">Pilih Saluran Aktif</option>
                            <optgroup label="Bank">
                                @foreach($channels->where('category', 'bank') as $ch)
                                    <option value="{{ $ch->id }}">{{ $ch->name }} ({{ $ch->code }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="E-Wallet">
                                @foreach($channels->where('category', 'ewallet') as $ch)
                                    <option value="{{ $ch->id }}">{{ $ch->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('bank_channel_id') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening / No. HP E-Wallet <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" wire:model="account_number"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            placeholder="Contoh: 1234567890 atau 08123456789"
                            class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition text-slate-800" />
                        @error('account_number') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Account Name (Locked to Mitra's Identity) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">Nama Pemilik Rekening</label>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                🔒 Terkunci Otomatis
                            </span>
                        </div>
                        <div class="relative">
                            <input type="text" wire:model="account_name" readonly
                                class="w-full px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-200 bg-slate-100 text-slate-700 cursor-not-allowed select-none focus:outline-none" />
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Nama dikunci sesuai identitas profil Mitra Anda untuk mencegah penarikan dana ke pihak ketiga.
                        </p>
                    </div>

                    <!-- Is Primary Checkbox -->
                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_primary_checkbox" wire:model="is_primary" class="w-4 h-4 rounded text-[#0066FF] focus:ring-[#0066FF] border-slate-300">
                        <label for="is_primary_checkbox" class="text-xs font-bold text-slate-700 cursor-pointer">
                            Jadikan sebagai Rekening Utama
                        </label>
                    </div>

                    <!-- Security PIN -->
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-900 mb-1">
                            Konfirmasi PIN Transaksi (6 Digit) <span class="text-red-500">*</span>
                        </label>
                        <input type="password" inputmode="numeric" maxlength="6" wire:model="account_pin"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                            placeholder="••••••"
                            class="w-full text-center tracking-[0.5em] text-base font-black px-3.5 py-2 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition" />
                        @error('account_pin') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-3">
                        <button type="button" wire:click="closeAddModal" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition active:scale-95 cursor-pointer">
                            Simpan Rekening
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal 2: Buat / Ubah PIN Transaksi -->
    @if($showPinModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all my-auto">
                <div class="text-center mb-4">
                    <div class="w-14 h-14 bg-blue-50 text-[#0066FF] rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100 shadow-2xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900">
                        @if($pinMode === 'setup')
                            Buat PIN Transaksi
                        @elseif($pinMode === 'reset')
                            Reset PIN Transaksi
                        @else
                            Ubah PIN Transaksi
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        @if($pinMode === 'setup')
                            Buat 6 digit PIN untuk keamanan penarikan dana dan rekening
                        @elseif($pinMode === 'reset')
                            Pilih metode verifikasi untuk membuat PIN baru
                        @else
                            Masukkan PIN saat ini dan buat 6 digit PIN baru
                        @endif
                    </p>
                </div>

                <form wire:submit="savePin" class="space-y-3.5">
                    @if($pinMode === 'change')
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700">PIN Saat Ini (6 Digit)</label>
                                <button type="button" wire:click="openPinModal('reset')" class="text-[11px] font-bold text-[#0066FF] hover:underline cursor-pointer">
                                    Lupa PIN?
                                </button>
                            </div>
                            <input type="password" inputmode="numeric" maxlength="6" wire:model="current_pin"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                                placeholder="••••••"
                                class="w-full text-center tracking-[0.5em] text-base font-black px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition" />
                            @error('current_pin') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @elseif($pinMode === 'reset')
                        <!-- Tab Pilihan Metode Reset: Password vs OTP Email -->
                        <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl gap-1">
                            <button type="button" wire:click="switchResetMethod('password')"
                                class="py-1.5 text-xs font-bold rounded-lg transition {{ $resetMethod === 'password' ? 'bg-white text-[#0066FF] shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                Kata Sandi Akun
                            </button>
                            <button type="button" wire:click="switchResetMethod('otp')"
                                class="py-1.5 text-xs font-bold rounded-lg transition {{ $resetMethod === 'otp' ? 'bg-white text-[#0066FF] shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                                Kode OTP Email
                            </button>
                        </div>

                        @if($resetMethod === 'password')
                            <div x-data="{ showPass: false }">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-slate-700">Kata Sandi Akun</label>
                                    <button type="button" wire:click="openPinModal('change')" class="text-[11px] font-bold text-[#0066FF] hover:underline cursor-pointer">
                                        Ingat PIN?
                                    </button>
                                </div>
                                <div class="relative">
                                    <input :type="showPass ? 'text' : 'password'" wire:model="account_password"
                                        placeholder="Masukkan kata sandi akun SayaBantu"
                                        class="w-full text-xs font-medium px-3.5 py-2.5 pr-10 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition outline-none" />
                                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPass" x-cloak style="display:none;" class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                                @error('account_password') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                                <div class="mt-1 flex justify-end">
                                    <button type="button" wire:click="switchResetMethod('otp')" class="text-[11px] font-bold text-slate-500 hover:text-[#0066FF] hover:underline cursor-pointer">
                                        Lupa kata sandi juga? Pakai OTP Email &rarr;
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Metode Reset via OTP Email -->
                            <div class="space-y-3">
                                <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-3.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="text-[10px] font-bold text-slate-500 block uppercase tracking-wider">Kirim OTP ke:</span>
                                            <span class="text-xs font-black text-[#0066FF] truncate block">{{ $this->getMaskedEmail() }}</span>
                                        </div>
                                        <button type="button" wire:click="sendOtp" wire:loading.attr="disabled"
                                            class="px-3 py-1.5 bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold rounded-xl transition shadow-xs active:scale-95 disabled:opacity-50 cursor-pointer shrink-0">
                                            <span wire:loading.remove wire:target="sendOtp">{{ $otpSent ? 'Kirim Ulang' : 'Kirim Kode' }}</span>
                                            <span wire:loading wire:target="sendOtp">Mengirim...</span>
                                        </button>
                                    </div>
                                    @if($otpSent)
                                        <p class="text-[11px] text-emerald-700 font-semibold mt-2 pt-2 border-t border-blue-200/60 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            <span>Kode OTP telah dikirim ke email Anda. Berlaku 10 menit.</span>
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">Kode OTP (6 Digit)</label>
                                        <button type="button" wire:click="switchResetMethod('password')" class="text-[11px] font-bold text-[#0066FF] hover:underline cursor-pointer">
                                            Pakai Sandi Akun
                                        </button>
                                    </div>
                                    <input type="text" inputmode="numeric" maxlength="6" wire:model="otp_code"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                                        placeholder="••••••"
                                        class="w-full text-center tracking-[0.5em] text-base font-black px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition placeholder:font-normal placeholder:tracking-normal placeholder:text-xs placeholder:text-slate-300" />
                                    @error('otp_code') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            {{ $pinMode === 'setup' ? 'PIN 6 Digit Baru' : 'PIN Baru (6 Digit)' }}
                        </label>
                        <input type="password" inputmode="numeric" maxlength="6" wire:model="new_pin"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                            placeholder="••••••"
                            class="w-full text-center tracking-[0.5em] text-base font-black px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition" />
                        @error('new_pin') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi PIN Baru</label>
                        <input type="password" inputmode="numeric" maxlength="6" wire:model="new_pin_confirmation"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                            placeholder="••••••"
                            class="w-full text-center tracking-[0.5em] text-base font-black px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition" />
                        @error('new_pin_confirmation') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" wire:click="closePinModal" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#0066FF] hover:bg-[#0052CC] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition active:scale-95 cursor-pointer">
                            {{ $pinMode === 'reset' ? 'Reset PIN' : 'Simpan PIN' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal 3: Hapus Rekening Confirmation -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all my-auto">
                <div class="text-center mb-4">
                    <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-red-100 shadow-2xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Hapus Rekening?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Masukkan PIN transaksi Anda untuk mengonfirmasi penghapusan rekening bank ini.
                    </p>
                </div>

                <form wire:submit="deleteAccount" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PIN Transaksi (6 Digit)</label>
                        <input type="password" inputmode="numeric" maxlength="6" wire:model="delete_pin"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                            placeholder="••••••"
                            class="w-full text-center tracking-[0.5em] text-base font-black px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 transition" />
                        @error('delete_pin') <span class="text-red-600 text-[11px] font-semibold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="button" wire:click="closeDeleteModal" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-red-600/25 transition active:scale-95 cursor-pointer">
                            Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

