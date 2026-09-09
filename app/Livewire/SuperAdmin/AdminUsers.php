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
            $this->province = '';
            $this->selectedCityCode = '';
            return;
        }

        $city = City::find($id);
        if ($city) {
            $this->province = $city->province;
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
        $this->selectedCityCode = '';
        $this->selectedDistrictCode = '';
        $this->selectedVillageCode = '';
        $this->apiDistricts = [];
        $this->apiVillages = [];

        if ($this->city_id) {
            $city = City::find($this->city_id);
            if ($city) {
                $this->province = $city->province;
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
        $this->verified = 0;
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
     * Toggle admin status (active <-> inactive) directly via button
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'Admin tidak ditemukan');
            return;
        }

        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Status admin {$user->name} berhasil {$statusLabel}.");
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
        $this->place_of_birth = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->place_of_birth);
        $this->nik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        $this->phone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if ($this->rt) $this->rt = preg_replace('/[^0-9]/', '', (string) $this->rt);
        if ($this->rw) $this->rw = preg_replace('/[^0-9]/', '', (string) $this->rw);

        $isEdit = !empty($this->selectedUser);

        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => $emailRules,
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'role' => 'required|string',
            'verified' => 'required|boolean',
            'city_id' => 'required|exists:cities,id',
            'managed_city_ids' => 'nullable|array',
            'managed_city_ids.*' => 'exists:cities,id',
            'nik' => [
                $isEdit ? 'nullable' : 'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                ($this->selectedUser ? $this->selectedUser->id : $this->userId) 
                    ? \Illuminate\Validation\Rule::unique('users', 'nik')->ignore($this->selectedUser ? $this->selectedUser->id : $this->userId) 
                    : \Illuminate\Validation\Rule::unique('users', 'nik'),
            ],
            'place_of_birth' => [$isEdit ? 'nullable' : 'required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth' => $isEdit ? 'nullable|date|before_or_equal:today' : 'required|date|before_or_equal:today',
            'gender' => $isEdit ? 'nullable|in:Laki-laki,Perempuan' : 'required|in:Laki-laki,Perempuan',
            'address' => 'nullable|string|max:1000',
            'kelurahan' => $isEdit ? 'nullable|string|max:100' : 'required|string|max:100',
            'kecamatan' => $isEdit ? 'nullable|string|max:100' : 'required|string|max:100',
            'province' => $isEdit ? 'nullable|string|max:100' : 'required|string|max:100',
            'rt' => 'nullable|string|max:3|regex:/^[0-9]+$/',
            'rw' => 'nullable|string|max:3|regex:/^[0-9]+$/',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:150',
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
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'nik.unique' => 'NIK sudah digunakan oleh pengguna lain.',
            'place_of_birth.required' => 'Tempat lahir wajib diisi.',
            'place_of_birth.regex' => 'Tempat lahir hanya boleh berisi huruf dan spasi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'city_id.required' => 'Kota domisili wajib dipilih.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'kelurahan.required' => 'Kelurahan / Desa wajib diisi.',
            'kelurahan.regex' => 'Kelurahan / Desa hanya boleh berisi huruf.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'kecamatan.regex' => 'Kecamatan hanya boleh berisi huruf.',
            'province.required' => 'Provinsi wajib diisi.',
            'verified.required' => 'Status verifikasi wajib dipilih.',
            'verified.boolean' => 'Format status verifikasi tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ];

        $this->validate($rules, $messages);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status ?? 'active',
            'verified' => filter_var($this->verified, FILTER_VALIDATE_BOOLEAN),
            'city_id' => $this->city_id,
            'address' => $this->address,
            'nik' => $this->nik,
            'place_of_birth' => $this->place_of_birth,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'kelurahan' => $this->kelurahan,
            'kecamatan' => $this->kecamatan,
            'province' => $this->province,
            'religion' => $this->religion,
            'marital_status' => $this->marital_status,
            'occupation' => $this->occupation,
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
