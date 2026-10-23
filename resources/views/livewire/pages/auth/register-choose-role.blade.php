<?php

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public function choose($role): void
    {
        $allowed = ['customer', 'mitra'];
        $role = in_array($role, $allowed) ? $role : 'customer';

        $this->redirect(route('register', ['role' => $role]), navigate: false);
    }
}; ?>

<div class="w-full">

    {{-- Top Logo + Heading --}}
    <div class="text-center pt-2 pb-5">
        <x-sayabantu-logo size="lg" />
        <h2 class="text-xl font-extrabold text-gray-900 mt-4 tracking-tight">Daftar Akun Baru</h2>
        <p class="text-xs text-gray-400 mt-1">Pilih jenis akun untuk melanjutkan pendaftaran</p>
    </div>

    @php $googlePending = session('google_pending'); @endphp

    @if($googlePending)
        {{-- ============================================================ --}}
        {{-- MODE GOOGLE PENDING: user sudah autentikasi Google,          --}}
        {{-- tinggal pilih role saja — 1 klik langsung selesai            --}}
        {{-- ============================================================ --}}

        {{-- Info banner --}}
        <div class="mb-5 p-3.5 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-3">
            @if(!empty($googlePending['avatar']))
            <div class="relative w-10 h-10 flex-shrink-0">
                <img src="{{ $googlePending['avatar'] }}"
                     alt="{{ $googlePending['name'] }}"
                     referrerpolicy="no-referrer"
                     class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-200"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-10 h-10 rounded-full bg-[#0066FF] text-white text-sm font-bold items-center justify-center ring-2 ring-blue-200 absolute inset-0" style="display:none;">
                    {{ strtoupper(substr($googlePending['name'], 0, 1)) }}
                </div>
            </div>
            @else
                <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-blue-900">Halo, {{ $googlePending['name'] }}! 👋</p>
                <p class="text-[11px] text-blue-600 mt-0.5 leading-snug">Akun Google Anda belum terdaftar.<br>Pilih jenis akun di bawah untuk melanjutkan.</p>
            </div>
        </div>

        {{-- Pilih role — klik 1x langsung daftar via Google dengan role itu --}}
        <div class="space-y-3">

            {{-- Customer --}}
            <a href="{{ route('auth.google', ['role' => 'customer']) }}"
                class="w-full flex items-center gap-4 p-5 bg-white rounded-2xl border-2 border-slate-100 hover:border-[#0066FF] hover:shadow-xl hover:shadow-blue-500/10 active:scale-[0.98] transition-all duration-200 group cursor-pointer block">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-100 text-[#0066FF] flex items-center justify-center flex-shrink-0 group-hover:from-[#0066FF] group-hover:to-blue-600 group-hover:text-white transition-all duration-200 shadow-sm">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#0066FF] mb-1">👤 Butuh Bantuan</span>
                    <h3 class="font-extrabold text-[15px] text-gray-900 group-hover:text-[#0066FF] transition-colors leading-tight">Daftar sebagai Customer</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Cari bantuan serabutan & perbaikan rumah</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-50 text-gray-300 group-hover:bg-[#0066FF] group-hover:text-white flex items-center justify-center flex-shrink-0 transition-all transform group-hover:translate-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>

            {{-- Mitra --}}
            <a href="{{ route('auth.google', ['role' => 'mitra']) }}"
                class="w-full flex items-center gap-4 p-5 bg-white rounded-2xl border-2 border-slate-100 hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-500/10 active:scale-[0.98] transition-all duration-200 group cursor-pointer block">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:from-emerald-500 group-hover:to-emerald-600 group-hover:text-white transition-all duration-200 shadow-sm">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7h-4V5l-2-2h-4L8 5v2H4c-1.1 0-2 .9-2 2v5c0 .75.4 1.41 1 1.78V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-3.22c.6-.37 1-1.03 1-1.78V9c0-1.1-.9-2-2-2zm-8-2h4v2h-4V5zm0 10H5v-2h7v2zm2 0v-2h2v2h-2zm4 0h-2v-2h2v2zm0-4H5V9h14v2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 mb-1">🤝 Penyedia Jasa</span>
                    <h3 class="font-extrabold text-[15px] text-gray-900 group-hover:text-emerald-700 transition-colors leading-tight">Daftar sebagai Mitra</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Terima pesanan & dapatkan penghasilan tambahan</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-50 text-gray-300 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-all transform group-hover:translate-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>
        </div>

        <p class="text-center text-[11px] text-gray-400 mt-4 px-4 leading-relaxed">
            Akun akan dibuat menggunakan Google Anda:<br>
            <span class="font-semibold text-gray-500">{{ $googlePending['email'] }}</span>
        </p>

    @else
        {{-- ============================================================ --}}
        {{-- MODE NORMAL: user belum autentikasi Google                   --}}
        {{-- Tampilkan kartu dengan 2 opsi: email atau Google             --}}
        {{-- ============================================================ --}}

        <div class="space-y-3">

            {{-- ========== CUSTOMER CARD ========== --}}
            <div class="w-full rounded-2xl border-2 border-slate-100 bg-white overflow-hidden hover:border-[#0066FF] hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-200 group">
                <a href="{{ route('register', ['role' => 'customer']) }}"
                    class="flex items-center gap-4 p-5 cursor-pointer block active:scale-[0.99] transition-transform">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-100 text-[#0066FF] flex items-center justify-center flex-shrink-0 group-hover:from-[#0066FF] group-hover:to-blue-600 group-hover:text-white transition-all duration-200 shadow-sm">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#0066FF] mb-1">👤 Butuh Bantuan</span>
                        <h3 class="font-extrabold text-[15px] text-gray-900 group-hover:text-[#0066FF] transition-colors leading-tight">Sebagai Customer</h3>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Cari bantuan serabutan, perbaikan, dan kebersihan.</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-50 text-gray-300 group-hover:bg-[#0066FF] group-hover:text-white flex items-center justify-center flex-shrink-0 transition-all transform group-hover:translate-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </a>
                <div class="border-t border-slate-100 mx-4 mb-3">
                    <a href="{{ route('auth.google', ['role' => 'customer']) }}"
                        class="w-full mt-2.5 flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-[#0066FF] text-xs font-semibold text-gray-600 hover:text-[#0066FF] transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                        Daftar sebagai Customer dengan Google
                    </a>
                </div>
            </div>

            {{-- ========== MITRA CARD ========== --}}
            <div class="w-full rounded-2xl border-2 border-slate-100 bg-white overflow-hidden hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-200 group">
                <a href="{{ route('register', ['role' => 'mitra']) }}"
                    class="flex items-center gap-4 p-5 cursor-pointer block active:scale-[0.99] transition-transform">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:from-emerald-500 group-hover:to-emerald-600 group-hover:text-white transition-all duration-200 shadow-sm">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7h-4V5l-2-2h-4L8 5v2H4c-1.1 0-2 .9-2 2v5c0 .75.4 1.41 1 1.78V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-3.22c.6-.37 1-1.03 1-1.78V9c0-1.1-.9-2-2-2zm-8-2h4v2h-4V5zm0 10H5v-2h7v2zm2 0v-2h2v2h-2zm4 0h-2v-2h2v2zm0-4H5V9h14v2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 mb-1">🤝 Penyedia Jasa</span>
                        <h3 class="font-extrabold text-[15px] text-gray-900 group-hover:text-emerald-700 transition-colors leading-tight">Daftar Menjadi Mitra</h3>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Terima pesanan dan dapatkan penghasilan tambahan.</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-50 text-gray-300 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center flex-shrink-0 transition-all transform group-hover:translate-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </a>
                <div class="border-t border-slate-100 mx-4 mb-3">
                    <a href="{{ route('auth.google', ['role' => 'mitra']) }}"
                        class="w-full mt-2.5 flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-emerald-500 text-xs font-semibold text-gray-600 hover:text-emerald-700 transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/></svg>
                        Daftar sebagai Mitra dengan Google
                    </a>
                </div>
            </div>

            <p class="text-center text-[11px] text-gray-400 pt-1 px-4 leading-relaxed">
                Dengan mendaftar, Anda menyetujui
                <span class="text-[#0066FF] font-semibold">Syarat & Ketentuan</span>
                serta <span class="text-[#0066FF] font-semibold">Kebijakan Privasi</span> kami.
            </p>
        </div>
    @endif

    {{-- Login Link --}}
    <div class="mt-6 pt-4 pb-2 text-center border-t border-slate-100">
        <p class="text-sm text-gray-500 font-medium">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-[#0066FF] font-bold hover:underline ml-1">Login</a>
        </p>
    </div>

</div>