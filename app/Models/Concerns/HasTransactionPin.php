<?php

namespace App\Models\Concerns;

use App\Models\UserBankAccount;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

/**
 * Trait pengelolaan PIN Transaksi 6 digit dan Rekening Bank Mitra
 */
trait HasTransactionPin
{
    /**
     * Relasi ke daftar rekening bank / e-wallet tersimpan
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(UserBankAccount::class)->orderByDesc('is_primary')->orderBy('created_at');
    }

    /**
     * Rekening utama (default) atau rekening pertama
     */
    public function primaryBankAccount(): ?UserBankAccount
    {
        return $this->bankAccounts()->where('is_primary', true)->first() ?: $this->bankAccounts()->first();
    }

    /**
     * Periksa apakah pengguna sudah mengatur PIN transaksi
     */
    public function hasTransactionPin(): bool
    {
        return ! empty($this->transaction_pin);
    }

    /**
     * Pasang / perbarui PIN transaksi 6 digit (disimpan dalam bentuk Hash)
     */
    public function setTransactionPin(string $pin): void
    {
        $this->update([
            'transaction_pin' => Hash::make($pin),
            'pin_attempts' => 0,
            'pin_locked_until' => null,
        ]);
    }

    /**
     * Periksa apakah input PIN transaksi sesuai
     */
    public function verifyTransactionPin(string $pin): bool
    {
        if ($this->isPinLocked()) {
            return false;
        }

        if (Hash::check($pin, (string) $this->transaction_pin)) {
            $this->resetPinAttempts();

            return true;
        }

        $this->recordPinFailedAttempt();

        return false;
    }

    /**
     * Periksa apakah PIN transaksi sedang dikunci karena salah berulang kali
     */
    public function isPinLocked(): bool
    {
        return $this->pin_locked_until && Carbon::now()->lessThan($this->pin_locked_until);
    }

    /**
     * Dapatkan sisa menit penguncian PIN
     */
    public function getPinLockRemainingMinutes(): int
    {
        if (! $this->isPinLocked()) {
            return 0;
        }

        $diff = Carbon::now()->diffInMinutes($this->pin_locked_until, false);

        return max(1, (int) ceil($diff));
    }

    /**
     * Catat percobaan PIN yang salah dan kunci jika mencapai batas (3 kali)
     */
    public function recordPinFailedAttempt(): void
    {
        $attempts = (int) ($this->pin_attempts ?? 0) + 1;
        $lockedUntil = null;

        if ($attempts >= 3) {
            // Kunci selama 15 menit
            $lockedUntil = Carbon::now()->addMinutes(15);
        }

        $this->update([
            'pin_attempts' => $attempts,
            'pin_locked_until' => $lockedUntil,
        ]);
    }

    /**
     * Reset penghitung percobaan salah
     */
    public function resetPinAttempts(): void
    {
        if ($this->pin_attempts > 0 || $this->pin_locked_until !== null) {
            $this->update([
                'pin_attempts' => 0,
                'pin_locked_until' => null,
            ]);
        }
    }
}

