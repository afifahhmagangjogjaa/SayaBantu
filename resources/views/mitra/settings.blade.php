<x-mitra-layout>
    <x-slot name="title">Pengaturan</x-slot>

    <div class="bg-slate-50/60 pb-4">
        <!-- Header - Brand Blue with Wave Separator -->
        <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white bg-[#0066FF]">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-2">
                    <a href="{{ route('mitra.profile') }}" wire:navigate aria-label="Kembali ke Profil" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="text-center flex-1 px-2">
                        <h1 class="text-lg font-black text-white tracking-tight leading-tight">Pengaturan</h1>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola preferensi dan keamanan akun Anda</p>
                    </div>
                    <a href="{{ route('mitra.notifications.index') }}" wire:navigate aria-label="Notifikasi" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Wave Separator (SVG) bergelombang -->
            <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
            </svg>
        </div>

        <!-- Settings Content -->
        <div class="max-w-md mx-auto px-5 pt-2 relative z-10">
            <div class="space-y-3">
                <!-- Notification Settings -->
                <a href="{{ route('mitra.settings.notifications') }}" wire:navigate
                    class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 flex items-center gap-3.5 hover:border-blue-200 hover:shadow-xs active:scale-[0.99] transition group">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50 text-[#0066FF] border border-blue-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-900 text-xs group-hover:text-[#0066FF] transition truncate">Pengaturan Notifikasi</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Kelola preferensi notifikasi</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Password Settings -->
                <a href="{{ route('mitra.settings.password') }}" wire:navigate
                    class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 flex items-center gap-3.5 hover:border-blue-200 hover:shadow-xs active:scale-[0.99] transition group">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50 text-[#0066FF] border border-blue-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-900 text-xs group-hover:text-[#0066FF] transition truncate">Ubah Kata Sandi</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Perbarui keamanan akun Anda</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0066FF] transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Delete Account -->
                <button onclick="confirmDelete()"
                    class="w-full bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 flex items-center gap-3.5 hover:border-red-200 hover:shadow-xs active:scale-[0.99] transition group text-left cursor-pointer">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-red-50 text-red-600 border border-red-100 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-900 text-xs group-hover:text-red-600 transition truncate">Hapus Akun</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">Hapus akun secara permanen</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-red-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-xs z-[100] flex items-center justify-center p-5"
        onclick="if(event.target === this) closeDeleteModal()">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100 shadow-2xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h2 class="text-lg font-black text-slate-900 mb-1.5">Hapus Akun</h2>
            <p class="text-xs text-slate-500 mb-4 leading-relaxed">Apakah Anda yakin ingin menghapus akun?</p>

            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-5">
                <p class="text-[11px] text-red-800 leading-relaxed font-medium">
                    Tindakan ini akan menghapus secara permanen akun dan semua data terkait. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <form action="{{ route('profile.delete') }}" method="POST" class="space-y-2.5">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-md shadow-red-600/20 active:scale-95 cursor-pointer">
                    Ya, Hapus Akun
                </button>

                <button type="button" onclick="closeDeleteModal()"
                    class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-3 rounded-xl transition active:scale-95 cursor-pointer">
                    Batal
                </button>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-mitra-layout>
