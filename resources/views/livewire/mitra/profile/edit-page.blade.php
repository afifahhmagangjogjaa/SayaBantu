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
                        
                        <button @click="show = false" 
                                class="w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg active:scale-95 cursor-pointer"
                                style="background: linear-gradient(to bottom right, #0098e7, #0060b0);">
                            Oke
                        </button>
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

            {{-- Banner KTP belum terverifikasi --}}
            @if(!optional(auth()->user())->verified)
            <div class="mb-5 p-4 rounded-xl border border-orange-200 bg-orange-50 flex items-start gap-3">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-orange-800">Verifikasi KTP Diperlukan</p>
                    <p class="text-xs text-orange-700 mt-0.5 leading-relaxed">Scroll ke bawah ke bagian <strong>Foto KTP & Selfie</strong>, upload kedua foto, lalu tunggu verifikasi dari admin untuk bisa menggunakan semua fitur.</p>
                    <a href="#section-ktp" class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-orange-600 underline">
                        ↓ Langsung ke Upload KTP
                    </a>
                </div>
            </div>
            @endif


            <livewire:profile.update-profile-information-form />

            {{-- Section Upload KTP (selalu tampil untuk mitra) --}}
            @if(auth()->user()?->role === 'mitra')
            <div id="section-ktp" class="mt-8 pt-6 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #0098e7, #0060b0);">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Foto KTP & Selfie</h2>
                        <p class="text-xs text-gray-500">Diperlukan untuk verifikasi akun</p>
                    </div>
                    @if(auth()->user()?->verified)
                    <span class="ml-auto inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Terverifikasi
                    </span>
                    @else
                    <span class="ml-auto inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Belum Verified
                    </span>
                    @endif
                </div>
                <livewire:profile.verification />
            </div>
            @endif

        </div>
    </div>

</div>