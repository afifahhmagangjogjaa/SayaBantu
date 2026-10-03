<?php

namespace App\Livewire\Customer;

use App\Models\BalanceTransaction;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TopupHistory extends Component
{
    use WithPagination;

    public $filterStatus = 'all';
    public $selectedTransaction = null;
    public $showDetailModal = false;

    protected $queryString = ['filterStatus'];

    #[On('balance-updated')]
    public function refreshTransactions()
    {
        $this->dispatch('$refresh');
    }

    public function mount()
    {
        //
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function viewDetail($transactionId)
    {
        $this->selectedTransaction = BalanceTransaction::with(['user', 'approvedBy'])->find($transactionId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedTransaction = null;
    }

    public function render()
    {
        $query = BalanceTransaction::topupRequests()
            ->where('user_id', auth()->id())
            ->with(['approvedBy']);

        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'approved') {
                $query->whereIn('status', ['approved', 'completed']);
            } else {
                $query->where('status', $this->filterStatus);
            }
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.customer.topup-history', [
            'transactions' => $transactions,
        ]);
    }
}
