<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UpdateProfileInformationForm extends Component
{
    public $name;
    public $email;
    public $nik;
    public $phone;
    public $address;
    public $rt;
    public $rw;
    public $city_id;
    public $religion;
    public $occupation;
    public $marital_status;
    public $gender;
    public $place_of_birth;
    public $date_of_birth;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'city_id' => ['required', 'exists:cities,id'],
            'address' => ['required', 'string', 'max:500'],
            'religion' => ['required', 'string', 'max:50'],
            'marital_status' => ['required', 'string', 'max:50'],
            'occupation' => ['required', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'rw' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'gender' => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(17)->format('Y-m-d')],
        ];
    }

    protected $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
        'email.required' => 'Email wajib diisi.',
        'nik.required' => 'NIK wajib diisi.',
        'nik.size' => 'NIK harus 16 digit angka.',
        'nik.regex' => 'NIK hanya boleh berisi angka.',
        'phone.required' => 'Nomor HP wajib diisi.',
        'phone.min' => 'Nomor HP minimal 10 digit angka.',
        'phone.max' => 'Nomor HP maksimal 13 digit angka.',
        'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
        'city_id.required' => 'Kota domisili wajib dipilih.',
        'address.required' => 'Alamat lengkap wajib diisi.',
        'religion.required' => 'Agama wajib dipilih.',
        'marital_status.required' => 'Status pernikahan wajib dipilih.',
        'occupation.required' => 'Pekerjaan wajib diisi.',
        'rt.max' => 'RT maksimal 3 karakter angka.',
        'rw.max' => 'RW maksimal 3 karakter angka.',
        'gender.required' => 'Jenis kelamin wajib dipilih.',
        'place_of_birth.required' => 'Tempat lahir wajib diisi.',
        'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
        'date_of_birth.before_or_equal' => 'Anda harus berusia minimal 17 tahun.',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nik = $user->nik;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->rt = $user->rt;
        $this->rw = $user->rw;
        $this->city_id = $user->city_id;
        $this->religion = $user->religion;
        $this->occupation = $user->occupation;
        $this->marital_status = $user->marital_status;
        $this->gender = $user->gender;
        $this->place_of_birth = $user->place_of_birth;
        $this->date_of_birth = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : null;
    }

    public function updateProfileInformation()
    {
        // Sanitasi input
        $this->name = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->name);
        $this->nik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        $this->phone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if ($this->rt) $this->rt = preg_replace('/[^0-9]/', '', (string) $this->rt);
        if ($this->rw) $this->rw = preg_replace('/[^0-9]/', '', (string) $this->rw);

        $this->validate();

        $user = Auth::user();

        // Check if email changed and already exists
        if (
            $this->email !== $user->email &&
            \App\Models\User::where('email', $this->email)->where('id', '!=', $user->id)->exists()
        ) {
            $this->addError('email', 'Email sudah digunakan oleh akun lain.');
            return;
        }

        // Check if NIK changed and already exists on other users
        if (
            $this->nik !== $user->nik &&
            \App\Models\User::where('nik', $this->nik)->where('id', '!=', $user->id)->exists()
        ) {
            $this->addError('nik', 'NIK sudah terdaftar pada akun lain.');
            return;
        }

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'nik' => $this->nik,
            'phone' => $this->phone,
            'address' => $this->address,
            'rt' => !empty($this->rt) ? $this->rt : null,
            'rw' => !empty($this->rw) ? $this->rw : null,
            'city_id' => $this->city_id,
            'religion' => !empty($this->religion) ? $this->religion : null,
            'occupation' => !empty($this->occupation) ? $this->occupation : null,
            'marital_status' => !empty($this->marital_status) ? $this->marital_status : null,
            'gender' => !empty($this->gender) ? $this->gender : null,
            'place_of_birth' => !empty($this->place_of_birth) ? $this->place_of_birth : null,
            'date_of_birth' => !empty($this->date_of_birth) ? $this->date_of_birth : null,
        ]);

        session()->flash('message', 'Perubahan profil berhasil disimpan!');
        
        $redirectUrl = Auth::user()->role === 'mitra' ? route('mitra.profile') : route('profile');
        
        $this->dispatch('profile-saved', redirectUrl: $redirectUrl);
    }

    public function render()
    {
        $cities = \App\Models\City::where('is_active', true)->orderBy('name')->get();
        return view('livewire.profile.update-profile-information-form', compact('cities'));
    }
}
