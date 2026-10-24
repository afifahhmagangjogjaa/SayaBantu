<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

#[Layout('layouts.superadmin')]
class AdminUsers extends Component
{
    use WithPagination;

    public $search = '';
    public $title = 'Manajemen Admin';
    public $breadcrumb = 'Manajemen Admin';
    public $roleFilter = 'admin';
    public $perPage = 10;
    public $selectedUser = null;
    public $userId = null;

    // form fields
    public $name;
    public $email;
    public $phone;
    public $role = 'admin';
    public $status = 'active';
    public $verified = false;
    public $city_id = null;
    public $managed_city_ids = []; 
    public $address = null;
    public $nik = null;
    public $place_of_birth = null;
    public $date_of_birth = null;
    public $gender = null;
    public $rt = null;
    public $rw = null;
    public $kelurahan = null;
    public $kecamatan = null;
    public $province = null;
    public $religion = null;
    public $marital_status = null;
    public $occupation = null;
    public $password = null;

    // modal flags
    public $showViewModal = false;
    public $showEditModal = false;
    public $showCreateModal = false;
    public $showConfirmDelete = false;
    public $confirmingDeleteId = null;
    public $showConfirmDeactivate = false;
    public $confirmingDeactivateId = null;
    public $confirmingDeactivateName = '';

    // Wilayah.id API dropdowns for Kecamatan & Kelurahan
    public $apiProvinces = [];
    public $apiCities = [];
    public $apiDistricts = [];
    public $apiVillages = [];
    public $selectedCityCode = '';
    public $selectedDistrictCode = '';
    public $selectedVillageCode = '';

    public function fetchProvinces()
    {
        try {
            $response = \App\Support\ExternalHttp::client()->get('https://wilayah.id/api/provinces.json');
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
            $response = \App\Support\ExternalHttp::client()->get("https://wilayah.id/api/regencies/{$provinceCode}.json");
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
            $master = \App\Support\JsonMaster::districts();
            $found = \App\Support\JsonMaster::find($master, $cityCode);
            if ($found !== []) {
                $this->apiDistricts = $found;
                return;
            }

            $cleanCode = (strlen($cityCode) === 4 && !str_contains($cityCode, '.')) ? substr($cityCode, 0, 2) . '.' . substr($cityCode, 2, 2) : $cityCode;
            $response = \App\Support\ExternalHttp::client()->get("https://wilayah.id/api/districts/{$cleanCode}.json");
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
            $response = \App\Support\ExternalHttp::client()->get("https://wilayah.id/api/villages/{$districtCode}.json");
            if ($response->successful()) {
                $this->apiVillages = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->apiVillages = [];
        }
    }

    public function updatedCityId($id)
    {
        if (!$id) {
            $this->province = '';
            return;
        }

        $city = City::find($id);
        if ($city) {
            $this->province = $city->province;
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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function viewUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'User not found');
            return;
        }
        $this->selectedUser = $user;
        $this->showViewModal = true;
    }

    public function editUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'User not found');
            return;
        }
        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role = $user->role;
        $this->status = $user->status ?? 'active';
        $this->verified = $user->verified ? '1' : '0';
        $this->city_id = $user->city_id;
        $this->managed_city_ids = $user->managedCities->pluck('id')->toArray();
        $this->address = $user->address;
        $this->nik = $user->nik;
        $this->place_of_birth = $user->place_of_birth;
        $this->date_of_birth = optional($user->date_of_birth)?->format('Y-m-d');
        $this->gender = $user->gender;
        $this->rt = $user->rt;
        $this->rw = $user->rw;
        $this->kelurahan = $user->kelurahan;
        $this->kecamatan = $user->kecamatan;
        $this->province = $user->province;
        $this->religion = $user->religion;
        $this->marital_status = $user->marital_status;
        $this->occupation = $user->occupation;

        $this->syncCascadeData();

        $this->showEditModal = true;
    }

    protected function syncCascadeData()
    {
        if ($this->city_id) {
            $city = City::find($this->city_id);
            if ($city) {
                $this->province = $city->province;
            }
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->showConfirmDelete = true;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function resetForm()
    {
        $this->selectedUser = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'admin';
        $this->status = 'active';
        $this->verified = 1;
        $this->city_id = null;
        $this->managed_city_ids = [];
        $this->address = null;
        $this->nik = null;
        $this->place_of_birth = null;
        $this->date_of_birth = null;
        $this->gender = null;
        $this->rt = null;
        $this->rw = null;
        $this->kelurahan = null;
        $this->kecamatan = null;
        $this->province = null;
        $this->religion = null;
        $this->marital_status = null;
        $this->occupation = null;
        $this->password = null;

        $this->selectedCityCode = '';
        $this->selectedDistrictCode = '';
        $this->selectedVillageCode = '';
        $this->apiCities = [];
        $this->apiDistricts = [];
        $this->apiVillages = [];
    }

    /**
     * Confirm before deactivating admin or directly activate if inactive
     */
    public function confirmDeactivate($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'Admin tidak ditemukan');
            return;
        }

        if ($user->status === 'active') {
            $this->confirmingDeactivateId = $user->id;
            $this->confirmingDeactivateName = $user->name;
            $this->showConfirmDeactivate = true;
        } else {
            // Langsung aktifkan kembali
            $user->update(['status' => 'active']);
            session()->flash('message', "Status admin {$user->name} berhasil diaktifkan kembali.");
        }
    }

    public function executeDeactivateAdmin()
    {
        if ($this->confirmingDeactivateId) {
            $user = User::find($this->confirmingDeactivateId);
            if ($user) {
                $user->update(['status' => 'inactive']);
                $user->purgeSessions();
                session()->flash('message', "Status admin {$user->name} berhasil dinonaktifkan.");
            }
        }
        $this->closeDeactivateModal();
    }

    public function closeDeactivateModal()
    {
        $this->showConfirmDeactivate = false;
        $this->confirmingDeactivateId = null;
        $this->confirmingDeactivateName = '';
    }

    public function toggleStatus($id)
    {
        $this->confirmDeactivate($id);
    }

    public function saveUser()
    {
        // build validation rules and handle unique email on update
        $emailRules = ['required', 'email', 'max:255'];
        if ($this->selectedUser) {
            $emailRules[] = Rule::unique('users', 'email')->ignore($this->selectedUser->id);
        } else {
            $emailRules[] = 'unique:users,email';
        }

        // Sanitasi input
        $this->name = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->name);
        $this->phone = preg_replace('/[^0-9]/', '', (string) $this->phone);

        $isEdit = !empty($this->selectedUser);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => $emailRules,
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'role' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'managed_city_ids' => 'required|array|min:1',
            'managed_city_ids.*' => 'exists:cities,id',
            'address' => 'nullable|string|max:1000',
        ];

        if ($isEdit) {
            $rules['password'] = 'nullable|string|min:8';
        } else {
            $rules['password'] = 'required|string|min:8';
        }

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'email.required' => 'Email wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.min' => 'Nomor HP minimal 10 digit angka.',
            'phone.max' => 'Nomor HP maksimal 13 digit angka.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'city_id.required' => 'Kota basis operasional wajib dipilih.',
            'managed_city_ids.required' => 'Pilih minimal satu kota yang dikelola oleh admin.',
            'managed_city_ids.min' => 'Pilih minimal satu kota yang dikelola oleh admin.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ];

        $this->validate($rules, $messages);

        if (empty($this->province) && !empty($this->city_id)) {
            $this->province = City::find($this->city_id)?->province;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role ?: 'admin',
            'status' => $this->status ?? 'active',
            'verified' => true,
            'city_id' => $this->city_id,
            'province' => $this->province ?: null,
            'address' => $this->address ?: null,
            'kelurahan' => null,
            'kecamatan' => null,
            'nik' => $this->nik ?: null,
            'place_of_birth' => null,
            'date_of_birth' => null,
            'gender' => null,
            'rt' => null,
            'rw' => null,
            'religion' => null,
            'marital_status' => null,
            'occupation' => null,
        ];

        // Convert empty string values to null for optional database columns
        $data = array_map(function ($val) {
            return $val === '' ? null : $val;
        }, $data);

        if ($this->selectedUser) {
            $user = User::find($this->selectedUser->id);
            if (!$user) {
                session()->flash('error', 'User not found');
                return;
            }
            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            } else {
                unset($data['password']);
            }
            $user->update($data);

            if ($this->role === 'admin') {
                $user->managedCities()->sync($this->managed_city_ids ?? []);
            } else {
                $user->managedCities()->sync([]);
            }

            session()->flash('message', 'User updated successfully');
        } else {
            $data['password'] = bcrypt($this->password);
            $data['email_verified_at'] = now();
            $data['is_completed'] = true;
            $data['verified'] = true;
            $user = User::create($data);

            if ($this->role === 'admin') {
                $user->managedCities()->sync($this->managed_city_ids ?? []);
            }

            session()->flash('message', 'User created successfully');
        }

        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function deleteUser()
    {
        if (!$this->confirmingDeleteId) {
            return;
        }
        $user = User::find($this->confirmingDeleteId);
        if (!$user) {
            session()->flash('error', 'User not found');
            $this->showConfirmDelete = false;
            return;
        }
        $user->delete();
        session()->flash('message', 'User deleted');
        $this->showConfirmDelete = false;
        $this->confirmingDeleteId = null;
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showConfirmDelete = false;
        $this->confirmingDeleteId = null;
        $this->showConfirmDeactivate = false;
        $this->confirmingDeactivateId = null;
        $this->confirmingDeactivateName = '';
    }

    public function render()
    {
        $users = User::with(['city', 'managedCities'])
            ->where('role', 'admin')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $cities = City::orderBy('name')->get();

        // compute available cities for create modal: exclude cities already assigned to other admins
        $assignedToOthers = DB::table('admin_city')
            ->when($this->selectedUser, function ($q) {
                // when editing, exclude cities assigned to this user so they remain selectable
                $q->where('user_id', '!=', $this->selectedUser->id);
            })
            ->pluck('city_id')
            ->toArray();

        $availableCities = City::whereNotIn('id', $assignedToOthers)->orderBy('name')->get();

        return view('superadmin.admin-users', compact('users', 'cities', 'availableCities'));
    }
}
