<x-guest-layout>
    <style>
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }
    </style>
    <div class="w-full flex-1 flex flex-col justify-between py-2">
        <div>
            <!-- Header -->
            <div class="pt-2 mb-6 text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded-full mb-3 border border-green-200 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Email Berhasil Diverifikasi</span>
                </div>

                <h2 class="text-2xl font-extrabold text-gray-900 mb-1.5 tracking-tight">Buat Kata Sandi</h2>
                <p class="text-xs text-gray-500 font-medium leading-relaxed">
                    Buat kata sandi baru untuk mengamankan akun Anda
                </p>
            </div>

            <form action="{{ route('onboarding.password.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Kata Sandi Baru -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autofocus
                            placeholder="Minimal 8 karakter"
                            class="w-full pl-4 pr-12 py-3.5 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium outline-none @error('password') border-red-500 @enderror">
                        
                        <!-- Show / Hide Button with safe spacing -->
                        <button type="button" onclick="togglePass('password', 'eye-open-1', 'eye-closed-1')"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-700 active:scale-90 transition focus:outline-none cursor-pointer"
                            tabindex="-1"
                            aria-label="Tampilkan atau sembunyikan kata sandi">
                            <!-- Eye Slashed (Saat Tersembunyi) -->
                            <svg id="eye-closed-1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <!-- Eye Open (Saat Terlihat) -->
                            <svg id="eye-open-1" class="w-5 h-5 text-[#0098e7] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            placeholder="Ketik ulang kata sandi Anda"
                            class="w-full pl-4 pr-12 py-3.5 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium outline-none">
                        
                        <!-- Show / Hide Button with safe spacing -->
                        <button type="button" onclick="togglePass('password_confirmation', 'eye-open-2', 'eye-closed-2')"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-700 active:scale-90 transition focus:outline-none cursor-pointer"
                            tabindex="-1"
                            aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
                            <!-- Eye Slashed (Saat Tersembunyi) -->
                            <svg id="eye-closed-2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <!-- Eye Open (Saat Terlihat) -->
                            <svg id="eye-open-2" class="w-5 h-5 text-[#0098e7] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit"
                        class="w-full text-white font-bold py-3.5 px-4 rounded-full shadow-md hover:shadow-lg active:scale-98 transition text-sm tracking-wide cursor-pointer"
                        style="background-color: #0098e7;">
                        Simpan Kata Sandi & Lanjutkan →
                    </button>
                </div>
            </form>

            <!-- Option to cancel / restart -->
            <div class="mt-4 text-center">
                <form action="{{ route('onboarding.cancel') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-red-600 transition font-medium inline-flex items-center gap-1.5 py-1 px-3 rounded-lg hover:bg-red-50 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Batalkan Pendaftaran / Ganti Email</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
            <p class="text-[11px] text-gray-400">
                🔒 Kata sandi Anda dienkripsi secara aman dengan standar keamanan tingkat tinggi.
            </p>
        </div>
    </div>

    <!-- Pure Native JavaScript for Instant Show/Hide Password -->
    <script>
        function togglePass(inputId, eyeOpenId, eyeClosedId) {
            const input = document.getElementById(inputId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }
    </script>
</x-guest-layout>