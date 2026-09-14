<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2">
        <div>
            <!-- Header Step 3 -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Upload Foto Selfie</h2>

                <!-- Step Progress Bar -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
                </div>

                <!-- Sub Row: Left hint & Right Step indicator -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Upload foto selfie sambil memegang KTP</span>
                    <span class="font-bold text-blue-600">Langkah 3 dari 4</span>
                </div>
            </div>

            <form action="{{ route('onboarding.step3.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                @php
                    $existingSelfie = !empty($user->selfie_photo) ? asset('storage/' . $user->selfie_photo) : null;
                @endphp

                <!-- Upload Area & Examples -->
                <div class="space-y-4">
                    <!-- Upload Box Selfie -->
                    <div id="selfie_upload_box" class="{{ $existingSelfie ? 'hidden' : '' }}">
                        <label for="selfie_photo" class="block cursor-pointer">
                            <div class="bg-gradient-to-b from-blue-50/40 to-gray-50/70 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/60 transition py-7 px-5 text-center shadow-2xs group">
                                <div class="mx-auto w-16 h-16 bg-blue-100/90 text-blue-600 rounded-full flex items-center justify-center mb-3 shadow-2xs group-hover:scale-105 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 mb-1">Ambil atau Pilih Foto Selfie</h3>
                                <p class="text-xs text-gray-500 mb-3.5">Format JPG, PNG (Maksimal 2MB)</p>
                                <div class="inline-flex items-center gap-1.5 bg-[#0098e7] hover:bg-[#0086cc] text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-xs transition active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>Pilih Foto Selfie</span>
                                </div>
                            </div>
                        </label>
                        <input id="selfie_photo" name="selfie_photo" type="file" accept="image/*" class="hidden" {{ $existingSelfie ? '' : 'required' }} onchange="previewSelfie(this)">
                    </div>

                    <!-- Preview Selfie -->
                    <div id="selfie_preview_box" class="{{ $existingSelfie ? '' : 'hidden' }} relative bg-white rounded-2xl overflow-hidden shadow-md border-2 border-blue-500">
                        <img id="selfie_preview_img" src="{{ $existingSelfie ?? '' }}" alt="Preview Selfie" class="w-full h-auto max-h-56 object-contain bg-gray-900">
                        <div class="absolute bottom-2.5 left-3 bg-gray-900/80 backdrop-blur-xs text-white text-[11px] font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            Foto Selfie Berhasil Dipilih
                        </div>
                        <button type="button" onclick="removeSelfie()"
                            class="absolute top-2.5 right-2.5 bg-red-600 hover:bg-red-700 text-white rounded-full p-1.5 shadow-md transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @error('selfie_photo')
                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p>
                    @enderror

                    <!-- Petunjuk / Panduan Foto Selfie -->
                    <div class="bg-blue-50/60 rounded-2xl p-4 border border-blue-100/80">
                        <h4 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Panduan Foto Selfie dengan KTP:
                        </h4>
                        <ul class="text-[11px] text-gray-600 space-y-1.5 list-disc list-inside mb-3">
                            <li>Pegang fisik KTP Anda di samping wajah atau di bawah dagu.</li>
                            <li>Pastikan wajah Anda dan seluruh tulisan di KTP terlihat jelas serta tidak buram.</li>
                            <li>Hindari penggunaan kacamata hitam, topi, atau masker.</li>
                            <li>Pastikan pencahayaan terang dan tidak ada pantulan cahaya (glare) di KTP.</li>
                        </ul>

                        <!-- Contoh Benar & Salah Mini -->
                        <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-blue-100">
                            <!-- Good Example -->
                            <div class="bg-white rounded-2xl p-3 border-2 border-green-500 shadow-2xs flex flex-col justify-between">
                                <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                                    <img src="{{ asset('images/contoh-selfie-benar.jpg') }}" alt="Contoh Selfie Benar" class="w-full h-full object-cover rounded-xl">
                                </div>
                                <p class="text-xs text-green-700 font-bold text-center">✓ Benar</p>
                                <p class="text-[11px] text-gray-500 text-center mt-0.5">Wajah & KTP jelas</p>
                            </div>

                            <!-- Bad Example -->
                            <div class="bg-white rounded-2xl p-3 border-2 border-red-500 shadow-2xs flex flex-col justify-between">
                                <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                                    <img src="{{ asset('images/contoh-selfie-salah.jpg') }}" alt="Contoh Selfie Salah" class="w-full h-full object-cover rounded-xl">
                                </div>
                                <p class="text-xs text-red-700 font-bold text-center">✗ Salah</p>
                                <p class="text-[11px] text-gray-500 text-center mt-0.5">Blur atau gelap</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-5 pb-2 flex items-center gap-3">
                        <a href="{{ route('onboarding.step2') }}"
                            class="w-28 py-3.5 px-3 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-full inline-flex items-center justify-center text-sm font-bold text-gray-700 transition cursor-pointer flex-shrink-0">
                            ← Kembali
                        </a>
                        <button type="submit"
                            class="flex-1 py-3.5 px-4 text-white font-bold rounded-full shadow-md hover:shadow-lg active:scale-98 transition text-sm tracking-wide inline-flex items-center justify-center gap-1.5 cursor-pointer"
                            style="background-color: #0098e7;">
                            <span>Lanjut ke Review</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewSelfie(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('selfie_preview_img').src = e.target.result;
                    document.getElementById('selfie_upload_box').classList.add('hidden');
                    document.getElementById('selfie_preview_box').classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeSelfie() {
            const input = document.getElementById('selfie_photo');
            input.value = '';
            document.getElementById('selfie_preview_img').src = '';
            document.getElementById('selfie_preview_box').classList.add('hidden');
            document.getElementById('selfie_upload_box').classList.remove('hidden');
            input.required = true;
        }
    </script>
</x-guest-layout>
