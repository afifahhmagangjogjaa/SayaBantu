<?php

use App\Models\Registration;
use App\Models\City;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $nik = '';
    public string $full_name = '';
    public string $place_of_birth = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $address = '';
    public string $rt = '';
    public string $rw = '';
    public string $kelurahan = '';
    public string $kecamatan = '';
    public string $city = '';
    public ?int $city_id = null;
    public string $province = '';
    
    public $cities = [];
    public $apiProvinces = [];
    public $apiCities = [];
    public $apiDistricts = [];
    public $apiVillages = [];
    public string $selectedCityCode = '';
    public string $selectedDistrictCode = '';
    public string $selectedVillageCode = '';

    // Auto-detect gender from NIK
    public function updatedNik($value)
    {
        if (strlen($value) >= 8) {
            $tglLahir = (int) substr($value, 6, 2);
            // Jika tanggal > 40, berarti perempuan
            $this->gender = $tglLahir > 40 ? 'Perempuan' : 'Laki-laki';
        }
    }

    public function fetchProvinces()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get('https://wilayah.id/api/provinces.json');
            if ($response->successful()) {
                $this->apiProvinces = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->apiProvinces = [];
        }
    }

    public function fetchCities($provinceCode)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://wilayah.id/api/regencies/{$provinceCode}.json");
            if ($response->successful()) {
                $this->apiCities = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->apiCities = [];
        }
    }

    public function fetchDistricts($cityCode)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://wilayah.id/api/districts/{$cityCode}.json");
            if ($response->successful()) {
                $this->apiDistricts = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->apiDistricts = [];
        }
    }

    public function fetchVillages($districtCode)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://wilayah.id/api/villages/{$districtCode}.json");
            if ($response->successful()) {
                $this->apiVillages = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->apiVillages = [];
        }
    }

    public function updatedCityId($id)
    {
        $this->kecamatan = '';
        $this->selectedDistrictCode = '';
        $this->apiDistricts = [];
        $this->kelurahan = '';
        $this->selectedVillageCode = '';
        $this->apiVillages = [];

        if (!$id) {
            $this->city = '';
            $this->province = '';
            $this->selectedCityCode = '';
            return;
        }

        $city = City::find($id);
        if ($city) {
            $this->city = $city->name;
            $this->province = $city->province ?? '';
            $this->resolveCityCodeAndFetchDistricts($city);
        }
    }

    public function resolveCityCodeAndFetchDistricts($city)
    {
        if (!empty($city->code)) {
            $this->selectedCityCode = $city->code;
            $this->fetchDistricts($this->selectedCityCode);
            return;
        }

        $this->fetchProvinces();
        $cleanProvInput = trim(preg_replace('/\b(DAERAH|ISTIMEWA|KHUSUS|IBUKOTA|PROVINSI|DI|DKI)\b/i', '', $city->province ?? ''));
        $prov = collect($this->apiProvinces)->first(function ($p) use ($city, $cleanProvInput) {
            $pName = $p['name'];
            $cleanP = trim(preg_replace('/\b(DAERAH|ISTIMEWA|KHUSUS|IBUKOTA|PROVINSI|DI|DKI)\b/i', '', $pName));
            return strtoupper(trim($city->province)) === strtoupper(trim($pName)) ||
                (!empty($cleanProvInput) && stripos($cleanP, $cleanProvInput) !== false) ||
                (!empty($cleanProvInput) && stripos($pName, $cleanProvInput) !== false) ||
                stripos($pName, trim($city->province)) !== false ||
                stripos(trim($city->province), $pName) !== false;
        });

        if ($prov) {
            $this->fetchCities($prov['code']);
            $cityNameClean = trim(preg_replace('/\b(KOTA\s+ADM|KOTA|KABUPATEN|KAB)\b/i', '', $city->name ?? ''));
            $cityApi = collect($this->apiCities)->first(function ($c) use ($city, $cityNameClean) {
                $apiName = $c['name'];
                $cleanApi = trim(preg_replace('/\b(KOTA\s+ADM|KOTA|KABUPATEN|KAB)\b/i', '', $apiName));
                return strtoupper(trim($city->name)) === strtoupper(trim($apiName)) ||
                    (!empty($cityNameClean) && stripos($cleanApi, $cityNameClean) !== false) ||
                    (!empty($cityNameClean) && stripos($apiName, $cityNameClean) !== false);
            });

            if ($cityApi) {
                $this->selectedCityCode = $cityApi['code'];
                $city->update(['code' => $this->selectedCityCode]);
                $this->fetchDistricts($this->selectedCityCode);
            }
        }
    }

    public function updatedSelectedDistrictCode($code)
    {
        $dist = collect($this->apiDistricts)->firstWhere('code', $code);
        $this->kecamatan = $dist ? $dist['name'] : '';

        $this->kelurahan = '';
        $this->selectedVillageCode = '';
        $this->apiVillages = [];

        if ($code) {
            $this->fetchVillages($code);
        }
    }

    public function updatedSelectedVillageCode($code)
    {
        $vill = collect($this->apiVillages)->firstWhere('code', $code);
        $this->kelurahan = $vill ? $vill['name'] : '';
    }

    // Preload saved registration values if a registration UUID exists in session
    public function mount(): void
    {
        $this->cities = City::where('is_active', true)->orderBy('name')->get();

        $uuid = Session::get('registration_uuid');
        if (!$uuid) {
            return;
        }

        $registration = Registration::where('uuid', $uuid)->first();
        if (!$registration) {
            return;
        }

        $this->nik = $registration->nik ?? $this->nik;
        $this->full_name = $registration->full_name ?? $this->full_name;
        $this->place_of_birth = $registration->place_of_birth ?? $this->place_of_birth;
        $this->date_of_birth = $registration->date_of_birth ?? $this->date_of_birth;
        $this->gender = $registration->gender ?? $this->gender;
        $this->address = $registration->address ?? $this->address;
        $this->rt = $registration->rt ?? $this->rt;
        $this->rw = $registration->rw ?? $this->rw;
        $this->kelurahan = $registration->kelurahan ?? $this->kelurahan;
        $this->kecamatan = $registration->kecamatan ?? $this->kecamatan;
        $this->city = $registration->city ?? $this->city;
        $this->city_id = $registration->city_id ? (int) $registration->city_id : null;
        $this->province = $registration->province ?? $this->province;

        if ($this->city_id) {
            $city = City::find($this->city_id);
            if ($city) {
                $this->resolveCityCodeAndFetchDistricts($city);
                if (!empty($this->kecamatan) && !empty($this->apiDistricts)) {
                    $distApi = collect($this->apiDistricts)->firstWhere('name', strtoupper(trim($this->kecamatan)));
                    if (!$distApi) {
                        $distApi = collect($this->apiDistricts)->first(function ($d) {
                            return str_contains(strtoupper($d['name']), strtoupper(trim($this->kecamatan))) || str_contains(strtoupper(trim($this->kecamatan)), strtoupper($d['name']));
                        });
                    }
                    if ($distApi) {
                        $this->selectedDistrictCode = $distApi['code'];
                        $this->fetchVillages($this->selectedDistrictCode);

                        if (!empty($this->kelurahan) && !empty($this->apiVillages)) {
                            $villApi = collect($this->apiVillages)->firstWhere('name', strtoupper(trim($this->kelurahan)));
                            if (!$villApi) {
                                $villApi = collect($this->apiVillages)->first(function ($v) {
                                    return str_contains(strtoupper($v['name']), strtoupper(trim($this->kelurahan))) || str_contains(strtoupper(trim($this->kelurahan)), strtoupper($v['name']));
                                });
                            }
                            if ($villApi) {
                                $this->selectedVillageCode = $villApi['code'];
                            }
                        }
                    }
                }
            }
        }
    }

    public function nextStep(): void
    {
        $this->nik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        $this->full_name = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->full_name);
        $this->place_of_birth = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->place_of_birth);
        if ($this->rt) $this->rt = preg_replace('/[^0-9]/', '', (string) $this->rt);
        if ($this->rw) $this->rw = preg_replace('/[^0-9]/', '', (string) $this->rw);

        $validated = $this->validate([
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'place_of_birth' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(17)->format('Y-m-d')],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'address' => ['required', 'string', 'max:500'],
            'rt' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'rw' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'city_id' => ['required', 'exists:cities,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kelurahan' => ['required', 'string', 'max:100'],
        ], [
            'nik.size' => 'NIK harus tepat 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berupa angka.',
            'full_name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'place_of_birth.regex' => 'Tempat lahir hanya boleh berupa huruf dan spasi.',
            'city_id.required' => 'Kota / Kabupaten domisili wajib dipilih.',
            'kecamatan.required' => 'Kecamatan wajib dipilih.',
            'kelurahan.required' => 'Kelurahan / Desa wajib dipilih.',
            'rt.max' => 'RT maksimal 3 karakter.',
            'rw.max' => 'RW maksimal 3 karakter.',
        ]);

        // Simpan atau update record registration di database
        $uuid = Session::get('registration_uuid');

        if ($uuid) {
            $registration = Registration::where('uuid', $uuid)->first();
        } else {
            $registration = null;
        }

        $role = Session::get('registration_role', 'customer');

        $cityId = $validated['city_id'] ?? null;
        $cityName = null;
        if ($cityId) {
            $cityRec = City::find($cityId);
            if ($cityRec) {
                $cityName = $cityRec->name;
            }
        }

        $dataToSave = $validated + ['status' => 'in_progress', 'role' => $role, 'city_id' => $cityId, 'city' => $cityName];

        if ($registration) {
            $registration->update($dataToSave);
        } else {
            $registration = Registration::create(array_merge($dataToSave, [
                'uuid' => Str::uuid()->toString(),
            ]));
            Session::put('registration_uuid', $registration->uuid);
        }

        // Clear client-side saved draft for step1 (if any)
        $this->dispatch('clear-registration-step1');

        $this->redirect(route('register.step2'), navigate: true);
    }
}; ?>
<div class="w-full">
    <!-- Header Step 1 -->
    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Data Diri</h2>

        <!-- Step Progress Bar -->
        <div class="flex items-center gap-1.5 mb-1.5">
            <div class="flex-1 h-1.5 bg-blue-600 rounded-full"></div>
            <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
            <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
            <div class="flex-1 h-1.5 bg-gray-200 rounded-full"></div>
        </div>

        <!-- Sub Row: Left hint & Right Step indicator -->
        <div class="flex items-center justify-between text-xs text-gray-500">
            <span>Isi data sesuai KTP asli Anda</span>
            <span class="font-bold text-blue-600">Langkah 1 dari 4</span>
        </div>
    </div>

    <form wire:submit="nextStep" class="space-y-4">
        <!-- NIK -->
        <div>
            <label for="nik" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span></label>
            <input wire:model.live="nik" id="nik" type="text" maxlength="16" placeholder="Masukkan 16 digit NIK"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
            <div class="flex justify-between items-center mt-1">
                <span class="text-[11px] text-gray-400">Harus 16 angka</span>
                <span class="text-[11px] font-semibold {{ strlen($nik) === 16 ? 'text-green-600' : 'text-gray-500' }}">{{ strlen($nik) }}/16 digit</span>
            </div>
            <x-input-error :messages="$errors->get('nik')" class="mt-1" />
        </div>

        <!-- Nama Lengkap -->
        <div>
            <label for="full_name" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Nama Lengkap <span class="text-red-500">*</span></label>
            <input wire:model="full_name" id="full_name" type="text" placeholder="Nama lengkap sesuai KTP"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
            <x-input-error :messages="$errors->get('full_name')" class="mt-1" />
        </div>

        <!-- Tempat & Tanggal Lahir -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="place_of_birth" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Tempat Lahir <span class="text-red-500">*</span></label>
                <input wire:model="place_of_birth" id="place_of_birth" type="text" placeholder="Kota lahir (hanya huruf)"
                    oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                    class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('place_of_birth')" class="mt-1" />
            </div>
            <div>
                <label for="date_of_birth" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input wire:model="date_of_birth" id="date_of_birth" type="date" max="{{ now()->subYears(17)->format('Y-m-d') }}" onkeydown="return false" onclick="this.showPicker()"
                    class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
            </div>
        </div>

        <!-- Jenis Kelamin -->
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Jenis Kelamin <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-2 gap-3">
                <label
                    class="flex items-center px-4 py-3 rounded-xl cursor-pointer border transition {{ $gender === 'Laki-laki' ? 'border-blue-500 bg-blue-50/50 text-blue-900 ring-1 ring-blue-500 font-semibold' : 'border-gray-200 bg-gray-50/70 text-gray-700' }}">
                    <input wire:model.live="gender" type="radio" value="Laki-laki" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm">Laki-laki</span>
                </label>
                <label
                    class="flex items-center px-4 py-3 rounded-xl cursor-pointer border transition {{ $gender === 'Perempuan' ? 'border-blue-500 bg-blue-50/50 text-blue-900 ring-1 ring-blue-500 font-semibold' : 'border-gray-200 bg-gray-50/70 text-gray-700' }}">
                    <input wire:model.live="gender" type="radio" value="Perempuan" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm">Perempuan</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('gender')" class="mt-1" />
        </div>

        <!-- Alamat -->
        <div>
            <label for="address" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Alamat Lengkap <span class="text-red-500">*</span></label>
            <textarea wire:model="address" id="address" rows="3" placeholder="Jalan, nomor rumah, gedung, dll."
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium"></textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
        </div>

        <!-- RT/RW (Maks 3 digit - Opsional) -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="rt" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">RT <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input wire:model="rt" id="rt" type="text" inputmode="numeric" maxlength="3"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" placeholder="001"
                    class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('rt')" class="mt-1" />
            </div>
            <div>
                <label for="rw" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">RW <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input wire:model="rw" id="rw" type="text" inputmode="numeric" maxlength="3"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" placeholder="002"
                    class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('rw')" class="mt-1" />
            </div>
        </div>

        <!-- Provinsi (Otomatis) -->
        <div>
            <label for="province" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Provinsi</label>
            <input wire:model="province" id="province" type="text" readonly placeholder="Otomatis terisi dari kota yang dipilih"
                class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-600 text-sm cursor-not-allowed focus:outline-none font-medium">
            <x-input-error :messages="$errors->get('province')" class="mt-1" />
        </div>

        <!-- Kota/Kabupaten (Dropdown Kota Aktif di DB) -->
        <div>
            <label for="city_id" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kota / Kabupaten Domisili <span class="text-red-500">*</span></label>
            <select wire:model.live="city_id" id="city_id"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <option value="">-- Pilih Kota / Kabupaten --</option>
                @foreach ($cities as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->province ?? 'Indonesia' }})</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('city_id')" class="mt-1" />
        </div>

        <!-- Kecamatan (Dropdown API) -->
        <div>
            <label for="kecamatan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kecamatan <span class="text-red-500">*</span></label>
            <select wire:model.live="selectedDistrictCode" id="kecamatan"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium"
                {{ empty($apiDistricts) ? 'disabled' : '' }}>
                <option value="">{{ empty($city_id) ? '-- Pilih Kota Terlebih Dahulu --' : (empty($apiDistricts) ? '-- Memuat Kecamatan... --' : '-- Pilih Kecamatan --') }}</option>
                @foreach ($apiDistricts as $d)
                    <option value="{{ $d['code'] }}">{{ $d['name'] }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kecamatan')" class="mt-1" />
        </div>

        <!-- Kelurahan/Desa (Dropdown API) -->
        <div>
            <label for="kelurahan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kelurahan / Desa <span class="text-red-500">*</span></label>
            <select wire:model.live="selectedVillageCode" id="kelurahan"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium"
                {{ empty($apiVillages) ? 'disabled' : '' }}>
                <option value="">{{ empty($selectedDistrictCode) ? '-- Pilih Kecamatan Terlebih Dahulu --' : (empty($apiVillages) ? '-- Memuat Kelurahan... --' : '-- Pilih Kelurahan / Desa --') }}</option>
                @foreach ($apiVillages as $v)
                    <option value="{{ $v['code'] }}">{{ $v['name'] }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('kelurahan')" class="mt-1" />
        </div>

        <!-- Next Button -->
        <div class="pt-5 pb-3">
            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-primary-500 hover:bg-primary-600 active:scale-98 text-white font-bold py-3.5 rounded-full shadow-md hover:shadow-lg transition text-base tracking-wide disabled:opacity-50"
                style="background-color: #0098e7;">
                <span wire:loading.remove>Lanjutkan</span>
                <span wire:loading>Memproses...</span>
            </button>
        </div>
    </form>

    <script>
        (function () {
            const prefix = 'registration_step1_';
            const fields = ['nik', 'full_name', 'place_of_birth', 'date_of_birth', 'gender', 'address', 'rt', 'rw', 'kelurahan', 'kecamatan', 'city_id', 'city', 'province'];

            window.addEventListener('DOMContentLoaded', () => {
                try {
                    fields.forEach(name => {
                        const key = prefix + name;
                        const el = document.getElementById(name);
                        if (!el) return;
                        const val = localStorage.getItem(key);
                        if (val !== null) {
                            el.value = val;
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                } catch (e) {}
            });

            fields.forEach(name => {
                const el = document.getElementById(name);
                if (!el) return;
                el.addEventListener('input', (ev) => {
                    try { localStorage.setItem(prefix + name, ev.target.value); } catch (e) {}
                });
            });

            document.addEventListener('clear-registration-step1', () => {
                try { fields.forEach(name => localStorage.removeItem(prefix + name)); } catch (e) {}
            });
        })();
    </script>
</div>