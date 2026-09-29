<?php

namespace App\Livewire\Mitra\Helps;

use App\Models\Help;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.mitra')]
class ProcessingHelps extends Component
{
    use WithPagination;

    public $tab = 'diproses'; // 'diproses' | 'selesai' | 'dibatalkan'
    public $search = '';

    protected $queryString = [
        'tab' => ['except' => 'diproses'],
    ];

    public $processingStatuses = [
        'memperoleh_mitra',
        'taken',
        'partner_on_the_way',
        'partner_arrived',
        'in_progress',
        'sedang_diproses',
        'partner_cancel_requested',
        'diproses_mitra',
        'waiting_customer_confirmation'
    ];

    public function mount()
    {
        if (request()->routeIs('mitra.helps.completed')) {
            $this->tab = 'selesai';
        } elseif (request()->has('tab') && in_array(request('tab'), ['diproses', 'selesai', 'dibatalkan'])) {
            $this->tab = request('tab');
        }
    }

    public function switchTab($tab)
    {
        if (in_array($tab, ['diproses', 'selesai', 'dibatalkan'])) {
            $this->tab = $tab;
            $this->resetPage();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadHelps()
    {
        // Handled dynamically in render()
    }

    public function completeHelp($helpId)
    {
        $help = Help::where('id', $helpId)->where('mitra_id', auth()->id())->first();
        if (!$help) {
            $this->dispatch('error', 'Bantuan tidak ditemukan atau bukan milik Anda');
            return;
        }

        $help->update([
            'status' => 'waiting_customer_confirmation',
        ]);

        if (!$help->service_completed_at) {
            $help->update(['service_completed_at' => now()]);
        }

        $this->dispatch('help-completed');
        session()->flash('success', 'Menunggu konfirmasi dari customer');
    }

    public function render()
    {
        $user = auth()->user();

        try {
            Help::autoConfirmExpiredCustomerHelps();
        } catch (\Throwable $e) {}

        $query = Help::where('mitra_id', $user->id)
            ->with(['user', 'city', 'category', 'rating']);

        if ($this->tab === 'diproses') {
            $query->whereIn('status', $this->processingStatuses)
                ->orderByDesc('taken_at');
        } elseif ($this->tab === 'selesai') {
            $query->where('status', 'selesai')
                ->latest('service_completed_at');
        } elseif ($this->tab === 'dibatalkan') {
            $query->whereIn('status', ['dibatalkan', 'rejected', 'cancelled'])
                ->latest();
        }

        if (!empty($this->search)) {
            $keyword = trim($this->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('helps.title', 'like', '%' . $keyword . '%')
                    ->orWhere('helps.description', 'like', '%' . $keyword . '%')
                    ->orWhere('helps.location', 'like', '%' . $keyword . '%')
                    ->orWhere('helps.full_address', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($u) use ($keyword) {
                        $u->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('phone', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('city', function ($c) use ($keyword) {
                        $c->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('category', function ($cat) use ($keyword) {
                        $cat->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        $helps = $query->paginate(10);

        $processingCount = Help::where('mitra_id', $user->id)
            ->whereIn('status', $this->processingStatuses)
            ->count();

        $completedCount = Help::where('mitra_id', $user->id)
            ->where('status', 'selesai')
            ->count();

        $cancelledCount = Help::where('mitra_id', $user->id)
            ->whereIn('status', ['dibatalkan', 'rejected', 'cancelled'])
            ->count();

        return view('livewire.mitra.helps.processing-helps', [
            'helps' => $helps,
            'tab' => $this->tab,
            'search' => $this->search,
            'processingCount' => $processingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
        ]);
    }
}
