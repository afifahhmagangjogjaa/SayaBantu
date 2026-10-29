{{-- Partial bersama: dipakai halaman customer/mitra. Variabel wajib: notificationsRoute --}}
<div class="bg-slate-50/60 pb-4">
    <!-- Header - Brand Blue with Wave Separator -->
    <div class="px-5 pt-6 pb-12 relative overflow-hidden text-white bg-[#0066FF]">
        <!-- Decorative circle overlays -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between text-white mb-2">
                <a href="javascript:history.back()" aria-label="Kembali" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="text-center flex-1 px-2">
                    <h1 class="text-lg font-black text-white tracking-tight leading-tight">Pengaturan Notifikasi</h1>
                    <p class="text-xs text-blue-100 font-medium mt-0.5">Kelola preferensi pemberitahuan Anda</p>
                </div>
                <a href="{{ route($notificationsRoute) }}" wire:navigate aria-label="Notifikasi" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
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

    <!-- Notification Settings Content -->
    <div class="max-w-md mx-auto px-5 pt-2 relative z-10">
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-5">
            <livewire:profile.notification-settings />
        </div>
    </div>
</div>
