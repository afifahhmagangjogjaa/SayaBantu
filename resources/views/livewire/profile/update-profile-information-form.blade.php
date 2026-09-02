<div x-data="{ 
    showModal: false,
    redirectUrl: ''
}" 
x-on:profile-saved.window="
    redirectUrl = $event.detail.redirectUrl;
    showModal = true;
">

    <!-- Center Modal Popup -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
        
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-3xl shadow-2xl max-w-xs w-full p-6 text-center border border-gray-100">
            
            <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-emerald-100 flex items-center justify-center shadow-inner">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            
            <h2 class="text-xl font-bold text-gray-900 mb-2">Berhasil!</h2>
            <p class="text-sm text-gray-600 mb-6">Perubahan profil Anda telah berhasil disimpan.</p>
            
            <button @click="window.location.href = redirectUrl" 
                    class="w-full text-white font-bold py-3.5 rounded-xl transition shadow-lg active:scale-95"
                    style="background: linear-gradient(to bottom right, #0098e7, #0060b0);">
                Oke
            </button>
        </div>
    </div>

    <form wire:submit.prevent="updateProfileInformation" class="space-y-5">

        @if((empty(auth()->user()->ktp_photo) || empty(auth()->user()->selfie_photo)) && auth()->user()?->role !== 'mitra')
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl mb-2">
            <p class="text-sm text-amber-900 font-bold mb-1">Upload Data Diri (KTP &amp; Selfie)</p>
            <p class="text-xs text-amber-800 mb-2">
                Untuk mengambil bantuan, Anda juga wajib mengunggah Foto KTP dan Foto Selfie (Wajah &amp; KTP).
            </p>
            <a href="{{ route('profile.settings.verification') }}" class="inline-block px-3 py-1.5 bg-amber-200 text-amber-900 font-bold rounded-lg text-xs hover:bg-amber-300 transition">
                Ke Halaman Upload &rarr;
            </a>
        </div>
        @endif

        <!-- Name -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" style="color: #0098e7;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Nama Lengkap
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <input type="text" wire:model="name" required
                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white"
                placeholder="Masukkan nama lengkap">
            @error('name')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- NIK (16 digit angka) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    NIK (Nomor Induk Kependudukan)
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <input type="text" wire:model="nik" required maxlength="16" inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white font-mono tracking-wider"
                placeholder="16 digit NIK sesuai KTP">
            <p class="mt-1 text-[11px] text-gray-400">Harus tepat 16 digit angka.</p>
            @error('nik')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Place and Date of Birth -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Tempat Lahir
                        <span class="text-red-500 font-bold">*</span>
                    </span>
                </label>
                <input type="text" wire:model="place_of_birth" required
                    class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white"
                    placeholder="Contoh: Jakarta">
                @error('place_of_birth')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Tanggal Lahir
                        <span class="text-red-500 font-bold">*</span>
                    </span>
                </label>
                <input type="date" wire:model="date_of_birth" required max="{{ now()->subYears(17)->format('Y-m-d') }}"
                    onkeydown="return false" onclick="this.showPicker && this.showPicker()"
                    class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white cursor-pointer">
                @error('date_of_birth')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Gender (Jenis Kelamin) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Jenis Kelamin
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <select wire:model="gender" required
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            @error('gender')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" style="color: #0098e7;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    Email
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <input type="email" wire:model="email" required
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white"
                placeholder="email@contoh.com">
            @error('email')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Phone (Maks 13 digit angka) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    No. HP / WhatsApp
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <input type="tel" wire:model="phone" required maxlength="13" inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white"
                placeholder="08123456789">
            <p class="mt-1 text-[11px] text-gray-400">Maksimal 13 digit angka.</p>
            @error('phone')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- City (Kota Domisili) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    Kota
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <select wire:model="city_id" required
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                <option value="">-- Pilih Kota --</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
            @error('city_id')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Address (Alamat Lengkap) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                    Alamat Lengkap
                    <span class="text-red-500 font-bold">*</span>
                </span>
            </label>
            <textarea wire:model="address" rows="3" required
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition resize-none bg-white"
                placeholder="Jl. Merdeka No. 123, Kelurahan, Kecamatan"></textarea>
            @error('address')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- RT / RW (Maksimal 3 digit) -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    RT
                </label>
                <input type="text" wire:model="rt" inputmode="numeric" maxlength="3"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" placeholder="001"
                    class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                @error('rt')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    RW
                </label>
                <input type="text" wire:model="rw" inputmode="numeric" maxlength="3"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" placeholder="002"
                    class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                @error('rw')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Religion (Agama) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Agama <span class="text-red-500">*</span>
                </span>
            </label>
            <select wire:model="religion"
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                <option value="">Pilih Agama</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen (Protestan)</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Khonghucu">Khonghucu</option>
            </select>
            @error('religion')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Occupation (Pekerjaan) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Pekerjaan <span class="text-red-500">*</span>
                </span>
            </label>
            <input type="text" wire:model="occupation"
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white"
                placeholder="Contoh: Karyawan Swasta, Wiraswasta, Mahasiswa">
            @error('occupation')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Marital Status (Status Perkawinan) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    Status Perkawinan <span class="text-red-500">*</span>
                </span>
            </label>
            <select wire:model="marital_status"
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                <option value="">Pilih Status Perkawinan</option>
                <option value="Belum Kawin">Belum Kawin</option>
                <option value="Kawin">Kawin</option>
                <option value="Cerai Hidup">Cerai Hidup</option>
                <option value="Cerai Mati">Cerai Mati</option>
            </select>
            @error('marital_status')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full text-white font-semibold text-sm py-4 rounded-xl hover:shadow-lg transition active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed shadow-md"
                style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
                <span wire:loading.remove wire:target="updateProfileInformation" class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </span>
                <span wire:loading wire:target="updateProfileInformation" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>