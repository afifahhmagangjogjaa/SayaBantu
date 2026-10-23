<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between"
        x-data="{
            showPass: false,
            showConfirmPass: false,
            role: '{{ $role ?? 'customer' }}',
            nameVal: '{{ old('name', '') }}',
            emailVal: '{{ old('email', '') }}',
            newPass: '',
            confirmPass: '',
            get score() {
                let s = 0;
                if (this.newPass.length >= 8) s++;
                if (/[A-Z]/.test(this.newPass)) s++;
                if (/[0-9]/.test(this.newPass)) s++;
                if (/[^A-Za-z0-9]/.test(this.newPass)) s++;
                return s;
            }
        }">
        <div>
            <!-- Header Register matching mockups -->
            <div class="pt-1 mb-5">
                @if ($role === 'mitra')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 mb-2.5">
                        <span>🤝</span> Mitra Sayabantu
                    </span>
                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                        Daftar Menjadi Mitra
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Bergabung bersama kami dan dapatkan peluang penghasilan setiap hari.
                    </p>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#0066FF] border border-blue-200/60 mb-2.5">
                        <span>👤</span> Akun Customer
                    </span>
                    <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                        Daftar Akun Baru
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Buat akun untuk mulai mencari bantuan serabutan dengan mudah.
                    </p>
                @endif
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            placeholder="Contoh: Budi Santoso"
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            @input="nameVal = $event.target.value"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium @error('name') border-red-500 @enderror">
                    </div>

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
                    <label for="email" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            @input="emailVal = $event.target.value"
                            placeholder="nama@email.com"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium @error('email') border-red-500 @enderror">
                    </div>

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
                    <label for="password" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                            @input="newPass = $event.target.value"
                            placeholder="Minimal 8 karakter"
                            class="w-full pl-10 pr-12 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium @error('password') border-red-500 @enderror">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer active:scale-95 transition" tabindex="-1" aria-label="Toggle password visibility">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password_confirmation" :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" required
                            @input="confirmPass = $event.target.value"
                            placeholder="Ulangi kata sandi"
                            class="w-full pl-10 pr-12 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-[#0066FF] focus:ring-4 focus:ring-blue-100 transition shadow-2xs font-medium">
                        <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer active:scale-95 transition" tabindex="-1" aria-label="Toggle confirm password visibility">
                            <svg x-show="!showConfirmPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg x-show="showConfirmPass" class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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

                <div class="pt-2">
                    <button type="submit"
                        class="w-full text-white font-bold py-3.5 sm:py-4 px-6 rounded-full shadow-md shadow-blue-500/25 hover:bg-[#0055D4] active:scale-[0.98] transition text-base tracking-wide bg-[#0066FF] cursor-pointer">
                        Lanjut ke Data Diri
                    </button>
                    <p class="text-[11px] text-gray-400 text-center mt-3 leading-relaxed">
                        Dengan mendaftar, Anda menyetujui
                        <a href="#" class="text-[#0066FF] hover:underline font-semibold">Syarat & Ketentuan</a> dan
                        <a href="#" class="text-[#0066FF] hover:underline font-semibold">Kebijakan Privasi</a> kami.
                    </p>
                </div>
            </form>
        </div>

        <div class="mt-6 pt-3 text-center">
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-[#0066FF] hover:underline ml-1">Login</a>
            </p>
        </div>
    </div>
</x-guest-layout>
