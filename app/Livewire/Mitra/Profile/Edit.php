<?php

namespace App\Livewire\Mitra\Profile;

use App\Models\City;
use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{
    public bool $showModal = false;

    public ?string $name = null;

    public ?string $phone = null;

    public ?int $city_id = null;

    public ?string $bio = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'phone' => [
                'nullable',
                'string',
                'min:10',
                'max:13',
                'regex:/^[0-9]+$/',
                \Illuminate\Validation\Rule::unique('users', 'phone')->ignore(auth()->id()),
            ],
            'city_id' => ['nullable', \Illuminate\Validation\Rule::exists('cities', 'id')->where('is_active', true)],
            'bio' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
        'phone.min' => 'Nomor HP minimal 10 digit angka.',
        'phone.max' => 'Nomor HP maksimal 13 digit angka.',
        'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
        'phone.unique' => 'Nomor HP sudah digunakan oleh akun lain.',
    ];

    #[On('openEditProfile')]
    public function openModal(): void
    {
        $user = auth()->user();

        $this->name = $user->name;
        $this->phone = $user->phone ?? '';
        $this->city_id = $user->city_id ?? null;
        $this->bio = $user->bio ?? '';
        $this->showModal = true;
    }

    #[On('closeEditProfile')]
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $this->name = preg_replace('/[^a-zA-Z\s]/', '', (string) $this->name);
        $this->phone = preg_replace('/[^0-9]/', '', (string) $this->phone);

        $this->validate();

        $user = auth()->user();
        $user->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'city_id' => $this->city_id,
            'bio' => $this->bio,
        ]);

        $this->showModal = false;

        $this->dispatch('profile-updated');
        session()->flash('success', 'Profil berhasil diperbarui.');
    }

    public function render()
    {
        $cities = City::where('is_active', true)->orderBy('name')->get();

        return view('livewire.mitra.profile.edit', compact('cities'));
    }
}
