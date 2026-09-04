<x-guest-layout>
    <div class="w-full flex-1 flex flex-col justify-between py-2">
        <div>
            <!-- Header Step 1 -->
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Data Diri</h2>

                <!-- Step Progress Bar -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
                </div>

                <!-- Sub Row: Left hint & Right Step indicator -->
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Isi data sesuai KTP asli Anda</span>
                    <span class="font-bold text-blue-600">Langkah 1 dari 3</span>
                </div>
            </div>

            <form action="{{ route('onboarding.step1.store') }}" method="POST" class="space-y-4" id="step1Form">
                @csrf

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span>
                    </label>
                    <input name="nik" id="nik" type="text" maxlength="16" placeholder="Masukkan 16 digit NIK"
                        value="{{ old('nik', $user->nik ?? '') }}" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16); updateNikCount(this.value);"
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('nik') border-red-500 @enderror">
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[11px] text-gray-400">Harus 16 angka</span>
                        <span id="nik_counter" class="text-[11px] font-semibold text-gray-500">{{ strlen(old('nik', $user->nik ?? '')) }}/16 digit</span>
                    </div>
                    @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input name="name" id="name" type="text" placeholder="Nama lengkap sesuai KTP"
                        value="{{ old('name', $user->name !== explode('@', $user->email)[0] ? $user->name : '') }}" required
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tempat & Tanggal Lahir -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="place_of_birth" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input name="place_of_birth" id="place_of_birth" type="text" placeholder="Kota lahir"
                            value="{{ old('place_of_birth', $user->place_of_birth ?? '') }}" required
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('place_of_birth') border-red-500 @enderror">
                        @error('place_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input name="date_of_birth" id="date_of_birth" type="date" 
                            max="{{ now()->subYears(17)->format('Y-m-d') }}"
                            value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" required
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                @php $currentGender = old('gender', $user->gender ?? 'Laki-laki'); @endphp
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center px-4 py-3 rounded-xl cursor-pointer border transition border-gray-200 bg-gray-50/70 text-gray-700 hover:bg-blue-50/40">
                            <input name="gender" type="radio" value="Laki-laki" id="gender_male" class="w-4 h-4 text-blue-600 focus:ring-blue-500" {{ $currentGender === 'Laki-laki' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-medium">Laki-laki</span>
                        </label>
                        <label class="flex items-center px-4 py-3 rounded-xl cursor-pointer border transition border-gray-200 bg-gray-50/70 text-gray-700 hover:bg-blue-50/40">
                            <input name="gender" type="radio" value="Perempuan" id="gender_female" class="w-4 h-4 text-blue-600 focus:ring-blue-500" {{ $currentGender === 'Perempuan' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-medium">Perempuan</span>
                        </label>
                    </div>
                    @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- No WhatsApp -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        No. WhatsApp / HP <span class="text-red-500">*</span>
                    </label>
                    <input name="phone" id="phone" type="text" maxlength="13" placeholder="08xxxxxxxxxx"
                        value="{{ old('phone', $user->phone ?? '') }}" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('phone') border-red-500 @enderror">
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[11px] text-gray-400">Format angka: 10 - 13 digit</span>
                    </div>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Alamat Lengkap -->
                <div>
                    <label for="address" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Alamat Lengkap KTP <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" id="address" rows="2" placeholder="Jalan, nomor rumah, gedung, dll." required
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium @error('address') border-red-500 @enderror">{{ old('address', $user->address ?? '') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- RT / RW -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="rt" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            RT <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <input name="rt" id="rt" type="text" maxlength="5" placeholder="001"
                            value="{{ old('rt', $user->rt ?? '') }}"
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm font-medium focus:bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label for="rw" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            RW <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <input name="rw" id="rw" type="text" maxlength="5" placeholder="002"
                            value="{{ old('rw', $user->rw ?? '') }}"
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm font-medium focus:bg-white focus:border-blue-500">
                    </div>
                </div>

                <!-- Kelurahan & Kecamatan -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="kelurahan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            Kelurahan / Desa <span class="text-red-500">*</span>
                        </label>
                        <input name="kelurahan" id="kelurahan" type="text" placeholder="Kelurahan / Desa"
                            value="{{ old('kelurahan', $user->kelurahan ?? '') }}" required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s\.\,\'\-]/g, '')"
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm font-medium focus:bg-white focus:border-blue-500 @error('kelurahan') border-red-500 @enderror">
                        @error('kelurahan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kecamatan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            Kecamatan <span class="text-red-500">*</span>
                        </label>
                        <input name="kecamatan" id="kecamatan" type="text" placeholder="Kecamatan"
                            value="{{ old('kecamatan', $user->kecamatan ?? '') }}" required
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s\.\,\'\-]/g, '')"
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm font-medium focus:bg-white focus:border-blue-500 @error('kecamatan') border-red-500 @enderror">
                        @error('kecamatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Kota / Kabupaten & Provinsi -->
                <div class="space-y-3">
                    <div>
                        <label for="city_id" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            Kota / Kabupaten <span class="text-red-500">*</span>
                        </label>
                        <select name="city_id" id="city_id" required onchange="updateProvince(this)"
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 font-medium @error('city_id') border-red-500 @enderror">
                            <option value="">Pilih Kota / Kabupaten...</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" data-province="{{ $city->province ?? '' }}" {{ old('city_id', $user->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }} {{ !empty($city->province) ? '— ' . $city->province : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="province" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                            Provinsi <span class="text-red-500">*</span>
                        </label>
                        <input name="province" id="province" type="text" placeholder="Provinsi domisili"
                            value="{{ old('province', $user->province ?? '') }}" required
                            class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm font-medium focus:bg-white focus:border-blue-500 @error('province') border-red-500 @enderror">
                        @error('province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-5 pb-2">
                    <button type="submit"
                        class="w-full text-white font-bold py-3.5 rounded-full shadow-md hover:shadow-lg active:scale-98 transition text-base tracking-wide cursor-pointer"
                        style="background-color: #0098e7;">
                        Lanjutkan ke Foto KTP →
                    </button>
                </div>
            </form>

            <!-- Option to cancel / restart -->
            <div class="mt-3 text-center pb-2">
                <form action="{{ route('onboarding.cancel') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-red-600 transition font-medium inline-flex items-center gap-1.5 py-1 px-3 rounded-lg hover:bg-red-50 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Batalkan Pendaftaran & Ganti Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateNikCount(val) {
            const counter = document.getElementById('nik_counter');
            counter.innerText = val.length + '/16 digit';
            if (val.length === 16) {
                counter.className = 'text-[11px] font-semibold text-green-600';
            } else {
                counter.className = 'text-[11px] font-semibold text-gray-500';
            }

            // Auto-detect gender
            if (val.length >= 8) {
                const tgl = parseInt(val.substr(6, 2), 10);
                if (!isNaN(tgl)) {
                    if (tgl > 40) {
                        document.getElementById('gender_female').checked = true;
                    } else {
                        document.getElementById('gender_male').checked = true;
                    }
                }
            }
        }

        function updateProvince(select) {
            const selectedOpt = select.options[select.selectedIndex];
            const prov = selectedOpt.getAttribute('data-province');
            if (prov) {
                document.getElementById('province').value = prov;
            }
        }
    </script>
</x-guest-layout>
