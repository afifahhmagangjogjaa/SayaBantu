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

                @php
                    $provinces = $cities->pluck('province')->filter()->unique()->values();
                    $savedProvince = old('province', $user->province ?? '');
                    $savedCityId = old('city_id', $user->city_id ?? '');
                    if (!$savedProvince && $savedCityId) {
                        $selectedCityModel = $cities->firstWhere('id', $savedCityId);
                        if ($selectedCityModel) {
                            $savedProvince = $selectedCityModel->province;
                        }
                    }
                @endphp

                <!-- 1. PROVINSI -->
                <div>
                    <label for="province" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Provinsi <span class="text-red-500">*</span>
                    </label>
                    <select name="province" id="province" required onchange="handleProvinceChange()"
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 font-medium @error('province') border-red-500 @enderror">
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ $savedProvince === $prov ? 'selected' : '' }}>
                                {{ $prov }}
                            </option>
                        @endforeach
                    </select>
                    @error('province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 2. KOTA / KABUPATEN -->
                <div>
                    <label for="city_id" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Kota / Kabupaten <span class="text-red-500">*</span>
                    </label>
                    <select name="city_id" id="city_id" required onchange="handleCityChange()" disabled
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 font-medium disabled:bg-gray-100 disabled:cursor-not-allowed @error('city_id') border-red-500 @enderror">
                        <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                    </select>
                    @error('city_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 3. KECAMATAN -->
                <div>
                    <label for="kecamatan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <select name="kecamatan" id="kecamatan" required onchange="handleDistrictChange()" disabled
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 font-medium disabled:bg-gray-100 disabled:cursor-not-allowed @error('kecamatan') border-red-500 @enderror">
                        <option value="">-- Pilih Kota Terlebih Dahulu --</option>
                    </select>
                    @error('kecamatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 4. KELURAHAN / DESA -->
                <div>
                    <label for="kelurahan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Kelurahan / Desa <span class="text-red-500">*</span>
                    </label>
                    <select name="kelurahan" id="kelurahan" required disabled
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 font-medium disabled:bg-gray-100 disabled:cursor-not-allowed @error('kelurahan') border-red-500 @enderror">
                        <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                    </select>
                    @error('kelurahan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

    <!-- Data Master Cities dari Server -->
    <script>
        const allCities = @json($cities);

        function updateNikCount(val) {
            const counter = document.getElementById('nik_counter');
            if (counter) {
                counter.innerText = val.length + '/16 digit';
                if (val.length === 16) {
                    counter.className = 'text-[11px] font-semibold text-green-600';
                } else {
                    counter.className = 'text-[11px] font-semibold text-gray-500';
                }
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

        const extractList = (json) => Array.isArray(json) ? json : (json && json.data ? json.data : []);

        function cleanGeoName(str) {
            return (str || '')
                .toUpperCase()
                .replace(/\b(KOTA\s+ADM|KOTA\s+ADMINISTRASI|KABUPATEN|KOTA|KAB|DAERAH\s+ISTIMEWA|DAERAH\s+KHUSUS\s+IBUKOTA|PROVINSI|KEPULAUAN|KEP|DI|DKI)\b/g, '')
                .replace(/[^A-Z0-9]/g, '')
                .trim();
        }

        function getSignificantWords(str) {
            const s = (str || '')
                .toUpperCase()
                .replace(/\b(DAERAH|ISTIMEWA|KHUSUS|IBUKOTA|KOTA|ADM|ADMINISTRASI|KABUPATEN|KAB|PROVINSI|KEPULAUAN|KEP|DI|DKI)\b/g, ' ')
                .replace(/[^A-Z0-9\s]/g, ' ');
            return s.split(/\s+/).filter(w => w.length > 2);
        }

        function matchesGeo(str1, str2) {
            if (!str1 || !str2) return false;
            const s1 = cleanGeoName(str1);
            const s2 = cleanGeoName(str2);
            if (s1 && s2 && (s1 === s2 || s1.includes(s2) || s2.includes(s1))) return true;

            const w1 = getSignificantWords(str1);
            const w2 = getSignificantWords(str2);
            if (w1.length === 0 || w2.length === 0) return false;

            return w1.every(word => w2.some(w => w.includes(word) || word.includes(w))) ||
                   w2.every(word => w1.some(w => w.includes(word) || word.includes(w)));
        }

        let provincesCache = null;

        async function handleProvinceChange(initialCityId = null, initialKecamatan = null, initialKelurahan = null) {
            const provSelect = document.getElementById('province');
            const citySelect = document.getElementById('city_id');
            const kecSelect = document.getElementById('kecamatan');
            const kelSelect = document.getElementById('kelurahan');

            const selectedProv = provSelect.value;

            citySelect.innerHTML = '<option value="">-- Pilih Kota / Kabupaten --</option>';
            citySelect.disabled = true;
            kecSelect.innerHTML = '<option value="">-- Pilih Kota Terlebih Dahulu --</option>';
            kecSelect.disabled = true;
            kelSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
            kelSelect.disabled = true;

            if (!selectedProv) {
                citySelect.innerHTML = '<option value="">-- Pilih Provinsi Terlebih Dahulu --</option>';
                return;
            }

            // Filter kota berdasarkan provinsi yang dipilih
            const filteredCities = allCities.filter(c => c.province && (
                c.province.toUpperCase().trim() === selectedProv.toUpperCase().trim() ||
                matchesGeo(c.province, selectedProv)
            ));

            if (filteredCities.length === 0) {
                citySelect.innerHTML = '<option value="">-- Tidak ada kota terdaftar --</option>';
                return;
            }

            filteredCities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city.id;
                opt.textContent = city.name;
                opt.setAttribute('data-code', city.code || '');
                opt.setAttribute('data-name', city.name);
                opt.setAttribute('data-province', city.province || '');
                citySelect.appendChild(opt);
            });

            citySelect.disabled = false;

            if (initialCityId) {
                citySelect.value = initialCityId;
                if (citySelect.selectedIndex > 0) {
                    await handleCityChange(initialKecamatan, initialKelurahan);
                }
            } else if (filteredCities.length === 1) {
                citySelect.selectedIndex = 1;
                await handleCityChange(initialKecamatan, initialKelurahan);
            }
        }

        async function handleCityChange(initialKecamatan = null, initialKelurahan = null) {
            const citySelect = document.getElementById('city_id');
            const selectedOpt = citySelect.options[citySelect.selectedIndex];
            const kecSelect = document.getElementById('kecamatan');
            const kelSelect = document.getElementById('kelurahan');

            if (!selectedOpt || !selectedOpt.value) {
                kecSelect.innerHTML = '<option value="">-- Pilih Kota Terlebih Dahulu --</option>';
                kecSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                kelSelect.disabled = true;
                return;
            }

            const cityId = selectedOpt.value;
            const provName = selectedOpt.getAttribute('data-province') || document.getElementById('province').value || '';
            let cityCode = selectedOpt.getAttribute('data-code') || '';
            const cityName = selectedOpt.getAttribute('data-name') || selectedOpt.textContent || '';

            kecSelect.innerHTML = '<option value="">-- Memuat Kecamatan... --</option>';
            kecSelect.disabled = true;
            kelSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
            kelSelect.disabled = true;

            try {
                let districts = [];

                // 1. Prioritaskan panggil backend proxy internal (kebal CORS & otomatis resolve kode)
                try {
                    const resProxy = await fetch(`{{ route('api.wilayah.districts') }}?city_id=${cityId}&city_name=${encodeURIComponent(cityName)}&province=${encodeURIComponent(provName)}`);
                    if (resProxy.ok) {
                        const proxyData = await resProxy.json();
                        districts = extractList(proxyData);
                    }
                } catch (err) {}

                // 2. Fallback jika backend proxy gagal
                if (districts.length === 0 && cityCode) {
                    try {
                        const resDist = await fetch(`https://wilayah.id/api/districts/${cityCode}.json`);
                        if (resDist.ok) {
                            const distData = await resDist.json();
                            districts = extractList(distData);
                        }
                    } catch (err) {}
                }

                // 3. Fallback kedua jika masih belum ketemu
                if (districts.length === 0) {
                    if (!provincesCache || provincesCache.length === 0) {
                        try {
                            const resProv = await fetch('https://wilayah.id/api/provinces.json');
                            if (resProv.ok) {
                                const provData = await resProv.json();
                                provincesCache = extractList(provData);
                            }
                        } catch (e) {
                            provincesCache = [];
                        }
                    }

                    let matchedProv = (provincesCache || []).find(p => matchesGeo(p.name, provName));

                    if (matchedProv) {
                        try {
                            const resReg = await fetch(`https://wilayah.id/api/regencies/${matchedProv.code}.json`);
                            if (resReg.ok) {
                                const regData = await resReg.json();
                                const regList = extractList(regData);

                                let matchedReg = regList.find(r => matchesGeo(r.name, cityName));

                                if (matchedReg) {
                                    cityCode = matchedReg.code;
                                    const resDist = await fetch(`https://wilayah.id/api/districts/${cityCode}.json`);
                                    if (resDist.ok) {
                                        const distData = await resDist.json();
                                        districts = extractList(distData);
                                    }
                                }
                            }
                        } catch (e) {}
                    }
                }

                if (districts.length > 0) {
                    kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                    districts.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.name;
                        opt.setAttribute('data-code', d.code);
                        opt.textContent = d.name;
                        kecSelect.appendChild(opt);
                    });
                    kecSelect.disabled = false;

                    if (initialKecamatan) {
                        for (let i = 0; i < kecSelect.options.length; i++) {
                            const opt = kecSelect.options[i];
                            if (matchesGeo(opt.value, initialKecamatan)) {
                                kecSelect.selectedIndex = i;
                                break;
                            }
                        }
                        if (kecSelect.selectedIndex > 0) {
                            await handleDistrictChange(initialKelurahan);
                        }
                    }
                } else {
                    kecSelect.innerHTML = '<option value="">-- Gagal memuat kecamatan --</option>';
                }
            } catch (e) {
                console.error('Error fetching districts:', e);
                kecSelect.innerHTML = '<option value="">-- Gagal memuat kecamatan --</option>';
            }
        }

        async function handleDistrictChange(initialKelurahan = null) {
            const kecSelect = document.getElementById('kecamatan');
            const kelSelect = document.getElementById('kelurahan');
            const selectedOpt = kecSelect.options[kecSelect.selectedIndex];

            if (!selectedOpt || !selectedOpt.value) {
                kelSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                kelSelect.disabled = true;
                return;
            }

            const districtCode = selectedOpt.getAttribute('data-code');
            if (!districtCode) {
                kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
                return;
            }

            kelSelect.innerHTML = '<option value="">-- Memuat Kelurahan... --</option>';
            kelSelect.disabled = true;

            try {
                let villages = [];

                // 1. Prioritaskan panggil backend proxy internal
                try {
                    const resProxy = await fetch(`{{ route('api.wilayah.villages') }}?district_code=${encodeURIComponent(districtCode)}`);
                    if (resProxy.ok) {
                        const proxyData = await resProxy.json();
                        villages = extractList(proxyData);
                    }
                } catch (err) {}

                // 2. Fallback direct API
                if (villages.length === 0) {
                    try {
                        const resVill = await fetch(`https://wilayah.id/api/villages/${districtCode}.json`);
                        if (resVill.ok) {
                            const villData = await resVill.json();
                            villages = extractList(villData);
                        }
                    } catch (e) {}
                }

                if (villages.length > 0) {
                    kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
                    villages.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.name;
                        opt.setAttribute('data-code', v.code);
                        opt.textContent = v.name;
                        kelSelect.appendChild(opt);
                    });
                    kelSelect.disabled = false;

                    if (initialKelurahan) {
                        const cleanTargetKel = cleanGeoName(initialKelurahan);
                        for (let i = 0; i < kelSelect.options.length; i++) {
                            const opt = kelSelect.options[i];
                            const cleanOpt = cleanGeoName(opt.value);
                            if (cleanOpt === cleanTargetKel || cleanOpt.includes(cleanTargetKel) || cleanTargetKel.includes(cleanOpt)) {
                                kelSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                } else {
                    kelSelect.innerHTML = '<option value="">-- Tidak ada data kelurahan --</option>';
                }
            } catch (e) {
                console.error('Error fetching villages:', e);
                kelSelect.innerHTML = '<option value="">-- Gagal memuat kelurahan --</option>';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const provSelect = document.getElementById('province');
            const initialCityId = @json(old('city_id', $user->city_id ?? ''));
            const initialKecamatan = @json(old('kecamatan', $user->kecamatan ?? ''));
            const initialKelurahan = @json(old('kelurahan', $user->kelurahan ?? ''));

            if (provSelect && provSelect.value) {
                handleProvinceChange(initialCityId, initialKecamatan, initialKelurahan);
            }
        });
    </script>
</x-guest-layout>
