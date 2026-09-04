<?php

namespace App\Livewire\Admin\Helps;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Help;

class Index extends Component
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

    public function viewHelp($id)
    {
        return redirect()->route('admin.helps.show', $id);
    }

    public function approveHelp($id)
    {
        $help = Help::findOrFail($id);
        $help->update(['status' => 'menunggu_mitra']);
        session()->flash('message', 'Bantuan berhasil disetujui');
    }

    public function rejectHelp($id)
    {
        $help = Help::findOrFail($id);
        $help->update(['status' => 'rejected']);
        session()->flash('message', 'Bantuan ditolak');
    }

    public function render()
    {
        $query = Help::query()
            ->with(['customer', 'category', 'city'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('status', $this->statusFilter);
            });

        // Filter by admin's managed cities (multi-city support)
        if (auth()->user() && auth()->user()->role === 'admin') {
            $adminCityIds = auth()->user()->getAdminCityIds();
            if (!empty($adminCityIds)) {
                $query->where(function ($q) use ($adminCityIds) {
                    $q->whereIn('city_id', $adminCityIds)
                      ->orWhereHas('customer', function ($c) use ($adminCityIds) {
                          $c->whereIn('city_id', $adminCityIds);
                      });
                });
            }
        }

        $helps = $query->latest()->paginate($this->perPage);

        // Statistics - filtered by admin's managed cities
        $statsQuery = Help::query();
        if (auth()->user() && auth()->user()->role === 'admin') {
            $adminCityIds = auth()->user()->getAdminCityIds();
            if (!empty($adminCityIds)) {
                $statsQuery->where(function ($q) use ($adminCityIds) {
                    $q->whereIn('city_id', $adminCityIds)
                      ->orWhereHas('customer', function ($c) use ($adminCityIds) {
                          $c->whereIn('city_id', $adminCityIds);
                      });
                });
            }
        }

        $totalHelps = $statsQuery->count();
        $waitingMitraHelps = (clone $statsQuery)->where('status', 'menunggu_mitra')->count();
        $completedHelps = (clone $statsQuery)->where('status', 'selesai')->count();
        $complaintHelps = (clone $statsQuery)->whereIn('status', ['komplain', 'disputed'])->count();

        return view('admin.helps-approved', compact('helps', 'totalHelps', 'waitingMitraHelps', 'completedHelps', 'complaintHelps'))
            ->layout('layouts.admin', ['pageTitle' => 'Manajemen Bantuan']);
    }
}
