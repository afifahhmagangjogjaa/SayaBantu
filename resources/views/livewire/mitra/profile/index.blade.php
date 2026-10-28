<div class="min-h-screen bg-slate-50/60 pb-28">
    @php
        $user = $user ?? auth()->user();
        $totalHelped = $totalHelped ?? \App\Models\Help::where('mitra_id', $user->id)->count();
        $completedHelps = $completedHelps ?? \App\Models\Help::where('mitra_id', $user->id)->where('status', 'selesai')->count();
        $averageRating = number_format($averageRating ?? ($user->mitra_average_rating ?? 0), 1);
        $totalRatings = $totalRatings ?? ($user->mitra_rating_count ?? 0);

        $isVerified = (bool) ($user->verified ?? false);
        $isKtpVerified = $isVerified;
        $isEmailVerified = $user ? $user->hasVerifiedEmail() : false;
        $isFullyVerified = $isKtpVerified && $isEmailVerified;

        $hasKtp = !empty($user->ktp_photo) || !empty($user->ktp_path);
        $hasSelfie = !empty($user->selfie_photo);
        $hasDocs = $hasKtp && $hasSelfie;
    @endphp

    <div class="max-w-md mx-auto">
        <!-- Top Blue Header Backdrop (SayaBantu Brand Blue dengan wave bergelombang) -->
        <div class="relative overflow-hidden pt-8 pb-20 px-5 bg-[#0066FF]">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-44 h-44 bg-white/10 rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-36 h-36 bg-white/10 rounded-full -ml-14 -mb-14 pointer-events-none"></div>
            
            <!-- Wave Separator (SVG) bergelombang di bagian bawah latar biru -->
            <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <!-- Top Profile Card Berwarna (Gradasi Biru Royal Bergelombang & Glass Border) -->
        <div class="px-5 -mt-16 relative z-10 mb-7">
            <div class="bg-gradient-to-r from-[#0052CC] to-[#0044AA] rounded-3xl p-5 text-white shadow-2xl relative overflow-hidden border border-white/20">
                <!-- Decorative circle overlay -->
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>

                <div class="relative z-10 flex items-center justify-between gap-3.5">
                    <!-- Avatar & User Info -->
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="relative w-16 h-16 rounded-full overflow-hidden border-2 border-white/40 shadow-sm flex-shrink-0 bg-white/20">
                            <x-user-avatar :user="$user" class="w-full h-full object-cover text-2xl font-bold text-white" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <h1 class="text-base font-extrabold text-white truncate leading-snug">
                                {{ trim(preg_replace('/\s*\((?:Mitra|Customer)[^)]*\)|\s*(?:Mitra|Customer)\b/i', '', $user->name ?? 'Budi Santoso')) }}
                            </h1>

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
                            
                            <!-- Rating Row: ⭐ 4.8 (12 ulasan) -->
                            <div class="flex items-center gap-1.5 mt-1.5 text-xs">
                                <span class="text-amber-300">⭐</span>
                                <span class="font-bold text-white">{{ $totalRatings > 0 ? $averageRating : '4.8' }}</span>
                                <a href="{{ route('mitra.ratings') }}" wire:navigate class="text-blue-100 hover:text-white hover:underline font-medium">
                                    ({{ $totalRatings > 0 ? $totalRatings : '12' }} ulasan)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Button -->
                    <a href="{{ route('mitra.profile.edit') }}" wire:navigate aria-label="Edit Profil"
                       class="p-2.5 bg-white/15 hover:bg-white/25 text-white rounded-xl transition flex-shrink-0 border border-white/20 shadow-xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu List (Sesuai Desain Mockup: Data Diri, Rekening Bank, Riwayat Order, Pusat Bantuan, Pengaturan) -->
        <!-- Catatan: Keahlian sengaja tidak disertakan sesuai permintaan user -->
        <div class="px-5 pt-2">
            <div class="divide-y divide-gray-100 bg-white rounded-3xl px-3 py-1 border border-gray-100 shadow-2xs">
                
                <!-- 1. Data Diri -->
                <a href="{{ route('mitra.profile.edit') }}" wire:navigate class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group">
                    <div class="flex items-center gap-4 min-w-0">
                        <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Data Diri</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <!-- 2. Rekening Bank -->
                <a href="{{ route('mitra.withdraw.form') }}" wire:navigate class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group">
                    <div class="flex items-center gap-4 min-w-0">
                        <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Rekening Bank</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <!-- 3. Riwayat Order -->
                <a href="{{ route('mitra.helps.completed') }}" wire:navigate class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group">
                    <div class="flex items-center gap-4 min-w-0">
                        <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Riwayat Order</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <!-- 4. Pusat Bantuan -->
                <a href="{{ route('mitra.help-support') }}" wire:navigate class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group">
                    <div class="flex items-center gap-4 min-w-0">
                        <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Pusat Bantuan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <!-- 5. Pengaturan -->
                <a href="{{ route('mitra.settings') }}" wire:navigate class="py-4 px-2 flex items-center justify-between hover:bg-slate-50/80 active:bg-slate-100/70 rounded-2xl transition group">
                    <div class="flex items-center gap-4 min-w-0">
                        <svg class="w-5 h-5 text-slate-700 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800 group-hover:text-[#0066FF] transition">Pengaturan</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Tombol Keluar (Outlined Pill Button Sesuai Mockup) -->
            <div class="mt-8 mb-4">
                <button type="button" onclick="document.getElementById('logout-modal').classList.remove('hidden')"
                        class="w-full py-3.5 px-6 rounded-2xl border-1.5 border-[#0066FF] text-[#0066FF] font-bold text-sm text-center hover:bg-blue-50/80 active:scale-[0.99] transition cursor-pointer bg-white shadow-2xs">
                    Keluar
                </button>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-[100] flex items-center justify-center p-5" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <h2 class="text-lg font-black text-gray-900 mb-1.5">Konfirmasi Keluar</h2>
                <p class="text-xs text-gray-500 leading-relaxed">Apakah Anda yakin ingin keluar dari akun Mitra SayaBantu?</p>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="space-y-2.5">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-md shadow-red-600/20 active:scale-95 cursor-pointer">
                    Ya, Keluar
                </button>
                <button type="button" onclick="document.getElementById('logout-modal').classList.add('hidden')" class="w-full bg-slate-100 hover:bg-slate-200 text-gray-700 font-bold text-xs py-3 rounded-xl transition active:scale-95 cursor-pointer">
                    Batal
                </button>
            </form>
        </div>
    </div>
</div>
