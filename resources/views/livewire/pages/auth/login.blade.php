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
    public function fillCredentials(string $email, string $name = '', string $password = 'Sayabantu123!'): void
    {
        $this->form->email = $email;
        $this->form->password = $password;
        $this->selectedEmail = $email;
        $this->selectedName = $name;
        $this->resetErrorBag();
    }

    /**
     * Login langsung (1 klik) untuk akun testing.
     * Langsung autentikasi tanpa perlu klik tombol Masuk lagi.
     */
    public function quickLogin(string $email, string $password = 'Sayabantu123!'): void
    {
        $this->resetErrorBag();

        // Cek akun ada dan password valid
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            $this->addError('quickLogin', 'Akun testing tidak ditemukan: ' . $email);
            return;
        }

        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            $this->addError('quickLogin', 'Password tidak cocok untuk akun: ' . $email);
            return;
        }

        // Cek status akun
        if ($user->status === 'banned' || $user->is_banned) {
            session()->flash('error', 'Akun ini diblokir.');
            return;
        }

        // Login langsung
        Auth::login($user, false);
        Session::regenerate();

        // Redirect sesuai role
        if ($user->role === 'mitra') {
            $redirect = route('mitra.dashboard', absolute: false);
        } elseif (in_array($user->role, ['customer', 'kustomer'])) {
            $redirect = route('customer.dashboard', absolute: false);
        } elseif ($user->role === 'admin') {
            $redirect = route('admin.dashboard', absolute: false);
        } elseif ($user->role === 'super_admin') {
            $redirect = route('superadmin.dashboard', absolute: false);
        } else {
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

        <form wire:submit="login" class="space-y-4">
            <!-- Email / Username Address -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Email atau Username <span class="text-red-500">*</span>
                </label>
                <input wire:model="form.email" id="email" type="text" name="email" required
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

            <!-- Log In Button -->
            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled" wire:target="login"
                    class="w-full bg-primary-500 hover:bg-primary-600 active:scale-98 text-white font-bold py-3.5 rounded-full shadow-md hover:shadow-lg transition text-base tracking-wide disabled:opacity-50 flex items-center justify-center gap-2">
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
                class="block w-full py-3 rounded-full border-2 border-primary-500 text-primary-600 hover:bg-primary-50 font-bold text-center text-sm transition active:scale-98">
                Daftar Akun Baru
            </a>
        </div>

        <!-- ========================================================================= -->
        <!-- AKUN TESTING — 1 KLIK LANGSUNG MASUK                                      -->
        <!-- ========================================================================= -->
        <div class="mt-6 pt-5 border-t border-dashed border-gray-300" x-data="{ tab: 'customer', loading: null }">
            <!-- Header -->
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Akun Testing — Login Cepat
                    </span>
                    <p class="text-[10px] text-gray-500 mt-0.5">⚡ Klik 1× untuk langsung masuk ke dashboard</p>
                </div>
                <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/></svg>
                    Sayabantu123!
                </span>
            </div>

            @error('quickLogin')
                <div class="mb-2 p-2.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror

            <!-- Tab Buttons -->
            <div class="flex gap-1.5 p-1 bg-gray-100 rounded-xl mb-3 text-xs font-semibold">
                <button type="button" @click="tab = 'customer'"
                    :class="tab === 'customer' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-1.5 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>👤</span> Customer
                </button>
                <button type="button" @click="tab = 'mitra'"
                    :class="tab === 'mitra' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-1.5 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>🤝</span> Mitra
                </button>
                <button type="button" @click="tab = 'admin'"
                    :class="tab === 'admin' ? 'bg-white text-purple-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-1.5 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>🛡️</span> Admin
                </button>
            </div>

            <!-- Tab 1: Customer -->
            <div x-show="tab === 'customer'" class="space-y-1.5">
                @php
                    $testCustomers = [
                        ['name' => 'Dewi Rahayu', 'area' => 'Yogyakarta', 'email' => 'dewi.jogja@test.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Gondokusuman'],
                        ['name' => 'Budi Hartono', 'area' => 'Sleman', 'email' => 'budi.sleman@test.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Depok'],
                        ['name' => 'Siti Nurhaliza', 'area' => 'Jakarta Pusat', 'email' => 'customer@sayabantu.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Gambir'],
                        ['name' => 'Budi Santoso', 'area' => 'Jakarta Pusat', 'email' => 'budi@example.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Gambir'],
                        ['name' => 'Dewi Lestari', 'area' => 'Jakarta Pusat', 'email' => 'customer.pending@sayabantu.com', 'bal' => 'Rp 0', 'ver' => false, 'loc' => 'Pending Verifikasi KTP'],
                    ];
                @endphp
                @foreach ($testCustomers as $c)
                    <button type="button"
                        wire:click="quickLogin('{{ $c['email'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="quickLogin('{{ $c['email'] }}')"
                        @click="loading = '{{ $c['email'] }}'"
                        class="w-full p-2.5 rounded-xl border text-left transition active:scale-[0.98] flex items-center gap-2.5 group cursor-pointer relative overflow-hidden
                            {{ $c['ver'] ? 'border-gray-200 bg-white hover:border-blue-400 hover:bg-blue-50/50 hover:shadow-sm' : 'border-dashed border-gray-200 bg-gray-50 opacity-80' }}">
                        <!-- Loading overlay -->
                        <div wire:loading wire:target="quickLogin('{{ $c['email'] }}')"
                            class="absolute inset-0 bg-blue-50/90 flex items-center justify-center z-10 rounded-xl">
                            <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs text-blue-600 font-semibold ml-1.5">Masuk...</span>
                        </div>
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 {{ $c['ver'] ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ strtoupper(substr($c['name'], 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-blue-700 truncate">{{ $c['name'] }}</p>
                                @if($c['ver'])
                                    <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-emerald-100 text-emerald-700 flex-shrink-0">✓ Verified</span>
                                @else
                                    <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-amber-100 text-amber-700 flex-shrink-0">Pending</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-500 truncate">{{ $c['area'] }} • {{ $c['loc'] }} • Saldo {{ $c['bal'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endforeach
            </div>

            <!-- Tab 2: Mitra -->
            <div x-show="tab === 'mitra'" style="display: none;" class="space-y-1.5">
                @php
                    $testMitras = [
                        ['name' => 'Hendra Wijaya', 'area' => 'Yogyakarta', 'email' => 'hendra.mitra@test.com', 'bal' => 'Rp 250rb', 'ver' => true, 'loc' => 'Gondokusuman'],
                        ['name' => 'Rina Susanti', 'area' => 'Yogyakarta', 'email' => 'rina.mitra@test.com', 'bal' => 'Rp 250rb', 'ver' => true, 'loc' => 'Mergangsan'],
                        ['name' => 'Doni Setiawan', 'area' => 'Sleman', 'email' => 'doni.mitra@test.com', 'bal' => 'Rp 250rb', 'ver' => true, 'loc' => 'Mlati'],
                        ['name' => 'Rahmat Hidayat', 'area' => 'Jakarta Pusat', 'email' => 'mitra@sayabantu.com', 'bal' => 'Rp 250rb', 'ver' => true, 'loc' => 'Menteng'],
                        ['name' => 'Ahmad Relawan', 'area' => 'Jakarta Pusat', 'email' => 'ahmad@example.com', 'bal' => 'Rp 250rb', 'ver' => true, 'loc' => 'Senen'],
                    ];
                @endphp
                @foreach ($testMitras as $m)
                    <button type="button"
                        wire:click="quickLogin('{{ $m['email'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="quickLogin('{{ $m['email'] }}')"
                        class="w-full p-2.5 rounded-xl border text-left transition active:scale-[0.98] flex items-center gap-2.5 group cursor-pointer relative overflow-hidden border-gray-200 bg-white hover:border-emerald-400 hover:bg-emerald-50/50 hover:shadow-sm">
                        <div wire:loading wire:target="quickLogin('{{ $m['email'] }}')"
                            class="absolute inset-0 bg-emerald-50/90 flex items-center justify-center z-10 rounded-xl">
                            <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs text-emerald-600 font-semibold ml-1.5">Masuk...</span>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($m['name'], 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-emerald-700 truncate">{{ $m['name'] }}</p>
                                <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-emerald-100 text-emerald-700 flex-shrink-0">✓ Aktif</span>
                            </div>
                            <p class="text-[10px] text-gray-500 truncate">{{ $m['area'] }} • {{ $m['loc'] }} • Saldo {{ $m['bal'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endforeach
            </div>

            <!-- Tab 3: Admin & Super Admin -->
            <div x-show="tab === 'admin'" style="display: none;" class="space-y-1.5">
                @php
                    $testAdmins = [
                        ['name' => 'Super Admin', 'area' => 'Nasional', 'email' => 'superadmin@sayabantu.com', 'role' => 'Super Admin', 'color' => 'indigo'],
                        ['name' => 'Admin Jakarta Pusat', 'area' => 'Jakarta Pusat', 'email' => 'admin.jakarta@sayabantu.com', 'role' => 'Admin Wilayah', 'color' => 'purple'],
                        ['name' => 'Admin DI Yogyakarta', 'area' => 'Yogyakarta', 'email' => 'admin.jogja@test.com', 'role' => 'Admin Wilayah', 'color' => 'purple'],
                        ['name' => 'Admin Jakarta Selatan', 'area' => 'Jakarta Selatan', 'email' => 'admin.jaksel@sayabantu.com', 'role' => 'Admin Wilayah', 'color' => 'blue'],
                        ['name' => 'Admin Utama', 'area' => 'Operasional', 'email' => 'admin@sayabantu.com', 'role' => 'Admin', 'color' => 'violet'],
                    ];
                @endphp
                @foreach ($testAdmins as $a)
                    <button type="button"
                        wire:click="quickLogin('{{ $a['email'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="quickLogin('{{ $a['email'] }}')"
                        class="w-full p-2.5 rounded-xl border text-left transition active:scale-[0.98] flex items-center gap-2.5 group cursor-pointer relative overflow-hidden border-gray-200 bg-white hover:border-purple-400 hover:bg-purple-50/50 hover:shadow-sm">
                        <div wire:loading wire:target="quickLogin('{{ $a['email'] }}')"
                            class="absolute inset-0 bg-purple-50/90 flex items-center justify-center z-10 rounded-xl">
                            <svg class="animate-spin h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs text-purple-600 font-semibold ml-1.5">Masuk...</span>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ strtoupper(substr($a['name'], 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-purple-700 truncate">{{ $a['name'] }}</p>
                                <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-purple-100 text-purple-700 flex-shrink-0">{{ $a['role'] }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 truncate">{{ $a['area'] }} • {{ $a['email'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-purple-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endforeach
            </div>
        </div>
        <!-- ========================================================================= -->
        <!-- AKHIR BLOK AKUN TESTING                                                   -->
        <!-- ========================================================================= -->
    </div>


</div>

