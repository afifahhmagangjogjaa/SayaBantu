<?php

namespace App\Livewire\Customer\Addresses;

use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public bool $showDeleteModal = false;

    public ?int $deleteId = null;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'customer') {
            abort(403, 'Fitur alamat multi-lokasi hanya tersedia untuk Customer.');
        }

        // Jika belum ada alamat tersimpan sama sekali, namun user memiliki data alamat di profil,
        // buat alamat utama default dari profil user.
        if ($user->customerAddresses()->count() === 0 && ! empty($user->address)) {
            CustomerAddress::create([
                'user_id' => $user->id,
                'label' => 'Rumah',
                'recipient_name' => $user->name,
                'phone' => $user->phone ?? '',
                'city_id' => $user->city_id,
                'city_name' => $user->city,
                'province' => $user->province,
                'district' => $user->kecamatan,
                'village' => $user->kelurahan,
                'address' => $user->address,
                'is_primary' => true,
            ]);
        }
    }

    public function setAsPrimary(int $id): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        $target = CustomerAddress::where('user_id', $userId)->findOrFail($id);

        // Reset status utama alamat lain
        CustomerAddress::where('user_id', $userId)->update(['is_primary' => false]);
        $target->update(['is_primary' => true]);

        // Sinkronkan ke data profil pengguna di users
        $user = User::find($userId);
        if ($user) {
            $cityName = $target->city_name ?: ($target->city?->name);
            $province = $target->province ?: ($target->city?->province);

            $user->update([
                'address' => $target->address,
                'city_id' => $target->city_id,
                'city' => $cityName,
                'province' => $province,
                'kecamatan' => $target->district,
                'kelurahan' => $target->village,
            ]);
        }

        session()->flash('message', 'Alamat utama berhasil diubah.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->showDeleteModal = false;
    }

    public function delete(): void
    {
        $userId = Auth::id();
        if (! $this->deleteId || ! $userId) {
            return;
        }

        $addr = CustomerAddress::where('user_id', $userId)->findOrFail($this->deleteId);
        $wasPrimary = (bool) $addr->is_primary;
        $addr->delete();

        // Jika yang dihapus adalah alamat utama, jadikan alamat berikutnya sebagai utama
        if ($wasPrimary) {
            $next = CustomerAddress::where('user_id', $userId)->first();
            if ($next) {
                $next->update(['is_primary' => true]);
                $user = User::find($userId);
                if ($user) {
                    $cityName = $next->city_name ?: ($next->city?->name);
                    $province = $next->province ?: ($next->city?->province);

                    $user->update([
                        'address' => $next->address,
                        'city_id' => $next->city_id,
                        'city' => $cityName,
                        'province' => $province,
                        'kecamatan' => $next->district,
                        'kelurahan' => $next->village,
                    ]);
                }
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('message', 'Alamat berhasil dihapus.');
    }

    public function render()
    {
        $userId = Auth::id();
        $addresses = CustomerAddress::where('user_id', $userId)
            ->with('city')
            ->orderByDesc('is_primary')
            ->orderByDesc('id')
            ->get();

        return view('livewire.customer.addresses.index', [
            'addresses' => $addresses,
        ]);
    }
}
