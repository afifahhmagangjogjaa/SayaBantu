<div>
    <div class="max-w-4xl mx-auto py-2 space-y-6">
        {{-- Modal Popup Sukses "Password Baru Tersimpan!" --}}
        @if ($saved)
            <div x-data="{ show: true }"
                 x-show="show"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-title"
                 role="dialog"
                 aria-modal="true">
                
                <!-- Backdrop with Blur -->
                <div x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
                     @click="show = false; $wire.saved = false">
                </div>

                <!-- Modal Panel -->
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="show"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-3xl bg-white text-center shadow-2xl transition-all w-full max-w-sm border border-gray-100 p-6 sm:p-7"
                         @click.stop>
                        
                        <!-- Close Button -->
                        <button type="button" 
                                @click="show = false; $wire.saved = false"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-full transition cursor-pointer"
                                title="Tutup">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Icon Sukses -->
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 shadow-xs mb-4">
                            <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <!-- Content -->
                        <h3 class="text-lg font-bold text-gray-900" style="font-weight: 700 !important;">
                            Password Baru Tersimpan!
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed">
                            Kata sandi Super Admin berhasil diperbarui. Silakan gunakan kata sandi baru untuk login berikutnya.
                        </p>

                        <!-- Tombol OK -->
                        <div class="mt-6">
                            <button type="button"
                                    @click="show = false; $wire.saved = false"
                                    style="background-color: #4f46e5; color: #ffffff;"
                                    class="w-full inline-flex justify-center items-center py-2.5 px-5 rounded-xl text-white text-xs sm:text-sm font-bold transition shadow-xs hover:opacity-90 cursor-pointer">
                                OK, Mengerti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 1. Kartu Profil Super Admin --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div class="flex items-center gap-4 sm:gap-5">
                    {{-- Avatar Box dengan Huruf Inisial (Sesuai Navbar & Sidebar) --}}
                    <div style="position: relative; width: 68px; height: 68px; flex-shrink: 0;">
                        <div style="width: 68px; height: 68px; background-color: #4f46e5; color: #ffffff; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 28px; box-shadow: 0 4px 10px -2px rgba(79, 70, 229, 0.3);">
                            {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                        </div>
                        <span style="position: absolute; bottom: -2px; right: -2px; width: 18px; height: 18px; background-color: #10b981; border: 3px solid #ffffff; border-radius: 9999px; display: block;" 
                              title="Status Akun: Aktif">
                        </span>
                    </div>

                    {{-- Nama, Email, & Badges --}}
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ $user->name ?? 'Super Admin' }}</h2>
                        <p class="text-xs sm:text-sm text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-2.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                <span class="text-amber-500">⭐</span> Super Admin
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                Akses Penuh Sistem
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metadata Rapi: Kolom Label, Titik Dua (:), dan Nilai Rata Kiri --}}
            <div class="mt-6 pt-5 border-t border-gray-100 space-y-2.5 sm:space-y-3 text-xs sm:text-sm">
                <div class="flex items-center">
                    <span class="w-36 sm:w-44 text-gray-500 flex-shrink-0">Tingkat Hak Akses</span>
                    <span class="text-gray-400 mr-3 flex-shrink-0">:</span>
                    <span class="font-semibold text-purple-700 truncate">Root / Global Administrator</span>
                </div>
                <div class="flex items-center">
                    <span class="w-36 sm:w-44 text-gray-500 flex-shrink-0">Status Akun</span>
                    <span class="text-gray-400 mr-3 flex-shrink-0">:</span>
                    <span class="inline-flex items-center font-medium text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Aktif
                    </span>
                </div>
                <div class="flex items-center">
                    <span class="w-36 sm:w-44 text-gray-500 flex-shrink-0">Sesi Login</span>
                    <span class="text-gray-400 mr-3 flex-shrink-0">:</span>
                    <span class="font-medium text-gray-700">{{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
                </div>
            </div>
        </div>

        {{-- 2. Kartu Peringatan Keamanan (Security Advisory) --}}
        <div class="bg-amber-50/60 border border-amber-300 rounded-2xl p-5 sm:p-6 shadow-2xs">
            <div class="flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-xl bg-amber-100/90 text-amber-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 space-y-2">
                    <h3 class="text-sm sm:text-base font-bold text-amber-900">Keamanan Tingkat Tinggi (Super Admin)</h3>
                    <ul class="space-y-1.5 text-xs sm:text-sm text-amber-900/90 list-disc list-inside leading-relaxed">
                        <li>Akun Super Admin memiliki kontrol penuh atas keuangan dan sistem.</li>
                        <li>Gunakan kata sandi unik yang kuat (minimal 8-12 karakter).</li>
                        <li>Kombinasikan huruf kapital, angka, dan karakter khusus.</li>
                        <li>Selalu lakukan logout saat menggunakan perangkat bersama.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 3. Kartu Form Perbarui Kata Sandi Super Admin --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8"
             x-data="{ 
                 showCurrent: false, 
                 showNew: false, 
                 showConfirm: false,
                 currentPass: @entangle('current_password').live,
                 newPass: @entangle('password').live,
                 confirmPass: @entangle('password_confirmation').live,
                 get score() {
                     let p = this.newPass || '';
                     if (!p) return 0;
                     let s = 0;
                     if (p.length >= 8) s++;
                     if (p.length >= 10) s++;
                     if (/[A-Z]/.test(p)) s++;
                     if (/[0-9]/.test(p)) s++;
                     if (/[^A-Za-z0-9]/.test(p)) s++;
                     return s;
                 },
                 get isValid() {
                     let cp = this.currentPass || '';
                     let p = this.newPass || '';
                     let c = this.confirmPass || '';
                     let hasMin = p.length >= 8;
                     let hasUpper = /[A-Z]/.test(p);
                     let hasNum = /[0-9]/.test(p);
                     let hasSpecial = /[^A-Za-z0-9]/.test(p);
                     let isMatch = p.length > 0 && p === c;
                     let isDiff = p !== cp;
                     return cp.length > 0 && hasMin && hasUpper && hasNum && hasSpecial && isMatch && isDiff;
                 }
             }">
            
            {{-- Header Form --}}
            <div class="flex items-center gap-3.5 pb-6 border-b border-gray-100">
                <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 border border-purple-100 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Perbarui Kata Sandi Super Admin</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Ketik kata sandi saat ini untuk konfirmasi otorisasi</p>
                </div>
            </div>

            {{-- Form Fields --}}
            <form wire:submit.prevent="updatePassword" class="mt-6 space-y-5">
                {{-- 1. Kata Sandi Saat Ini --}}
                <div>
                    <label for="sa_current_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Kata Sandi Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showCurrent ? 'text' : 'password'" 
                               id="sa_current_password" 
                               wire:model="current_password" 
                               autocomplete="current-password"
                               placeholder="Ketik kata sandi saat ini"
                               class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50/60 pr-10 focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition py-2.5 px-3.5 @error('current_password') border-red-400 bg-red-50/30 @enderror">
                        <button type="button" 
                                @click="showCurrent = !showCurrent" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer"
                                title="Tampilkan / sembunyikan kata sandi">
                            <!-- Eye Slash Icon (ketika tersembunyi / titik-titik) -->
                            <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <!-- Eye Open Icon (ketika terlihat / terbaca) -->
                            <svg x-show="showCurrent" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1 font-medium">
                            <span>⚠️</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                {{-- 2. Kata Sandi Baru --}}
                <div>
                    <label for="sa_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'" 
                               id="sa_password" 
                               wire:model.live.debounce.300ms="password" 
                               @input="newPass = $event.target.value"
                               autocomplete="new-password"
                               placeholder="Minimal 8 karakter unik"
                               class="w-full text-sm rounded-xl border bg-gray-50/60 pr-10 focus:bg-white transition py-2.5 px-3.5"
                               :class="(confirmPass && newPass && newPass === confirmPass && newPass.length >= 8) 
                                   ? 'border-emerald-500 bg-emerald-50/20 text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30' 
                                   : (@js($errors->has('password')) 
                                       ? 'border-red-400 bg-red-50/30 focus:border-red-500 focus:ring-1 focus:ring-red-500/30' 
                                       : 'border-gray-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500')">
                        <button type="button" 
                                @click="showNew = !showNew" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer"
                                title="Tampilkan / sembunyikan kata sandi">
                            <!-- Eye Slash Icon (ketika tersembunyi / titik-titik) -->
                            <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <!-- Eye Open Icon (ketika terlihat / terbaca) -->
                            <svg x-show="showNew" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        @if($message !== 'Konfirmasi kata sandi baru tidak cocok.')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1 font-medium">
                                <span>⚠️</span> <span>{{ $message }}</span>
                            </p>
                        @endif
                    @enderror

                    {{-- Password Strength Meter --}}
                    <div x-show="newPass && newPass.length > 0" x-transition class="mt-2.5">
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="text-gray-500">Kekuatan Kata Sandi:</span>
                            <span class="font-bold" 
                                  :class="score <= 2 ? 'text-red-500' : (score <= 3 ? 'text-amber-500' : 'text-emerald-600')"
                                  x-text="score <= 2 ? 'Lemah' : (score <= 3 ? 'Sedang' : 'Kuat & Aman')"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full transition-all duration-300 rounded-full" 
                                 :class="score <= 2 ? 'w-1/3 bg-red-500' : (score <= 3 ? 'w-2/3 bg-amber-500' : 'w-full bg-emerald-500')"></div>
                        </div>
                    </div>
                </div>

                {{-- 3. Konfirmasi Kata Sandi Baru --}}
                <div>
                    <label for="sa_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" 
                               id="sa_password_confirmation" 
                               wire:model.live.debounce.300ms="password_confirmation" 
                               @input="confirmPass = $event.target.value"
                               autocomplete="new-password"
                               placeholder="Ulangi kata sandi baru"
                               class="w-full text-sm rounded-xl border bg-gray-50/60 pr-10 focus:bg-white transition py-2.5 px-3.5"
                               :class="(confirmPass && newPass && newPass === confirmPass && newPass.length >= 8) 
                                   ? 'border-emerald-500 bg-emerald-50/20 text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30' 
                                   : ((confirmPass && newPass !== confirmPass) || @js($errors->has('password_confirmation')) 
                                       ? 'border-red-400 bg-red-50/30 focus:border-red-500 focus:ring-1 focus:ring-red-500/30' 
                                       : 'border-gray-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500')">
                        <button type="button" 
                                @click="showConfirm = !showConfirm" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition cursor-pointer"
                                title="Tampilkan / sembunyikan kata sandi">
                            <!-- Eye Slash Icon (ketika tersembunyi / titik-titik) -->
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <!-- Eye Open Icon (ketika terlihat / terbaca) -->
                            <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-600 flex items-center gap-1 font-medium">
                            <span>⚠️</span> <span>{{ $message }}</span>
                        </p>
                    @enderror

                    {{-- Real-time match check indicator --}}
                    <div x-show="confirmPass && confirmPass.length > 0" x-transition class="mt-1.5">
                        <p x-show="newPass === confirmPass" class="text-xs text-emerald-600 flex items-center gap-1 font-medium">
                            <span>✓</span> <span>Konfirmasi kata sandi cocok.</span>
                        </p>
                        <p x-show="newPass !== confirmPass" class="text-xs text-red-500 flex items-center gap-1 font-medium">
                            <span>✕</span> <span>Kata sandi tidak cocok.</span>
                        </p>
                    </div>
                </div>

                {{-- Criteria Checklist --}}
                <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-100 text-xs space-y-1.5">
                    <p class="font-bold text-gray-700 mb-1">Ketentuan kata sandi:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass && newPass.length >= 8 ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="newPass && newPass.length >= 8 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="newPass && newPass.length >= 8 ? '✓' : '✕'">✕</span>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass && /[A-Z]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="newPass && /[A-Z]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="newPass && /[A-Z]/.test(newPass) ? '✓' : '✕'">✕</span>
                            <span>Huruf besar (A-Z)</span>
                        </div>
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass && /[0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="newPass && /[0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="newPass && /[0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                            <span>Angka (0-9)</span>
                        </div>
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass && /[^A-Za-z0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="newPass && /[^A-Za-z0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="newPass && /[^A-Za-z0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                            <span>Karakter khusus / simbol</span>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            :disabled="!isValid"
                            :class="!isValid ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:opacity-90'"
                            style="background-color: #4f46e5; color: #ffffff;"
                            class="w-full sm:w-auto px-6 py-2.5 text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        <span wire:loading.remove>Simpan Password Baru</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
