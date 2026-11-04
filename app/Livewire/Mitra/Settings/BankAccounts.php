<?php

namespace App\Livewire\Mitra\Settings;

use App\Models\BankChannel;
use App\Models\UserBankAccount;
use App\Notifications\TransactionPinOtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.mitra')]
class BankAccounts extends Component
{
    // Modal controls
    public bool $showAddModal = false;
    public bool $showPinModal = false;
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    // Add Account Form
    public ?int $bank_channel_id = null;
    public string $account_number = '';
    public string $account_name = '';
    public bool $is_primary = false;
    public string $account_pin = '';

    // PIN Form
    public string $pinMode = 'setup'; // 'setup', 'change', or 'reset'
    public string $resetMethod = 'password'; // 'password' or 'otp'
    public string $current_pin = '';
    public string $new_pin = '';
    public string $new_pin_confirmation = '';
    public string $account_password = '';
    public string $otp_code = '';
    public bool $otpSent = false;

    // Delete confirmation PIN
    public string $delete_pin = '';

    // Flash message
    public ?string $feedbackMessage = null;
    public ?string $feedbackType = 'success';

    public function mount()
    {
        $this->account_name = $this->getCleanMitraName();
        $user = Auth::user();
        if (request()->query('action') === 'reset_pin') {
            $this->openPinModal('reset');
        } else {
            $this->pinMode = ($user && $user->hasTransactionPin()) ? 'change' : 'setup';
        }
    }

    /**
     * Dapatkan nama mitra bersih tanpa suffix (Mitra)
     */
    public function getCleanMitraName(): string
    {
        $user = Auth::user();
        if (! $user) {
            return '';
        }

        $clean = trim(preg_replace('/\s*\((?:Mitra|Customer)[^)]*\)|\s*(?:Mitra|Customer)\b/i', '', (string) $user->name));

        return $clean ?: (string) $user->name;
    }

    public function render()
    {
        $user = Auth::user();
        $accounts = $user ? $user->bankAccounts()->get() : collect();
        $channels = BankChannel::active()
            ->orderBy('category', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $isCustomer = $user && $user->role === 'customer';
        $layout = $isCustomer ? 'layouts.app' : 'layouts.mitra';

        return view('livewire.mitra.settings.bank-accounts', [
            'user' => $user,
            'accounts' => $accounts,
            'channels' => $channels,
            'isCustomer' => $isCustomer,
        ])->layout($layout);
    }

    public function openAddModal()
    {
        $user = Auth::user();

        // Jika belum buat PIN, wajib buat PIN terlebih dahulu demi keamanan
        if ($user && ! $user->hasTransactionPin()) {
            $this->openPinModal('setup');
            $this->feedbackMessage = 'Silakan atur PIN transaksi 6 digit terlebih dahulu sebelum menambahkan rekening bank.';
            $this->feedbackType = 'info';
            return;
        }

        $this->reset(['bank_channel_id', 'account_number', 'account_pin']);
        $this->account_name = $this->getCleanMitraName();
        $this->is_primary = ($user && $user->bankAccounts()->count() === 0);
        $this->resetErrorBag();
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->reset(['bank_channel_id', 'account_number', 'account_pin']);
        $this->resetErrorBag();
    }

    public function saveAccount()
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        // Periksa apakah PIN terkunci
        if ($user->isPinLocked()) {
            $this->addError('account_pin', 'PIN transaksi Anda terkunci karena salah 3x berturut-turut. Tunggu ' . $user->getPinLockRemainingMinutes() . ' menit.');
            return;
        }

        // Nama akun dikunci otomatis sesuai nama resmi profil mitra
        $accountName = $this->getCleanMitraName();
        $this->account_name = $accountName;

        $rules = [
            'bank_channel_id' => 'required|exists:bank_channels,id',
            'account_number' => 'required|numeric|digits_between:5,25',
            'account_pin' => 'required|digits:6',
        ];

        $messages = [
            'bank_channel_id.required' => 'Pilih bank atau e-wallet tujuan.',
            'bank_channel_id.exists' => 'Pilihan bank / e-wallet tidak valid.',
            'account_number.required' => 'Nomor rekening atau HP e-wallet wajib diisi.',
            'account_number.numeric' => 'Nomor rekening hanya boleh berisi angka.',
            'account_number.digits_between' => 'Nomor rekening harus terdiri dari 5 - 25 digit angka.',
            'account_pin.required' => 'Masukkan PIN transaksi 6 digit Anda.',
            'account_pin.digits' => 'PIN transaksi harus berupa 6 digit angka.',
        ];

        $this->validate($rules, $messages);

        // Verifikasi PIN
        if (! $user->verifyTransactionPin($this->account_pin)) {
            $attemptsLeft = max(0, 3 - (int) $user->pin_attempts);
            if ($attemptsLeft === 0) {
                $this->addError('account_pin', 'PIN salah! Akun Anda dikunci selama 15 menit.');
            } else {
                $this->addError('account_pin', 'PIN transaksi tidak sesuai. Sisa percobaan: ' . $attemptsLeft . ' kali.');
            }
            return;
        }

        $channel = BankChannel::find($this->bank_channel_id);
        if (! $channel || ! $channel->is_active) {
            $this->addError('bank_channel_id', 'Metode bank / e-wallet ini sedang tidak aktif.');
            return;
        }

        // Jika diset sebagai primary atau ini rekening pertama
        $isPrimary = $this->is_primary || ($user->bankAccounts()->count() === 0);

        if ($isPrimary) {
            $user->bankAccounts()->update(['is_primary' => false]);
        }

        $user->bankAccounts()->create([
            'bank_code' => $channel->code,
            'bank_name' => $channel->name,
            'account_number' => $this->account_number,
            'account_name' => $accountName,
            'is_primary' => $isPrimary,
        ]);

        $this->closeAddModal();
        $this->feedbackMessage = 'Rekening ' . $channel->name . ' berhasil ditambahkan.';
        $this->feedbackType = 'success';
    }

    public function setPrimary(int $id)
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $account = $user->bankAccounts()->where('id', $id)->first();
        if ($account) {
            $user->bankAccounts()->update(['is_primary' => false]);
            $account->update(['is_primary' => true]);
            $this->feedbackMessage = 'Rekening utama berhasil diperbarui ke ' . $account->bank_name . '.';
            $this->feedbackType = 'success';
        }
    }

    public function confirmDelete(int $id)
    {
        $user = Auth::user();
        $account = $user ? $user->bankAccounts()->where('id', $id)->first() : null;
        if (! $account) {
            return;
        }

        $this->deleteId = $id;
        $this->delete_pin = '';
        $this->resetErrorBag();
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->delete_pin = '';
        $this->resetErrorBag();
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        if (! $user || ! $this->deleteId) {
            return;
        }

        if ($user->isPinLocked()) {
            $this->addError('delete_pin', 'PIN transaksi Anda terkunci. Tunggu ' . $user->getPinLockRemainingMinutes() . ' menit.');
            return;
        }

        $this->validate([
            'delete_pin' => 'required|digits:6',
        ], [
            'delete_pin.required' => 'Masukkan PIN transaksi Anda untuk menghapus rekening.',
            'delete_pin.digits' => 'PIN transaksi harus 6 digit angka.',
        ]);

        if (! $user->verifyTransactionPin($this->delete_pin)) {
            $attemptsLeft = max(0, 3 - (int) $user->pin_attempts);
            if ($attemptsLeft === 0) {
                $this->addError('delete_pin', 'PIN salah! Akun Anda dikunci selama 15 menit.');
            } else {
                $this->addError('delete_pin', 'PIN transaksi tidak sesuai. Sisa percobaan: ' . $attemptsLeft . ' kali.');
            }
            return;
        }

        $account = $user->bankAccounts()->where('id', $this->deleteId)->first();
        if ($account) {
            $wasPrimary = $account->is_primary;
            $bankName = $account->bank_name;
            $account->delete();

            // Jika yang dihapus adalah primary, jadikan rekening sisa yang paling baru sebagai primary
            if ($wasPrimary) {
                $remaining = $user->bankAccounts()->first();
                if ($remaining) {
                    $remaining->update(['is_primary' => true]);
                }
            }

            $this->closeDeleteModal();
            $this->feedbackMessage = 'Rekening ' . $bankName . ' berhasil dihapus.';
            $this->feedbackType = 'success';
        }
    }

    public function openPinModal(?string $mode = null)
    {
        $user = Auth::user();
        $this->pinMode = $mode ?: (($user && $user->hasTransactionPin()) ? 'change' : 'setup');
        $this->reset(['current_pin', 'new_pin', 'new_pin_confirmation', 'account_password', 'otp_code', 'otpSent']);
        $this->resetMethod = 'password';
        $this->resetErrorBag();
        $this->showPinModal = true;
    }

    public function closePinModal()
    {
        $this->showPinModal = false;
        $this->reset(['current_pin', 'new_pin', 'new_pin_confirmation', 'account_password', 'otp_code', 'otpSent']);
        $this->resetMethod = 'password';
        $this->resetErrorBag();
    }

    public function switchResetMethod(string $method)
    {
        $this->resetMethod = $method;
        $this->reset(['account_password', 'otp_code']);
        $this->resetErrorBag();

        if ($method === 'otp' && ! $this->otpSent) {
            $this->sendOtp();
        }
    }

    public function sendOtp()
    {
        $user = Auth::user();
        if (! $user || ! $user->email) {
            $this->addError('otp_code', 'Alamat email tidak ditemukan pada akun Anda.');
            return;
        }

        $lastSent = Cache::get('pin_reset_otp_time_' . $user->id);
        if ($lastSent && (now()->timestamp - $lastSent < 60)) {
            $remaining = 60 - (now()->timestamp - $lastSent);
            $this->addError('otp_code', "Mohon tunggu {$remaining} detik sebelum meminta kode baru.");
            return;
        }

        $otp = (string) random_int(100000, 999999);

        Cache::put('pin_reset_otp_' . $user->id, $otp, now()->addMinutes(10));
        Cache::put('pin_reset_otp_time_' . $user->id, now()->timestamp, now()->addMinutes(10));

        try {
            $user->notify(new TransactionPinOtpNotification($otp));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mengirim email OTP Reset PIN: ' . $e->getMessage());
        }

        $this->otpSent = true;
        $this->resetErrorBag();

        $this->feedbackMessage = "Kode OTP 6 digit telah dikirim ke email {$this->getMaskedEmail()}. Silakan periksa kotak masuk/spam email Anda.";
        $this->feedbackType = 'info';
    }

    public function getMaskedEmail(): string
    {
        $email = Auth::user()?->email ?? '';
        if (! $email || ! str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', min(4, $len - 2)) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }

    public function savePin()
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->pinMode === 'setup') {
            $this->validate([
                'new_pin' => 'required|digits:6',
                'new_pin_confirmation' => 'required|same:new_pin',
            ], [
                'new_pin.required' => 'PIN transaksi 6 digit wajib diisi.',
                'new_pin.digits' => 'PIN transaksi harus berupa 6 digit angka.',
                'new_pin_confirmation.required' => 'Konfirmasi PIN transaksi wajib diisi.',
                'new_pin_confirmation.same' => 'Konfirmasi PIN tidak cocok dengan PIN baru.',
            ]);

            $user->setTransactionPin($this->new_pin);
            $this->closePinModal();
            $this->pinMode = 'change';
            $this->feedbackMessage = 'PIN transaksi 6 digit Anda berhasil dibuat! Simpan dan jaga kerahasiaannya.';
            $this->feedbackType = 'success';
        } elseif ($this->pinMode === 'reset') {
            if ($this->resetMethod === 'otp') {
                // Mode 'reset' via Kode OTP Email
                $this->validate([
                    'otp_code' => 'required|digits:6',
                    'new_pin' => 'required|digits:6',
                    'new_pin_confirmation' => 'required|same:new_pin',
                ], [
                    'otp_code.required' => 'Kode OTP 6 digit wajib diisi.',
                    'otp_code.digits' => 'Kode OTP harus berupa 6 digit angka.',
                    'new_pin.required' => 'PIN transaksi baru wajib diisi.',
                    'new_pin.digits' => 'PIN transaksi harus berupa 6 digit angka.',
                    'new_pin_confirmation.required' => 'Konfirmasi PIN baru wajib diisi.',
                    'new_pin_confirmation.same' => 'Konfirmasi PIN tidak cocok dengan PIN baru.',
                ]);

                $cachedOtp = Cache::get('pin_reset_otp_' . $user->id);
                if (! $cachedOtp || $cachedOtp !== trim($this->otp_code)) {
                    $this->addError('otp_code', 'Kode OTP salah atau telah kedaluwarsa. Silakan minta kode baru.');
                    return;
                }

                Cache::forget('pin_reset_otp_' . $user->id);
                Cache::forget('pin_reset_otp_time_' . $user->id);

                $user->setTransactionPin($this->new_pin);
                $user->resetPinAttempts();
                $this->closePinModal();
                $this->pinMode = 'change';
                $this->feedbackMessage = 'PIN transaksi berhasil direset menggunakan verifikasi OTP Email!';
                $this->feedbackType = 'success';
            } else {
                // Mode 'reset' via Kata Sandi Akun
                $this->validate([
                    'account_password' => 'required|string',
                    'new_pin' => 'required|digits:6',
                    'new_pin_confirmation' => 'required|same:new_pin',
                ], [
                    'account_password.required' => 'Kata sandi akun wajib diisi untuk verifikasi identitas.',
                    'new_pin.required' => 'PIN transaksi baru wajib diisi.',
                    'new_pin.digits' => 'PIN transaksi harus berupa 6 digit angka.',
                    'new_pin_confirmation.required' => 'Konfirmasi PIN baru wajib diisi.',
                    'new_pin_confirmation.same' => 'Konfirmasi PIN tidak cocok dengan PIN baru.',
                ]);

                if (! Hash::check($this->account_password, $user->password)) {
                    $this->addError('account_password', 'Kata sandi akun salah. Pastikan memasukkan kata sandi akun SayaBantu Anda.');
                    return;
                }

                $user->setTransactionPin($this->new_pin);
                $user->resetPinAttempts();
                $this->closePinModal();
                $this->pinMode = 'change';
                $this->feedbackMessage = 'PIN transaksi berhasil direset menggunakan verifikasi kata sandi!';
                $this->feedbackType = 'success';
            }
        } else {
            // Mode 'change'
            if ($user->isPinLocked()) {
                $this->addError('current_pin', 'PIN Anda terkunci karena salah 3x berturut-turut. Tunggu ' . $user->getPinLockRemainingMinutes() . ' menit.');
                return;
            }

            $this->validate([
                'current_pin' => 'required|digits:6',
                'new_pin' => 'required|digits:6|different:current_pin',
                'new_pin_confirmation' => 'required|same:new_pin',
            ], [
                'current_pin.required' => 'PIN saat ini wajib diisi.',
                'current_pin.digits' => 'PIN saat ini harus berupa 6 digit angka.',
                'new_pin.required' => 'PIN baru wajib diisi.',
                'new_pin.digits' => 'PIN baru harus berupa 6 digit angka.',
                'new_pin.different' => 'PIN baru harus berbeda dari PIN saat ini.',
                'new_pin_confirmation.required' => 'Konfirmasi PIN baru wajib diisi.',
                'new_pin_confirmation.same' => 'Konfirmasi PIN tidak cocok dengan PIN baru.',
            ]);

            if (! $user->verifyTransactionPin($this->current_pin)) {
                $attemptsLeft = max(0, 3 - (int) $user->pin_attempts);
                if ($attemptsLeft === 0) {
                    $this->addError('current_pin', 'PIN saat ini salah! Akun Anda dikunci selama 15 menit.');
                } else {
                    $this->addError('current_pin', 'PIN saat ini salah. Sisa percobaan: ' . $attemptsLeft . ' kali.');
                }
                return;
            }

            $user->setTransactionPin($this->new_pin);
            $this->closePinModal();
            $this->feedbackMessage = 'PIN transaksi berhasil diperbarui!';
            $this->feedbackType = 'success';
        }
    }
}

