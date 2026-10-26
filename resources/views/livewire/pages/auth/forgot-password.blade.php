<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $msg = match ($status) {
                Password::INVALID_USER => 'Alamat email tidak ditemukan.',
                Password::RESET_THROTTLED => 'Mohon tunggu beberapa saat sebelum meminta tautan baru.',
                default => 'Gagal mengirim link reset password. Silakan coba lagi nanti.',
            };
            $this->addError('email', $msg);
            return;
        }

        $this->reset('email');

        session()->flash('status', 'Link reset password berhasil dikirim ke email Anda! Silakan periksa kotak masuk atau folder spam.');
    }
}; ?>

<div class="flex-1 flex flex-col justify-between py-2">
    <div>
        <!-- Top Logo sayabantu.com -->
        <div class="text-center pt-1 mb-6">
            <x-sayabantu-logo size="lg" />
            <h2 class="text-xl font-extrabold text-gray-900 mt-4 tracking-tight">Lupa Kata Sandi?</h2>
            <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">
                Masukkan email Anda yang terdaftar, kami akan mengirimkan tautan untuk membuat kata sandi baru.
            </p>
        </div>

        <!-- Success Status -->
        @if (session('status'))
            <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-2.5 text-xs text-emerald-800 shadow-2xs">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium leading-relaxed">{{ session('status') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="sendPasswordResetLink" class="space-y-4">
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-1.5">
                    Alamat Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input type="email" id="email" wire:model="email" required autofocus
                        class="w-full pl-10 pr-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium"
                        placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full bg-[#0066FF] hover:bg-[#0055D4] active:scale-[0.98] text-white font-bold py-3.5 sm:py-4 rounded-full shadow-md shadow-blue-500/25 transition text-base tracking-wide disabled:opacity-60 flex items-center justify-center gap-2 cursor-pointer">
                    <svg wire:loading wire:target="sendPasswordResetLink" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="sendPasswordResetLink">Kirim Link Reset Password</span>
                    <span wire:loading wire:target="sendPasswordResetLink">Mengirim...</span>
                </button>
            </div>
        </form>

        <!-- Info Card -->
        <div class="mt-6 p-4 bg-blue-50/70 border border-blue-100 rounded-2xl flex items-start gap-3 text-xs text-blue-900 shadow-2xs">
            <div class="w-6 h-6 rounded-full bg-blue-100 text-[#0066FF] flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 mb-0.5">Periksa Email Anda</h4>
                <p class="text-gray-500 leading-relaxed">
                    Tautan reset kata sandi aman akan dikirimkan ke kotak masuk atau folder spam Anda dan berlaku selama 60 menit.
                </p>
            </div>
        </div>
    </div>

    <!-- Back to Login -->
    <div class="mt-8 pt-4 text-center">
        <p class="text-xs sm:text-sm text-gray-500 font-medium">
            Ingat kata sandi Anda?
            <a href="{{ route('login') }}" class="font-bold text-[#0066FF] hover:underline ml-1">
                Kembali ke Login
            </a>
        </p>
    </div>
</div>