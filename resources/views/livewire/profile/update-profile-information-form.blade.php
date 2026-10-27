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
                    class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold py-3.5 rounded-xl transition shadow-md active:scale-95 text-sm">
                Oke
            </button>
        </div>
    </div>

    <form wire:submit.prevent="updateProfileInformation" class="space-y-5">

        @if(!optional(auth()->user())->verified && (empty(auth()->user()->ktp_photo) || empty(auth()->user()->selfie_photo)))
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl mb-2">
            <p class="text-sm text-amber-900 font-bold mb-1">Upload Data Diri (KTP &amp; Selfie)</p>
            <p class="text-xs text-amber-800 mb-2">
                Untuk mengajukan atau mengambil bantuan, Anda juga wajib mengunggah Foto KTP dan Foto Selfie (Wajah &amp; KTP).
            </p>
            <a href="{{ route('profile.settings.verification') }}" wire:navigate class="inline-block px-3 py-1.5 bg-amber-200 text-amber-900 font-bold rounded-lg text-xs hover:bg-amber-300 transition">
                Ke Halaman Upload &rarr;
            </a>
        </div>
        @endif

        <!-- Name -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" style="color: #0066FF;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Nama Lengkap

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

        <!-- Gender (Jenis Kelamin) -->
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Jenis Kelamin

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
                    <svg class="w-4 h-4" style="color: #0066FF;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    Email

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

                </span>
            </label>
            <select wire:model="city_id" id="profile_city_id" required
                class="w-full px-4 py-3.5 text-sm rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition bg-white">
                <option value="">Pilih Kota</option>
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

                </span>
            </label>
            <x-smart-address-input 
                wire:model="address" 
                id="profile_address"
                cityRef="#profile_city_id"
                rows="3"
                required
                placeholder="Ketik nama jalan, kelurahan, kecamatan, atau kota..."
            />
            @error('address')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold text-sm py-3.5 rounded-xl hover:shadow-lg transition active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
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

    <script>
        window.addEventListener('smart-address-selected', function(ev) {
            const item = ev.detail;
            if (item && item.city && typeof @this !== 'undefined') {
                @this.selectCityByName(item.city);
            }
        });
    </script>
</div>
