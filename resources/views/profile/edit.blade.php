<x-app-layout>
    <x-slot name="title">Edit Profil</x-slot>

    <div class="min-h-screen bg-slate-50 pb-20">
        <div class="max-w-md mx-auto">
            <!-- Header - Brand Blue with Wave Separator -->
            <div class="px-5 pt-6 pb-16 relative overflow-hidden text-white bg-[#0066FF]">
                <!-- Decorative circle overlays -->
                <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between text-white mb-3">
                        <a href="{{ route('profile') }}" wire:navigate aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-xl transition active:scale-95 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div class="text-center flex-1 px-2">
                            <h1 class="text-lg font-black text-white tracking-tight leading-tight">Edit Profil</h1>
                            <p class="text-xs text-blue-100 font-medium mt-0.5">Perbarui informasi data diri Anda</p>
                        </div>

                        <div class="w-9"></div>
                    </div>
                </div>

                <!-- Wave Separator (SVG) bergelombang -->
                <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                    <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#f8fafc"></path>
                </svg>
            </div>

            <!-- Form Content Container -->
            <div class="px-5 pt-2 space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
