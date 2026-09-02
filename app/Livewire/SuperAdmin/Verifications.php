<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\User;

#[Layout('layouts.superadmin')]
class Verifications extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

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
        session()->flash('message', 'View KTP #' . $id);
    }

    public function approveKtp($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('message', 'User tidak ditemukan');
            return;
        }

        // Update user status
        $user->verified = true;
        $user->status = 'active';
        if (array_key_exists('email_verified_at', $user->getAttributes())) {
            $user->email_verified_at = now();
        }
        $user->save();

        // If there's an associated registration, approve it too
        $reg = \App\Models\Registration::where('email', $user->email)->first();
        if ($reg) {
            $reg->update(['status' => 'approved']);
        }

        session()->flash('message', 'User berhasil diverifikasi.');
    }

    public function rejectKtp($id)
    {
        $user = User::find($id);
        if (!$user) {
            session()->flash('message', 'User tidak ditemukan');
            return;
        }

        $user->verified = false;
        $user->save();

        // If there's an associated registration, reject it too
        $reg = \App\Models\Registration::where('email', $user->email)->first();
        if ($reg) {
            $reg->update(['status' => 'rejected']);
        }

        session()->flash('message', 'Verifikasi ditolak.');
    }

    public function render()
    {
        $verifications = User::query()
            ->where('role', 'mitra')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('superadmin.verifications', compact('verifications'));
    }
}
