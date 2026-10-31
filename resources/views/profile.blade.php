<x-app-layout>
    <x-slot name="title">Profil</x-slot>

    <div class="bg-slate-50/60 pb-2">
        @php
            $user = auth()->user() ? auth()->user()->fresh() : null;
            $userBalance = \App\Models\UserBalance::where('user_id', optional($user)->id)->first();
            $customerBalance = $userBalance ? $userBalance->balance : 0;

            $isVerified = (bool) ($user->verified ?? false);
            $isKtpVerified = $isVerified;
            $isEmailVerified = $user ? $user->hasVerifiedEmail() : false;
            $isFullyVerified = $isKtpVerified && $isEmailVerified;

            $hasKtp = !empty($user->ktp_photo) || !empty($user->ktp_path);
            $hasSelfie = !empty($user->selfie_photo);
            $hasDocs = $hasKtp && $hasSelfie;

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
            <!-- Top Blue Header Backdrop (SayaBantu Brand Blue dengan wave bergelombang) -->
            <div class="relative overflow-hidden pt-8 pb-20 px-5 bg-[#0066FF]">
                <!-- Decorative circle overlays -->
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

                <div class="relative z-10 text-center text-white mb-2">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">Profil Saya</h1>
                    <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola akun dan informasi Anda</p>
                </div>
                
                <!-- Wave Separator (SVG) bergelombang -->
                <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                    <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
                </svg>
            </div>

            <!-- Profile Card Berwarna (Gradasi Biru Royal & Glass Border) -->
            <div class="px-5 -mt-16 relative z-10 mb-4">
                <div class="bg-gradient-to-r from-[#0052CC] to-[#0044AA] rounded-3xl p-5 text-white shadow-2xl relative overflow-hidden border border-white/20">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>

                    <div class="relative z-10 flex items-center justify-between gap-3.5">
                        <!-- Avatar & User Info -->
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Foto Profil Pengguna -->
                            <div class="relative group cursor-pointer shrink-0" 
                                 onclick="Livewire.dispatch('openModal')"
                                 title="Klik untuk ubah foto profil">
                                <div class="w-16 h-16 rounded-full border-2 border-white ring-2 ring-white/30 shadow-md overflow-hidden bg-white/20 flex items-center justify-center">
                                    <x-user-avatar :user="$user" class="w-full h-full object-cover text-white font-bold text-xl" />
                                </div>
                                <div class="absolute -bottom-0.5 -right-0.5 bg-white text-[#0066FF] p-1 rounded-full shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Nama & Status -->
                            <div class="min-w-0 flex-1">
                                <h2 class="text-base font-extrabold text-white truncate leading-snug">
                                    {{ trim(preg_replace('/\s*\((?:Mitra|Customer)[^)]*\)|\s*(?:Mitra|Customer)\b/i', '', $user->name ?? 'Pengguna')) }}
                                </h2>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-xs text-blue-100 font-semibold">Aktif</span>
                                    <span class="w-1 h-1 rounded-full bg-blue-200 shrink-0"></span>
                                    @if($isFullyVerified || $isVerified)
                                        <span class="inline-flex items-center gap-1 bg-white text-[#0066FF] text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-2xs">
                                            <svg class="w-3 h-3 text-[#0066FF] fill-current shrink-0" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                            </svg>
                                            Terverifikasi
                                        </span>
                                    @elseif($hasDocs)
                                        <span class="inline-flex items-center gap-1 bg-amber-400/25 text-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-300/30">
                                            Proses Verifikasi
                                        </span>
                                    @else
                                        <a href="{{ route('profile.settings.verification') }}" wire:navigate 
                                           class="inline-flex items-center gap-1 bg-white/20 hover:bg-white/30 text-white text-[10px] font-bold px-2 py-0.5 rounded-full border border-white/30 transition">
                                            Belum Verifikasi &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Button -->
                        <a href="{{ route('profile.edit') }}" wire:navigate aria-label="Edit Profil"
                           class="p-2.5 bg-white/15 hover:bg-white/25 text-white rounded-xl transition flex-shrink-0 border border-white/20 shadow-xs active:scale-95">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Saldo / Wallet Berwarna -->
            <div class="px-5 mb-5">
                <div class="relative overflow-hidden rounded-2xl p-4 bg-gradient-to-r from-[#0066FF] via-[#005AE0] to-[#0044B8] text-white shadow-lg shadow-blue-500/15 border border-white/20 flex items-center justify-between gap-3">
                    <!-- Decorative subtle circle overlays -->
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute -left-6 -top-6 w-20 h-20 bg-white/10 rounded-full pointer-events-none"></div>

                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-11 h-11 rounded-xl bg-white/20 text-white flex items-center justify-center shrink-0 border border-white/25 backdrop-blur-xs shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] text-blue-100 font-medium">Saldo / Wallet</p>
                            <p class="text-lg font-black text-white tracking-tight leading-tight mt-0.5">
                                Rp {{ number_format($customerBalance, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Top Up -->
                    <a href="{{ route('customer.topup') }}" wire:navigate 
                       class="relative z-10 px-4 py-2 rounded-xl text-xs font-bold text-[#0066FF] bg-white hover:bg-blue-50 active:scale-95 transition shadow-sm shrink-0 flex items-center justify-center">
                        Top Up
                    </a>
                </div>
            </div>

            <!-- Bagian Konten Menu & Notifikasi -->
            <div class="px-5">

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
                            <p class="text-xs text-gray-600 mb-6">{{ $successMsg }}</p>
                            <button @click="show = false" 
                                    class="w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg active:scale-95 cursor-pointer bg-[#0066FF] hover:bg-[#0052CC]">
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
                                    Perhatian: Akun Anda dalam pengawasan Surat Peringatan. Hindari pelanggaran agar akun tidak dinonaktifkan secara permanen.
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

                <!-- Menu List (Rounded-3xl container seperti Mitra) -->
                <div class="divide-y divide-gray-100 bg-white rounded-3xl px-3 py-1 border border-gray-100 shadow-2xs">
                    <!-- 1. Pesanan Saya -->
                    <a href="{{ route('customer.helps.index') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                        <div class="flex items-center gap-4 min-w-0">
                            <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Pesanan Saya</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    @if($user && $user->role === 'customer')
                        <!-- 2. Alamat Saya (Multi-address Shopee style) -->
                        <a href="{{ route('customer.addresses') }}" wire:navigate 
                           class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                            <div class="flex items-center gap-4 min-w-0">
                                <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Alamat Saya</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $addressCount = $user->customerAddresses()->count();
                                @endphp
                                @if($addressCount > 0)
                                    <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $addressCount }} Alamat</span>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>

                        <!-- 3. Data Diri Akun -->
                        <a href="{{ route('profile.edit') }}" wire:navigate 
                           class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                            <div class="flex items-center gap-4 min-w-0">
                                <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Data Diri</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <!-- 2. Alamat & Data Diri -->
                        <a href="{{ route('profile.edit') }}" wire:navigate 
                           class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                            <div class="flex items-center gap-4 min-w-0">
                                <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Data Diri</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif

                    <!-- 3. Metode Pembayaran -->
                    <a href="{{ route('customer.topup') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                        <div class="flex items-center gap-4 min-w-0">
                            <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Metode Pembayaran</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- 4. Bantuan & FAQ -->
                    <a href="{{ route('customer.help-support') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                        <div class="flex items-center gap-4 min-w-0">
                            <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Bantuan & FAQ</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- 5. Pengaturan -->
                    <a href="{{ route('profile.settings') }}" wire:navigate 
                       class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group cursor-pointer">
                        <div class="flex items-center gap-4 min-w-0">
                            <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Pengaturan</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <!-- Tombol Keluar (Outlined Pill Button) -->
                <div class="mt-4 mb-2">
                    <button type="button" 
                            onclick="document.getElementById('logout-modal').classList.remove('hidden')" 
                            class="w-full py-3.5 px-6 rounded-2xl border-1.5 border-[#0066FF] text-[#0066FF] font-bold text-sm text-center hover:bg-blue-50/80 active:scale-[0.99] transition cursor-pointer bg-white shadow-2xs">
                        Keluar
                    </button>
                </div>
            </div>

            <!-- Modal Ganti Foto Profil Livewire -->
            @livewire('customer.update-profile-photo')

            <!-- Modal Konfirmasi Logout -->
            <div id="logout-modal" 
                 class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-[100] flex items-center justify-center p-5" 
                 onclick="if(event.target === this) this.classList.add('hidden')">
                <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all text-center">
                    <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100 shadow-2xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 mb-1.5">Konfirmasi Keluar</h2>
                    <p class="text-xs text-gray-500 leading-relaxed mb-6">Apakah Anda yakin ingin keluar dari akun aplikasi Anda?</p>

                    <form action="{{ route('logout') }}" method="POST" class="space-y-2.5">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-md shadow-red-600/20 active:scale-95 cursor-pointer">
                            Ya, Keluar
                        </button>
                        <button type="button" 
                                onclick="document.getElementById('logout-modal').classList.add('hidden')" 
                                class="w-full bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold text-xs py-3 rounded-xl transition active:scale-95 cursor-pointer">
                            Batal
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
