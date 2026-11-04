<?php

use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    // Current step: 1 = Input Email, 2 = Input OTP, 3 = Buat Sandi Baru
    public int $step = 1;

    // Form inputs
    public string $email = '';
    public string $otp_code = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Status & Feedback
    public ?string $statusMessage = null;
    public bool $isSuccess = false;

    /**
     * Mount the component: pastikan sesi login lama bersih dan restore state jika ada.
     */
    public function mount(): void
    {
        if (Auth::check()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        // Restore step & email jika pengguna me-refresh halaman saat sedang di tahap OTP / Sandi Baru
        if (session()->has('forgot_step') && session()->has('forgot_email')) {
            $sessEmail = strtolower(trim((string) session('forgot_email')));
            $cachedOtp = Cache::get('password_reset_otp_' . md5($sessEmail));
            $isVerified = Cache::get('password_reset_verified_' . md5($sessEmail));

            if (session('forgot_step') === 2 && $cachedOtp) {
                $this->step = 2;
                $this->email = $sessEmail;
            } elseif (session('forgot_step') === 3 && $isVerified) {
                $this->step = 3;
                $this->email = $sessEmail;
            } else {
                session()->forget(['forgot_step', 'forgot_email']);
                $this->step = 1;
            }
        }
    }

    /**
     * Langkah 1: Kirim kode OTP 6 digit ke email terdaftar.
     */
    public function sendOtp(): void
    {
        $this->statusMessage = null;
        $this->resetErrorBag();

        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $normalizedEmail = strtolower(trim($this->email));
        $this->email = $normalizedEmail;
        $user = User::where('email', $normalizedEmail)->first();

        if (! $user) {
            $this->addError('email', "Alamat email '{$this->email}' tidak terdaftar di sistem. Pastikan menggunakan email akun SayaBantu Anda.");
            return;
        }

        // Throttle check: jeda minimal 60 detik sebelum kirim ulang
        $cacheTimeKey = 'password_reset_otp_time_' . md5($normalizedEmail);
        $lastSent = Cache::get($cacheTimeKey);
        if ($lastSent && (now()->timestamp - $lastSent) < 60) {
            $remaining = 60 - (now()->timestamp - $lastSent);
            $this->addError('email', "Mohon tunggu {$remaining} detik sebelum meminta kode baru.");
            return;
        }

        // Generate kode OTP 6 digit acak
        $otp = (string) random_int(100000, 999999);
        $cacheOtpKey = 'password_reset_otp_' . md5($normalizedEmail);

        Cache::put($cacheOtpKey, $otp, now()->addMinutes(10));
        Cache::put($cacheTimeKey, now()->timestamp, now()->addMinutes(10));

        try {
            $user->notify(new PasswordResetOtpNotification($otp));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email OTP Reset Password: ' . $e->getMessage());
            $this->addError('email', 'Gagal mengirim email OTP. Silakan periksa koneksi internet atau coba lagi nanti.');
            return;
        }

        $this->step = 2;
        $this->reset('otp_code');

        // Simpan ke sesi agar tidak hilang jika halaman ter-refresh
        session([
            'forgot_step' => 2,
            'forgot_email' => $normalizedEmail,
        ]);

        $this->statusMessage = "Kode verifikasi 6 digit telah dikirim ke email " . $this->getMaskedEmailProperty() . ". Berlaku selama 10 menit.";
        $this->dispatch('otp-sent');
    }

    /**
     * Backward-compatible alias for sendOtp.
     */
    public function sendPasswordResetLink(): void
    {
        $this->sendOtp();
    }

    /**
     * Kirim ulang kode OTP di Langkah 2.
     */
    public function resendOtp(): void
    {
        $this->statusMessage = null;
        $this->resetErrorBag();

        $normalizedEmail = strtolower(trim($this->email));
        $cacheTimeKey = 'password_reset_otp_time_' . md5($normalizedEmail);
        $lastSent = Cache::get($cacheTimeKey);

        if ($lastSent && (now()->timestamp - $lastSent) < 60) {
            $remaining = 60 - (now()->timestamp - $lastSent);
            $this->addError('otp_code', "Mohon tunggu {$remaining} detik sebelum meminta kode baru.");
            return;
        }

        $user = User::where('email', $normalizedEmail)->first();
        if (! $user) {
            $this->addError('otp_code', 'Pengguna tidak ditemukan.');
            return;
        }

        $otp = (string) random_int(100000, 999999);
        $cacheOtpKey = 'password_reset_otp_' . md5($normalizedEmail);

        Cache::put($cacheOtpKey, $otp, now()->addMinutes(10));
        Cache::put($cacheTimeKey, now()->timestamp, now()->addMinutes(10));

        try {
            $user->notify(new PasswordResetOtpNotification($otp));
            $this->statusMessage = "Kode OTP baru telah berhasil dikirim ulang ke email Anda.";
            $this->dispatch('otp-sent');
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim ulang email OTP: ' . $e->getMessage());
            $this->addError('otp_code', 'Gagal mengirim ulang kode OTP. Silakan coba lagi nanti.');
        }
    }

    /**
     * Langkah 2: Verifikasi kode OTP 6 digit.
     */
    public function verifyOtp(): void
    {
        $this->statusMessage = null;
        $this->resetErrorBag();

        $this->validate([
            'otp_code' => ['required', 'digits:6'],
        ], [
            'otp_code.required' => 'Kode OTP 6 digit wajib diisi.',
            'otp_code.digits' => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $normalizedEmail = strtolower(trim($this->email));
        $cacheOtpKey = 'password_reset_otp_' . md5($normalizedEmail);
        $cachedOtp = Cache::get($cacheOtpKey);

        if (! $cachedOtp || $cachedOtp !== trim($this->otp_code)) {
            $this->addError('otp_code', 'Kode OTP salah atau telah kedaluwarsa. Silakan periksa kembali atau kirim ulang kode.');
            return;
        }

        // Tandai email terverifikasi untuk reset password selama 15 menit
        Cache::put('password_reset_verified_' . md5($normalizedEmail), true, now()->addMinutes(15));
        Cache::forget($cacheOtpKey);

        $this->step = 3;
        $this->reset(['otp_code', 'password', 'password_confirmation']);

        // Simpan ke sesi agar tahap 3 bertahan jika halaman ter-refresh
        session([
            'forgot_step' => 3,
            'forgot_email' => $normalizedEmail,
        ]);

        $this->statusMessage = "Kode OTP berhasil diverifikasi! Silakan buat kata sandi baru Anda.";
    }

    /**
     * Kembali ke langkah 1 untuk mengubah email.
     */
    public function changeEmail(): void
    {
        session()->forget(['forgot_step', 'forgot_email']);
        $this->step = 1;
        $this->reset(['otp_code']);
        $this->statusMessage = null;
        $this->resetErrorBag();
    }

    /**
     * Langkah 3: Simpan kata sandi baru ke database.
     */
    public function resetPassword(): void
    {
        $this->statusMessage = null;
        $this->resetErrorBag();

        $normalizedEmail = strtolower(trim($this->email));

        // Verifikasi bahwa user telah melewati verifikasi OTP
        if (! Cache::get('password_reset_verified_' . md5($normalizedEmail))) {
            $this->addError('password', 'Sesi verifikasi OTP Anda telah berakhir. Silakan ulangi proses verifikasi.');
            session()->forget(['forgot_step', 'forgot_email']);
            $this->step = 1;
            return;
        }

        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, angka, dan karakter khusus / simbol.',
        ]);

        $user = User::where('email', $normalizedEmail)->first();
        if (! $user) {
            $this->addError('password', 'Akun pengguna tidak ditemukan.');
            return;
        }

        $user->forceFill([
            'password' => Hash::make($this->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Bersihkan cache verifikasi & sesi
        Cache::forget('password_reset_verified_' . md5($normalizedEmail));
        Cache::forget('password_reset_otp_time_' . md5($normalizedEmail));
        session()->forget(['forgot_step', 'forgot_email']);

        event(new PasswordReset($user));

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->isSuccess = true;
    }

    /**
     * Masked email untuk keamanan tampilan.
     */
    public function getMaskedEmailProperty(): string
    {
        if (empty($this->email)) return '';
        $parts = explode('@', $this->email);
        $name = $parts[0] ?? '';
        $domain = $parts[1] ?? '';
        if (strlen($name) <= 2) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', min(4, max(1, strlen($name) - 2)));
        }
        return $maskedName . '@' . $domain;
    }
}; ?>

<div class="flex-1 flex flex-col justify-between py-1">
    <div>
        <!-- Top Logo sayabantu.com -->
        <div class="text-center pt-0 pb-3">
            <x-sayabantu-logo size="md" />
        </div>

        <!-- Multi-Step Segmented Progress Bar -->
        @if (! $isSuccess)
            <div class="mb-5 px-1">
                <!-- Segmented Progress Indicator -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-1.5 rounded-full transition-all duration-500 {{ $step >= 1 ? 'bg-[#0066FF]' : 'bg-slate-200' }}"></div>
                    <div class="flex-1 h-1.5 rounded-full transition-all duration-500 {{ $step >= 2 ? 'bg-[#0066FF]' : 'bg-slate-200' }}"></div>
                    <div class="flex-1 h-1.5 rounded-full transition-all duration-500 {{ $step >= 3 ? 'bg-[#0066FF]' : 'bg-slate-200' }}"></div>
                </div>
                <!-- Step Label Row -->
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500">
                    <span class="text-slate-700 font-bold">
                        @if($step === 1) 1. Masukkan Email Akun
                        @elseif($step === 2) 2. Verifikasi 6 Digit OTP
                        @elseif($step === 3) 3. Buat Kata Sandi Baru
                        @endif
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-[#0066FF] border border-blue-100">
                        Langkah {{ $step }} dari 3
                    </span>
                </div>
            </div>
        @endif

        <!-- Global Feedback / Alert Banner -->
        @if ($statusMessage)
            <div class="mb-4 p-3.5 bg-emerald-50/90 border border-emerald-200 rounded-2xl flex items-start gap-2.5 text-xs text-emerald-800 shadow-2xs animate-fade-in">
                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="font-medium leading-relaxed">{{ $statusMessage }}</span>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- STATE SUKSES: KATA SANDI BERHASIL DIRESET                                 -->
        <!-- ========================================================================= -->
        @if ($isSuccess)
            <div wire:key="step-container-success" class="text-center py-6 px-1">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-500/25 ring-4 ring-emerald-50">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                    🎉 Kata Sandi Diperbarui
                </span>
                <h3 class="text-2xl font-black text-gray-900 tracking-tight mt-1">Kata Sandi Siap Digunakan!</h3>
                <p class="text-xs text-gray-500 mt-2 max-w-xs mx-auto leading-relaxed">
                    Kata sandi akun Anda telah berhasil diganti. Sekarang Anda dapat langsung masuk ke SayaBantu dengan kata sandi baru.
                </p>

                <div class="mt-8">
                    <a href="{{ route('login') }}"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#0066FF] to-[#0052CC] hover:from-[#0052CC] hover:to-[#0041a8] active:scale-[0.98] text-white font-bold py-3.5 sm:py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all text-base tracking-wide cursor-pointer group">
                        <span>Masuk ke Akun Sekarang</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>

        <!-- ========================================================================= -->
        <!-- STEP 1: MASUKKAN EMAIL                                                    -->
        <!-- ========================================================================= -->
        @elseif ($step === 1)
            <div wire:key="step-container-1">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#0066FF] to-blue-500 text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/25 ring-4 ring-blue-50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Lupa Kata Sandi?</h2>
                    <p class="text-xs text-gray-500 mt-1.5 max-w-xs mx-auto leading-relaxed">
                        Jangan khawatir! Masukkan alamat email akun Anda untuk menerima kode OTP verifikasi.
                    </p>
                </div>

                <form wire:submit.prevent="sendOtp" action="javascript:void(0);" onsubmit="event.preventDefault(); return false;" novalidate class="space-y-4">
                    @csrf
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Alamat Email Akun
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                            <input type="email" id="email" wire:model="email" wire:keydown.enter.prevent="sendOtp" autofocus
                                oninvalid="this.setCustomValidity('Alamat email wajib diisi.')" oninput="this.setCustomValidity('')"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-gray-900 text-sm placeholder-slate-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <div class="mt-2.5 p-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs text-rose-700 font-semibold flex items-start gap-2.5 shadow-2xs animate-shake">
                                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="leading-relaxed">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="button" wire:click="sendOtp" wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-[#0066FF] to-[#0052CC] hover:from-[#0052CC] hover:to-[#0041a8] active:scale-[0.98] text-white font-bold py-3.5 sm:py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all text-base tracking-wide disabled:opacity-60 flex items-center justify-center gap-2 cursor-pointer group">
                            <svg wire:loading wire:target="sendOtp" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="sendOtp" class="flex items-center gap-1.5">
                                <span>Kirim Kode OTP</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="sendOtp">Mengirim Kode ke Email...</span>
                        </button>
                    </div>
                </form>

                <!-- Info Card -->
                <div class="mt-6 p-4 bg-gradient-to-br from-blue-50/80 to-indigo-50/40 border border-blue-100 rounded-2xl flex items-start gap-3 text-xs text-blue-900 shadow-2xs">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-[#0066FF] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Verifikasi Resmi & Aman</h4>
                        <p class="text-gray-500 mt-0.5 leading-relaxed">
                            Kode OTP 6 digit rahasia akan dikirimkan ke kotak masuk email Anda dan berlaku selama 10 menit.
                        </p>
                    </div>
                </div>
            </div>

        <!-- ========================================================================= -->
        <!-- STEP 2: VERIFIKASI KODE OTP                                               -->
        <!-- ========================================================================= -->
        @elseif ($step === 2)
            <div wire:key="step-container-2">
                <div class="text-center mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/25 ring-4 ring-blue-50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Verifikasi Kode OTP</h2>
                    <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                        Masukkan 6 digit kode verifikasi yang telah dikirimkan ke:
                    </p>

                    <!-- Target Email Card with Edit Button -->
                    <div class="inline-flex items-center gap-2 mt-2 px-3.5 py-1.5 bg-blue-50/90 border border-blue-200/80 rounded-full text-xs font-bold text-blue-900 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate max-w-[190px]">{{ $email }}</span>
                        <button type="button" wire:click="changeEmail"
                            class="text-[#0066FF] hover:underline font-extrabold text-[11px] ml-1 cursor-pointer">
                            Ubah
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="verifyOtp" action="javascript:void(0);" onsubmit="event.preventDefault(); return false;" novalidate class="space-y-5">
                    @csrf
                    <!-- Premium 6-Box OTP Display with Hidden Input Overlay -->
                    <div x-data="{
                        otp: $wire.entangle('otp_code'),
                        isFocused: false
                    }" class="my-4">
                        <label class="block text-xs font-bold text-gray-700 mb-2 text-center">
                            Masukkan 6 Digit Angka
                        </label>
                        
                        <div class="relative max-w-[310px] mx-auto">
                            <!-- Real Hidden Input for native keyboard, SMS autofill & paste support -->
                            <input type="text"
                                   id="otp_code_input"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="6"
                                   autocomplete="one-time-code"
                                   x-model="otp"
                                   @focus="isFocused = true"
                                   @blur="isFocused = false"
                                   @input="otp = (otp || '').replace(/[^0-9]/g, '').slice(0, 6); if(otp.length === 6) { $wire.verifyOtp(); }"
                                   class="absolute inset-0 w-full h-full opacity-0 z-20 cursor-pointer tracking-widest text-center"
                                   autofocus>

                            <!-- 6 Elegant Visual Digit Cells -->
                            <div class="flex items-center justify-between gap-1.5 sm:gap-2">
                                <template x-for="i in 6" :key="i">
                                    <div class="w-11 h-14 sm:w-12 sm:h-15 rounded-2xl border-2 flex items-center justify-center text-xl font-black font-mono transition-all duration-200 select-none relative"
                                         :class="{
                                             'border-[#0066FF] bg-white ring-4 ring-blue-100 shadow-md shadow-blue-500/10 scale-105 text-[#0066FF]': isFocused && ((otp || '').length === i - 1 || ((otp || '').length === 6 && i === 6)),
                                             'border-blue-300 bg-blue-50/40 text-gray-900 shadow-2xs': (otp || '').length >= i && !(isFocused && (otp || '').length === i - 1),
                                             'border-slate-200 bg-slate-50/70 text-slate-300': (otp || '').length < i && !(isFocused && (otp || '').length === i - 1)
                                         }">
                                        <span x-show="(otp || '').length >= i" x-text="(otp || '')[i - 1]" class="text-gray-900 font-extrabold text-2xl"></span>
                                        <span x-show="isFocused && (otp || '').length === i - 1" class="w-0.5 h-6 bg-[#0066FF] animate-pulse"></span>
                                        <span x-show="!isFocused && (otp || '').length < i" class="text-slate-300 text-sm">•</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        @error('otp_code')
                            <div class="mt-3 p-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs text-rose-700 font-semibold flex items-center justify-center gap-2 text-center animate-shake">
                                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Resend Countdown Timer via Alpine.js -->
                    <div class="text-center pt-1" x-data="{
                        countdown: 60,
                        timer: null,
                        init() {
                            this.startTimer();
                            $wire.on('otp-sent', () => {
                                this.countdown = 60;
                                this.startTimer();
                            });
                        },
                        startTimer() {
                            if (this.timer) clearInterval(this.timer);
                            this.timer = setInterval(() => {
                                if (this.countdown > 0) {
                                    this.countdown--;
                                } else {
                                    clearInterval(this.timer);
                                }
                            }, 1000);
                        },
                        get formattedTime() {
                            let m = Math.floor(this.countdown / 60);
                            let s = this.countdown % 60;
                            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                        }
                    }">
                        <template x-if="countdown > 0">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-full text-xs text-slate-500 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Kirim ulang kode dalam</span>
                                <span class="font-bold text-[#0066FF]" x-text="formattedTime"></span>
                            </div>
                        </template>
                        <template x-if="countdown === 0">
                            <div class="flex items-center justify-center gap-1.5 text-xs">
                                <span class="text-slate-500 font-medium">Belum menerima kode?</span>
                                <button type="button" wire:click="resendOtp" wire:loading.attr="disabled"
                                    class="font-extrabold text-[#0066FF] hover:underline cursor-pointer disabled:opacity-50 inline-flex items-center gap-1">
                                    <svg wire:loading.remove wire:target="resendOtp" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span wire:loading.remove wire:target="resendOtp">Kirim Ulang OTP</span>
                                    <span wire:loading wire:target="resendOtp">Mengirim...</span>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="button" wire:click="verifyOtp" wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-[#0066FF] to-[#0052CC] hover:from-[#0052CC] hover:to-[#0041a8] active:scale-[0.98] text-white font-bold py-3.5 sm:py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all text-base tracking-wide disabled:opacity-60 flex items-center justify-center gap-2 cursor-pointer group">
                            <svg wire:loading wire:target="verifyOtp" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="verifyOtp" class="flex items-center gap-1.5">
                                <span>Verifikasi & Lanjutkan</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="verifyOtp">Memverifikasi...</span>
                        </button>
                    </div>
                </form>
            </div>

        <!-- ========================================================================= -->
        <!-- STEP 3: BUAT KATA SANDI BARU                                              -->
        <!-- ========================================================================= -->
        @elseif ($step === 3)
            <div wire:key="step-container-3">
                <div class="text-center mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-500 text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-emerald-500/25 ring-4 ring-emerald-50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Buat Kata Sandi Baru</h2>
                    <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                        Silakan tentukan kata sandi baru yang aman untuk akun Anda.
                    </p>
                </div>

                <form wire:submit.prevent="resetPassword" action="javascript:void(0);" onsubmit="event.preventDefault(); return false;" novalidate class="space-y-4" x-data="{
                    showPass: false,
                    showConfirm: false,
                    pass: $wire.entangle('password'),
                    conf: $wire.entangle('password_confirmation'),
                    get hasMin() { return (this.pass || '').length >= 8; },
                    get hasUpper() { return /[A-Z]/.test(this.pass || ''); },
                    get hasNum() { return /[0-9]/.test(this.pass || ''); },
                    get hasSpecial() { return /[^A-Za-z0-9]/.test(this.pass || ''); },
                    get score() {
                        let s = 0;
                        if (this.hasMin) s++;
                        if (this.hasUpper) s++;
                        if (this.hasNum) s++;
                        if (this.hasSpecial) s++;
                        return s;
                    },
                    get isMatch() { return this.pass && this.conf && this.pass === this.conf; }
                }">
                    @csrf
                    <!-- Input Password Baru -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-gray-700">
                                Kata Sandi Baru
                            </label>
                            <span class="text-[11px] font-bold"
                                  :class="{
                                      'text-slate-400': score === 0,
                                      'text-rose-500': score === 1,
                                      'text-amber-500': score === 2 || score === 3,
                                      'text-emerald-600': score === 4
                                  }"
                                  x-text="score === 0 ? '' : (score === 1 ? 'Lemah' : (score < 4 ? 'Cukup Kuat' : 'Sangat Kuat'))">
                            </span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" id="password" wire:model="password" autofocus
                                oninvalid="this.setCustomValidity('Kata sandi baru wajib diisi.')" oninput="this.setCustomValidity('')"
                                class="w-full pl-11 pr-11 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-gray-900 text-sm placeholder-slate-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium"
                                placeholder="Minimal 8 karakter kombinasi">
                            <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>

                        <!-- Dynamic Password Strength Bar -->
                        <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden flex gap-0.5">
                            <div class="h-full flex-1 rounded-full transition-all duration-300" :class="score >= 1 ? (score === 1 ? 'bg-rose-500' : (score < 4 ? 'bg-amber-400' : 'bg-emerald-500')) : 'bg-slate-200'"></div>
                            <div class="h-full flex-1 rounded-full transition-all duration-300" :class="score >= 2 ? (score < 4 ? 'bg-amber-400' : 'bg-emerald-500') : 'bg-slate-200'"></div>
                            <div class="h-full flex-1 rounded-full transition-all duration-300" :class="score >= 3 ? (score < 4 ? 'bg-amber-400' : 'bg-emerald-500') : 'bg-slate-200'"></div>
                            <div class="h-full flex-1 rounded-full transition-all duration-300" :class="score >= 4 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                        </div>

                        @error('password')
                            <div class="mt-2.5 p-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs text-rose-700 font-semibold flex items-start gap-2 shadow-2xs">
                                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Real-time Checklist Ketentuan Password -->
                    <div class="p-3.5 bg-slate-50/90 rounded-2xl border border-slate-200/70 text-[11px] space-y-1.5">
                        <p class="font-extrabold text-slate-700 mb-1">Ketentuan Keamanan Sandi:</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="flex items-center gap-1.5 transition-colors duration-200" :class="hasMin ? 'text-emerald-700 font-bold' : 'text-slate-400'">
                                <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" :class="hasMin ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                                <span>Min. 8 Karakter</span>
                            </div>
                            <div class="flex items-center gap-1.5 transition-colors duration-200" :class="hasUpper ? 'text-emerald-700 font-bold' : 'text-slate-400'">
                                <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" :class="hasUpper ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                                <span>Huruf Besar (A-Z)</span>
                            </div>
                            <div class="flex items-center gap-1.5 transition-colors duration-200" :class="hasNum ? 'text-emerald-700 font-bold' : 'text-slate-400'">
                                <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" :class="hasNum ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                                <span>Angka (0-9)</span>
                            </div>
                            <div class="flex items-center gap-1.5 transition-colors duration-200" :class="hasSpecial ? 'text-emerald-700 font-bold' : 'text-slate-400'">
                                <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0" :class="hasSpecial ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-400'">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                                <span>Simbol (!@#$...)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Input Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" wire:model="password_confirmation"
                                oninvalid="this.setCustomValidity('Konfirmasi kata sandi wajib diisi.')" oninput="this.setCustomValidity('')"
                                class="w-full pl-11 pr-11 py-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl text-gray-900 text-sm placeholder-slate-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium"
                                placeholder="Ulangi kata sandi baru">
                            <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <!-- Status kecocokan konfirmasi -->
                        <template x-if="conf && conf.length > 0">
                            <div class="mt-2 flex items-center gap-1.5 text-xs font-bold" :class="isMatch ? 'text-emerald-600' : 'text-amber-600'">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isMatch">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isMatch">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span x-text="isMatch ? 'Kata sandi cocok dan siap disimpan' : 'Konfirmasi kata sandi belum cocok'"></span>
                            </div>
                        </template>
                        @error('password_confirmation')
                            <div class="mt-2.5 p-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs text-rose-700 font-semibold flex items-start gap-2 shadow-2xs">
                                <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="button" wire:click="resetPassword" wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-[#0066FF] to-[#0052CC] hover:from-[#0052CC] hover:to-[#0041a8] active:scale-[0.98] text-white font-bold py-3.5 sm:py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all text-base tracking-wide disabled:opacity-60 flex items-center justify-center gap-2 cursor-pointer group">
                            <svg wire:loading wire:target="resetPassword" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="resetPassword" class="flex items-center gap-1.5">
                                <span>Simpan Kata Sandi Baru</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="resetPassword">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Back to Login (Footer) -->
    @if (! $isSuccess)
        <div class="mt-8 pt-4 border-t border-slate-100 text-center">
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Ingat kata sandi Anda?
                <a href="{{ route('login') }}" class="font-extrabold text-[#0066FF] hover:underline ml-1 inline-flex items-center gap-1">
                    <span>Kembali ke Login</span>
                </a>
            </p>
        </div>
    @endif
</div>
