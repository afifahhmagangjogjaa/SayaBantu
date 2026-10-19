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
    public $city_id;
    public $gender;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'nik' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'city_id' => ['required', \Illuminate\Validation\Rule::exists('cities', 'id')->where('is_active', true)],
            'address' => ['required', 'string', 'max:500'],
            'gender' => ['required', 'string', 'in:Laki-laki,Perempuan'],
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
        'gender.required' => 'Jenis kelamin wajib dipilih.',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nik = $user->nik;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->city_id = $user->city_id;
        $this->gender = $user->gender;
    }

    public function updateProfileInformation()
    {
        $this->name = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->name);
        $this->nik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        $this->phone = preg_replace('/[^0-9]/', '', (string) $this->phone);

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
            'city_id' => $this->city_id,
            'gender' => !empty($this->gender) ? $this->gender : null,
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
