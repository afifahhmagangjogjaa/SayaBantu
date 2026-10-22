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

<div class="flex-1 flex flex-col justify-between py-2">
    <div>
        <div class="text-center mb-6">
            <x-sayabantu-logo size="lg" />
            <h2 class="text-xl font-extrabold text-gray-900 mt-4 tracking-tight">Daftar Akun Baru</h2>
            <p class="text-xs text-gray-400 mt-1">Pilih jenis akun untuk melanjutkan pendaftaran</p>
        </div>

        <div class="space-y-3.5">
            <!-- Customer Option (Gambar 2) -->
            <button wire:click="choose('customer')" type="button"
                class="w-full p-4 bg-white rounded-2xl border border-gray-200 hover:border-[#0066FF] hover:bg-blue-50/30 active:scale-[0.99] transition flex items-center gap-3.5 text-left shadow-2xs group cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#0066FF] flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition">
                    👤
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm text-gray-900 group-hover:text-[#0066FF]">Sebagai Customer</h3>
                    <p class="text-xs text-gray-500 mt-0.5 leading-snug">Cari bantuan serabutan, perbaikan, dan kebersihan dengan mudah.</p>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-[#0066FF] transition transform group-hover:translate-x-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            <!-- Mitra Option (Gambar 1) -->
            <button wire:click="choose('mitra')" type="button"
                class="w-full p-4 bg-white rounded-2xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/30 active:scale-[0.99] transition flex items-center gap-3.5 text-left shadow-2xs group cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-105 transition">
                    🤝
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm text-gray-900 group-hover:text-emerald-700">Daftar Menjadi Mitra</h3>
                    <p class="text-xs text-gray-500 mt-0.5 leading-snug">Terima pesanan pekerjaan dan dapatkan penghasilan tambahan.</p>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-emerald-600 transition transform group-hover:translate-x-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>
    </div>

    <!-- Login Link at bottom -->
    <div class="mt-8 pt-4 text-center">
        <p class="text-xs text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" wire:navigate class="text-[#0066FF] font-bold hover:underline">Login</a>
        </p>
    </div>
</div>