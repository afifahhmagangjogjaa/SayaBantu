<x-app-layout>
    <x-slot name="title">Ubah Kata Sandi</x-slot>

    <style>
        /* Sembunyikan icon mata bawaan browser Windows / Edge agar tidak dobel */
        input::-ms-reveal,
        input::-ms-clear,
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none !important;
        }
    </style>

    <div class="min-h-screen bg-gray-50 pb-24">
        <!-- BRImo Header -->
        <div class="relative bg-gradient-to-br from-[#0098e7] via-[#0077cc] to-[#0060b0] pb-24 overflow-hidden">
            <!-- Decorative Circles -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative max-w-md mx-auto px-6 pt-4 pb-6">
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ auth()->user() && auth()->user()->role === 'mitra' ? route('mitra.profile') : route('profile') }}" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition" title="Kembali ke Profil">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="w-10"></div>
                </div>
                
                <h1 class="text-2xl font-bold text-white mb-2">Ubah Kata Sandi</h1>
                <p class="text-sm text-white/90">Perbarui kata sandi untuk keamanan akun Anda</p>
            </div>

            <!-- Curved Separator -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                    <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#F9FAFB"/>
                </svg>
            </div>
        </div>

        <!-- Password Form -->
        <div class="max-w-md mx-auto px-6 -mt-16 relative z-10">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <!-- Dynamic Alert Container -->
                <div id="alert-container">
                    @if(session('status'))
                        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-green-900">Berhasil!</h4>
                                    <p class="text-xs text-green-800 mt-0.5">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl transition">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-red-900">Gagal Mengubah Kata Sandi</h4>
                                    <ul class="text-xs text-red-700 mt-1 list-disc list-inside space-y-0.5">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <form id="password-form" method="POST" action="{{ route('profile.password.update') }}" class="space-y-5"
                    x-data="{
                        currentPass: '',
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
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" style="color: #0098e7;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                Kata Sandi Saat Ini
                            </div>
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required autocomplete="off"
                                @input="currentPass = $event.target.value"
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#0098e7] focus:ring-2 focus:ring-[#0098e7]/20"
                                placeholder="Masukkan kata sandi saat ini">
                            <button type="button" onclick="togglePassword('current_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                                <svg class="w-5 h-5 eye-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="w-5 h-5 eye-slash-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" style="color: #0098e7;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                Kata Sandi Baru
                            </div>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required autocomplete="new-password"
                                @input="newPass = $event.target.value"
                                class="w-full px-4 py-3 pr-12 rounded-xl border transition focus:ring-2 focus:ring-[#0098e7]/20"
                                :class="(confirmPass && newPass && newPass === confirmPass && newPass.length >= 8) ? 'border-emerald-500 bg-emerald-50/10 focus:border-emerald-500' : 'border-gray-300 focus:border-[#0098e7]'"
                                placeholder="Masukkan kata sandi baru">
                            <button type="button" onclick="togglePassword('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                                <svg class="w-5 h-5 eye-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="w-5 h-5 eye-slash-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>

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

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" style="color: #0098e7;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                                Konfirmasi Kata Sandi Baru
                            </div>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                                @input="confirmPass = $event.target.value"
                                class="w-full px-4 py-3 pr-12 rounded-xl border transition focus:ring-2 focus:ring-[#0098e7]/20"
                                :class="(confirmPass && newPass && newPass === confirmPass && newPass.length >= 8) ? 'border-emerald-500 bg-emerald-50/10 focus:border-emerald-500' : 'border-gray-300 focus:border-[#0098e7]'"
                                placeholder="Masukkan ulang kata sandi baru">
                            <button type="button" onclick="togglePassword('password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                                <svg class="w-5 h-5 eye-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="w-5 h-5 eye-slash-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
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
                    <div class="p-3.5 bg-gray-50/80 rounded-xl border border-gray-100 text-xs space-y-1.5 mt-2">
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

                    <!-- Submit Button with loading state -->
                    <button type="submit" id="submit-btn"
                        :disabled="!isValid"
                        :class="!isValid ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:shadow-lg'"
                        class="w-full py-3.5 rounded-xl text-white font-bold transition mt-6 flex items-center justify-center gap-2" style="background: linear-gradient(to right, #0098e7, #0060b0);">
                        <svg id="btn-spinner" class="hidden w-5 h-5 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="btn-text">Ubah Kata Sandi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            if (btn) {
                const eye = btn.querySelector('.eye-icon');
                const eyeSlash = btn.querySelector('.eye-slash-icon');
                if (eye && eyeSlash) {
                    eye.classList.toggle('hidden', !isPassword);
                    eyeSlash.classList.toggle('hidden', isPassword);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('password-form');
            const alertContainer = document.getElementById('alert-container');
            const submitBtn = document.getElementById('submit-btn');
            const btnSpinner = document.getElementById('btn-spinner');
            const btnText = document.getElementById('btn-text');

            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Set loading state
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnText.textContent = 'Menyimpan...';

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Success: clear inputs
                        form.reset();

                        // Show success banner
                        alertContainer.innerHTML = `
                            <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl transition animate-fadeIn">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-green-900">Berhasil!</h4>
                                        <p class="text-xs text-green-800 mt-0.5">${data.message || 'Kata sandi berhasil diperbarui! Silakan gunakan kata sandi baru saat login.'}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        // Collect errors
                        let errorsHtml = '';
                        if (data.errors) {
                            Object.values(data.errors).flat().forEach(err => {
                                errorsHtml += `<li>${err}</li>`;
                            });
                        } else if (data.message) {
                            errorsHtml += `<li>${data.message}</li>`;
                        } else {
                            errorsHtml += `<li>Terjadi kesalahan saat mengubah kata sandi.</li>`;
                        }

                        alertContainer.innerHTML = `
                            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl transition animate-fadeIn">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-red-900">Gagal Mengubah Kata Sandi</h4>
                                        <ul class="text-xs text-red-700 mt-1 list-disc list-inside space-y-0.5">
                                            ${errorsHtml}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                } catch (err) {
                    console.error('Password update error:', err);
                    alertContainer.innerHTML = `
                        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl transition">
                            <p class="text-xs text-red-700">Terjadi kesalahan koneksi. Silakan coba lagi.</p>
                        </div>
                    `;
                } finally {
                    submitBtn.disabled = false;
                    btnSpinner.classList.add('hidden');
                    btnText.textContent = 'Ubah Kata Sandi';
                }
            });
        });
    </script>
</x-app-layout>