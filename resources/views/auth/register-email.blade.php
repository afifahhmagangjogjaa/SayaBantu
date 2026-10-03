<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2"
        x-data="{
            showPass: false,
            showConfirmPass: false,
            nameVal: '{{ old('name', '') }}',
            emailVal: '{{ old('email', '') }}',
            newPass: '',
            confirmPass: '',
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
                let p = this.newPass || '';
                let c = this.confirmPass || '';
                let hasMin = p.length >= 8;
                let hasUpper = /[A-Z]/.test(p);
                let hasNum = /[0-9]/.test(p);
                let hasSpecial = /[^A-Za-z0-9]/.test(p);
                let isMatch = p.length > 0 && p === c;
                let hasName = this.nameVal && this.nameVal.trim().length > 0;
                let hasEmail = this.emailVal && this.emailVal.trim().length > 0;
                return hasName && hasEmail && hasMin && hasUpper && hasNum && hasSpecial && isMatch;
            }
        }">
        <div>
            <!-- Header Register -->
            <div class="pt-2 mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-1.5 tracking-tight">Daftar Akun Baru</h2>
                <p class="text-xs text-gray-500 font-medium">
                    Lengkapi data untuk mendaftar sebagai <span class="font-bold text-blue-600">{{ $role === 'mitra' ? 'Mitra' : 'Customer' }}</span>
                </p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Contoh: Budi Santoso"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, ''); $dispatch('input', this.value)"
                        @input="nameVal = $event.target.value"
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('name') border-red-500 @enderror">

                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Alamat Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        @input="emailVal = $event.target.value"
                        placeholder="nama@email.com"
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('email') border-red-500 @enderror">

                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Kata Sandi -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                            @input="newPass = $event.target.value"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-3 pr-12 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('password') border-red-500 @enderror">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer active:scale-95 transition" tabindex="-1" aria-label="Toggle password visibility">
                            <!-- Eye Slashed (Saat Tersembunyi) -->
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <!-- Eye Open (Saat Terlihat) -->
                            <svg x-show="showPass" class="w-5 h-5 text-[#0098e7]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Password Strength Meter --}}
                    <div x-show="newPass && newPass.length > 0" x-transition class="mt-2.5">
                        <div class="flex items-center justify-between text-[11px] mb-1">
                            <span class="text-gray-500">Kekuatan Kata Sandi:</span>
                            <span class="font-bold" 
                                  :class="score <= 2 ? 'text-red-500' : (score <= 3 ? 'text-amber-500' : 'text-emerald-600')"
                                  x-text="score <= 2 ? 'Lemah' : (score <= 3 ? 'Sedang' : 'Kuat & Aman')"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full transition-all duration-300 rounded-full" 
                                 :class="score <= 2 ? 'w-1/3 bg-red-500' : (score <= 3 ? 'w-2/3 bg-amber-500' : 'w-full bg-emerald-500')"></div>
                        </div>
                    </div>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" required
                            @input="confirmPass = $event.target.value"
                            placeholder="Ulangi kata sandi"
                            class="w-full px-4 py-3 pr-12 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                        <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer active:scale-95 transition" tabindex="-1" aria-label="Toggle confirm password visibility">
                            <!-- Eye Slashed (Saat Tersembunyi) -->
                            <svg x-show="!showConfirmPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <!-- Eye Open (Saat Terlihat) -->
                            <svg x-show="showConfirmPass" class="w-5 h-5 text-[#0098e7]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Status Kecocokan Password --}}
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
                <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-200/80 text-xs space-y-1.5">
                    <p class="font-bold text-gray-700 mb-1">Ketentuan kata sandi:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="newPass.length >= 8 ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="newPass.length >= 8 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="newPass.length >= 8 ? '✓' : '✕'">✕</span>
                            <span>Minimal 8 karakter</span>
                        </div>
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[A-Z]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="/[A-Z]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="/[A-Z]/.test(newPass) ? '✓' : '✕'">✕</span>
                            <span>Huruf besar (A-Z)</span>
                        </div>
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="/[0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="/[0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                            <span>Angka (0-9)</span>
                        </div>
                        <div class="flex items-center gap-1.5 transition-colors duration-150" :class="/[^A-Za-z0-9]/.test(newPass) ? 'text-emerald-600 font-semibold' : 'text-gray-500'">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] transition-all font-bold"
                                  :class="/[^A-Za-z0-9]/.test(newPass) ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400'"
                                  x-text="/[^A-Za-z0-9]/.test(newPass) ? '✓' : '✕'">✕</span>
                            <span>Karakter khusus / simbol</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit"
                        :disabled="!isValid"
                        :class="!isValid ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:shadow-lg active:scale-98'"
                        class="w-full text-white font-bold py-3.5 px-4 rounded-full shadow-md transition text-sm tracking-wide"
                        style="background-color: #0098e7;">
                        Daftar Sekarang →
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 pt-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500 font-medium">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">Masuk ke Akun</a>
            </p>
        </div>
    </div>
</x-guest-layout>