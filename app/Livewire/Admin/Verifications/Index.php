<?php

namespace App\Livewire\Admin\Verifications;

use App\Models\Registration;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $perPage = 10;

    public $showModal = false;

    public $selectedId = null;

    public $showRejectForm = false;

    public $rejectReason = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function viewKtp($id)
    {
        $this->selectedId = $id;
        $this->showRejectForm = false;
        $this->rejectReason = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->selectedId = null;
        $this->showRejectForm = false;
        $this->rejectReason = '';
        $this->showModal = false;
    }

    public function openRejectForm()
    {
        $this->showRejectForm = true;
    }

    public function cancelRejectForm()
    {
        $this->showRejectForm = false;
        $this->rejectReason = '';
    }

    public function approveKtp($id = null)
    {
        $targetId = $id ?? $this->selectedId;
        $reg = Registration::find($targetId);
        if (! $reg) {
            session()->flash('message', 'Registrasi tidak ditemukan');

            return;
        }

        $admin = auth()->user();
        if ($admin && $admin->role === 'admin') {
            $cityIds = collect($admin->getAdminCityIds());
            if ($cityIds->isNotEmpty()) {
                $isAllowed = false;
                if ($reg->city_id && $cityIds->contains($reg->city_id)) {
                    $isAllowed = true;
                } elseif (! empty($reg->city)) {
                    $cityNames = \App\Models\City::whereIn('id', $cityIds)
                        ->pluck('name')
                        ->map(fn ($n) => strtolower(trim($n)))
                        ->all();
                    foreach ($cityNames as $cn) {
                        if (str_contains(strtolower($reg->city), $cn)) {
                            $isAllowed = true;
                            break;
                        }
                    }
                }

                if (! $isAllowed) {
                    session()->flash('message', 'Anda tidak memiliki hak akses untuk memproses verifikasi di luar wilayah kelola.');

                    return;
                }
            }
        }

        $reg->update(['status' => 'approved']);

        // Jika ada user terkait, update status, verifikasi, dan sinkronkan data alamat
        try {
            if (! empty($reg->email)) {
                $user = User::where('email', $reg->email)->first();
                if ($user) {
                    $user->verified = true;
                    $user->status = 'active';

                    // Sinkronkan data alamat & KTP jika di user masih kosong atau belum update
                    $cityName = $user->city ?: $reg->city;
                    $cityId = $user->city_id ?: $reg->city_id;
                    if (empty($cityId) && ! empty($cityName)) {
                        $matchedCity = \App\Models\City::whereRaw('LOWER(name) = ?', [strtolower(trim($cityName))])->first();
                        if ($matchedCity) {
                            $cityId = $matchedCity->id;
                            $cityName = $matchedCity->name;
                        }
                    }

                    $user->city_id = $cityId;
                    $user->city = $cityName;
                    $user->province = $user->province ?: $reg->province;
                    $user->kecamatan = $user->kecamatan ?: $reg->kecamatan;
                    $user->kelurahan = $user->kelurahan ?: $reg->kelurahan;
                    $user->address = $user->address ?: $reg->address;
                    $user->rt = $user->rt ?: $reg->rt;
                    $user->rw = $user->rw ?: $reg->rw;
                    $user->nik = $user->nik ?: $reg->nik;
                    $user->place_of_birth = $user->place_of_birth ?: $reg->place_of_birth;
                    $user->date_of_birth = $user->date_of_birth ?: $reg->date_of_birth;
                    $user->gender = $user->gender ?: $reg->gender;
                    $user->religion = $user->religion ?: $reg->religion;
                    $user->marital_status = $user->marital_status ?: $reg->marital_status;
                    $user->occupation = $user->occupation ?: $reg->occupation;

                    if (empty($user->ktp_photo) && ! empty($reg->ktp_photo_path)) {
                        $user->ktp_photo = $reg->ktp_photo_path;
                    }
                    if (empty($user->selfie_photo) && ! empty($reg->selfie_photo_path)) {
                        $user->selfie_photo = $reg->selfie_photo_path;
                    }

                    $user->save();

                    try {
                        $user->notify(new \App\Notifications\KtpVerificationStatusNotification('approved'));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Gagal kirim notif KTP approved: '.$e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            // do not block admin action if user update fails
        }

        session()->flash('message', 'Registrasi berhasil disetujui (Terverifikasi).');
        $this->closeModal();
    }

    public function confirmReject($id = null)
    {
        $targetId = $id ?? $this->selectedId;
        if (! $targetId) {
            session()->flash('message', 'Registrasi tidak ditemukan');
            $this->closeModal();

            return;
        }

        $this->validate([
            'rejectReason' => 'nullable|string|max:500',
        ]);

        $reg = Registration::find($targetId);
        if (! $reg) {
            session()->flash('message', 'Registrasi tidak ditemukan');
            $this->closeModal();

            return;
        }

        $admin = auth()->user();
        if ($admin && $admin->role === 'admin') {
            $cityIds = collect($admin->getAdminCityIds());
            if ($cityIds->isNotEmpty()) {
                $isAllowed = false;
                if ($reg->city_id && $cityIds->contains($reg->city_id)) {
                    $isAllowed = true;
                } elseif (! empty($reg->city)) {
                    $cityNames = \App\Models\City::whereIn('id', $cityIds)
                        ->pluck('name')
                        ->map(fn ($n) => strtolower(trim($n)))
                        ->all();
                    foreach ($cityNames as $cn) {
                        if (str_contains(strtolower($reg->city), $cn)) {
                            $isAllowed = true;
                            break;
                        }
                    }
                }

                if (! $isAllowed) {
                    session()->flash('message', 'Anda tidak memiliki hak akses untuk memproses verifikasi di luar wilayah kelola.');
                    $this->closeModal();

                    return;
                }
            }
        }

        $reason = trim($this->rejectReason) ?: 'Dokumen tidak memenuhi persyaratan verifikasi.';

        $reg->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        try {
            if (! empty($reg->email)) {
                $user = User::where('email', $reg->email)->first();
                if ($user) {
                    $user->verified = false;
                    $user->status = 'inactive';
                    $user->save();

                    try {
                        $user->notify(new \App\Notifications\KtpVerificationStatusNotification('rejected', $reason));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Gagal kirim notif KTP rejected: '.$e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        session()->flash('message', 'Registrasi berhasil ditolak. Alasan penolakan telah disimpan.');
        $this->closeModal();
    }

    public function render()
    {
        $query = Registration::query();

        if ($this->search) {
            $searchTerm = trim($this->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$searchTerm.'%')
                    ->orWhere('nik', 'like', '%'.$searchTerm.'%')
                    ->orWhere('address', 'like', '%'.$searchTerm.'%')
                    ->orWhere('city', 'like', '%'.$searchTerm.'%')
                    ->orWhere('kelurahan', 'like', '%'.$searchTerm.'%')
                    ->orWhere('kecamatan', 'like', '%'.$searchTerm.'%')
                    ->orWhere('province', 'like', '%'.$searchTerm.'%');
            });
        }

        if ($this->statusFilter) {
            if ($this->statusFilter === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('status')
                        ->orWhere('status', 'pending')
                        ->orWhereNotIn('status', ['approved', 'rejected']);
                });
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        $admin = auth()->user();
        // Filter by admin's managed cities if user is admin
        if ($admin && $admin->role === 'admin') {
            $cityIds = collect($admin->getAdminCityIds());

            if ($cityIds->isNotEmpty()) {
                $cityNames = \App\Models\City::whereIn('id', $cityIds)
                    ->pluck('name')
                    ->map(fn ($n) => strtolower(trim($n)))
                    ->all();

                $query->where(function ($q) use ($cityIds, $cityNames) {
                    $q->whereIn('city_id', $cityIds);
                    if (! empty($cityNames)) {
                        $q->orWhere(function ($subQ) use ($cityNames) {
                            foreach ($cityNames as $cityName) {
                                $subQ->orWhereRaw('LOWER(city) LIKE ?', ['%'.$cityName.'%']);
                            }
                        });
                    }
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $verifications = $query->with('user')->latest()->paginate($this->perPage);
        $selected = $this->selectedId ? Registration::with('user')->find($this->selectedId) : null;

        return view('admin.verifications', compact('verifications', 'selected'));
    }
}
