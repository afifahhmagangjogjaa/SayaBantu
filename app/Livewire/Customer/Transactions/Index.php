<?php

namespace App\Livewire\Customer\Transactions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BalanceTransaction;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $activeTab = 'masuk'; // 'masuk' (Saldo Masuk) or 'keluar' (Uang Keluar)
    public $selectedTransaction = null;

    protected $queryString = [
        'activeTab' => ['except' => 'masuk'],
    ];

    public function mount()
    {
        $tab = request()->query('tab') ?? request()->query('activeTab');
        if (in_array(strtolower($tab ?? ''), ['keluar', 'out', 'expense', 'mitra'])) {
            $this->activeTab = 'keluar';
        } else {
            $this->activeTab = 'masuk';
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = in_array($tab, ['keluar', 'out', 'expense', 'mitra']) ? 'keluar' : 'masuk';
        $this->resetPage();
    }

    public function showTransaction($id)
    {
        $transaction = BalanceTransaction::with(['help', 'approvedBy'])->find($id);
        
        if (!$transaction || $transaction->user_id !== auth()->id()) {
            session()->flash('error', 'Transaksi tidak ditemukan.');
            return;
        }

        $this->selectedTransaction = [
            'id' => $transaction->id,
            'type' => $transaction->type ?? 'topup',
            'status' => $transaction->status,
            'amount' => $transaction->amount,
            'admin_fee' => $transaction->admin_fee,
            'total_payment' => $transaction->total_payment,
            'description' => $transaction->description,
            'payment_method' => $this->formatPaymentMethod($transaction->payment_method ?? $transaction->payment_type),
            'order_id' => $transaction->order_id,
            'request_code' => $transaction->request_code,
            'reference_id' => $transaction->reference_id,
            'help_title' => $transaction->help?->title,
            'proof_of_payment' => $transaction->proof_of_payment,
            'rejection_reason' => $transaction->rejection_reason,
            'approved_by_name' => $transaction->approvedBy?->name,
            'approved_at' => $transaction->approved_at ? $transaction->approved_at->format('d M Y • H:i') . ' WIB' : null,
            'created_at' => $transaction->created_at ? $transaction->created_at->format('d M Y • H:i') . ' WIB' : '-',
            'created_at_human' => $transaction->created_at ? $transaction->created_at->diffForHumans() : '',
        ];
    }

    public function closeTransaction()
    {
        $this->selectedTransaction = null;
    }

    protected function formatPaymentMethod($method)
    {
        if (!$method) return null;
        return match (strtolower($method)) {
            'bank_bca' => 'Bank BCA',
            'bank_mandiri' => 'Bank Mandiri',
            'bank_bni' => 'Bank BNI',
            'bank_bri' => 'Bank BRI',
            'qris' => 'QRIS',
            default => strtoupper(str_replace('_', ' ', $method)),
        };
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->activeTab === 'masuk') {
            // Tab 1: Saldo Masuk (Top Up, Deposit, Refund/Pengembalian)
            $transactions = BalanceTransaction::where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereIn('type', ['topup', 'deposit'])
                      ->orWhere('description', 'like', '%topup%')
                      ->orWhere('description', 'like', '%top-up%')
                      ->orWhere('description', 'like', '%refund%')
                      ->orWhere('description', 'like', '%pengembalian%');
                })
                ->with(['help'])
                ->latest()
                ->paginate(10);
        } else {
            // Tab 2: Uang Keluar (Pembayaran Bantuan buat Mitra)
            $transactions = BalanceTransaction::where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereIn('type', ['deduction', 'withdraw', 'fee'])
                      ->orWhere('description', 'like', 'Pembayaran%');
                })
                ->with(['help'])
                ->latest()
                ->paginate(10);
        }

        return view('livewire.customer.transactions.index', [
            'transactions' => $transactions,
            'user' => $user,
            'activeTab' => $this->activeTab,
            'selectedTransaction' => $this->selectedTransaction,
        ]);
    }
}
