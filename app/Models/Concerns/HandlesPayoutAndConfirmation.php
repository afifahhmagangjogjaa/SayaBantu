<?php

namespace App\Models\Concerns;

/**
 * Perhitungan pendapatan mitra dan konfirmasi penyelesaian oleh customer.
 * Diekstrak dari App\Models\Help agar model tidak terlalu besar.
 */
trait HandlesPayoutAndConfirmation
{
    public function getMitraPayoutAmount(): float
    {
        if ((float) $this->net_mitra_amount > 0) {
            return (float) $this->net_mitra_amount;
        }

        $base = (float) $this->base_amount > 0 ? (float) $this->base_amount : (float) $this->amount;

        if ($base > 0) {
            if ((float) $this->mitra_fee_amount > 0) {
                return max(0, round($base - (float) $this->mitra_fee_amount, 2));
            }
            if ((float) $this->mitra_fee_percent > 0) {
                return max(0, round($base * (1 - ((float) $this->mitra_fee_percent / 100)), 2));
            }
            return $base;
        }

        return 0.0;
    }

    public function getCustomerConfirmationDeadline(): ?\Carbon\Carbon
    {
        $base = $this->service_completed_at ?? $this->updated_at;
        return $base ? $base->copy()->addHours(24) : null;
    }

    public function isCustomerConfirmationExpired(): bool
    {
        if ($this->status !== 'waiting_customer_confirmation') {
            return false;
        }

        $deadline = $this->getCustomerConfirmationDeadline();
        return $deadline ? now()->greaterThanOrEqualTo($deadline) : false;
    }

    public function autoConfirmIfExpired(): bool
    {
        if (!$this->isCustomerConfirmationExpired()) {
            return false;
        }

        return (bool) \App\Console\Commands\AutoConfirmHelps::autoConfirmSingleHelp($this);
    }
}
