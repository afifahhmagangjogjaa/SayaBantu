<div class="min-h-screen bg-white">
    <div class="max-w-md mx-auto">
        <!-- Header - BRImo Style -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between text-white mb-3">
                    <a href="{{ route('mitra.profile') }}" aria-label="Kembali" class="p-2 hover:bg-white/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <h1 class="text-lg font-bold">Edit Profil</h1>

                    <div class="w-9"></div>
                </div>

                <p class="text-center text-sm text-white/90 mt-2">Perbarui informasi profil Anda</p>
            </div>

            <!-- Curved separator -->
            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 pb-24">

            {{-- Pop up Pesan Sukses --}}
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
                        
                        <a href="{{ route('mitra.profile') }}" 
                           class="block w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg active:scale-95 cursor-pointer text-center"
                           style="background: linear-gradient(to bottom right, #0098e7, #0060b0);">
                            Oke
                        </a>
                    </div>
                </div>
            @endif

            {{-- Pesan error dari redirect (misal: belum terverifikasi) --}}
            @if(session('error'))
            <div class="mb-4 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
            @endif

            <livewire:profile.update-profile-information-form />

        </div>
    </div>

</div>