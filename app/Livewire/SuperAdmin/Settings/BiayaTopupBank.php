<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AppSetting;

use App\Models\BalanceTransaction;
use Carbon\Carbon;

#[Layout('layouts.superadmin')]
#[Title('Biaya Admin Top-Up & Bank - Super Admin')]
class BiayaTopupBank extends Component
{
    public $tier1_limit;
    public $tier1_fee;
    public $tier2_limit;
    public $tier2_fee;
    public $tier3_percentage;
    public $tier3_max;
    
    public $payment_banks = [];

    // Modal state for Bank Management
    public bool $showBankModal = false;
    public $editingBankIndex = null;
    public string $bank_code = '';
    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $bank_account_name = '';
    public bool $bank_enabled = true;

    protected function rules()
    {
        return [
            'tier1_limit'      => 'required|numeric|min:0',
            'tier1_fee'        => 'required|numeric|min:0',
            'tier2_limit'      => 'required|numeric|min:0',
            'tier2_fee'        => 'required|numeric|min:0',
            'tier3_percentage' => 'required|numeric|min:0|max:100',
            'tier3_max'        => 'required|numeric|min:0',
        ];
    }

    public function mount()
    {
        // Load top-up fee settings
        $this->tier1_limit = (int) AppSetting::get('topup_tier1_limit', 50000);
        $this->tier1_fee = (int) AppSetting::get('topup_tier1_fee', 5000);
        $this->tier2_limit = (int) AppSetting::get('topup_tier2_limit', 100000);
        $this->tier2_fee = (int) AppSetting::get('topup_tier2_fee', 7500);
        $this->tier3_percentage = (float) AppSetting::get('topup_tier3_percentage', 3);
        $this->tier3_max = (int) AppSetting::get('topup_tier3_max', 15000);

        // Load payment banks config (stored as JSON)
        $paymentMethods = json_decode((string) AppSetting::get('topup_payment_methods', '{}'), true) ?: [];
        $this->payment_banks = $paymentMethods['banks'] ?? [
            ['code' => 'bca', 'name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'mandiri', 'name' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'bni', 'name' => 'BNI', 'account_number' => '5555666677', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'bri', 'name' => 'BRI', 'account_number' => '8888999900', 'account_name' => 'PT sayabantu', 'enabled' => true],
        ];
    }

    public function openAddBankModal()
    {
        $this->resetBankForm();
        $this->editingBankIndex = null;
        $this->showBankModal = true;
    }

    public function openEditBankModal($index)
    {
        if (!isset($this->payment_banks[$index])) {
            return;
        }

        $bank = $this->payment_banks[$index];
        $this->editingBankIndex = (int) $index;
        $this->bank_code = (string) ($bank['code'] ?? '');
        $this->bank_name = (string) ($bank['name'] ?? '');
        $this->bank_account_number = (string) ($bank['account_number'] ?? '');
        $this->bank_account_name = (string) ($bank['account_name'] ?? '');
        $this->bank_enabled = !empty($bank['enabled']);
        $this->showBankModal = true;
    }

    public function closeBankModal()
    {
        $this->showBankModal = false;
        $this->resetBankForm();
    }

    public function resetBankForm()
    {
        $this->bank_code = '';
        $this->bank_name = '';
        $this->bank_account_number = '';
        $this->bank_account_name = '';
        $this->bank_enabled = true;
        $this->resetErrorBag(['bank_code', 'bank_name', 'bank_account_number', 'bank_account_name']);
    }

    public function saveBankModal()
    {
        $this->validate([
            'bank_code'           => 'required|string|max:30',
            'bank_name'           => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name'   => 'required|string|max:150',
        ], [
            'bank_code.required'           => 'Kode bank wajib diisi (contoh: bca, mandiri).',
            'bank_name.required'           => 'Nama bank wajib diisi.',
            'bank_account_number.required' => 'Nomor rekening wajib diisi.',
            'bank_account_name.required'   => 'Nama pemilik rekening wajib diisi.',
        ]);

        $bankData = [
            'code'           => strtolower(trim($this->bank_code)),
            'name'           => trim($this->bank_name),
            'account_number' => trim($this->bank_account_number),
            'account_name'   => trim($this->bank_account_name),
            'enabled'        => (bool) $this->bank_enabled,
        ];

        if ($this->editingBankIndex !== null && isset($this->payment_banks[$this->editingBankIndex])) {
            $this->payment_banks[$this->editingBankIndex] = $bankData;
            $msg = 'Rekening ' . $bankData['name'] . ' berhasil diperbarui.';
        } else {
            $this->payment_banks[] = $bankData;
            $msg = 'Rekening ' . $bankData['name'] . ' berhasil ditambahkan.';
        }

        $this->persistPaymentBanks();

        $this->showBankModal = false;
        $this->resetBankForm();

        $this->dispatch('bankSaved', message: $msg);
        session()->flash('bank_message', $msg);
    }

    public function toggleBankStatus($index)
    {
        if (isset($this->payment_banks[$index])) {
            $this->payment_banks[$index]['enabled'] = !$this->payment_banks[$index]['enabled'];
            $this->persistPaymentBanks();
            $statusText = $this->payment_banks[$index]['enabled'] ? 'diaktifkan' : 'dinonaktifkan';
            $msg = 'Rekening ' . ($this->payment_banks[$index]['name'] ?? 'Bank') . ' berhasil ' . $statusText . '.';
            $this->dispatch('bankSaved', message: $msg);
            session()->flash('bank_message', $msg);
        }
    }

    public function removeBank($index)
    {
        if (isset($this->payment_banks[$index])) {
            $deletedName = $this->payment_banks[$index]['name'] ?? 'Bank';
            unset($this->payment_banks[$index]);
            $this->payment_banks = array_values($this->payment_banks);
            $this->persistPaymentBanks();
            $msg = 'Rekening ' . $deletedName . ' berhasil dihapus.';
            $this->dispatch('bankSaved', message: $msg);
            session()->flash('bank_message', $msg);
        }
    }

    private function persistPaymentBanks()
    {
        $paymentMethods = [
            'banks' => array_values(array_map(function($b) {
                return [
                    'code'           => (string) ($b['code'] ?? ''),
                    'name'           => (string) ($b['name'] ?? ''),
                    'account_number' => isset($b['account_number']) ? (string) $b['account_number'] : null,
                    'account_name'   => isset($b['account_name']) ? (string) $b['account_name'] : null,
                    'enabled'        => !empty($b['enabled']),
                ];
            }, $this->payment_banks ?? [])),
        ];

        AppSetting::set('topup_payment_methods', json_encode($paymentMethods));
    }

    public function save()
    {
        $nominalFields = ['tier1_limit', 'tier1_fee', 'tier2_limit', 'tier2_fee', 'tier3_max'];
        foreach ($nominalFields as $f) {
            if (isset($this->$f)) {
                $this->$f = (int) preg_replace('/\D/', '', (string) $this->$f);
            }
        }

        $this->validate();

        // Save top-up fee settings
        AppSetting::set('topup_tier1_limit', (string) $this->tier1_limit);
        AppSetting::set('topup_tier1_fee', (string) $this->tier1_fee);
        AppSetting::set('topup_tier2_limit', (string) $this->tier2_limit);
        AppSetting::set('topup_tier2_fee', (string) $this->tier2_fee);
        AppSetting::set('topup_tier3_percentage', (string) $this->tier3_percentage);
        AppSetting::set('topup_tier3_max', (string) $this->tier3_max);

        // Also ensure banks are persisted
        $this->persistPaymentBanks();

        session()->flash('message', 'Pengaturan biaya admin top-up berhasil disimpan.');

        $this->dispatch('settingsSaved', message: 'Pengaturan biaya admin top-up berhasil disimpan.');
    }

    public function render()
    {
        // Prepare topup admin fee chart data: daily (30 days), monthly (12 months), yearly (5 years)
        $topupAdminChart = [
            'daily' => ['labels' => [], 'data' => []],
            'monthly' => ['labels' => [], 'data' => []],
            'yearly' => ['labels' => [], 'data' => []],
        ];

        // Daily - last 30 days
        $days = 30;
        $startDay = Carbon::today()->subDays($days - 1);
        for ($i = 0; $i < $days; $i++) {
            $d = $startDay->copy()->addDays($i);
            $topupAdminFee = (float) BalanceTransaction::whereDate('created_at', $d->toDateString())
                ->where('status', 'completed')
                ->sum('admin_fee');
            $topupAdminChart['daily']['labels'][] = $d->format('d M');
            $topupAdminChart['daily']['data'][] = $topupAdminFee;
        }

        // Monthly - last 12 months
        $months = 12;
        $startMonth = Carbon::now()->startOfMonth()->subMonths($months - 1);
        for ($i = 0; $i < $months; $i++) {
            $m = $startMonth->copy()->addMonths($i);
            $topupAdminFee = (float) BalanceTransaction::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->where('status', 'completed')
                ->sum('admin_fee');
            $topupAdminChart['monthly']['labels'][] = $m->format('M Y');
            $topupAdminChart['monthly']['data'][] = $topupAdminFee;
        }

        // Yearly - last 5 years
        $years = 5;
        $startYear = Carbon::now()->startOfYear()->subYears($years - 1);
        for ($i = 0; $i < $years; $i++) {
            $y = $startYear->copy()->addYears($i);
            $topupAdminFee = (float) BalanceTransaction::whereYear('created_at', $y->year)
                ->where('status', 'completed')
                ->sum('admin_fee');
            $topupAdminChart['yearly']['labels'][] = (string) $y->year;
            $topupAdminChart['yearly']['data'][] = $topupAdminFee;
        }

        // Summary stats for top-up admin
        $totalTopupAdmin = (float) BalanceTransaction::where('status', 'completed')->sum('admin_fee');
        $monthTopupAdmin = (float) BalanceTransaction::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->where('status', 'completed')
            ->sum('admin_fee');
        $countTopupAdmin = (int) BalanceTransaction::where('admin_fee', '>', 0)
            ->where('status', 'completed')
            ->count();
        $avgTopupAdmin = $countTopupAdmin ? ($totalTopupAdmin / $countTopupAdmin) : 0;

        // Breakdown by payment channel
        $bankTopupAdmin = (float) BalanceTransaction::where('status', 'completed')
            ->where('payment_method', 'like', 'bank%')
            ->sum('admin_fee');
        $bankTopupCount = (int) BalanceTransaction::where('status', 'completed')
            ->where('payment_method', 'like', 'bank%')
            ->where('admin_fee', '>', 0)
            ->count();

        $qrisTopupAdmin = (float) BalanceTransaction::where('status', 'completed')
            ->where('payment_method', 'qris')
            ->sum('admin_fee');
        $qrisTopupCount = (int) BalanceTransaction::where('status', 'completed')
            ->where('payment_method', 'qris')
            ->where('admin_fee', '>', 0)
            ->count();

        $totalTopupAmount = (float) BalanceTransaction::where('status', 'completed')
            ->where('admin_fee', '>', 0)
            ->sum('amount');
        $avgTopupAmount = $countTopupAdmin ? ($totalTopupAmount / $countTopupAdmin) : 0;
        $feeRatio = $totalTopupAmount > 0 ? round(($totalTopupAdmin / $totalTopupAmount) * 100, 2) : 0;

        $topupBreakdown = [
            'bank' => [
                'total' => $bankTopupAdmin,
                'count' => $bankTopupCount,
                'avg'   => $bankTopupCount ? ($bankTopupAdmin / $bankTopupCount) : 0,
                'percent' => $totalTopupAdmin > 0 ? round(($bankTopupAdmin / $totalTopupAdmin) * 100, 1) : 0,
            ],
            'qris' => [
                'total' => $qrisTopupAdmin,
                'count' => $qrisTopupCount,
                'avg'   => $qrisTopupCount ? ($qrisTopupAdmin / $qrisTopupCount) : 0,
                'percent' => $totalTopupAdmin > 0 ? round(($qrisTopupAdmin / $totalTopupAdmin) * 100, 1) : 0,
            ],
            'volume' => [
                'total_amount' => $totalTopupAmount,
                'avg_amount'   => $avgTopupAmount,
                'fee_ratio'    => $feeRatio,
                'count'        => $countTopupAdmin,
            ],
        ];

        return view('superadmin.biaya-topup-bank', compact(
            'topupAdminChart',
            'totalTopupAdmin',
            'monthTopupAdmin',
            'countTopupAdmin',
            'avgTopupAdmin',
            'topupBreakdown'
        ));
    }
}