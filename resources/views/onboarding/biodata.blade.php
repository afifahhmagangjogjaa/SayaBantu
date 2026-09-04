<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2">
        <div>
            <!-- Header -->
            <div class="pt-2 mb-6 text-center">
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full mb-3 border border-blue-200 shadow-sm">
                    Langkah Terakhir: Data Identitas
                </span>
                <h2 class="text-2xl font-bold text-gray-900 mb-1.5 tracking-tight">Lengkapi Biodata & Dokumen</h2>
                <p class="text-xs text-gray-500 font-medium">Lengkapi data identitas Anda sesuai KTP yang berlaku</p>
            </div>

            <form action="{{ route('onboarding.biodata.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4.5">
                @csrf

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Nama Lengkap (Sesuai KTP) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap Anda"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NIK -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        NIK (16 Digit) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}" placeholder="16 digit nomor NIK" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('nik') border-red-500 @enderror">
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- No WhatsApp -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        No. WhatsApp / HP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('phone') border-red-500 @enderror">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kota Domisili -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Kota Domisili <span class="text-red-500">*</span>
                    </label>
                    <select name="city_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('city_id') border-red-500 @enderror">
                        <option value="">Pilih Kota Domisili</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Agama & Status Pernikahan -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Agama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="religion" value="{{ old('religion') }}" required placeholder="Islam"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('religion') border-red-500 @enderror">
                        @error('religion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="marital_status" required class="w-full px-3 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white">
                            <option value="Belum Kawin" {{ old('marital_status') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('marital_status') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('marital_status') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('marital_status') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>
                </div>

                <!-- Pekerjaan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}" required placeholder="Contoh: Karyawan Swasta"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('occupation') border-red-500 @enderror">
                    @error('occupation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- RT / RW (Opsional) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">RT (Opsional)</label>
                        <input type="text" name="rt" value="{{ old('rt') }}" maxlength="5" placeholder="001"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none transition bg-gray-50/50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">RW (Opsional)</label>
                        <input type="text" name="rw" value="{{ old('rw') }}" maxlength="5" placeholder="002"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none transition bg-gray-50/50 focus:bg-white">
                    </div>
                </div>

                <!-- Alamat Lengkap -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="2" required placeholder="Jl. Nama Jalan, No. Rumah, Kelurahan, Kecamatan"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm outline-none transition bg-gray-50/50 focus:bg-white @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- ========================================================= -->
                <!-- SEKSI 1: UPLOAD FOTO KTP + TIPS + CONTOH                  -->
                <!-- ========================================================= -->
                <div class="pt-3 border-t border-gray-100 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-900">
                            1. Foto Fisik KTP Asli <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400 font-medium">Maksimal 2MB (JPG/PNG)</span>
                    </div>

                    <!-- Upload Box KTP -->
                    <div id="ktp_upload_box">
                        <label for="ktp_photo" class="block cursor-pointer">
                            <div class="bg-gradient-to-b from-blue-50/40 to-gray-50/70 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/60 transition py-6 px-4 text-center shadow-2xs group">
                                <div class="mx-auto w-14 h-14 bg-blue-100/90 text-blue-600 rounded-full flex items-center justify-center mb-2.5 shadow-2xs group-hover:scale-105 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-xs font-bold text-gray-900 mb-0.5">Ambil atau Pilih Foto KTP</h3>
                                <p class="text-[11px] text-gray-500 mb-3">Format JPG, PNG (Maksimal 2MB)</p>
                                <div class="inline-flex items-center gap-1.5 bg-[#0098e7] hover:bg-[#0086cc] text-white px-4 py-2 rounded-full text-xs font-bold shadow-xs transition active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>Pilih Foto KTP</span>
                                </div>
                            </div>
                        </label>
                        <input id="ktp_photo" name="ktp_photo" type="file" accept="image/*" class="hidden" required onchange="previewImage(this, 'ktp')">
                    </div>

                    <!-- Preview KTP (Hidden by default) -->
                    <div id="ktp_preview_box" class="hidden relative bg-white rounded-2xl overflow-hidden shadow-md border-2 border-blue-500">
                        <img id="ktp_preview_img" src="" alt="Preview KTP" class="w-full h-auto max-h-56 object-contain bg-gray-900">
                        <div class="absolute bottom-2.5 left-3 bg-gray-900/80 backdrop-blur-xs text-white text-[11px] font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            Foto KTP Berhasil Dipilih
                        </div>
                        <button type="button" onclick="removeImage('ktp')"
                            class="absolute top-3 right-3 bg-red-500 text-white rounded-full p-2 shadow-lg hover:bg-red-600 transition active:scale-95 cursor-pointer z-10" title="Hapus / Ganti Foto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @error('ktp_photo') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror

                    <!-- Tips KTP -->
                    <div class="bg-blue-50/80 border border-blue-200/80 rounded-2xl p-3.5 text-blue-900">
                        <h4 class="text-xs font-bold text-blue-950 mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            Tips Foto KTP:
                        </h4>
                        <ul class="text-[11px] text-blue-900/90 space-y-1 pl-5 list-disc leading-relaxed">
                            <li>Pastikan foto fisik KTP terlihat utuh dan tidak terpotong</li>
                            <li>Semua tulisan (NIK, Nama, Alamat) terbaca jelas</li>
                            <li>Hindari pantulan lampu atau bayangan gelap</li>
                        </ul>
                    </div>

                    <!-- Contoh Foto KTP yang Baik -->
                    <div class="bg-gray-50/70 border border-gray-200/80 rounded-2xl p-3.5 shadow-2xs">
                        <p class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Contoh Foto KTP yang Baik:
                        </p>
                        <div class="bg-white rounded-xl p-2.5 border-2 border-green-500 shadow-2xs">
                            <img src="{{ asset('images/contoh-ktp.jpg') }}" alt="Contoh Foto KTP" class="w-full max-h-36 object-contain rounded-lg mx-auto">
                            <p class="text-[11px] text-green-700 font-bold mt-1.5 text-center">✓ Posisi Lurus, Terang & Tulisan Terbaca Jelas</p>
                        </div>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- SEKSI 2: UPLOAD FOTO SELFIE + TIPS + CONTOH               -->
                <!-- ========================================================= -->
                <div class="pt-4 border-t border-gray-100 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-900">
                            2. Foto Selfie Memegang KTP <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-gray-400 font-medium">Maksimal 2MB (JPG/PNG)</span>
                    </div>

                    <!-- Upload Box Selfie -->
                    <div id="selfie_upload_box">
                        <label for="selfie_photo" class="block cursor-pointer">
                            <div class="bg-gradient-to-b from-blue-50/40 to-gray-50/70 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/60 transition py-6 px-4 text-center shadow-2xs group">
                                <div class="mx-auto w-14 h-14 bg-blue-100/90 text-blue-600 rounded-full flex items-center justify-center mb-2.5 shadow-2xs group-hover:scale-105 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-xs font-bold text-gray-900 mb-0.5">Ambil atau Pilih Foto Selfie</h3>
                                <p class="text-[11px] text-gray-500 mb-3">Pegang KTP di samping wajah dengan jelas</p>
                                <div class="inline-flex items-center gap-1.5 bg-[#0098e7] hover:bg-[#0086cc] text-white px-4 py-2 rounded-full text-xs font-bold shadow-xs transition active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    </svg>
                                    <span>Ambil Foto Selfie</span>
                                </div>
                            </div>
                        </label>
                        <input id="selfie_photo" name="selfie_photo" type="file" accept="image/*" capture="user" class="hidden" required onchange="previewImage(this, 'selfie')">
                    </div>

                    <!-- Preview Selfie (Hidden by default) -->
                    <div id="selfie_preview_box" class="hidden relative bg-white rounded-2xl overflow-hidden shadow-md border-2 border-blue-500">
                        <img id="selfie_preview_img" src="" alt="Preview Selfie" class="w-full h-auto max-h-56 object-contain bg-gray-900">
                        <div class="absolute bottom-2.5 left-3 bg-gray-900/80 backdrop-blur-xs text-white text-[11px] font-semibold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-400"></span>
                            Foto Selfie Berhasil Dipilih
                        </div>
                        <button type="button" onclick="removeImage('selfie')"
                            class="absolute top-3 right-3 bg-red-500 text-white rounded-full p-2 shadow-lg hover:bg-red-600 transition active:scale-95 cursor-pointer z-10" title="Hapus / Ganti Foto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @error('selfie_photo') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror

                    <!-- Info Box Selfie -->
                    <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-3.5 text-amber-900">
                        <h4 class="text-xs font-bold text-amber-950 mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Penting:
                        </h4>
                        <ul class="text-[11px] text-amber-900/90 space-y-1 pl-5 list-disc leading-relaxed">
                            <li>Wajah Anda dan fisik KTP harus terlihat utuh & jelas</li>
                            <li>Pegang KTP di samping wajah tanpa menutupi bagian muka</li>
                            <li>Gunakan pencahayaan yang cukup dan jangan gunakan filter</li>
                        </ul>
                    </div>

                    <!-- Contoh Selfie Benar & Salah -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Good Example -->
                        <div class="bg-white rounded-2xl p-3 border-2 border-green-500 shadow-2xs flex flex-col justify-between">
                            <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                                <img src="{{ asset('images/contoh-selfie-benar.jpg') }}" alt="Contoh Selfie Benar" class="w-full h-full object-cover rounded-xl">
                            </div>
                            <p class="text-xs text-green-700 font-bold text-center">✓ Benar</p>
                            <p class="text-[10px] text-gray-500 text-center mt-0.5">Wajah & KTP jelas</p>
                        </div>

                        <!-- Bad Example -->
                        <div class="bg-white rounded-2xl p-3 border-2 border-red-500 shadow-2xs flex flex-col justify-between">
                            <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                                <img src="{{ asset('images/contoh-selfie-salah.jpg') }}" alt="Contoh Selfie Salah" class="w-full h-full object-cover rounded-xl">
                            </div>
                            <p class="text-xs text-red-700 font-bold text-center">✗ Salah</p>
                            <p class="text-[10px] text-gray-500 text-center mt-0.5">Blur atau gelap</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 pb-2">
                    <button type="submit" 
                            class="w-full py-3.5 px-4 text-white font-bold text-base rounded-full shadow-md hover:shadow-lg active:scale-[0.99] transition"
                            style="background: linear-gradient(135deg, #0098e7, #0077cc);">
                        Selesaikan Pendaftaran & Masuk
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                Data Anda dienkripsi dan disimpan secara aman.
            </p>
        </div>
    </div>

    <!-- Script Preview Gambar Instan -->
    <script>
        function previewImage(input, type) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewBox = document.getElementById(type + '_preview_box');
                    const uploadBox = document.getElementById(type + '_upload_box');
                    const previewImg = document.getElementById(type + '_preview_img');

                    previewImg.src = e.target.result;
                    uploadBox.classList.add('hidden');
                    previewBox.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage(type) {
            const input = document.getElementById(type + '_photo');
            const previewBox = document.getElementById(type + '_preview_box');
            const uploadBox = document.getElementById(type + '_upload_box');
            const previewImg = document.getElementById(type + '_preview_img');

            input.value = '';
            previewImg.src = '';
            previewBox.classList.add('hidden');
            uploadBox.classList.remove('hidden');
        }
    </script>
</x-guest-layout>