<?php

namespace App\Livewire\Admin\Verifications;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Registration;
use App\Models\User;

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
        if (!$reg) {
            session()->flash('message', 'Registrasi tidak ditemukan');
            return;
        }
        $reg->update(['status' => 'approved']);

        // Jika ada user terkait, update status dan verifikasi
        try {
            if (!empty($reg->email)) {
                $user = User::where('email', $reg->email)->first();
                if ($user) {
                    $user->verified = true;
                    $user->status = 'active';
                    if (array_key_exists('email_verified_at', $user->getAttributes())) {
                        $user->email_verified_at = now();
                    }
                    $user->save();
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
        if (!$targetId) {
            session()->flash('message', 'Registrasi tidak ditemukan');
            $this->closeModal();
            return;
        }

        $this->validate([
            'rejectReason' => 'nullable|string|max:500',
        ]);

        $reg = Registration::find($targetId);
        if (!$reg) {
            session()->flash('message', 'Registrasi tidak ditemukan');
            $this->closeModal();
            return;
        }

        $reason = trim($this->rejectReason) ?: 'Dokumen tidak memenuhi persyaratan verifikasi.';

        $reg->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        try {
            if (!empty($reg->email)) {
                $user = User::where('email', $reg->email)->first();
                if ($user) {
                    $user->verified = false;
                    $user->status = 'inactive';
                    $user->save();
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
                $q->where('full_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nik', 'like', '%' . $searchTerm . '%')
                  ->orWhere('address', 'like', '%' . $searchTerm . '%')
                  ->orWhere('city', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kelurahan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kecamatan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('province', 'like', '%' . $searchTerm . '%');
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
                    ->map(fn($n) => strtolower(trim($n)))
                    ->all();

                $query->where(function ($q) use ($cityIds, $cityNames) {
                    $q->whereIn('city_id', $cityIds);
                    if (!empty($cityNames)) {
                        $q->orWhere(function ($subQ) use ($cityNames) {
                            foreach ($cityNames as $cityName) {
                                $subQ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $cityName . '%']);
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