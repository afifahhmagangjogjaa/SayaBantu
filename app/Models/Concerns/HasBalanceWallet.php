<?php

namespace App\Models\Concerns;

use App\Models\BalanceTransaction;
use App\Models\UserBalance;

/**
 * Saldo user: relasi saldo, mutasi, dan penarikan.
 * Diekstrak dari App\Models\User agar model tidak terlalu besar.
 */
trait HasBalanceWallet
{
    public function balance()
    {
        return $this->hasOne(UserBalance::class);
    }

    public function adjustBalance(int $amountDelta): void
    {
        // Use UserBalance helper methods to modify balance and record transactions.
        $userBalance = $this->balance()->first();
        if (!$userBalance) {
            $userBalance = $this->balance()->create(['balance' => 0]);
        }

        if ($amountDelta === 0)
            return;

        if ($amountDelta > 0) {
            // Refund / topup
            $userBalance->addBalance($amountDelta, 'refund');
        } else {
            // Deduction
            $userBalance->deductBalance(abs($amountDelta), 'withdraw_deduction');
        }
    }

    public function getBalanceAttribute()
    {
        // Prefer UserBalance row
        $userBalance = $this->getRelationValue('balance') ?? $this->balance()->first();
        if ($userBalance && isset($userBalance->balance)) {
            // return integer rounded value (Rupiah)
            return (int) round($userBalance->balance);
        }

        // Fallback to users.balance column if present
        return isset($this->attributes['balance']) ? (int) $this->attributes['balance'] : 0;
    }

    public function transactions()
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    public function hasPendingOrProcessingWithdraws(): bool
    {
        return $this->withdrawRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
    }
}
