<?php

namespace App\Livewire\Customer;

use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\AppSetting;
use App\Notifications\TopupRequestSubmitted;
use App\Notifications\NewTopupRequest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TopupRequest extends Component
{
    use WithFileUploads;

    // Step management
    public $currentStep = 1;

    // Step 1 - Form data
    public $amount;
    public $customerName;
    public $customerPhone;
    public $customerEmail;
    public $customerNotes;

    // Step 2 - Payment detail (calculated)
    public $adminFee = 0;
    public $totalPayment = 0;
    // Unique 3-digit code and final transfer amount (includes code)
    public $uniqueCode = null;
    public $uniqueTotal = 0;

    // Step 3 - Payment method
    public $paymentMethod;
    public $proofOfPayment;
    public $proofPreview;

    // Others
    public $requestCode;
    public $transactionId;
    public $availableBanks = [];
    public $qrisEnabled = false;
    public $hasActivePending = false;
    public $pendingTransaction = null;

    protected $rules = [
        'amount' => 'required|numeric|min:10000|max:10000000',
        'customerName' => 'required|string|max:100',
        'customerPhone' => 'required|numeric|digits_between:10,15',
        'customerEmail' => 'nullable|email|max:100',
        'customerNotes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'amount.required' => 'Nominal harus diisi',
        'amount.min' => 'Minimal top-up adalah Rp 10.000',
        'amount.max' => 'Maksimal top-up adalah Rp 10.000.000',
        'customerName.required' => 'Nama lengkap harus diisi',
        'customerPhone.required' => 'Nomor telepon harus diisi',
        'customerPhone.digits_between' => 'Nomor telepon tidak valid',
        'customerEmail.email' => 'Format email tidak valid',
    ];

    public function mount()
    {
        $user = auth()->user();

        if ($user && !$user->canTopup()) {
            session()->flash('error', $user->getCannotTopupReason());
            return redirect()->route('customer.dashboard');
        }

        // ==========================================
        // CEK APAKAH ADA REQUEST YANG MASIH PENDING
        // ==========================================
        $this->checkActivePending();
        
        // Load from session if exists
        $sessionData = session('topup_form_data');
        
        if ($sessionData) {
            $this->currentStep = $sessionData['currentStep'] ?? 1;
            $this->amount = $sessionData['amount'] ?? null;
            $this->customerName = $sessionData['customerName'] ?? $user->name;
            $this->customerPhone = $sessionData['customerPhone'] ?? ($user->phone ?? '');
            $this->customerEmail = $sessionData['customerEmail'] ?? $user->email;
            $this->customerNotes = $sessionData['customerNotes'] ?? null;
            $this->paymentMethod = $sessionData['paymentMethod'] ?? null;
            $this->adminFee = $sessionData['adminFee'] ?? 0;
            $this->totalPayment = $sessionData['totalPayment'] ?? 0;
            $this->uniqueCode = $sessionData['uniqueCode'] ?? null;
            $this->uniqueTotal = $sessionData['uniqueTotal'] ?? 0;
        } else {
            $this->customerName = $user->name;
            $this->customerPhone = $user->phone ?? '';
            $this->customerEmail = $user->email;
        }

        $this->loadPaymentSettings();
    }

    protected function loadPaymentSettings()
    {
        $raw = AppSetting::get('topup_payment_methods', '{}');
        $methods = json_decode((string) $raw, true) ?: [];

        $this->qrisEnabled = $methods['qris']['enabled'] ?? true;

        $defaultBanks = [
            ['code' => 'bca', 'name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'mandiri', 'name' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'bni', 'name' => 'BNI', 'account_number' => '5555666677', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'bri', 'name' => 'BRI', 'account_number' => '8888999900', 'account_name' => 'PT sayabantu', 'enabled' => true],
        ];

        $banks = $methods['banks'] ?? $defaultBanks;

        $this->availableBanks = collect($banks)
            ->filter(fn($bank) => $bank['enabled'] ?? false)
            ->map(fn($bank) => array_merge($bank, ['value' => 'bank_' . ($bank['code'] ?? '')]))
            ->values()
            ->toArray();
    }

    public function setQuickAmount($amount)
    {
        $this->amount = $amount;
        $this->calculateFees();
        $this->dispatch('topup-request-amount-updated', $this->amount);
    }

    public function calculateFees()
    {
        if (!$this->amount) {
            $this->adminFee = 0;
            $this->totalPayment = 0;
            return;
        }

        $amount = floatval($this->amount);

        $tier1_limit = (int) AppSetting::get('topup_tier1_limit', 50000);
        $tier1_fee = (int) AppSetting::get('topup_tier1_fee', 9000);
        $tier2_limit = (int) AppSetting::get('topup_tier2_limit', 100000);
        $tier2_fee = (int) AppSetting::get('topup_tier2_fee', 7500);
        $tier3_percentage = (float) AppSetting::get('topup_tier3_percentage', 3);
        $tier3_max = (int) AppSetting::get('topup_tier3_max', 15000);

        if ($amount < $tier1_limit) {
            $this->adminFee = $tier1_fee;
        } elseif ($amount < $tier2_limit) {
            $this->adminFee = $tier2_fee;
        } else {
            $fee = $amount * ($tier3_percentage / 100);
            $this->adminFee = min($fee, $tier3_max);
        }

        $this->totalPayment = $amount + $this->adminFee;
        $this->ensureUniqueSuffix();
        $this->saveFormData();
    }

    protected function saveFormData()
    {
        session([
            'topup_form_data' => [
                'currentStep' => $this->currentStep,
                'amount' => $this->amount,
                'customerName' => $this->customerName,
                'customerPhone' => $this->customerPhone,
                'customerEmail' => $this->customerEmail,
                'customerNotes' => $this->customerNotes,
                'paymentMethod' => $this->paymentMethod,
                'adminFee' => $this->adminFee,
                'totalPayment' => $this->totalPayment,
                'uniqueCode' => $this->uniqueCode,
                'uniqueTotal' => $this->uniqueTotal,
            ]
        ]);
    }

    public function resetFormData()
    {
        session()->forget('topup_form_data');
        
        $user = auth()->user();
        $this->currentStep = 1;
        $this->amount = null;
        $this->customerName = $user->name;
        $this->customerPhone = $user->phone ?? '';
        $this->customerEmail = $user->email;
        $this->customerNotes = null;
        $this->paymentMethod = null;
        $this->proofOfPayment = null;
        $this->adminFee = 0;
        $this->totalPayment = 0;
        
        session()->flash('success', 'Data form berhasil direset');
    }

    public function checkActivePending()
    {
        $user = auth()->user();
        if ($user) {
            $this->pendingTransaction = BalanceTransaction::where('user_id', $user->id)
                ->where('type', 'topup')
                ->where('status', 'waiting_approval')
                ->latest()
                ->first();

            $this->hasActivePending = !is_null($this->pendingTransaction);
        } else {
            $this->pendingTransaction = null;
            $this->hasActivePending = false;
        }
    }

    public function nextStep()
    {
        $user = auth()->user();
        if ($user && !$user->canTopup()) {
            session()->flash('error', $user->getCannotTopupReason());
            return redirect()->route('customer.dashboard');
        }

        $this->checkActivePending();
        if ($this->hasActivePending) {
            session()->flash('error', 'Anda masih memiliki permintaan top-up yang menunggu persetujuan admin.');
            return;
        }

        if ($this->currentStep == 1) {
            $this->validate();
            $this->calculateFees();
            $this->currentStep = 2;
        } elseif ($this->currentStep == 2) {
            $this->currentStep = 3;
        }
        $this->saveFormData();
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->saveFormData();
        }
    }

    public function selectPaymentMethod($method)
    {
        $this->paymentMethod = $method;
        $this->saveFormData();
    }

    public function submitRequest()
    {
        $user = auth()->user();
        if ($user && !$user->canTopup()) {
            session()->flash('error', $user->getCannotTopupReason());
            return redirect()->route('customer.dashboard');
        }

        // ==========================================
        // DOUBLE CHECK SAAT SUBMIT (MENGHINDARI BYPASS)
        // ==========================================
        $hasPending = BalanceTransaction::where('user_id', $user->id)
            ->where('type', 'topup')
            ->where('status', 'waiting_approval')
            ->exists();

        if ($hasPending) {
            session()->flash('error', 'Anda masih memiliki permintaan top-up yang menunggu persetujuan admin.');
            return redirect()->route('customer.topup.history');
        }

        // Validate step 3
        $this->validate([
            'paymentMethod' => 'required',
            'proofOfPayment' => 'required|image|max:2048|mimes:jpg,jpeg,png',
        ], [
            'paymentMethod.required' => 'Silakan pilih metode pembayaran',
            'proofOfPayment.required' => 'Bukti transfer wajib diupload',
            'proofOfPayment.image' => 'File harus berupa gambar',
            'proofOfPayment.max' => 'Ukuran file maksimal 2MB',
        ]);

        $allowed = array_merge(
            $this->qrisEnabled ? ['qris'] : [],
            array_map(fn($b) => $b['value'], $this->availableBanks)
        );

        if (!in_array($this->paymentMethod, $allowed)) {
            session()->flash('error', 'Metode pembayaran tidak valid');
            return;
        }

        try {
            $proofPath = $this->proofOfPayment->store('proof-of-payment', 'public');

            $this->requestCode = $this->generateRequestCode();

            $this->ensureUniqueSuffix();

            $transaction = BalanceTransaction::create([
                'user_id' => auth()->id(),
                'amount' => $this->amount,
                'admin_fee' => $this->adminFee,
                'total_payment' => $this->uniqueTotal ?: $this->totalPayment,
                'type' => 'topup',
                'description' => 'Top-up saldo via ' . $this->getPaymentMethodName(),
                'status' => 'waiting_approval',
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail,
                'payment_method' => $this->paymentMethod,
                'proof_of_payment' => $proofPath,
                'request_code' => $this->requestCode,
                'customer_notes' => ($this->customerNotes ? $this->customerNotes . ' | ' : '') . 'UniqueCode:' . ($this->uniqueCode ?? '000'),
                'expired_at' => now()->addHours(24),
            ]);

            $this->transactionId = $transaction->id;

            try {
                auth()->user()->notify(new TopupRequestSubmitted($transaction));
            } catch (\Throwable $e) {
                \Log::warning('Gagal kirim notifikasi user: ' . $e->getMessage());
            }

            try {
                $this->notifyAdmins($transaction);
            } catch (\Throwable $e) {
                \Log::warning('Gagal kirim notifikasi admin: ' . $e->getMessage());
            }

            $this->dispatch('topupRequestCreated');

            session()->forget('topup_form_data');

            session()->flash('success', 'Request top-up berhasil dikirim! Kode request: ' . $this->requestCode);

            return redirect()->route('customer.topup.history');

        } catch (\Exception $e) {
            \Log::error('Topup request error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function generateRequestCode()
    {
        $date = now()->format('Ymd');
        $lastCode = BalanceTransaction::where('request_code', 'like', "TPU-{$date}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastCode) {
            $parts = explode('-', $lastCode->request_code);
            $sequence = intval($parts[2] ?? 0) + 1;
        }

        return "TPU-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    protected function ensureUniqueSuffix()
    {
        if (!$this->totalPayment) {
            $this->uniqueCode = null;
            $this->uniqueTotal = 0;
            return;
        }

        $total = (int) round($this->totalPayment);
        $base = intdiv($total, 1000) * 1000;

        if ($this->uniqueCode) {
            $existingBase = intdiv((int) $this->uniqueTotal, 1000) * 1000;
            if ($existingBase === $base && (int) $this->uniqueTotal >= $total) {
                return;
            }
        }

        try {
            $code = random_int(1, 999);
        } catch (\Exception $e) {
            $code = mt_rand(1, 999);
        }

        $padded = str_pad($code, 3, '0', STR_PAD_LEFT);
        $uniqueTotal = $base + $code;

        if ($uniqueTotal < $total) {
            $base += 1000;
            $uniqueTotal = $base + $code;
        }

        $this->uniqueCode = $padded;
        $this->uniqueTotal = $uniqueTotal;
    }

    protected function getPaymentMethodName()
    {
        if ($this->paymentMethod === 'qris') {
            return 'QRIS';
        }

        if (str_starts_with($this->paymentMethod, 'bank_')) {
            $code = substr($this->paymentMethod, 5);
            $bank = collect($this->availableBanks)->first(fn($b) => ($b['code'] ?? '') === $code);
            if ($bank) {
                return 'Transfer Bank ' . ($bank['name'] ?? strtoupper($code));
            }
        }

        return 'Transfer Bank';
    }

    protected function notifyAdmins($transaction)
    {
        $customerCity = auth()->user()->city_id;
        $cityAdmins = User::getAdminsForCity($customerCity);

        $superAdmins = User::where('role', 'super_admin')
            ->where('status', 'active')
            ->get();

        $allAdmins = $cityAdmins->merge($superAdmins)->unique('id');

        foreach ($allAdmins as $admin) {
            $admin->notify(new NewTopupRequest($transaction));
        }
    }

    public function render()
    {
        $this->checkActivePending();

        return view('livewire.customer.topup-request');
    }
}