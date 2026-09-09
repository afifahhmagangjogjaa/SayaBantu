<x-mitra-layout>
    <x-slot name="title">Ubah Kata Sandi</x-slot>

    <div class="min-h-screen bg-gray-50 pb-24">
        <!-- BRImo-like Header -->
        <div class="relative bg-gradient-to-br from-[#0098e7] via-[#0077cc] to-[#0060b0] pb-24 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>

            <div class="relative max-w-md mx-auto px-6 pt-4 pb-6">
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('mitra.profile') }}" class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition" title="Kembali ke Profil">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="w-10"></div>
                </div>

                <h1 class="text-2xl font-bold text-white mb-2">Ubah Kata Sandi</h1>
                <p class="text-sm text-white/90">Perbarui kata sandi untuk keamanan akun Anda</p>
            </div>

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

                <form id="password-form" method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
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
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#0098e7] focus:ring-2 focus:ring-[#0098e7]/20"
                                placeholder="Masukkan kata sandi saat ini">
                            <button type="button" onclick="togglePassword('current_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#0098e7] focus:ring-2 focus:ring-[#0098e7]/20"
                                placeholder="Masukkan kata sandi baru">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
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
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#0098e7] focus:ring-2 focus:ring-[#0098e7]/20"
                                placeholder="Masukkan ulang kata sandi baru">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button with loading state -->
                    <button type="submit" id="submit-btn"
                        class="w-full py-3.5 rounded-xl text-white font-bold hover:shadow-lg transition mt-6 flex items-center justify-center gap-2" style="background: linear-gradient(to right, #0098e7, #0060b0);">
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
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
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
</x-mitra-layout>