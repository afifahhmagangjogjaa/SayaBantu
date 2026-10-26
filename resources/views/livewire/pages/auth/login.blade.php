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
    public string $role = 'customer';

    public function mount(): void
    {
        $this->role = request()->query('role', 'customer');
    }

    public function setRole(string $role): void
    {
        $this->role = in_array($role, ['mitra', 'customer']) ? $role : 'customer';
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();

        if ($user->role === 'mitra') {
            $redirect = route('mitra.dashboard', absolute: false);
        } elseif ($user->role === 'kustomer' || $user->role === 'customer') {
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

    /**
     * Login langsung (1 klik) untuk akun testing.
     */
    public function quickLogin(string $email, string $password = 'Sayabantu123!'): void
    {
        $this->resetErrorBag();

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            $this->addError('quickLogin', 'Akun testing tidak ditemukan: ' . $email);
            return;
        }

        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            $this->addError('quickLogin', 'Password tidak cocok untuk akun: ' . $email);
            return;
        }

        if ($user->status === 'banned' || $user->is_banned) {
            session()->flash('error', 'Akun ini diblokir.');
            return;
        }

        Auth::login($user, false);
        Session::regenerate();

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

<div class="w-full flex-1 flex flex-col justify-between" x-data="{ testOpen: false, demoRole: 'customer' }">
    <div>
        <!-- Top Logo sayabantu.com -->
        <div class="pt-1 pb-3 text-center">
            <x-sayabantu-logo size="lg" />
            
            <h2 class="text-xl font-extrabold text-gray-900 mt-4 tracking-tight">
                Masuk ke Akun Anda
            </h2>
            <p class="text-xs text-gray-500 mt-1">
                Jasa Serabutan, Mudah, Cepat, Terpercaya
            </p>
        </div>

        <!-- Session Status / Alert -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if (session('error'))
            <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-2xl text-red-700 flex items-start gap-2.5 text-xs shadow-xs">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="leading-relaxed font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <form wire:submit="login" class="space-y-4 mt-1">
            <!-- Nomor HP / Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-1.5">
                    Nomor HP / Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <input wire:model="form.email" id="email" type="text" name="email" required
                        autocomplete="username" placeholder="Contoh: 08123456789 atau email"
                        class="w-full pl-10 pr-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium">
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-gray-700">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-[11px] font-semibold text-[#0066FF] hover:underline">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input wire:model="form.password" id="password" x-bind:type="show ? 'text' : 'password'" name="password" required
                        autocomplete="current-password" placeholder="••••••••"
                        class="w-full pl-10 pr-12 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium">
                    
                    <button type="button" @click="show = !show"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                        <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
            </div>

            <!-- Login Button -->
            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled" wire:target="login"
                    class="w-full bg-[#0066FF] hover:bg-[#0055D4] active:scale-[0.98] text-white font-bold py-3.5 sm:py-4 rounded-full shadow-md shadow-blue-500/25 transition text-base tracking-wide disabled:opacity-60 flex items-center justify-center gap-2 cursor-pointer">
                    <svg wire:loading wire:target="login" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="login">Login</span>
                    <span wire:loading wire:target="login">Memproses...</span>
                </button>
            </div>
        </form>

        <!-- Divider matching mockup: 'atau' -->
        <div class="relative flex items-center justify-center my-4">
            <div class="border-t border-gray-200 w-full"></div>
            <span class="bg-white px-3 text-xs text-gray-400 font-medium absolute">atau</span>
        </div>

        <!-- Google Login Button matching mockup -->
        <div>
            <a href="{{ route('auth.google') }}"
                class="w-full py-3.5 px-4 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold text-center text-sm transition active:scale-[0.98] flex items-center justify-center gap-2.5 shadow-2xs cursor-pointer">
                <!-- Google G colored SVG logo -->
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Login dengan Google</span>
            </a>
        </div>

        <!-- Register Link matching mockup -->
        <div class="mt-4 text-center">
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Belum punya akun?
                <a href="{{ route('register.choose-role') }}" class="font-bold text-[#0066FF] hover:underline ml-1">
                    Daftar
                </a>
            </p>
        </div>

        <!-- ========================================================================= -->
        <!-- AKUN TESTING — 1 KLIK LANGSUNG MASUK (DROPDOWN / DRAWER BERSIH)           -->
        <!-- ========================================================================= -->
        <div class="mt-6 pt-3 border-t border-slate-100">
            <button type="button" @click="testOpen = !testOpen"
                    class="w-full flex items-center justify-between text-xs font-bold text-gray-700 hover:text-[#0066FF] py-1 transition cursor-pointer">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Akun Demo / Testing</span>
                </div>
                <div class="flex items-center gap-1 text-[11px] text-gray-400 font-normal">
                    <span x-text="testOpen ? 'Sembunyikan' : 'Buka Demo'"></span>
                    <svg class="w-3.5 h-3.5 transform transition-transform" :class="testOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            <div x-show="testOpen" x-transition class="mt-2.5">

            @error('quickLogin')
                <div class="mb-2 p-2 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </div>
            @enderror

            <!-- Testing Tab Selector -->
            <div class="mb-2 px-3 py-1.5 bg-blue-50/90 border border-blue-100 rounded-xl flex items-center justify-between text-[11px] text-blue-900 font-medium">
                <span>Password semua akun: <code class="font-bold text-[#0066FF] bg-white px-1.5 py-0.5 rounded border border-blue-200">Sayabantu123!</code></span>
                <span class="text-blue-600 font-semibold">1 Klik Masuk</span>
            </div>

            <div class="flex gap-1 p-1 bg-slate-100 rounded-xl mb-2.5 text-xs font-semibold">
                <button type="button" @click="demoRole = 'customer'"
                    :class="demoRole === 'customer' ? 'bg-white text-blue-700 shadow-2xs' : 'text-gray-500 hover:text-gray-800'"
                    class="flex-1 py-1 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>👤</span> Customer
                </button>
                <button type="button" @click="demoRole = 'mitra'"
                    :class="demoRole === 'mitra' ? 'bg-white text-emerald-700 shadow-2xs' : 'text-gray-500 hover:text-gray-800'"
                    class="flex-1 py-1 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>🤝</span> Mitra
                </button>
                <button type="button" @click="demoRole = 'admin'"
                    :class="demoRole === 'admin' ? 'bg-white text-purple-700 shadow-2xs' : 'text-gray-500 hover:text-gray-800'"
                    class="flex-1 py-1 px-2 rounded-lg transition text-center flex items-center justify-center gap-1">
                    <span>🛡️</span> Admin
                </button>
            </div>

            <!-- Tab 1: Customer -->
            <div x-show="demoRole === 'customer'" class="space-y-1.5">
                @php
                    $testCustomers = [
                        ['name' => 'Dewi Rahayu', 'area' => 'Yogyakarta', 'email' => 'dewi.jogja@test.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Gondokusuman'],
                        ['name' => 'Budi Hartono', 'area' => 'Sleman', 'email' => 'budi.sleman@test.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Depok'],
                        ['name' => 'Siti Nurhaliza', 'area' => 'Jakarta Pusat', 'email' => 'customer@sayabantu.com', 'bal' => 'Rp 1jt', 'ver' => true, 'loc' => 'Gambir'],
                    ];
                @endphp
                @foreach ($testCustomers as $c)
                    <button type="button"
                        wire:click="quickLogin('{{ $c['email'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="quickLogin('{{ $c['email'] }}')"
                        class="w-full p-2.5 rounded-xl border border-gray-200 bg-white hover:border-[#0066FF] hover:bg-blue-50/40 text-left transition active:scale-[0.99] flex items-center gap-2.5 group cursor-pointer relative overflow-hidden shadow-2xs">
                        <div wire:loading wire:target="quickLogin('{{ $c['email'] }}')"
                            class="absolute inset-0 bg-blue-50/90 flex items-center justify-center z-10 rounded-xl">
                            <svg class="animate-spin h-4 w-4 text-[#0066FF]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs text-[#0066FF] font-semibold ml-1.5">Masuk...</span>
                        </div>
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 bg-blue-100 text-[#0066FF]">
                            {{ strtoupper(substr($c['name'], 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-[#0066FF] truncate">{{ $c['name'] }}</p>
                                <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-emerald-100 text-emerald-700 flex-shrink-0">✓ Saldo {{ $c['bal'] }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 truncate">{{ $c['area'] }} • {{ $c['loc'] }}</p>
                        </div>
                        <span class="text-[10px] font-semibold text-[#0066FF] px-2 py-1 rounded-lg bg-blue-50 group-hover:bg-[#0066FF] group-hover:text-white transition">Masuk →</span>
                    </button>
                @endforeach
            </div>

            <!-- Tab 2: Mitra -->
            <div x-show="demoRole === 'mitra'" class="space-y-1.5">
                @php
                    $testMitras = [
                        ['name' => 'Budi Santoso', 'area' => 'Yogyakarta', 'email' => 'budi.mitra@test.com', 'bal' => 'Rp 250rb', 'loc' => 'Mitra Aktif (⭐ 4.8)'],
                        ['name' => 'Hendra Wijaya', 'area' => 'Yogyakarta', 'email' => 'hendra.mitra@test.com', 'bal' => 'Rp 250rb', 'loc' => 'Gondokusuman'],
                        ['name' => 'Rina Susanti', 'area' => 'Yogyakarta', 'email' => 'rina.mitra@test.com', 'bal' => 'Rp 250rb', 'loc' => 'Mergangsan'],
                        ['name' => 'Doni Setiawan', 'area' => 'Sleman', 'email' => 'doni.mitra@test.com', 'bal' => 'Rp 250rb', 'loc' => 'Mlati'],
                    ];
                @endphp
                @foreach ($testMitras as $m)
                    <button type="button"
                        wire:click="quickLogin('{{ $m['email'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="quickLogin('{{ $m['email'] }}')"
                        class="w-full p-2.5 rounded-xl border border-gray-200 bg-white hover:border-emerald-500 hover:bg-emerald-50/40 text-left transition active:scale-[0.99] flex items-center gap-2.5 group cursor-pointer relative overflow-hidden shadow-2xs">
                        <div wire:loading wire:target="quickLogin('{{ $m['email'] }}')"
                            class="absolute inset-0 bg-emerald-50/90 flex items-center justify-center z-10 rounded-xl">
                            <svg class="animate-spin h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs text-emerald-600 font-semibold ml-1.5">Masuk...</span>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($m['name'], 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-emerald-700 truncate">{{ $m['name'] }}</p>
                                <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-emerald-100 text-emerald-700 flex-shrink-0">✓ Mitra Aktif</span>
                            </div>
                            <p class="text-[10px] text-gray-500 truncate">{{ $m['area'] }} • {{ $m['loc'] }} • Saldo {{ $m['bal'] }}</p>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-700 px-2 py-1 rounded-lg bg-emerald-50 group-hover:bg-emerald-600 group-hover:text-white transition">Masuk →</span>
                    </button>
                @endforeach
            </div>

            <!-- Tab 3: Admin & Super Admin -->
            <div x-show="demoRole === 'admin'" class="space-y-1.5">
                @php
                    $testAdmins = [
                        ['name' => 'Super Admin', 'area' => 'Nasional', 'email' => 'superadmin@sayabantu.com', 'role' => 'Super Admin'],
                        ['name' => 'Admin DI Yogyakarta', 'area' => 'Yogyakarta', 'email' => 'admin.jogja@test.com', 'role' => 'Admin Wilayah'],
                        ['name' => 'Admin Jakarta Pusat', 'area' => 'Jakarta Pusat', 'email' => 'admin.jakarta@sayabantu.com', 'role' => 'Admin Wilayah'],
                    ];
                @endphp
                @foreach ($testAdmins as $a)
                    <button type="button"
                        wire:click="quickLogin('{{ $a['email'] }}')"
                        wire:loading.attr="disabled"
                        wire:target="quickLogin('{{ $a['email'] }}')"
                        class="w-full p-2.5 rounded-xl border border-gray-200 bg-white hover:border-purple-400 hover:bg-purple-50/40 text-left transition active:scale-[0.99] flex items-center gap-2.5 group cursor-pointer relative overflow-hidden shadow-2xs">
                        <div wire:loading wire:target="quickLogin('{{ $a['email'] }}')"
                            class="absolute inset-0 bg-purple-50/90 flex items-center justify-center z-10 rounded-xl">
                            <svg class="animate-spin h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs text-purple-600 font-semibold ml-1.5">Masuk...</span>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($a['name'], 0, 1)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold text-gray-900 group-hover:text-purple-700 truncate">{{ $a['name'] }}</p>
                                <span class="inline-flex text-[9px] font-bold px-1 py-0.5 rounded bg-purple-100 text-purple-700 flex-shrink-0">{{ $a['role'] }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 truncate">{{ $a['area'] }} • {{ $a['email'] }}</p>
                        </div>
                        <span class="text-[10px] font-semibold text-purple-700 px-2 py-1 rounded-lg bg-purple-50 group-hover:bg-purple-600 group-hover:text-white transition">Masuk →</span>
                    </button>
                @endforeach
            </div>
            </div>
        </div>
        <!-- ========================================================================= -->
    </div>
</div>


