<x-app-layout>
    <x-slot name="title">Profil</x-slot>

    <div class="min-h-screen bg-white pb-28">
        @php
            $user = auth()->user() ? auth()->user()->fresh() : null;
            $userBalance = \App\Models\UserBalance::where('user_id', optional($user)->id)->first();
            $customerBalance = $userBalance ? $userBalance->balance : 0;

            $isVerified = (bool) ($user->verified ?? false);
            $isKtpVerified = $isVerified;
            $isEmailVerified = $user ? $user->hasVerifiedEmail() : false;
            $isFullyVerified = $isKtpVerified && $isEmailVerified;

            $missingFields = $user ? $user->getMissingBiodataFields() : [];
            $hasKtp = !empty($user->ktp_photo) || !empty($user->ktp_path);
            $hasSelfie = !empty($user->selfie_photo);
            $hasDocs = $hasKtp && $hasSelfie;

            // Format nomor telepon untuk tampilan yang rapi (contoh: 0812 3456 7890)
            $displayPhone = $user->phone ?? '';
            if ($displayPhone) {
                $clean = preg_replace('/[^0-9]/', '', (string) $displayPhone);
                if (strlen($clean) >= 10 && strlen($clean) <= 13) {
                    $displayPhone = substr($clean, 0, 4) . ' ' . substr($clean, 4, 4) . ' ' . substr($clean, 8);
                }
            } else {
                $displayPhone = $user->email ?? '-';
            }
        @endphp

        <div class="max-w-md mx-auto">
            <!-- Header Biru Profil (Sesuai Desain Acuan) -->
            <div class="relative px-5 pt-8 pb-6 bg-[#0077FF] rounded-b-[28px] shadow-sm text-white"
                 style="background: linear-gradient(180deg, #0077FF 0%, #0066FF 100%);">
                
                <!-- Baris Info Pengguna & Ikon Pengaturan -->
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Foto Profil Pengguna -->
                        <div class="relative group cursor-pointer shrink-0" 
                             onclick="Livewire.dispatch('openModal')"
                             title="Klik untuk ubah foto profil">
                            <div class="w-16 h-16 rounded-full border-2 border-white ring-2 ring-white/30 shadow-md overflow-hidden bg-white/20 flex items-center justify-center">
                                <x-user-avatar :user="$user" class="w-full h-full object-cover text-white font-bold text-xl" />
                            </div>
                            <!-- Badge kamera kecil -->
                            <div class="absolute -bottom-0.5 -right-0.5 bg-white text-[#0066FF] p-1 rounded-full shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Nama & Nomor HP -->
                        <div class="min-w-0 flex-1">
                            <h1 class="text-lg font-bold text-white tracking-tight leading-snug truncate">
                                {{ $user->name ?? 'Pengguna' }}
                            </h1>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <p class="text-sm text-blue-100 font-normal truncate">
                                    {{ $displayPhone }}
                                </p>
                                @if($isFullyVerified)
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-emerald-400 text-white shrink-0" title="Akun Terverifikasi">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Pengaturan (Gear) di Kanan Atas -->
                    <a href="{{ route('profile.settings') }}" wire:navigate 
                       class="p-2 text-white/90 hover:text-white rounded-full hover:bg-white/15 active:scale-95 transition shrink-0" 
                       aria-label="Pengaturan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                </div>

                <!-- Card Saldo / Wallet -->
                <div class="mt-5 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 text-white flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <!-- Icon Saldo -->
                        <div class="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-blue-100 font-medium">Saldo / Wallet</p>
                            <p class="text-xl font-bold text-white tracking-tight leading-tight mt-0.5">
                                Rp {{ number_format($customerBalance, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Top Up -->
                    <a href="{{ route('customer.topup') }}" wire:navigate 
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-white/25 hover:bg-white/35 active:scale-95 backdrop-blur-sm border border-white/30 transition shadow-xs shrink-0 flex items-center justify-center">
                        Top Up
                    </a>
                </div>
            </div>

            <!-- Bagian Konten Menu & Notifikasi Penting -->
            <div class="px-5 pt-4">

                <!-- Pop up Pesan Sukses -->
                @if(session()->has('message') || session()->has('status'))
                    @php
                        $successMsg = session('message') ?? session('status');
                    @endphp
                    <div x-data="{ show: true }" x-show="show" x-cloak
                         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
                        <div x-show="show"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             class="bg-white rounded-3xl shadow-2xl max-w-xs w-full p-6 text-center border border-gray-100">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center shadow-inner">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1">Berhasil!</h2>
                            <p class="text-sm text-gray-600 mb-6">{{ $successMsg }}</p>
                            <button @click="show = false" 
                                    class="w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg active:scale-95 cursor-pointer bg-[#0066FF] hover:bg-blue-700">
                                Oke
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Peringatan Surat Peringatan (Jika warning_level >= 2) -->
                @if($user && $user->warning_level >= 2)
                    <div class="mb-4 bg-red-50 border border-red-200 rounded-2xl p-4 text-xs text-red-900 shadow-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-red-950 text-sm">Surat Peringatan {{ $user->warning_level }}</div>
                                <p class="mt-1 text-red-700 leading-snug">
                                    ⚠️ Perhatian: Akun Anda dalam pengawasan Surat Peringatan. Hindari pelanggaran agar akun tidak dinonaktifkan secara permanen.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Peringatan Verifikasi KTP (Hanya jika belum terverifikasi) -->
                @if(!$isVerified)
                    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-xs text-amber-900 shadow-2xs flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-lg shrink-0">🆔</span>
                            <div class="min-w-0">
                                <p class="font-bold text-amber-950 text-xs">Verifikasi Identitas KTP</p>
                                <p class="text-[11px] text-amber-800 truncate">
                                    {{ !$hasDocs ? 'Belum upload KTP & selfie' : 'Sedang ditinjau oleh Admin' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('profile.settings.verification') }}" wire:navigate 
                           class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shrink-0 active:scale-95 transition">
                            Kelola
                        </a>
                    </div>
                @endif

                <!-- Daftar Menu Vertikal (Sesuai Desain Gambar: Favorit ditiadakan) -->
                <div class="divide-y divide-gray-100 bg-white">
                    <!-- 1. Pesanan Saya -->
                    <a href="{{ route('customer.helps.index') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-gray-50 active:bg-gray-100 transition rounded-xl group cursor-pointer">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-6 h-6 flex items-center justify-center text-gray-700 shrink-0">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <span class="text-[15px] font-medium text-gray-800">Pesanan Saya</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- Catatan: Menu Favorit tidak dimuat sesuai permintaan pengguna -->

                    <!-- 2. Alamat Saya -->
                    <a href="{{ route('profile.edit') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-gray-50 active:bg-gray-100 transition rounded-xl group cursor-pointer">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-6 h-6 flex items-center justify-center text-gray-700 shrink-0">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-[15px] font-medium text-gray-800">Alamat Saya</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- 3. Metode Pembayaran -->
                    <a href="{{ route('customer.topup') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-gray-50 active:bg-gray-100 transition rounded-xl group cursor-pointer">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-6 h-6 flex items-center justify-center text-gray-700 shrink-0">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span class="text-[15px] font-medium text-gray-800">Metode Pembayaran</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- 4. Bantuan & FAQ -->
                    <a href="{{ route('customer.help-support') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-gray-50 active:bg-gray-100 transition rounded-xl group cursor-pointer">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-6 h-6 flex items-center justify-center text-gray-700 shrink-0">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-[15px] font-medium text-gray-800">Bantuan & FAQ</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- 5. Pengaturan -->
                    <a href="{{ route('profile.settings') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-gray-50 active:bg-gray-100 transition rounded-xl group cursor-pointer">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-6 h-6 flex items-center justify-center text-gray-700 shrink-0">
                                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-[15px] font-medium text-gray-800">Pengaturan</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- 6. Keluar -->
                    <button type="button" 
                            onclick="document.getElementById('logout-modal').classList.remove('hidden')" 
                            class="w-full py-4 px-2 flex items-center justify-between hover:bg-red-50/50 active:bg-red-50 transition rounded-xl group cursor-pointer text-left">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-6 h-6 flex items-center justify-center text-gray-700 group-hover:text-red-600 shrink-0 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <span class="text-[15px] font-medium text-gray-800 group-hover:text-red-600 transition-colors">Keluar</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Ganti Foto Profil Livewire -->
            @livewire('customer.update-profile-photo')

            <!-- Modal Konfirmasi Logout -->
            <div id="logout-modal" 
                 class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center p-5" 
                 onclick="if(event.target === this) this.classList.add('hidden')">
                <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Keluar</h2>
                    <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari akun aplikasi Anda?</p>

                    <form action="{{ route('logout') }}" method="POST" class="space-y-3">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-red-600 text-white font-semibold py-3.5 rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-600/30 cursor-pointer">
                            Ya, Keluar
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('logout-modal').classList.add('hidden')" 
                                class="w-full bg-gray-100 text-gray-700 font-semibold py-3.5 rounded-xl hover:bg-gray-200 transition cursor-pointer">
                            Batal
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
