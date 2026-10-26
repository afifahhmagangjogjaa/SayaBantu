<div class="min-h-screen bg-white">
    @php
        // Mitra stats
        $totalHelped = $totalHelped ?? \App\Models\Help::where('mitra_id', $user->id)->count();
        $completedHelps = $completedHelps ?? \App\Models\Help::where('mitra_id', $user->id)->where('status', 'selesai')->count();
        $averageRating = number_format($averageRating ?? ($user->mitra_average_rating ?? 0), 1);
        $totalRatings = $totalRatings ?? ($user->mitra_rating_count ?? 0);
    @endphp

    <div class="max-w-md mx-auto">
        <!-- Top Blue Header (Matching Dashboard Design) -->
        <div class="px-5 pt-8 pb-24 bg-[#0066FF] text-white flex items-center justify-between shadow-xs relative">
            <div class="flex items-center gap-3">
                <a href="{{ route('mitra.dashboard') }}" wire:navigate aria-label="Kembali" class="p-2 -ml-2 hover:bg-white/20 rounded-xl transition inline-flex items-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-base font-extrabold text-white leading-tight">Profil Saya</h1>
                    <p class="text-[11px] text-blue-100 mt-0.5">Kelola informasi akun & keahlian</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('mitra.profile.edit') }}" wire:navigate class="p-2 rounded-xl text-white hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Profile Card (Overlapping Header) -->
        <div class="px-5 -mt-16 relative z-20">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 flex flex-col items-center text-center">
                <div class="relative">
                    <x-user-avatar :user="$user"
                        class="w-24 h-24 rounded-full mx-auto text-3xl font-bold text-[#0066FF] bg-blue-50 ring-4 ring-white shadow-md" />

                    <button onclick="openMitraPhotoModal()" class="absolute bottom-0 right-0 bg-[#0066FF] p-2 rounded-full shadow-lg hover:scale-110 active:scale-95 transition-transform duration-200 border-2 border-white">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <h2 class="text-xl font-black text-gray-900 mt-4 tracking-tight">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 font-medium mt-1">{{ $user->email }}</p>

                @php
                    $isKtpVerified = (bool) ($user->verified ?? false);
                    $isEmailVerified = $user->hasVerifiedEmail();
                    $isFullyVerified = $isKtpVerified && $isEmailVerified;
                    $badge = $user->mitra_badge;
                @endphp

                <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-[#0066FF] border border-blue-100">
                        <span>{{ $badge['icon'] }}</span>
                        <span>{{ $badge['label'] }}</span>
                    </span>

                    @if($isFullyVerified)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Terverifikasi</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="px-5 mt-4 relative z-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-3 divide-x divide-gray-50">
                    <div class="p-4 text-center">
                        <div class="text-xl font-black text-gray-900">{{ $totalHelped }}</div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Total Bantuan</p>
                    </div>
                    <div class="p-4 text-center">
                        <div class="text-xl font-black text-emerald-600">{{ $completedHelps }}</div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Selesai</p>
                    </div>
                    <div class="p-4 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <span class="text-xl font-black text-amber-500">{{ $averageRating }}</span>
                            <span class="text-amber-500 text-sm">⭐</span>
                        </div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-1">Rating ({{ $totalRatings }})</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Items (Sesuai Desain Gambar: Data Diri, Rekening Bank, Keahlian, Riwayat Order, Pusat Bantuan, Pengaturan, Keluar) -->
        <div class="px-5 pt-6 pb-24">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                
                <!-- 1. Data Diri -->
                <a href="{{ route('mitra.profile.edit') }}" wire:navigate class="p-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0066FF] flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-900">Data Diri</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Informasi pribadi, KTP, & kontak</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- 2. Rekening Bank -->
                <a href="{{ route('mitra.withdraw.form') }}" wire:navigate class="p-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-900">Rekening Bank</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Kelola rekening penarikan dana</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- 3. Keahlian -->
                <a href="{{ route('mitra.profile.edit') }}" wire:navigate class="p-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-900">Keahlian</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Kategori layanan yang ditawarkan</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- 4. Riwayat Order -->
                <a href="{{ route('mitra.helps.completed') }}" wire:navigate class="p-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-900">Riwayat Order</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Daftar pesanan selesai & riwayat tugas</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- 5. Pusat Bantuan -->
                <a href="{{ route('mitra.help-support') }}" wire:navigate class="p-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-900">Pusat Bantuan</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">FAQ & dukungan layanan pelanggan</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- 6. Pengaturan -->
                <a href="{{ route('mitra.settings') }}" wire:navigate class="p-4 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-gray-900">Pengaturan</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">Keamanan sandi & notifikasi</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Tombol Keluar (Logout) Sesuai Gambar -->
            <div class="mt-4">
                <button onclick="document.getElementById('logout-modal').classList.remove('hidden')" class="w-full bg-blue-50 hover:bg-blue-100 border border-blue-200 p-4 rounded-2xl transition flex items-center justify-center gap-3.5 cursor-pointer shadow-2xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#0066FF] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div class="text-left">
                        <h3 class="text-xs font-extrabold text-[#0066FF]">Keluar</h3>
                        <p class="text-[11px] text-blue-500 mt-0.5">Keluar dari akun aplikasi</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Logout Modal -->
        <div id="logout-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center p-5" onclick="if(event.target === this) this.classList.add('hidden')">
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 transform transition-all">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Logout</h2>
                    <p class="text-sm text-gray-600">Apakah Anda yakin ingin keluar dari aplikasi?</p>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="space-y-3">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 text-white font-semibold py-3.5 rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-600/30">
                        Ya, Logout
                    </button>
                    <button type="button" onclick="document.getElementById('logout-modal').classList.add('hidden')" class="w-full bg-gray-100 text-gray-700 font-semibold py-3.5 rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script>
        // Expose robust helpers on the window so onclick handlers can call them reliably.
        window.openMitraPhotoModal = function () {
            const tryEmit = () => {
                if (window.livewire && typeof window.livewire.emit === 'function') {
                    window.livewire.emit('openModal');
                    return true;
                }
                if (window.Livewire) {
                    if (typeof window.Livewire.emit === 'function') {
                        window.Livewire.emit('openModal');
                        return true;
                    }
                    if (typeof window.Livewire.dispatch === 'function') {
                        window.Livewire.dispatch('openModal');
                        return true;
                    }
                }
                return false;
            };

            if (tryEmit()) return;

            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                if (tryEmit()) {
                    clearInterval(interval);
                    return;
                }
                if (attempts >= 10) {
                    clearInterval(interval);
                    console.warn('openMitraPhotoModal: Livewire emit not available.');
                }
            }, 200);
        };
    </script>
</div>
