<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;
    public string $selectedEmail = '';
    public string $selectedName = '';

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Determine redirect destination based on user role
        $user = auth()->user();

        // Redirect to role-specific dashboard
        if ($user->role === 'mitra') {
            $redirect = route('mitra.dashboard', absolute: false);
        } elseif ($user->role === 'kustomer' || $user->role === 'customer') {
            $redirect = route('customer.dashboard', absolute: false);
        } elseif ($user->role === 'admin') {
            $redirect = route('admin.dashboard', absolute: false);
        } elseif ($user->role === 'super_admin') {
            $redirect = route('superadmin.dashboard', absolute: false);
        } else {
            // Default fallback
            $redirect = route('dashboard', absolute: false);
        }

        $this->redirectIntended(default: $redirect, navigate: false);
    }

    /**
     * Mengisi otomatis kolom email & password tanpa langsung submit login.
     * User tinggal menekan tombol 'Masuk'.
     */
    public function fillCredentials(string $email, string $name = '', string $password = 'password'): void
    {
        $this->form->email = $email;
        $this->form->password = $password;
        $this->selectedEmail = $email;
        $this->selectedName = $name;
        $this->resetErrorBag();
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

        <form wire:submit="login" class="space-y-4">
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
                    <div class="flex justify-end mt-2">
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                            Lupa Password?
                        </a>
                    </div>
                @endif
            </div>

            <!-- Banner Notifikasi Saat Akun Testing Dipilih -->
            @if ($selectedEmail)
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center justify-between shadow-xs animate-fade-in">
                    <div class="flex items-center gap-2">
                        <span class="text-base flex-shrink-0">👉</span>
                        <div>
                            <p class="font-bold text-emerald-900">Akun Terpilih: {{ $selectedName ?: $selectedEmail }}</p>
                            <p class="text-[11px] text-emerald-700">Email & password sudah terisi. Silakan klik tombol <b>Masuk</b> di bawah.</p>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('selectedEmail', '')" class="text-emerald-500 hover:text-emerald-700 text-base font-bold px-1">&times;</button>
                </div>
            @endif

            <!-- Log In Button -->
            <div class="pt-2">
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
        <div class="relative flex items-center justify-center my-5">
            <div class="border-t border-gray-200 w-full"></div>
            <span class="bg-white px-4 text-xs text-gray-400 font-medium absolute">atau</span>
        </div>

        <!-- Register Button -->
        <div>
            <a href="{{ route('register.choose-role') }}" wire:navigate
                class="block w-full py-3 rounded-full border-2 hover:bg-blue-50/60 font-bold text-center text-sm transition active:scale-98"
                style="border-color: #0098e7; color: #0098e7;">
                Daftar Akun Baru
            </a>
        </div>

        <!-- ========================================================================= -->
        <!-- AKUN TESTING JOGJA (KLIK UNTUK MENGISI FORM, BUKAN LANGSUNG MASUK)         -->
        <!-- ========================================================================= -->
        <div class="mt-6 pt-5 border-t border-dashed border-gray-300" x-data="{ tab: 'customer' }">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Akun Testing Wilayah Jogja
                    </span>
                    <p class="text-[10px] text-gray-500 mt-0.5">Klik akun di bawah untuk mengisi form otomatis, lalu klik tombol Masuk</p>
                </div>
                <span class="text-[10px] text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full font-semibold">
                    Password: password
                </span>
            </div>

            <!-- Tab Buttons -->
            <div class="flex gap-1.5 p-1 bg-gray-100 rounded-xl mb-3 text-xs font-semibold">
                <button type="button" @click="tab = 'customer'"
                    :class="tab === 'customer' ? 'bg-white text-blue-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-1.5 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>👤</span> Customer (5)
                </button>
                <button type="button" @click="tab = 'mitra'"
                    :class="tab === 'mitra' ? 'bg-white text-emerald-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-1.5 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>🤝</span> Mitra (5)
                </button>
                <button type="button" @click="tab = 'admin'"
                    :class="tab === 'admin' ? 'bg-white text-purple-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-1.5 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>🛡️</span> Admin (1)
                </button>
            </div>

            <!-- Tab 1: Customer Jogja (5 Akun) -->
            <div x-show="tab === 'customer'" class="space-y-1.5">
                @php
                    $testCustomers = [
                        ['name' => 'Dewi Rahayu', 'area' => 'Kota Yogyakarta', 'email' => 'dewi.jogja@test.com', 'desc' => 'Saldo Rp 500rb • Terban'],
                        ['name' => 'Budi Hartono', 'area' => 'Kabupaten Sleman', 'email' => 'budi.sleman@test.com', 'desc' => 'Saldo Rp 300rb • Depok'],
                        ['name' => 'Sari Wulandari', 'area' => 'Kabupaten Bantul', 'email' => 'sari.bantul@test.com', 'desc' => 'Saldo Rp 750rb • Bantul'],
                        ['name' => 'Agus Prasetyo', 'area' => 'Kab. Gunungkidul', 'email' => 'agus.gunungkidul@test.com', 'desc' => 'Saldo Rp 200rb • Wonosari'],
                        ['name' => 'Ratna Kusuma', 'area' => 'Kab. Kulonprogo', 'email' => 'ratna.kulonprogo@test.com', 'desc' => 'Saldo Rp 400rb • Wates'],
                    ];
                @endphp
                @foreach ($testCustomers as $c)
                    <button type="button"
                        wire:click="fillCredentials('{{ $c['email'] }}', '{{ $c['name'] }}', 'password')"
                        @click="
                            const e = document.getElementById('email');
                            const p = document.getElementById('password');
                            if (e) { e.value = '{{ $c['email'] }}'; }
                            if (p) { p.value = 'password'; }
                        "
                        class="w-full p-2.5 rounded-xl border text-left transition active:scale-98 flex items-center justify-between group cursor-pointer
                            {{ $selectedEmail === $c['email'] ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                👤
                            </span>
                            <div class="truncate">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-blue-700 truncate">
                                    {{ $c['name'] }} <span class="text-[10px] text-blue-600 font-semibold">({{ $c['area'] }})</span>
                                </p>
                                <p class="text-[11px] text-gray-500 font-mono truncate">{{ $c['email'] }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md flex-shrink-0 ml-2
                            {{ $selectedEmail === $c['email'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-blue-100 group-hover:text-blue-700' }}">
                            {{ $selectedEmail === $c['email'] ? '✓ Terpilih' : 'Pilih Akun' }}
                        </span>
                    </button>
                @endforeach
            </div>

            <!-- Tab 2: Mitra Jogja (5 Akun) -->
            <div x-show="tab === 'mitra'" style="display: none;" class="space-y-1.5">
                @php
                    $testMitras = [
                        ['name' => 'Hendra Wijaya', 'area' => 'Kota Yogyakarta', 'email' => 'hendra.mitra@test.com', 'status' => 'Aktif', 'badge' => 'bg-emerald-100 text-emerald-800'],
                        ['name' => 'Rina Susanti', 'area' => 'Kota Yogyakarta', 'email' => 'rina.mitra@test.com', 'status' => 'Aktif', 'badge' => 'bg-emerald-100 text-emerald-800'],
                        ['name' => 'Doni Setiawan', 'area' => 'Kabupaten Sleman', 'email' => 'doni.mitra@test.com', 'status' => 'Aktif', 'badge' => 'bg-emerald-100 text-emerald-800'],
                        ['name' => 'Fitri Handayani', 'area' => 'Kabupaten Bantul', 'email' => 'fitri.mitra@test.com', 'status' => 'Aktif', 'badge' => 'bg-emerald-100 text-emerald-800'],
                        ['name' => 'Yusuf Pratama', 'area' => 'Kota Yogyakarta', 'email' => 'yusuf.mitra@test.com', 'status' => 'Pending Approval', 'badge' => 'bg-amber-100 text-amber-800'],
                    ];
                @endphp
                @foreach ($testMitras as $m)
                    <button type="button"
                        wire:click="fillCredentials('{{ $m['email'] }}', '{{ $m['name'] }}', 'password')"
                        @click="
                            const e = document.getElementById('email');
                            const p = document.getElementById('password');
                            if (e) { e.value = '{{ $m['email'] }}'; }
                            if (p) { p.value = 'password'; }
                        "
                        class="w-full p-2.5 rounded-xl border text-left transition active:scale-98 flex items-center justify-between group cursor-pointer
                            {{ $selectedEmail === $m['email'] ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200' : 'border-gray-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/40' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                🤝
                            </span>
                            <div class="truncate">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-bold text-gray-900 group-hover:text-emerald-700 truncate">{{ $m['name'] }}</p>
                                    <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded {{ $m['badge'] }}">{{ $m['status'] }}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 font-mono truncate">{{ $m['email'] }} <span class="text-gray-400 font-sans">• {{ $m['area'] }}</span></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md flex-shrink-0 ml-2
                            {{ $selectedEmail === $m['email'] ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-emerald-100 group-hover:text-emerald-700' }}">
                            {{ $selectedEmail === $m['email'] ? '✓ Terpilih' : 'Pilih Akun' }}
                        </span>
                    </button>
                @endforeach
            </div>

            <!-- Tab 3: Admin Jogja & Super Admin -->
            <div x-show="tab === 'admin'" style="display: none;" class="space-y-1.5">
                @php
                    $testAdmins = [
                        ['name' => 'Admin Yogyakarta', 'area' => 'Kota Yogyakarta', 'email' => 'admin.jogja@test.com', 'role' => 'Admin Daerah', 'badge' => 'bg-purple-100 text-purple-800'],
                        ['name' => 'Super Admin', 'area' => 'Nasional', 'email' => 'superadmin@sayabantu.com', 'role' => 'Super Admin', 'badge' => 'bg-indigo-100 text-indigo-800'],
                    ];
                @endphp
                @foreach ($testAdmins as $a)
                    <button type="button"
                        wire:click="fillCredentials('{{ $a['email'] }}', '{{ $a['name'] }}', 'password')"
                        @click="
                            const e = document.getElementById('email');
                            const p = document.getElementById('password');
                            if (e) { e.value = '{{ $a['email'] }}'; }
                            if (p) { p.value = 'password'; }
                        "
                        class="w-full p-2.5 rounded-xl border text-left transition active:scale-98 flex items-center justify-between group cursor-pointer
                            {{ $selectedEmail === $a['email'] ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-200' : 'border-gray-200 bg-white hover:border-purple-300 hover:bg-purple-50/40' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                🛡️
                            </span>
                            <div class="truncate">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-bold text-gray-900 group-hover:text-purple-700 truncate">{{ $a['name'] }}</p>
                                    <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded {{ $a['badge'] }}">{{ $a['role'] }}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 font-mono truncate">{{ $a['email'] }} <span class="text-gray-400 font-sans">• {{ $a['area'] }}</span></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md flex-shrink-0 ml-2
                            {{ $selectedEmail === $a['email'] ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-purple-100 group-hover:text-purple-700' }}">
                            {{ $selectedEmail === $a['email'] ? '✓ Terpilih' : 'Pilih Akun' }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- AKHIR BLOK AKUN TESTING JOGJA                                             -->
        <!-- ========================================================================= -->
    </div>

    <!-- Admin Login Link -->
    <div class="text-center pt-6 pb-2">
        <a href="/admin/login" class="text-xs font-semibold text-gray-500 hover:text-blue-600 hover:underline transition">
            Buka Halaman Khusus Admin Login →
        </a>
    </div>
</div>

