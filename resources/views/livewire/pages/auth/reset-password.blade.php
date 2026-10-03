<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.blank')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        // Jika masih ada sesi login lama yang aktif, logout dulu agar sesi bersih dan aman
        if (Auth::check()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, angka, dan karakter khusus / simbol.',
            'email.required' => 'Alamat email tidak ditemukan.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $targetUserRole = null;

        // Reset user password
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use (&$targetUserRole) {
                $targetUserRole = $user->role;
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $errorMessage = match ($status) {
                Password::INVALID_TOKEN => 'Link ubah password ini sudah tidak berlaku atau sudah kadaluarsa. Silakan ajukan lupa password kembali.',
                Password::INVALID_USER => 'Akun dengan alamat email ini tidak ditemukan.',
                default => 'Gagal mengubah password. Silakan coba kembali atau ajukan link baru.',
            };

            $this->addError('general', $errorMessage);
            return;
        }

        // Pastikan logout penuh agar sesi bersih dan user harus login ulang
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        // Kirim notifikasi status berhasil ke halaman login
        session()->flash('status', 'Password berhasil diubah! Silakan login kembali dengan password baru Anda.');

        // Redirect sesuai role
        if (in_array($targetUserRole, ['admin', 'super_admin'])) {
            $this->redirectRoute('admin.login', navigate: false);
            return;
        }

        $this->redirectRoute('login', navigate: false);
    }

    public function getIsAdminUserProperty(): bool
    {
        if (!empty($this->email)) {
            $user = User::where('email', $this->email)->first();
            return $user && in_array($user->role, ['admin', 'super_admin']);
        }

        return false;
    }

    public function getLoginRouteProperty(): string
    {
        if ($this->isAdminUser) {
            return route('admin.login');
        }

        return route('login');
    }
}; ?>

<div>
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>

    @if ($this->isAdminUser)
        <!-- ============================================== -->
        <!-- UI KHUSUS SUPER ADMIN & ADMIN (2-COLUMN PANEL)  -->
        <!-- ============================================== -->
        <div class="min-h-screen flex" x-data="{
            showPass: false,
            showConfirm: false,
            newPass: '',
            confirmPass: '',
            get score() {
                let p = this.newPass || '';
                if (!p) return 0;
                let s = 0;
                if (p.length >= 8) s++;
                if (p.length >= 10) s++;
                if (/[A-Z]/.test(p)) s++;
                if (/[0-9]/.test(p)) s++;
                if (/[^A-Za-z0-9]/.test(p)) s++;
                return s;
            },
            get isValid() {
                let p = this.newPass || '';
                let c = this.confirmPass || '';
                let hasMin = p.length >= 8;
                let hasUpper = /[A-Z]/.test(p);
                let hasNum = /[0-9]/.test(p);
                let hasSpecial = /[^A-Za-z0-9]/.test(p);
                let isMatch = p.length > 0 && p === c;
                return hasMin && hasUpper && hasNum && hasSpecial && isMatch;
            }
        }">
            <!-- Left Side - Branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-500 to-primary-700 items-center justify-center p-12">
                <div class="max-w-md text-white">
                    <h1 class="text-5xl font-bold mb-6">sayabantu</h1>
                    <p class="text-xl mb-8 text-primary-100">Admin Panel</p>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 mt-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="font-semibold">Kelola Platform</p>
                                <p class="text-sm text-primary-100">Kontrol penuh terhadap semua aspek platform</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 mt-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="font-semibold">Moderasi Konten</p>
                                <p class="text-sm text-primary-100">Verifikasi dan kelola permintaan bantuan</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 mt-1 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <div>
                                <p class="font-semibold">Laporan & Analitik</p>
                                <p class="text-sm text-primary-100">Dashboard lengkap dengan statistik real-time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Admin Reset Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
                <div class="w-full max-w-md">
                    <!-- Logo for Mobile -->
                    <div class="lg:hidden text-center mb-8">
                        <h1 class="text-3xl font-bold text-primary-600">sayabantu</h1>
                        <p class="text-gray-600 mt-2">Admin Panel</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Ubah Password Admin</h2>
                            <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                                Buat kata sandi baru yang aman untuk akun administrator Anda.
                            </p>
                        </div>

                        <!-- General Error Alert -->
                        @error('general')
                            <div class="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start gap-3 shadow-2xs">
                                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-xs">
                                    <p class="font-bold text-red-800">Gagal Mengubah Password</p>
                                    <p class="mt-0.5 text-red-600 leading-relaxed">{{ $message }}</p>
                                    <a href="{{ route('admin.password.request') }}" class="inline-block mt-2 font-semibold text-primary-600 hover:underline">
                                        Minta link baru &rarr;
                                    </a>
                                </div>
                            </div>
                        @enderror

                        <form wire:submit="resetPassword" class="space-y-5">
                            <!-- Email Badge -->
                            @if(!empty($email))
                                <div class="p-3.5 bg-primary-50/70 border border-primary-100 rounded-xl flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary-500/10 text-primary-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[11px] font-medium text-gray-500 block leading-tight">Akun Administrator:</span>
                                        <span class="text-xs font-bold text-gray-900 truncate block">{{ $email }}</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                                        ✓ Terverifikasi
                                    </span>
                                </div>
                                <input type="hidden" wire:model="email">
                            @else
                                <div>
                                    <label for="email_admin" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                                    <input wire:model="email" id="email_admin" type="email" required autofocus
                                        placeholder="admin@sayabantu.com"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                                </div>
                            @endif

                            <!-- Password Baru -->
                            <div>
                                <label for="password_admin" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <div class="relative">
                                    <input wire:model="password" id="password_admin" :type="showPass ? 'text' : 'password'" required autofocus
                                        @input="newPass = $event.target.value"
                                        autocomplete="new-password" placeholder="Minimal 8 karakter"
                                        class="w-full pl-4 pr-11 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                    <button type="button" @click="showPass = !showPass"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer"
                                        tabindex="-1">
                                        <!-- Eye Slash (Hidden) -->
                                        <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <!-- Eye Open (Visible) -->
                                        <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />

                                {{-- Password Strength Meter --}}
                                <div x-show="newPass && newPass.length > 0" x-transition class="mt-2.5">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="text-gray-500">Kekuatan Kata Sandi:</span>
                                        <span class="font-bold" 
                                              :class="score <= 2 ? 'text-red-500' : (score <= 3 ? 'text-amber-500' : 'text-emerald-600')"
                                              x-text="score <= 2 ? 'Lemah' : (score <= 3 ? 'Sedang' : 'Kuat & Aman')"></span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full transition-all duration-300 rounded-full" 
                                             :class="score <= 2 ? 'w-1/3 bg-red-500' : (score <= 3 ? 'w-2/3 bg-amber-500' : 'w-full bg-emerald-500')"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <label for="password_confirm_admin" class="block text-sm font-medium text-gray-700 mb-2">Ulangi Password Baru</label>
                                <div class="relative">
                                    <input wire:model="password_confirmation" id="password_confirm_admin" :type="showConfirm ? 'text' : 'password'" required
                                        @input="confirmPass = $event.target.value"
                                        autocomplete="new-password" placeholder="Ketik ulang password baru Anda"
                                        class="w-full pl-4 pr-11 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                    <button type="button" @click="showConfirm = !showConfirm"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer"
                                        tabindex="-1">
                                        <!-- Eye Slash (Hidden) -->
                                        <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <!-- Eye Open (Visible) -->
                                        <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />

                                {{-- Status Kecocokan Password --}}
                                <div x-show="confirmPass && confirmPass.length > 0" x-transition class="mt-1.5">
                                    <p x-show="newPass === confirmPass" class="text-xs text-emerald-600 flex items-center gap-1 font-medium">
                                        <span>✓</span> <span>Konfirmasi kata sandi cocok.</span>
                                    </p>
                                    <p x-show="newPass !== confirmPass" class="text-xs text-red-500 flex items-center gap-1 font-medium">
                                        <span>✕</span> <span>Kata sandi tidak cocok.</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Criteria Checklist --}}
                            <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-200/80 text-xs space-y-1.5">
                                <p class="font-bold text-gray-700 mb-1">Ketentuan kata sandi:</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass.length >= 8 ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="newPass.length >= 8 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="newPass.length >= 8 ? '✓' : '✕'">✕</span>
                                        <span>Minimal 8 karakter</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[A-Z]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="/[A-Z]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="/[A-Z]/.test(newPass) ? '✓' : '✕'">✕</span>
                                        <span>Huruf besar (A-Z)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="/[0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="/[0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                                        <span>Angka (0-9)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[^A-Za-z0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="/[^A-Za-z0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="/[^A-Za-z0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                                        <span>Karakter khusus / simbol</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" wire:loading.attr="disabled"
                                :disabled="!isValid"
                                :class="!isValid ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-primary-700'"
                                class="w-full py-3 px-4 bg-primary-600 text-white font-semibold rounded-lg shadow-md transition duration-200 flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="resetPassword" class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan Password Baru</span>
                                </span>
                                <span wire:loading wire:target="resetPassword" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Menyimpan Password...</span>
                                </span>
                            </button>

                            <!-- Back to Login Link -->
                            <div class="text-center pt-2">
                                <a href="{{ route('admin.login') }}" wire:navigate
                                    class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    <span>Kembali ke Halaman Login</span>
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <p class="text-center text-sm text-gray-500 mt-8">&copy; {{ date('Y') }} sayabantu. All rights reserved.</p>
                </div>
            </div>
        </div>
    @else
        <!-- ============================================== -->
        <!-- UI KHUSUS CUSTOMER & MITRA (MOBILE CARD LAYOUT) -->
        <!-- ============================================== -->
        <div class="font-sans antialiased bg-gray-100 min-h-screen" x-data="{
            showPass: false,
            showConfirm: false,
            newPass: '',
            confirmPass: '',
            get score() {
                let p = this.newPass || '';
                if (!p) return 0;
                let s = 0;
                if (p.length >= 8) s++;
                if (p.length >= 10) s++;
                if (/[A-Z]/.test(p)) s++;
                if (/[0-9]/.test(p)) s++;
                if (/[^A-Za-z0-9]/.test(p)) s++;
                return s;
            },
            get isValid() {
                let p = this.newPass || '';
                let c = this.confirmPass || '';
                let hasMin = p.length >= 8;
                let hasUpper = /[A-Z]/.test(p);
                let hasNum = /[0-9]/.test(p);
                let hasSpecial = /[^A-Za-z0-9]/.test(p);
                let isMatch = p.length > 0 && p === c;
                return hasMin && hasUpper && hasNum && hasSpecial && isMatch;
            }
        }">
            <div class="max-w-md mx-auto min-h-screen flex flex-col bg-white shadow-md">
                <!-- Customer Header -->
                <div class="px-5 relative overflow-hidden flex-shrink-0 header-pattern" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0); padding-top: 32px; padding-bottom: 32px;">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between text-white mb-0">
                            <div class="flex items-center">
                                <a href="{{ route('login') }}" wire:navigate aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition inline-flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            </div>

                            <div class="text-center flex-1">
                                <h1 class="text-lg font-bold">sayabantu</h1>
                                <p class="text-xs text-white/90 mt-0.5">Platform Bantuan Sosial</p>
                            </div>

                            <div class="w-8"></div>
                        </div>
                    </div>

                    <!-- Curved separator into content -->
                    <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
                    </svg>
                </div>

                <!-- Customer Card Content -->
                <div class="bg-white rounded-t-3xl -mt-2 px-6 pt-5 pb-6 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Header Icon & Title -->
                        <div class="pt-2 mb-6 text-center">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1.5 tracking-tight">Ubah Password Baru</h2>
                            <p class="text-xs text-gray-500 font-medium">Buat password baru yang aman untuk akun Anda</p>
                        </div>

                        <!-- General Error Alert -->
                        @error('general')
                            <div class="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start gap-3 shadow-2xs">
                                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-xs">
                                    <p class="font-bold text-red-800">Gagal Mengubah Password</p>
                                    <p class="mt-0.5 text-red-600 leading-relaxed">{{ $message }}</p>
                                    <a href="{{ route('password.request') }}" class="inline-block mt-2 font-semibold text-blue-600 hover:underline">
                                        Minta link baru &rarr;
                                    </a>
                                </div>
                            </div>
                        @enderror

                        <form wire:submit="resetPassword" class="space-y-4">
                            <!-- Email Badge / Display -->
                            @if(!empty($email))
                                <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[11px] font-medium text-gray-500 block leading-tight">Ubah password untuk akun:</span>
                                        <span class="text-xs font-bold text-gray-900 truncate block">{{ $email }}</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                                        ✓ Terverifikasi
                                    </span>
                                </div>
                                <input type="hidden" wire:model="email">
                            @else
                                <div>
                                    <label for="email_cust" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                                        Alamat Email <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="email" id="email_cust" type="email" required autofocus
                                        placeholder="nama@example.com"
                                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                                </div>
                            @endif

                            <!-- Password Baru -->
                            <div>
                                <label for="password_cust" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                                    Password Baru <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input wire:model="password" id="password_cust" :type="showPass ? 'text' : 'password'" required autofocus
                                        @input="newPass = $event.target.value"
                                        autocomplete="new-password" placeholder="Minimal 8 karakter"
                                        class="w-full pl-4 pr-11 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                                    <button type="button" @click="showPass = !showPass"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer"
                                        tabindex="-1">
                                        <!-- Eye Slash (Hidden) -->
                                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <!-- Eye Open (Visible) -->
                                        <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />

                                {{-- Password Strength Meter --}}
                                <div x-show="newPass && newPass.length > 0" x-transition class="mt-2.5">
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="text-gray-500">Kekuatan Kata Sandi:</span>
                                        <span class="font-bold" 
                                              :class="score <= 2 ? 'text-red-500' : (score <= 3 ? 'text-amber-500' : 'text-emerald-600')"
                                              x-text="score <= 2 ? 'Lemah' : (score <= 3 ? 'Sedang' : 'Kuat & Aman')"></span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full transition-all duration-300 rounded-full" 
                                             :class="score <= 2 ? 'w-1/3 bg-red-500' : (score <= 3 ? 'w-2/3 bg-amber-500' : 'w-full bg-emerald-500')"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <label for="password_confirmation_cust" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                                    Ulangi Password Baru <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input wire:model="password_confirmation" id="password_confirmation_cust" :type="showConfirm ? 'text' : 'password'" required
                                        @input="confirmPass = $event.target.value"
                                        autocomplete="new-password" placeholder="Ketik ulang password baru Anda"
                                        class="w-full pl-4 pr-11 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                                    <button type="button" @click="showConfirm = !showConfirm"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer"
                                        tabindex="-1">
                                        <!-- Eye Slash (Hidden) -->
                                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <!-- Eye Open (Visible) -->
                                        <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />

                                {{-- Status Kecocokan Password --}}
                                <div x-show="confirmPass && confirmPass.length > 0" x-transition class="mt-1.5">
                                    <p x-show="newPass === confirmPass" class="text-xs text-emerald-600 flex items-center gap-1 font-medium">
                                        <span>✓</span> <span>Konfirmasi kata sandi cocok.</span>
                                    </p>
                                    <p x-show="newPass !== confirmPass" class="text-xs text-red-500 flex items-center gap-1 font-medium">
                                        <span>✕</span> <span>Kata sandi tidak cocok.</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Criteria Checklist --}}
                            <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-200/80 text-xs space-y-1.5">
                                <p class="font-bold text-gray-700 mb-1">Ketentuan kata sandi:</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass.length >= 8 ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="newPass.length >= 8 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="newPass.length >= 8 ? '✓' : '✕'">✕</span>
                                        <span>Minimal 8 karakter</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[A-Z]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="/[A-Z]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="/[A-Z]/.test(newPass) ? '✓' : '✕'">✕</span>
                                        <span>Huruf besar (A-Z)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="/[0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="/[0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                                        <span>Angka (0-9)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[^A-Za-z0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                              :class="/[^A-Za-z0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                              x-text="/[^A-Za-z0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                                        <span>Karakter khusus / simbol</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit" wire:loading.attr="disabled"
                                    :disabled="!isValid"
                                    :class="!isValid ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:opacity-95 active:scale-98'"
                                    style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);"
                                    class="w-full py-3.5 text-white rounded-xl font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="resetPassword" class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Simpan Password Baru</span>
                                    </span>
                                    <span wire:loading wire:target="resetPassword" class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Menyimpan Password...</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Customer Footer Back to Login -->
                    <div class="mt-6 text-center text-xs text-gray-500">
                        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-blue-600 hover:text-blue-700 transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            <span>Kembali ke Halaman Login</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
