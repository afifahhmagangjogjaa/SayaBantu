<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\City;
use Illuminate\Validation\Rule;

#[Layout('layouts.superadmin')]
class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $perPage = 10;
    public $selectedUser = null;
    public $userId = null;

    // form fields
    public $name;
    public $email;
    public $phone;
    public $role = 'kustomer';
    public $status = 'active';
    public $verified = false;
    public $city_id = null;
    public $managed_city_ids = []; // Array for multiple cities
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
    protected $queryString = [
        'roleFilter' => ['except' => '', 'as' => 'role']
    ];

    public function mount()
    {
        if (request()->routeIs('superadmin.customers*')) {
            $this->roleFilter = 'customer';
        } elseif (request()->routeIs('superadmin.mitra*')) {
            $this->roleFilter = 'mitra';
        } elseif (request()->has('role')) {
            $role = request()->get('role');
            if (in_array($role, ['mitra', 'kustomer', 'customer'])) {
                $this->roleFilter = $role;
            }
        }
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
        $user = User::with(['customerRatings.rater', 'customerRatings.help', 'mitraRatings.rater', 'mitraRatings.help', 'managedCities'])->find($id);
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
        $this->showEditModal = true;
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->showConfirmDelete = true;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        if ($this->roleFilter === 'mitra') {
            $this->role = 'mitra';
        } else {
            $this->role = 'kustomer';
        }
        $this->showCreateModal = true;
    }

    public function resetForm()
    {
        $this->selectedUser = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        // Default values
        $this->role = 'kustomer';

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
    }

    /**
     * Toggle user status (active <-> inactive) directly via button
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'User tidak ditemukan');
            return;
        }

        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Status {$user->name} berhasil {$statusLabel}.");
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

        // Auto-assign role based on current management section
        if ($this->roleFilter === 'mitra') {
            $this->role = 'mitra';
        } elseif ($this->roleFilter === 'customer' || $this->roleFilter === 'kustomer') {
            $this->role = 'kustomer';
        } elseif (empty($this->role)) {
            $this->role = 'kustomer';
        }

        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => $emailRules,
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'role' => 'required|string',
            'status' => 'nullable|string|in:active,inactive',
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
            'address' => $isEdit ? 'nullable|string|max:1000' : 'required|string|max:1000',
            'kelurahan' => $isEdit ? 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\,\'\-]+$/' : 'required|string|max:100|regex:/^[a-zA-Z\s\.\,\'\-]+$/',
            'kecamatan' => $isEdit ? 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\,\'\-]+$/' : 'required|string|max:100|regex:/^[a-zA-Z\s\.\,\'\-]+$/',
            'province' => $isEdit ? 'nullable|string|max:100' : 'required|string|max:100',
            'rt' => 'nullable|string|max:3|regex:/^[0-9]+$/',
            'rw' => 'nullable|string|max:3|regex:/^[0-9]+$/',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:150',
        ];

        // Password validation - required for new users, optional for update
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
            // Only update password if provided
            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            } else {
                unset($data['password']);
            }

            $user->update($data);
            
            // Sync managed cities for admin role
            if ($this->role === 'admin') {
                $user->managedCities()->sync($this->managed_city_ids ?? []);
            } else {
                $user->managedCities()->sync([]);
            }

            // Sync with registrations table if verified status changed
            try {
                if ($user->wasChanged('verified')) {
                    $reg = \App\Models\Registration::where('email', $user->email)->first();
                    if ($reg) {
                        $reg->update(['status' => $this->verified ? 'approved' : 'rejected']);
                    }
                }
            } catch (\Exception $e) {
                // ignore error to not break the save
            }
            
            session()->flash('message', 'User updated successfully');
        } else {
            // create new user with provided password
            $data['password'] = bcrypt($this->password);
            $data['email_verified_at'] = now();
            $data['is_completed'] = true;
            $user = User::create($data);
            
            // Sync managed cities for admin role
            if ($this->role === 'admin') {
                $user->managedCities()->sync($this->managed_city_ids ?? []);
            }

            if ($this->verified) {
                try {
                    \App\Models\Registration::updateOrCreate(
                        ['email' => $user->email],
                        [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'phone' => $user->phone,
                            'role' => $user->role,
                            'status' => 'approved',
                        ]
                    );
                } catch (\Exception $e) {
                    // ignore
                }
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
        $userEmail = $user->email;
        $user->delete();
        try {
            \App\Models\Registration::where('email', $userEmail)->delete();
        } catch (\Exception $e) {
            // ignore
        }
        session()->flash('message', 'User and associated verification data deleted');
        $this->showConfirmDelete = false;
        $this->confirmingDeleteId = null;
        $this->resetPage();
    }

    /**
     * Close any open modal and reset relevant state
     */
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
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                if (in_array($this->roleFilter, ['kustomer', 'customer'])) {
                    $query->whereIn('role', ['kustomer', 'customer']);
                } else {
                    $query->where('role', $this->roleFilter);
                }
            }, function ($query) {
                // default: show only mitra, kustomer, and customer
                $query->whereIn('role', ['mitra', 'kustomer', 'customer']);
            })
            ->latest()
            ->paginate($this->perPage);

        $cities = City::orderBy('name')->get();

        return view('superadmin.users', compact('users', 'cities'));
    }
}
