<?php

namespace App\Livewire\Admin;

use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\UserBalance;
use App\Notifications\TopupApproved;
use App\Notifications\TopupRejected;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class TopupApproval extends Component
{
    use WithPagination;

    public $selectedTransaction = null;
    public $showDetailModal = false;
    public $showRejectModal = false;
    public $rejectionReason = '';

    // Search and filters for both tables
    public $searchPending = '';
    public $searchHistory = '';
    public $historyStatusFilter = '';
    public $perPagePending = 10;
    public $perPageHistory = 10;
    
    protected $listeners = ['topupRequestCreated' => '$refresh'];

    public function updatedSearchPending()
    {
        $this->resetPage('pendingPage');
    }

    public function updatedSearchHistory()
    {
        $this->resetPage('historyPage');
    }

    public function updatedHistoryStatusFilter()
    {
        $this->resetPage('historyPage');
    }

    public function filterHistoryStatus($status)
    {
        $this->historyStatusFilter = $status;
        $this->resetPage('historyPage');
    }

    public function mount()
    {
        //
    }

    public function viewDetail($transactionId)
    {
        $this->selectedTransaction = BalanceTransaction::with(['user', 'user.city'])->find($transactionId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->showRejectModal = false;
        $this->selectedTransaction = null;
        $this->rejectionReason = '';
    }

    public function openRejectModal($transactionId)
    {
        $this->selectedTransaction = BalanceTransaction::with(['user'])->find($transactionId);
        $this->showRejectModal = true;
    }

    public function approve($transactionId)
    {
        // Admin tidak memiliki akses untuk approve
        session()->flash('error', 'Admin tidak memiliki akses untuk approve. Hubungi Super Admin.');
        return;
    }

    public function reject()
    {
        // Admin tidak memiliki akses untuk reject
        session()->flash('error', 'Admin tidak memiliki akses untuk reject. Hubungi Super Admin.');
        $this->closeModal();
        return;
    }

    public function render()
    {
        $user = auth()->user();

        // 1. Pending Table Query
        $pendingQuery = BalanceTransaction::where('type', 'topup')
            ->where('status', 'waiting_approval')
            ->with(['user', 'user.city']);

        if ($user->role === 'admin' && $user->city_id) {
            $pendingQuery->where(function ($q) use ($user) {
                $q->whereHas('user', function ($sq) use ($user) {
                    $sq->where('city_id', $user->city_id);
                })->orWhereHas('user', function ($sq) {
                    $sq->whereNull('city_id');
                });
            });
        }

        if ($this->searchPending) {
            $sp = trim($this->searchPending);
            $pendingQuery->where(function ($q) use ($sp) {
                $q->where('request_code', 'like', "%{$sp}%")
                  ->orWhere('id', 'like', "%{$sp}%")
                  ->orWhere('customer_phone', 'like', "%{$sp}%")
                  ->orWhereHas('user', function ($uq) use ($sp) {
                      $uq->where('name', 'like', "%{$sp}%")
                         ->orWhere('email', 'like', "%{$sp}%")
                         ->orWhere('phone', 'like', "%{$sp}%");
                  });
            });
        }

        $pendingRequests = $pendingQuery->orderBy('created_at', 'asc')
            ->paginate($this->perPagePending, ['*'], 'pendingPage');

        // 2. History Table Query
        $historyQuery = BalanceTransaction::where('type', 'topup')
            ->whereIn('status', ['completed', 'rejected', 'failed'])
            ->with(['user', 'user.city']);

        if ($user->role === 'admin' && $user->city_id) {
            $historyQuery->where(function ($q) use ($user) {
                $q->whereHas('user', function ($sq) use ($user) {
                    $sq->where('city_id', $user->city_id);
                })->orWhereHas('user', function ($sq) {
                    $sq->whereNull('city_id');
                });
            });
        }

        if ($this->historyStatusFilter && $this->historyStatusFilter !== 'all') {
            $historyQuery->where('status', $this->historyStatusFilter);
        }

        if ($this->searchHistory) {
            $sh = trim($this->searchHistory);
            $historyQuery->where(function ($q) use ($sh) {
                $q->where('request_code', 'like', "%{$sh}%")
                  ->orWhere('id', 'like', "%{$sh}%")
                  ->orWhere('customer_phone', 'like', "%{$sh}%")
                  ->orWhereHas('user', function ($uq) use ($sh) {
                      $uq->where('name', 'like', "%{$sh}%")
                         ->orWhere('email', 'like', "%{$sh}%")
                         ->orWhere('phone', 'like', "%{$sh}%");
                  });
            });
        }

        $historyRequests = $historyQuery->orderByDesc('updated_at')
            ->paginate($this->perPageHistory, ['*'], 'historyPage');

        // Counters
        $baseCityScope = function ($q) use ($user) {
            if ($user->role === 'admin' && $user->city_id) {
                $q->where(function ($sq) use ($user) {
                    $sq->whereHas('user', function ($ssq) use ($user) {
                        $ssq->where('city_id', $user->city_id);
                    })->orWhereHas('user', function ($ssq) {
                        $ssq->whereNull('city_id');
                    });
                });
            }
        };

        $totalPendingCount = BalanceTransaction::where('type', 'topup')
            ->where('status', 'waiting_approval')
            ->where($baseCityScope)
            ->count();

        $totalCompletedCount = BalanceTransaction::where('type', 'topup')
            ->where('status', 'completed')
            ->where($baseCityScope)
            ->count();

        $totalRejectedCount = BalanceTransaction::where('type', 'topup')
            ->where('status', 'rejected')
            ->where($baseCityScope)
            ->count();

        $totalHistoryCount = BalanceTransaction::where('type', 'topup')
            ->whereIn('status', ['completed', 'rejected', 'failed'])
            ->where($baseCityScope)
            ->count();
        
        $approvedToday = BalanceTransaction::where('type', 'topup')
            ->where('status', 'completed')
            ->whereNotNull('approved_by')
            ->whereDate('approved_at', now()->toDateString())
            ->where($baseCityScope)
            ->count();
            
        $totalPendingAmount = BalanceTransaction::where('type', 'topup')
            ->where('status', 'waiting_approval')
            ->where($baseCityScope)
            ->sum('amount');
            
        $totalApprovedAmountToday = BalanceTransaction::where('type', 'topup')
            ->where('status', 'completed')
            ->whereNotNull('approved_by')
            ->whereDate('approved_at', now()->toDateString())
            ->where($baseCityScope)
            ->sum('amount');

        return view('admin.topup-approval', [
            'pendingRequests' => $pendingRequests,
            'historyRequests' => $historyRequests,
            'approvedToday' => $approvedToday,
            'totalPendingAmount' => $totalPendingAmount,
            'totalApprovedAmountToday' => $totalApprovedAmountToday,
            'totalPendingCount' => $totalPendingCount,
            'totalCompletedCount' => $totalCompletedCount,
            'totalRejectedCount' => $totalRejectedCount,
            'totalHistoryCount' => $totalHistoryCount,
        ]);
    }
}
