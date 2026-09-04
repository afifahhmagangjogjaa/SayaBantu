<?php

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public function choose($role): void
    {
        // Normalize accepted roles
        $allowed = ['customer', 'mitra'];
        $role = in_array($role, $allowed) ? $role : 'customer';

        $this->redirect(route('register', ['role' => $role]));
    }
}; ?>

<div class="flex-1 flex flex-col justify-between">
    <div>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pilih Peran Anda</h2>
            <p class="text-sm text-gray-600 mt-1">Pilih jenis akun yang ingin Anda daftarkan</p>
        </div>

        <div class="space-y-4">
            <!-- Customer Option -->
            <button wire:click="choose('customer')" type="button"
                class="w-full p-5 bg-white rounded-2xl border-2 border-gray-100 hover:border-blue-500 hover:bg-blue-50/30 active:scale-[0.99] transition-all flex items-center gap-4 text-left shadow-sm group">
                <div class="w-13 h-13 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-105 transition">
                    👤
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-base text-gray-900">Customer</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Gunakan layanan untuk mencari bantuan cepat & terpercaya.</p>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-blue-600 transition transform group-hover:translate-x-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            <!-- Mitra Option -->
            <button wire:click="choose('mitra')" type="button"
                class="w-full p-5 bg-white rounded-2xl border-2 border-gray-100 hover:border-green-500 hover:bg-green-50/30 active:scale-[0.99] transition-all flex items-center gap-4 text-left shadow-sm group">
                <div class="w-13 h-13 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center text-2xl flex-shrink-0 group-hover:scale-105 transition">
                    🤝
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-base text-gray-900">Mitra</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Menjadi penyedia jasa profesional & dapatkan penghasilan.</p>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-green-600 transition transform group-hover:translate-x-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>
    </div>

    <!-- Login Link at bottom -->
    <div class="mt-auto pt-6 text-center">
        <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate class="text-primary-600 font-bold hover:underline" style="color: #0077cc;">Login</a></p>
    </div>
</div>