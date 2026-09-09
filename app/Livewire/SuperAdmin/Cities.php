<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\City;
use App\Models\User;
use App\Models\Province;
use Illuminate\Support\Facades\Schema;

#[Layout('layouts.superadmin')]
class Cities extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $cityId;

    // helper for delete modal display
    public $deletingCityName = null;

    // form fields
    public $name = '';
    public $province = '';
    public $province_id = null;
    public $admin_id = null;
    public $latitude = null;
    public $longitude = null;
    public $is_active = true;
    public $deleteId = null;
    // detail modal + chart data
    public $showDetailModal = false;
    public $detailCityName = null;
    public $chartLabels = [];
    public $chartCustomerData = [];
    public $chartMitraData = [];

    // provinces management
    public $provinces = [];
    public $showProvinceModal = false;
    public $provinceName = '';
    public $provinceEditId = null;
    public $showProvinceDeleteModal = false;
    public $deleteProvinceId = null;
    public $deletingProvinceName = null;
    public $filterProvinceId = null;

    public $apiProvinces = [];
    public $apiCities = [];
    public $selectedProvinceCode = '';
    public $selectedCityCode = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
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

    public function updatedSelectedProvinceCode($code)
    {
        $prov = collect($this->apiProvinces)->firstWhere('code', $code);
        $this->province = $prov ? $prov['name'] : '';
        $this->name = ''; // reset city name
        $this->selectedCityCode = '';
        $this->latitude = null;
        $this->longitude = null;
        
        if ($code) {
            $this->fetchCities($code);
        } else {
            $this->apiCities = [];
        }
    }

    public function updatedSelectedCityCode($code)
    {
        $city = collect($this->apiCities)->firstWhere('code', $code);
        if ($city) {
            $this->name = $city['name'];
            $this->fetchCoordinatesForCity($this->name, $this->province);
        } else {
            $this->name = '';
            $this->latitude = null;
            $this->longitude = null;
        }
    }

    public function fetchCoordinatesForCity($cityName, $provinceName = '')
    {
        $fallbackCoords = [
            'JAKARTA' => [-6.2088, 106.8456],
            'DKI JAKARTA' => [-6.2088, 106.8456],
            'KOTA ADM. JAKARTA PUSAT' => [-6.1805, 106.8284],
            'KOTA ADM. JAKARTA SELATAN' => [-6.2615, 106.8106],
            'KOTA ADM. JAKARTA BARAT' => [-6.1683, 106.7588],
            'KOTA ADM. JAKARTA TIMUR' => [-6.2250, 106.9004],
            'KOTA ADM. JAKARTA UTARA' => [-6.1214, 106.7741],
            'KOTA SURABAYA' => [-7.2575, 112.7521],
            'SURABAYA' => [-7.2575, 112.7521],
            'KOTA BANDUNG' => [-6.9175, 107.6191],
            'BANDUNG' => [-6.9175, 107.6191],
            'KOTA MEDAN' => [3.5952, 98.6722],
            'MEDAN' => [3.5952, 98.6722],
            'KOTA SEMARANG' => [-6.9667, 110.4167],
            'SEMARANG' => [-6.9667, 110.4167],
            'KOTA SURAKARTA' => [-7.5755, 110.8243],
            'SURAKARTA' => [-7.5755, 110.8243],
            'SOLO' => [-7.5755, 110.8243],
            'KOTA YOGYAKARTA' => [-7.7956, 110.3695],
            'YOGYAKARTA' => [-7.7956, 110.3695],
            'DI YOGYAKARTA' => [-7.7956, 110.3695],
            'KOTA MAKASSAR' => [-5.1477, 119.4327],
            'MAKASSAR' => [-5.1477, 119.4327],
            'KOTA PALEMBANG' => [-2.9761, 104.7754],
            'PALEMBANG' => [-2.9761, 104.7754],
            'KOTA DENPASAR' => [-8.6705, 115.2126],
            'DENPASAR' => [-8.6705, 115.2126],
            'BALI' => [-8.6705, 115.2126],
            'KOTA MALANG' => [-7.9666, 112.6326],
            'MALANG' => [-7.9666, 112.6326],
            'KABUPATEN PONOROGO' => [-7.8664, 111.4620],
            'PONOROGO' => [-7.8664, 111.4620],
            'KOTA BOGOR' => [-6.5971, 106.8060],
            'BOGOR' => [-6.5971, 106.8060],
            'KOTA BEKASI' => [-6.2383, 106.9756],
            'BEKASI' => [-6.2383, 106.9756],
            'KOTA TANGERANG' => [-6.1783, 106.6319],
            'TANGERANG' => [-6.1783, 106.6319],
            'KOTA TANGERANG SELATAN' => [-6.2889, 106.7179],
            'KOTA DEPOK' => [-6.4025, 106.7942],
            'DEPOK' => [-6.4025, 106.7942],
        ];

        $upperName = strtoupper(trim($cityName));
        $cleanName = trim(str_replace(['KOTA ADM. ', 'KOTA ', 'KABUPATEN ', 'KAB. '], '', $upperName));

        if (isset($fallbackCoords[$upperName])) {
            $this->latitude = $fallbackCoords[$upperName][0];
            $this->longitude = $fallbackCoords[$upperName][1];
            return;
        }

        if (isset($fallbackCoords[$cleanName])) {
            $this->latitude = $fallbackCoords[$cleanName][0];
            $this->longitude = $fallbackCoords[$cleanName][1];
            return;
        }

        // Fetch coordinates from Nominatim OpenStreetMap
        try {
            $cleanSearch = str_replace(['KOTA ADM. ', 'KOTA ', 'KABUPATEN ', 'KAB. '], '', $cityName);
            $query = urlencode($cleanSearch . ', ' . $provinceName . ', Indonesia');
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['User-Agent' => 'SayaBantu-App/1.0'])
                ->timeout(4)
                ->get("https://nominatim.openstreetmap.org/search?q={$query}&format=json&limit=1");

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                $this->latitude = round((float)$data['lat'], 7);
                $this->longitude = round((float)$data['lon'], 7);
                return;
            }
        } catch (\Exception $e) {
            // Fallback gracefully
        }

        $this->latitude = -6.2088;
        $this->longitude = 106.8456;
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'province', 'province_id', 'admin_id', 'latitude', 'longitude', 'is_active', 'cityId', 'editMode', 'selectedProvinceCode', 'selectedCityCode', 'apiCities']);
        $this->is_active = true;
        $this->fetchProvinces();
        $this->showModal = true;
    }

    public function editCity($id)
    {
        $city = City::findOrFail($id);
        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->province = $city->province;
        $this->province_id = $city->province_id;
        $this->admin_id = $city->admin_id;
        $this->latitude = $city->latitude;
        $this->longitude = $city->longitude;
        $this->is_active = $city->is_active;
        $this->editMode = true;
        
        // Fetch provinces to sync dropdowns
        $this->fetchProvinces();
        
        // Find matching province from API
        $prov = collect($this->apiProvinces)->firstWhere('name', $city->province);
        if ($prov) {
            $this->selectedProvinceCode = $prov['code'];
            $this->fetchCities($this->selectedProvinceCode);
            
            // Find matching city from API
            $cityApi = collect($this->apiCities)->firstWhere('name', $city->name);
            if ($cityApi) {
                $this->selectedCityCode = $cityApi['code'];
            }
        }

        // If coordinates were not set in database, attempt auto-lookup
        if (empty($this->latitude) || empty($this->longitude)) {
            $this->fetchCoordinatesForCity($this->name, $this->province);
        }

        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $city = City::find($id);
        $this->deletingCityName = $city ? $city->name : null;
        $this->showDeleteModal = true;
    }

    public function toggleStatus($id)
    {
        $city = City::findOrFail($id);
        $city->update(['is_active' => !$city->is_active]);
        session()->flash('message', 'Status kota berhasil diubah');
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'admin_id' => 'required|exists:users,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ];

        // if province_id chosen, validate it; otherwise require free-text province
        if ($this->province_id) {
            $rules['province_id'] = 'exists:provinces,id';
        } else {
            $rules['province'] = 'required|string|max:255';
        }

        $validated = $this->validate($rules);

        // Ensure selected user is actually an admin
        $admin = User::find($validated['admin_id']);
        if (!$admin || $admin->role !== 'admin') {
            $this->addError('admin_id', 'Pilih user dengan role admin sebagai pengelola kota');
            return;
        }

        if ($this->editMode && $this->cityId) {
            $city = City::findOrFail($this->cityId);
            $oldAdminId = $city->admin_id;
            // ensure province name is stored for backward compatibility
            if ($this->province_id) {
                $prov = Province::find($this->province_id);
                if ($prov) $validated['province'] = $prov->name;
                $validated['province_id'] = $this->province_id;
            }

            $city->update($validated);

            // If admin changed, clear old admin's city_id
            if ($oldAdminId && $oldAdminId !== $city->admin_id) {
                $oldAdmin = User::find($oldAdminId);
                if ($oldAdmin) {
                    $oldAdmin->city_id = null;
                    $oldAdmin->save();
                }
            }

            // assign new admin's city_id
            $admin->city_id = $city->id;
            $admin->save();

            session()->flash('message', 'Kota berhasil diperbarui');
        } else {
            if ($this->province_id) {
                $prov = Province::find($this->province_id);
                if ($prov) $validated['province'] = $prov->name;
                $validated['province_id'] = $this->province_id;
            }

            $city = City::create($validated);

            // assign admin to this new city
            $admin->city_id = $city->id;
            $admin->save();

            session()->flash('message', 'Kota berhasil dibuat dan admin ditetapkan');
        }

        $this->showModal = false;
        $this->reset(['name', 'province', 'province_id', 'admin_id', 'latitude', 'longitude', 'is_active', 'cityId', 'editMode']);
    }

    /** Provinces management */
    public function openProvinceModal($id = null)
    {
        if ($id) {
            $prov = Province::findOrFail($id);
            $this->provinceEditId = $prov->id;
            $this->provinceName = $prov->name;
        } else {
            $this->provinceEditId = null;
            $this->provinceName = '';
        }
        $this->showProvinceModal = true;
    }

    public function saveProvince()
    {
        $this->validate(['provinceName' => 'required|string|max:255']);

        if ($this->provinceEditId) {
            $prov = Province::findOrFail($this->provinceEditId);
            $prov->update(['name' => $this->provinceName]);
            session()->flash('message', 'Provinsi diperbarui');
        } else {
            Province::create(['name' => $this->provinceName]);
            session()->flash('message', 'Provinsi dibuat');
        }

        $this->showProvinceModal = false;
        $this->provinceEditId = null;
        $this->provinceName = '';
    }

    public function confirmDeleteProvince($id)
    {
        $this->deleteProvinceId = $id;
        $prov = Province::find($id);
        $this->deletingProvinceName = $prov ? $prov->name : null;
        $this->showProvinceDeleteModal = true;
    }

    public function deleteProvince()
    {
        if ($this->deleteProvinceId) {
            $prov = Province::findOrFail($this->deleteProvinceId);
            // detach province from cities (keep province name for compatibility)
            City::where('province_id', $prov->id)->update(['province_id' => null]);
            $prov->delete();
            session()->flash('message', 'Provinsi dihapus');
        }

        $this->showProvinceDeleteModal = false;
        $this->deleteProvinceId = null;
        $this->deletingProvinceName = null;
    }

    public function toggleProvinceStatus($id)
    {
        $prov = Province::findOrFail($id);
        $prov->update(['is_active' => !$prov->is_active]);
        session()->flash('message', 'Status provinsi berhasil diubah');
    }

    public function selectProvince($id = null)
    {
        if ($this->filterProvinceId === $id) {
            $this->filterProvinceId = null;
        } else {
            $this->filterProvinceId = $id;
        }
        $this->resetPage();
    }

    public function deleteCity()
    {
        if ($this->deleteId) {
            $city = City::findOrFail($this->deleteId);
            // unset admin's city relationship if set
            if ($city->admin_id) {
                $admin = User::find($city->admin_id);
                if ($admin) {
                    $admin->city_id = null;
                    $admin->save();
                }
            }
            $city->delete();
            session()->flash('message', 'Kota berhasil dihapus');
        }

        // reset delete helpers
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deletingCityName = null;
    }

    /**
     * Open detail modal for a city and prepare last-30-days chart data
     */
    public function openDetailModal($cityId)
    {
        $city = City::find($cityId);
        if (!$city) {
            session()->flash('error', 'Kota tidak ditemukan');
            return;
        }

        $this->detailCityName = $city->name;
        $this->chartLabels = [];
        $this->chartCustomerData = [];
        $this->chartMitraData = [];

        $days = 30;
        $today = now()->startOfDay();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $this->chartLabels[] = $date->format('d M');

            $customerCount = User::whereIn('role', ['customer', 'kustomer'])
                ->where('status', 'active')
                ->where('city_id', $cityId)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $mitraCount = User::where('role', 'mitra')
                ->where('status', 'active')
                ->where('city_id', $cityId)
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $this->chartCustomerData[] = $customerCount;
            $this->chartMitraData[] = $mitraCount;
        }

        $this->showDetailModal = true;
        // Do not call emit/dispatch here to avoid compatibility issues with Livewire versions.
        // The client will read the prepared arrays from the rendered DOM after Livewire updates.
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailCityName = null;
        $this->chartLabels = [];
        $this->chartCustomerData = [];
        $this->chartMitraData = [];
    }

    public function render()
    {
        $loadDistricts = Schema::hasTable('districts');

        $citiesQuery = City::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('province', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterProvinceId, function ($q) {
                $q->where('province_id', $this->filterProvinceId);
            })
            ->withCount(['users' => function ($q) {
                $q->whereIn('role', ['customer', 'kustomer', 'mitra']);
            }])
            ->latest();

        if ($loadDistricts) {
            $citiesQuery->with(['districts' => function ($q) { $q->orderBy('name'); }]);
        }

        $cities = $citiesQuery->paginate($this->perPage);

        // Show all admins in dropdown (superadmin can reassign any admin)
        $admins = User::where('role', 'admin')->get();

        // Load provinces for sidebar/dropdowns (guard table existence)
        if (Schema::hasTable('provinces')) {
            $provinces = Province::orderBy('name')->get();
        } else {
            $provinces = collect();
        }

        $this->provinces = $provinces;

        return view('superadmin.cities', compact('cities', 'admins', 'provinces', 'loadDistricts'));
    }
}
