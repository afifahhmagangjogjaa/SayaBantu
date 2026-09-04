<?php

namespace App\Livewire\Admin\Helps;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Help;

class Approved extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Admin view-only page, no approve/reject methods here

    public function render()
    {
        $query = Help::query()->with(['customer', 'category', 'city'])
            ->whereIn('status', ['partner_on_the_way', 'taken', 'waiting_customer_confirmation'])
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            });

        // Filter by admin's city if user is admin
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

        return view('admin.helps-approved', compact('helps'))
            ->layout('layouts.admin', ['pageTitle' => 'Manajemen Bantuan - Disetujui']);
    }
}
