<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        // Redirect admins to admin login page
        if (in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            Auth::logout();
            $this->addError('email', 'Admin users must login at /admin/login');
            return;
        }

        Session::regenerate();

        // Determine redirect destination based on user role
        $user = auth()->user();

        // Redirect to role-specific dashboard
        if ($user->role === 'mitra') {
            $redirect = route('mitra.dashboard', absolute: false);
        } elseif ($user->role === 'kustomer') {
            $redirect = route('customer.dashboard', absolute: false);
        } else {
            // Default fallback
            $redirect = route('dashboard', absolute: false);
        }

        $this->redirectIntended(default: $redirect, navigate: false);
    }
}; ?>

<div class="w-full flex-1 flex flex-col justify-between py-2">
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>

    <div>
        <!-- Header Login -->
        <div class="pt-2 mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-1.5 tracking-tight">Masuk ke Akun</h2>
            <p class="text-xs text-gray-500 font-medium">Masukkan email atau username Anda untuk melanjutkan</p>
        </div>

        <!-- Session Status / Alert -->
        <x-auth-session-status class="mb-5" :status="session('status')" />

        @if (session('error'))
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 flex items-start gap-3 shadow-sm animate-shake">
                <div class="p-1 bg-red-100 rounded-lg text-red-600 flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-red-800">Akun Diblokir / Akses Ditolak</h4>
                    <p class="text-xs text-red-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            <!-- Email / Username Address -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Email atau Username <span class="text-red-500">*</span>
                </label>
                <input wire:model="form.email" id="email" type="text" name="email" required autofocus
                    autocomplete="username" placeholder="nama@example.com atau username"
                    class="w-full px-4 py-3.5 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative" x-data="{ show: false }">
                    <input wire:model="form.password" id="password" x-bind:type="show ? 'text' : 'password'" name="password" required
                        autocomplete="current-password" placeholder="Masukkan kata sandi Anda"
                        class="w-full py-3.5 pl-4 pr-12 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                    <button type="button" @click="show = !show"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <!-- Eye Icon -->
                        <svg x-show="show" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Slash Icon -->
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />

                @if (Route::has('password.request'))
                    <div class="flex justify-end mt-4">
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                            Lupa Password?
                        </a>
                    </div>
                @endif
            </div>

            <!-- Log In Button -->
            <div class="pt-3">
                <button type="submit" wire:loading.attr="disabled" wire:target="login"
                    class="w-full bg-primary-500 hover:bg-primary-600 active:scale-98 text-white font-bold py-3.5 rounded-full shadow-md hover:shadow-lg transition text-base tracking-wide disabled:opacity-50 flex items-center justify-center gap-2"
                    style="background-color: #0098e7;">
                    <svg wire:loading wire:target="login" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="login">Masuk</span>
                    <span wire:loading wire:target="login">Memproses...</span>
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="relative flex items-center justify-center my-6">
            <div class="border-t border-gray-200 w-full"></div>
            <span class="bg-white px-4 text-xs text-gray-400 font-medium absolute">atau</span>
        </div>

        <!-- Register Button -->
        <div>
            <a href="{{ route('register.choose-role') }}" wire:navigate
                class="block w-full py-3.5 rounded-full border-2 hover:bg-blue-50/60 font-bold text-center text-sm transition active:scale-98"
                style="border-color: #0098e7; color: #0098e7;">
                Daftar Akun
            </a>
        </div>
    </div>

    <!-- Admin Login Link (Cleanly placed at bottom) -->
    <div class="text-center pt-6 pb-2">
        <a href="/admin/login" class="text-xs font-semibold text-gray-500 hover:text-blue-600 hover:underline transition">
            Masuk sebagai Admin →
        </a>
    </div>
</div>
