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
    // realtime city search (for nicer UX)
    public string $cityQuery = '';
    public array $searchResults = [];

    // Auto-detect gender from NIK
    public function updatedNik($value)
    {
        if (strlen($value) >= 8) {
            $tglLahir = (int) substr($value, 6, 2);
            // Jika tanggal > 40, berarti perempuan
            $this->gender = $tglLahir > 40 ? 'Perempuan' : 'Laki-laki';
        }
    }

    // Preload saved registration values if a registration UUID exists in session
    public function mount(): void
    {
        // Always load available cities so the dropdown can be rendered from DB
        $this->cities = City::orderBy('name')->get();

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
        $this->city_id = $registration->city_id ?? null;
        $this->province = $registration->province ?? $this->province;
        // load available cities so registrants pick canonical city names
        $this->cities = City::orderBy('name')->get();
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
            'kelurahan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'kecamatan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
        ], [
            'nik.size' => 'NIK harus tepat 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berupa angka.',
            'full_name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'place_of_birth.regex' => 'Tempat lahir hanya boleh berupa huruf dan spasi.',
            'kelurahan.regex' => 'Kelurahan / Desa hanya boleh berisi huruf.',
            'kecamatan.regex' => 'Kecamatan hanya boleh berisi huruf.',
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

        // Use selected city_id if provided; also store city name for readability
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

    public function updatedCityQuery($value)
    {
        $q = trim($value);
        if ($q === '') {
            $this->searchResults = [];
            return;
        }

        $limit = 10;

        $results = City::where('is_active', true)
            ->where(function ($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                  ->orWhere('province', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%");
            })
            ->whereRaw("COALESCE(code,'') NOT LIKE 'reqd-%' AND COALESCE(code,'') NOT LIKE 'regd-%'")
            ->select('id','name','province','code')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->toArray();

            if (count($results) < $limit) {
            $remaining = $limit - count($results);
            $regRows = collect();

            if (\Illuminate\Support\Facades\Schema::hasTable('req_regencies') && \Illuminate\Support\Facades\Schema::hasTable('req_provinces')) {
                $regRows = \Illuminate\Support\Facades\DB::table('req_regencies')
                    ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
                    ->where('req_regencies.regency', 'like', "%{$q}%")
                    ->select('req_regencies.id as regency_id', 'req_regencies.regency', 'req_provinces.province')
                    ->orderBy('req_regencies.regency')
                    ->limit($remaining)
                    ->get();
            }

            if (count($regRows) < $remaining && \Illuminate\Support\Facades\Schema::hasTable('reg_regencies') && \Illuminate\Support\Facades\Schema::hasTable('reg_provinces')) {
                $rem2 = $remaining - count($regRows);
                $rows = \Illuminate\Support\Facades\DB::table('reg_regencies')
                    ->join('reg_provinces','reg_regencies.province_id','=','reg_provinces.id')
                    ->where('reg_regencies.name','like',"%{$q}%")
                    ->select('reg_regencies.id as regency_id', 'reg_regencies.name as regency', 'reg_provinces.name as province')
                    ->orderBy('reg_regencies.name')
                    ->limit($rem2)
                    ->get();
                foreach ($rows as $r) $regRows->push($r);
            }

            // Also try kecamatan-level tables and map results to parent regency
            if (count($regRows) < $remaining && \Illuminate\Support\Facades\Schema::hasTable('req_districts') && \Illuminate\Support\Facades\Schema::hasTable('req_regencies') && \Illuminate\Support\Facades\Schema::hasTable('req_provinces')) {
                $remD = $remaining - count($regRows);
                $dist = \Illuminate\Support\Facades\DB::table('req_districts')
                    ->join('req_regencies', 'req_districts.regency_id', '=', 'req_regencies.id')
                    ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
                    ->where(function($b) use ($q) {
                        $b->where('req_districts.district', 'like', "%{$q}%")
                          ->orWhere('req_regencies.regency', 'like', "%{$q}%")
                          ->orWhere('req_provinces.province', 'like', "%{$q}%");
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw("CONCAT('reqr-', req_regencies.id) as regency_id"), 'req_regencies.regency', \Illuminate\Support\Facades\DB::raw("req_districts.district as matched_district"), 'req_provinces.province')
                    ->orderBy('req_districts.district')
                    ->limit($remD)
                    ->get();
                foreach ($dist as $d) $regRows->push($d);
            }

            // legacy tables
            if (count($regRows) < $remaining && \Illuminate\Support\Facades\Schema::hasTable('regencies') && \Illuminate\Support\Facades\Schema::hasTable('provinces')) {
                $rem3 = $remaining - count($regRows);
                $rows = \Illuminate\Support\Facades\DB::table('regencies')
                    ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                    ->where('regencies.regency','like',"%{$q}%")
                    ->select('regencies.id as regency_id','regencies.regency','provinces.province')
                    ->orderBy('regencies.regency')
                    ->limit($rem3)
                    ->get();
                foreach ($rows as $r) $regRows->push($r);
            }

            foreach ($regRows as $r) {
                $city = City::firstOrCreate(
                    ['code' => (string)($r->regency_id)],
                    ['name' => $r->regency, 'province' => $r->province, 'is_active' => true]
                );
                $exists = false;
                foreach ($results as $res) {
                    if ($res['id'] == $city->id) { $exists = true; break; }
                }
                if (! $exists) {
                    $results[] = ['id' => $city->id, 'name' => $city->name, 'province' => $city->province, 'code' => $city->code];
                }
            }
        }

        $this->searchResults = $results;
    }

    public function setCityId($id)
    {
        $this->city_id = $id;
        $city = City::find($id);
        if ($city) {
            $this->city = $city->name;
            $this->province = $city->province;
            // show chosen city in the search input so user sees selection
            $this->cityQuery = $city->name . ' — ' . $city->province;
        }
        $this->searchResults = [];
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

        <!-- Kelurahan/Desa -->
        <div>
            <label for="kelurahan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kelurahan / Desa <span class="text-red-500">*</span></label>
            <input wire:model="kelurahan" id="kelurahan" type="text" placeholder="Nama Kelurahan / Desa"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s\.\,\'\-]/g, '')"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
            <x-input-error :messages="$errors->get('kelurahan')" class="mt-1" />
        </div>

        <!-- Kecamatan -->
        <div>
            <label for="kecamatan" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kecamatan <span class="text-red-500">*</span></label>
            <input wire:model="kecamatan" id="kecamatan" type="text" placeholder="Nama Kecamatan"
                oninput="this.value = this.value.replace(/[^a-zA-Z\s\.\,\'\-]/g, '')"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
            <x-input-error :messages="$errors->get('kecamatan')" class="mt-1" />
        </div>

        <!-- Kota/Kabupaten (realtime search) -->
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Kota / Kabupaten <span class="text-red-500">*</span></label>
            @if(isset($cities) && count($cities) > 0)
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="cityQuery" id="city-search-input"
                        placeholder="Ketik & pilih nama Kota/Kabupaten..."
                        class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium" autocomplete="off">

                    <input type="hidden" wire:model="city_id" id="city_id">

                    @if (!empty($searchResults))
                        <ul class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto z-50 divide-y divide-gray-100">
                            @foreach ($searchResults as $c)
                                <li wire:click="setCityId({{ $c['id'] }})"
                                    class="px-4 py-3 text-sm hover:bg-blue-50/70 cursor-pointer transition flex items-start gap-2">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $c['name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $c['province'] }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @elseif (!empty($cityQuery) && strlen($cityQuery) >= 2 && empty($city_id))
                        <div class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-xl shadow-lg p-3.5 z-50 text-center">
                            <p class="text-xs text-gray-500">Kota tidak ditemukan</p>
                        </div>
                    @endif
                </div>
                <x-input-error :messages="$errors->get('city_id')" class="mt-1" />
            @else
                <input wire:model="city" id="city" type="text" placeholder="Ketik nama Kota/Kabupaten"
                    class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
                <x-input-error :messages="$errors->get('city')" class="mt-1" />
            @endif
        </div>

        <!-- Provinsi -->
        <div>
            <label for="province" class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Provinsi <span class="text-red-500">*</span></label>
            <input wire:model="province" id="province" type="text" placeholder="Nama Provinsi"
                class="w-full px-4 py-3 bg-gray-50/70 border border-gray-200 rounded-xl text-gray-900 text-sm placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-medium">
            <x-input-error :messages="$errors->get('province')" class="mt-1" />
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