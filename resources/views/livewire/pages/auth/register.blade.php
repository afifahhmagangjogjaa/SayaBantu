<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public function startRegistration(): void
    {
        // Redirect ke step 1 untuk registrasi multi-step
        $this->redirect(route('register.choose-role'), navigate: true);
    }
}; ?>

<div class="flex-1 flex flex-col justify-between">
    <div class="space-y-4">
        <!-- Title -->
        <div class="text-center">
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Daftar Akun Baru</h1>
            <p class="text-sm text-gray-600 mt-1">Lengkapi data untuk verifikasi identitas</p>
        </div>

        <!-- Yang Perlu Disiapkan Card -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm space-y-3">
            <h3 class="font-bold text-sm text-gray-900">Yang Perlu Disiapkan:</h3>
            <div class="space-y-3 text-gray-700">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-bold text-sm">1</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">Data KTP</h4>
                        <p class="text-xs text-gray-600 mt-0.5">NIK, nama lengkap & alamat sesuai KTP</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold text-sm">2</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">Foto KTP</h4>
                        <p class="text-xs text-gray-600 mt-0.5">Foto fisik KTP asli yang jelas</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-bold text-sm">3</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">Foto Selfie + KTP</h4>
                        <p class="text-xs text-gray-600 mt-0.5">Foto selfie sambil memegang KTP</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-orange-600 font-bold text-sm">4</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">Email & Password</h4>
                        <p class="text-xs text-gray-600 mt-0.5">Email aktif untuk login</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Penting Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 text-blue-900">
            <h4 class="font-bold text-blue-950 text-sm">Informasi Penting</h4>
            <p class="text-xs text-blue-900/90 mt-1 leading-relaxed">Data yang Anda berikan akan digunakan untuk verifikasi identitas. Pastikan benar dan sesuai KTP.</p>
        </div>
    </div>

    <!-- Actions (Mepet ke Bawah) -->
    <div class="mt-auto pt-5 space-y-2.5">
        <button wire:click="startRegistration" type="button" class="w-full bg-primary-500 hover:bg-primary-600 active:scale-98 text-white font-bold py-3.5 rounded-full shadow transition text-base tracking-wide" style="background-color: #0098e7;">
            Mulai Pendaftaran
        </button>

        <div class="text-center pt-0.5">
            <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate class="text-primary-600 font-bold hover:underline" style="color: #0077cc;">Login</a></p>
        </div>
    </div>
</div>