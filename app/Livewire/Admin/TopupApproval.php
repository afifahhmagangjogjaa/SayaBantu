<?php

namespace App\Livewire\Admin;

use App\Models\BalanceTransaction;
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

    public $deleteYear;
    public $deleteMonth = 'all';
    public $deleteMode = 'monthly'; // 'monthly', 'yearly', 'all'
    public $showDeleteModal = false;
    
    protected $listeners = ['topupRequestCreated' => '$refresh'];

    public function openDeleteModal()
    {
        $this->deleteYear = (int) date('Y');
        $this->deleteMonth = (string) (int) date('n');
        $this->deleteMode = 'monthly';
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function executeDelete()
    {
        if ($this->deleteMode === 'all') {
            $this->deleteAllHistory();
            return;
        }

        if ($this->deleteMode === 'yearly') {
            $this->deleteMonth = 'all';
        }

        $this->deleteHistoryByPeriod();
    }

    public function deleteHistoryItem($id)
    {
        $adminCityIds = auth()->user()?->getAdminCityIds() ?? [];
        $query = BalanceTransaction::where('id', $id);

        if (!empty($adminCityIds)) {
            $query->where(function ($q) use ($adminCityIds) {
                $q->whereHas('user', function ($sq) use ($adminCityIds) {
                    $sq->whereIn('city_id', $adminCityIds);
                })->orWhereHas('user', function ($sq) {
                    $sq->whereNull('city_id');
                });
            });
        }

        $tx = $query->first();
        if ($tx) {
            if ($tx->status === 'waiting_approval') {
                session()->flash('error', 'Permintaan top-up yang masih menunggu persetujuan tidak boleh dihapus.');
                return;
            }
            $tx->delete();
            session()->flash('success', 'Data riwayat top-up berhasil dihapus.');
        }
    }

    public function deleteHistoryByPeriod()
    {
        $adminCityIds = auth()->user()?->getAdminCityIds() ?? [];
        $query = BalanceTransaction::topupRequests()
            ->whereIn('status', ['completed', 'rejected', 'failed'])
            ->whereYear('created_at', (int) $this->deleteYear);

        if (!empty($adminCityIds)) {
            $query->where(function ($q) use ($adminCityIds) {
                $q->whereHas('user', function ($sq) use ($adminCityIds) {
                    $sq->whereIn('city_id', $adminCityIds);
                })->orWhereHas('user', function ($sq) {
                    $sq->whereNull('city_id');
                });
            });
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($this->deleteMonth && $this->deleteMonth !== 'all') {
            $monthNum = (int) $this->deleteMonth;
            $query->whereMonth('created_at', $monthNum);
            $periodLabel = ($monthNames[$monthNum] ?? "Bulan $monthNum") . " {$this->deleteYear}";
        } else {
            $periodLabel = "Tahun {$this->deleteYear}";
        }

        $count = $query->count();
        if ($count === 0) {
            session()->flash('error', "Tidak ada riwayat top-up pada periode {$periodLabel}.");
            $this->showDeleteModal = false;
            return;
        }

        $query->delete();
        $this->resetPage('historyPage');
        $this->showDeleteModal = false;
        session()->flash('success', "Berhasil menghapus {$count} data riwayat top-up pada periode {$periodLabel}.");
    }

    public function deleteAllHistory()
    {
        $adminCityIds = auth()->user()?->getAdminCityIds() ?? [];
        $query = BalanceTransaction::topupRequests()
            ->whereIn('status', ['completed', 'rejected', 'failed']);

        if (!empty($adminCityIds)) {
            $query->where(function ($q) use ($adminCityIds) {
                $q->whereHas('user', function ($sq) use ($adminCityIds) {
                    $sq->whereIn('city_id', $adminCityIds);
                })->orWhereHas('user', function ($sq) {
                    $sq->whereNull('city_id');
                });
            });
        }

        $count = $query->count();
        $query->delete();
        $this->resetPage('historyPage');
        $this->showDeleteModal = false;
        session()->flash('success', "Seluruh riwayat top-up ({$count} data) berhasil dihapus.");
    }

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
        $user = auth()->user();
        $adminCityIds = ($user && $user->role === 'admin') ? $user->getAdminCityIds() : [];

        $query = BalanceTransaction::where('id', $transactionId)
            ->where('status', 'waiting_approval');

        if (!empty($adminCityIds)) {
            $query->where(function ($q) use ($adminCityIds) {
                $q->whereHas('user', function ($sq) use ($adminCityIds) {
                    $sq->whereIn('city_id', $adminCityIds);
                })->orWhereHas('user', function ($sq) {
                    $sq->whereNull('city_id');
                });
            });
        }

        $transaction = $query->first();

        if (!$transaction) {
            session()->flash('error', 'Request tidak valid, sudah diproses, atau di luar wilayah kelola Anda.');
            return;
        }

        try {
            \DB::beginTransaction();

            // Update transaction status to 'completed' (BalanceTransactionObserver will automatically recalculate user balance)
            $transaction->update([
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'processed_at' => now(),
            ]);

            \DB::commit();

            // Send notification to customer
            if ($transaction->user) {
                $transaction->user->notify(new TopupApproved($transaction));
            }

            session()->flash('success', 'Request top-up berhasil disetujui! Saldo customer telah ditambahkan.');

            $this->closeModal();

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving topup by admin: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|max:500',
        ], [
            'rejectionReason.required' => 'Alasan penolakan harus diisi',
        ]);

        if (!$this->selectedTransaction || $this->selectedTransaction->status !== 'waiting_approval') {
            session()->flash('error', 'Request tidak valid atau sudah diproses.');
            return;
        }

        $user = auth()->user();
        $adminCityIds = ($user && $user->role === 'admin') ? $user->getAdminCityIds() : [];

        if (!empty($adminCityIds)) {
            $txUserCityId = $this->selectedTransaction->user?->city_id;
            if ($txUserCityId !== null && !in_array($txUserCityId, $adminCityIds)) {
                session()->flash('error', 'Anda tidak memiliki hak akses untuk memproses transaksi di luar wilayah kelola.');
                $this->closeModal();
                return;
            }
        }

        try {
            // Update transaction status
            $this->selectedTransaction->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $this->rejectionReason,
            ]);

            // Send notification to customer
            if ($this->selectedTransaction->user) {
                $this->selectedTransaction->user->notify(new TopupRejected($this->selectedTransaction));
            }

            session()->flash('success', 'Request top-up telah ditolak.');

            $this->closeModal();

        } catch (\Exception $e) {
            \Log::error('Error rejecting topup by admin: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = auth()->user();

        $adminCityIds = ($user && $user->role === 'admin') ? $user->getAdminCityIds() : [];

        // 1. Pending Table Query
        $pendingQuery = BalanceTransaction::topupRequests()
            ->where('status', 'waiting_approval')
            ->with(['user', 'user.city']);

        if (!empty($adminCityIds)) {
            $pendingQuery->where(function ($q) use ($adminCityIds) {
                $q->whereHas('user', function ($sq) use ($adminCityIds) {
                    $sq->whereIn('city_id', $adminCityIds);
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

        $pendingRequests = $pendingQuery->orderBy('created_at', 'desc')
            ->paginate($this->perPagePending, ['*'], 'pendingPage');

        // 2. History Table Query
        $historyQuery = BalanceTransaction::topupRequests()
            ->whereIn('status', ['completed', 'rejected', 'failed'])
            ->with(['user', 'user.city']);

        if (!empty($adminCityIds)) {
            $historyQuery->where(function ($q) use ($adminCityIds) {
                $q->whereHas('user', function ($sq) use ($adminCityIds) {
                    $sq->whereIn('city_id', $adminCityIds);
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
        $baseCityScope = function ($q) use ($adminCityIds) {
            if (!empty($adminCityIds)) {
                $q->where(function ($sq) use ($adminCityIds) {
                    $sq->whereHas('user', function ($ssq) use ($adminCityIds) {
                        $ssq->whereIn('city_id', $adminCityIds);
                    })->orWhereHas('user', function ($ssq) {
                        $ssq->whereNull('city_id');
                    });
                });
            }
        };

        $totalPendingCount = BalanceTransaction::topupRequests()
            ->where('status', 'waiting_approval')
            ->where($baseCityScope)
            ->count();

        $totalCompletedCount = BalanceTransaction::topupRequests()
            ->where('status', 'completed')
            ->where($baseCityScope)
            ->count();

        $totalRejectedCount = BalanceTransaction::topupRequests()
            ->where('status', 'rejected')
            ->where($baseCityScope)
            ->count();

        $totalHistoryCount = BalanceTransaction::topupRequests()
            ->whereIn('status', ['completed', 'rejected', 'failed'])
            ->where($baseCityScope)
            ->count();
        
        $approvedToday = BalanceTransaction::topupRequests()
            ->where('status', 'completed')
            ->whereNotNull('approved_by')
            ->whereDate('approved_at', now()->toDateString())
            ->where($baseCityScope)
            ->count();
            
        $totalPendingAmount = BalanceTransaction::topupRequests()
            ->where('status', 'waiting_approval')
            ->where($baseCityScope)
            ->sum('amount');
            
        $totalApprovedAmountToday = BalanceTransaction::topupRequests()
            ->where('status', 'completed')
            ->whereNotNull('approved_by')
            ->whereDate('approved_at', now()->toDateString())
            ->where($baseCityScope)
            ->sum('amount');

        $availableYears = BalanceTransaction::topupRequests()
            ->whereIn('status', ['completed', 'rejected', 'failed'])
            ->where($baseCityScope)
            ->selectRaw(\App\Support\Sql::yearExpression())
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        $curYear = (int) date('Y');
        if (empty($availableYears)) {
            $availableYears = [$curYear];
        } elseif (!in_array($curYear, $availableYears)) {
            array_unshift($availableYears, $curYear);
        }

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
            'availableYears' => $availableYears,
        ]);
    }
}
