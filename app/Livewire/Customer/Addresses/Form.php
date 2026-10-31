<?php

namespace App\Livewire\Customer\Addresses;

use App\Models\City;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public bool $isEdit = false;

    public ?int $addressId = null;

    // Form fields
    public string $label = 'Rumah';

    public string $customLabel = '';

    public string $recipient_name = '';

    public string $phone = '';

    public ?int $city_id = null;

    public string $province = '';

    public string $district = '';

    public string $village = '';

    public string $postal_code = '';

    public string $address = '';

    public string $benchmark = '';

    public bool $is_primary = false;

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'recipient_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string|max:1000',
            'district' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'benchmark' => 'nullable|string|max:255',
            'is_primary' => 'boolean',
        ];
    }

    protected $messages = [
        'label.required' => 'Label alamat wajib diisi.',
        'recipient_name.required' => 'Nama penerima / kontak wajib diisi.',
        'recipient_name.regex' => 'Nama penerima hanya boleh berisi huruf dan tanda baca umum.',
        'phone.required' => 'Nomor telepon wajib diisi.',
        'phone.min' => 'Nomor telepon minimal 10 digit angka.',
        'phone.max' => 'Nomor telepon maksimal 13 digit angka.',
        'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
        'city_id.required' => 'Kota / Kabupaten wajib dipilih.',
        'city_id.exists' => 'Kota yang dipilih tidak valid.',
        'address.required' => 'Alamat lengkap wajib diisi.',
    ];

    public function mount(?int $address = null): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'customer') {
            abort(403, 'Hanya customer yang dapat mengelola alamat.');
        }

        if ($address) {
            $addr = CustomerAddress::where('user_id', $user->id)->findOrFail($address);
            $this->isEdit = true;
            $this->addressId = $addr->id;
            $this->label = $addr->label;
            $this->customLabel = in_array($addr->label, ['Rumah', 'Kantor', 'Kos', 'Apartemen']) ? '' : $addr->label;
            $this->recipient_name = $addr->clean_recipient_name;
            $this->phone = $addr->phone;
            $this->city_id = $addr->city_id;
            $this->province = $addr->province ?? '';
            $this->district = $addr->district ?? '';
            $this->village = $addr->village ?? '';
            $this->postal_code = $addr->postal_code ?? '';
            $this->address = $addr->address;
            $this->benchmark = $addr->benchmark ?? '';
            $this->is_primary = (bool) $addr->is_primary;
        } else {
            $this->isEdit = false;
            $this->addressId = null;
            $this->label = 'Rumah';
            $this->customLabel = '';

            $cleanName = trim(preg_replace('/\s*\((?:Mitra|Customer|Pending[^)]*)\)|\s*(?:Mitra|Customer)\b/i', '', (string) ($user->name ?? '')));
            $this->recipient_name = $cleanName;
            $this->phone = $user->phone ?? '';
            $this->city_id = $user->city_id ?? null;
            $this->province = $user->province ?? '';
            $this->district = $user->kecamatan ?? '';
            $this->village = $user->kelurahan ?? '';
            $this->postal_code = '';
            $this->address = '';
            $this->benchmark = '';

            // Jika belum ada alamat tersimpan, otomatis jadikan alamat utama
            $hasAny = CustomerAddress::where('user_id', $user->id)->exists();
            $this->is_primary = ! $hasAny;
        }
    }

    public function setQuickLabel(string $preset): void
    {
        $this->label = $preset;
        if ($preset !== 'Lainnya') {
            $this->customLabel = '';
        }
    }

    public function updatedCustomLabel(string $val): void
    {
        if (! empty(trim($val))) {
            $this->label = trim($val);
        }
    }

    public function updatedCityId($val): void
    {
        if ($val) {
            $city = City::find($val);
            if ($city && ! empty($city->province)) {
                $this->province = is_string($city->province) ? $city->province : ($city->province->name ?? '');
            }
        }
    }

    public function save()
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'customer') {
            abort(403);
        }

        $this->phone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (! empty($this->customLabel)) {
            $this->label = trim($this->customLabel);
        }

        $this->validate();

        $city = City::find($this->city_id);
        $cityName = $city ? $city->name : null;
        $province = ! empty($this->province) ? $this->province : ($city ? (is_string($city->province) ? $city->province : null) : null);

        // Jika user belum punya alamat lain sama sekali, wajib jadi utama
        $otherAddressesCount = CustomerAddress::where('user_id', $user->id)
            ->when($this->isEdit && $this->addressId, function ($q) {
                $q->where('id', '!=', $this->addressId);
            })
            ->count();

        if ($otherAddressesCount === 0) {
            $this->is_primary = true;
        }

        if ($this->is_primary) {
            CustomerAddress::where('user_id', $user->id)->update(['is_primary' => false]);
        }

        $data = [
            'user_id' => $user->id,
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'city_id' => $this->city_id,
            'city_name' => $cityName,
            'province' => $province,
            'district' => $this->district ?: null,
            'village' => $this->village ?: null,
            'postal_code' => $this->postal_code ?: null,
            'address' => $this->address,
            'benchmark' => $this->benchmark ?: null,
            'is_primary' => (bool) $this->is_primary,
        ];

        if ($this->isEdit && $this->addressId) {
            $addr = CustomerAddress::where('user_id', $user->id)->findOrFail($this->addressId);
            $addr->update($data);
            $message = 'Alamat berhasil diperbarui.';
        } else {
            CustomerAddress::create($data);
            $message = 'Alamat baru berhasil ditambahkan.';
        }

        // Sinkronisasi data alamat utama ke users table
        if ($this->is_primary) {
            $freshUser = \App\Models\User::find($user->id);
            if ($freshUser) {
                $freshUser->update([
                    'address' => $this->address,
                    'city_id' => $this->city_id,
                    'city' => $cityName,
                    'province' => $province,
                    'kecamatan' => $this->district ?: $freshUser->kecamatan,
                    'kelurahan' => $this->village ?: $freshUser->kelurahan,
                ]);
            }
        }

        session()->flash('message', $message);

        return $this->redirect(route('customer.addresses'), navigate: true);
    }

    public function render()
    {
        $cities = City::where('is_active', true)->orderBy('name')->get();

        return view('livewire.customer.addresses.form', [
            'cities' => $cities,
        ]);
    }
}
