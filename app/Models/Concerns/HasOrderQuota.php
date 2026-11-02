<?php

namespace App\Models\Concerns;

/**
 * Kuota & aturan bisnis: berapa order yang boleh diambil/dibuat, batas topup dan withdraw.
 * Diekstrak dari App\Models\User agar model tidak terlalu besar.
 */
trait HasOrderQuota
{
    public function getMitraHelpsCount(): int
    {
        return $this->takenHelps()
            ->whereNotIn('status', ['cancelled', 'dibatalkan'])
            ->count();
    }

    public function getActiveOrdersCount(): int
    {
        return $this->getMitraHelpsCount();
    }

    public function getOngoingOrdersCount(): int
    {
        $inProgressStatuses = [
            'memperoleh_mitra',
            'taken',
            'partner_on_the_way',
            'partner_arrived',
            'in_progress',
            'sedang_diproses',
            'partner_cancel_requested',
            'diproses_mitra',
            'waiting_customer_confirmation',
        ];

        return $this->takenHelps()
            ->whereIn('status', $inProgressStatuses)
            ->count();
    }

    public function hasActiveOngoingOrder(): bool
    {
        return $this->getOngoingOrdersCount() >= 1;
    }

    public function getMaxActiveOrdersLimit(): int
    {
        return $this->hasVerifiedEmail() ? PHP_INT_MAX : 0;
    }

    public function canTakeMoreOrders(): bool
    {
        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($this->hasActiveOngoingOrder()) {
            return false;
        }

        return true;
    }

    public function canTakeHelp(): bool
    {
        return $this->isProfileComplete() && (bool) $this->verified && $this->hasVerifiedEmail() && ! $this->hasActiveOngoingOrder();
    }

    public function getCannotTakeHelpReason(): ?string
    {
        if (! $this->isProfileComplete()) {
            $missing = implode(', ', $this->getMissingProfileFields());

            return "Harap lengkapi biodata profil Anda ({$missing}) terlebih dahulu sebelum mengambil bantuan.";
        }

        if (! $this->hasVerifiedEmail()) {
            return 'Email Anda belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu untuk dapat mengambil bantuan.';
        }

        if (! $this->verified) {
            return 'Akun Anda belum terverifikasi KTP oleh Admin. Silakan tunggu proses verifikasi selesai sebelum dapat mengambil bantuan.';
        }

        if ($this->hasActiveOngoingOrder()) {
            return 'Anda masih memiliki 1 order aktif yang sedang berjalan. Harap selesaikan order tersebut terlebih dahulu sebelum mengambil order baru.';
        }

        return null;
    }

    public function canTopup(): bool
    {
        return $this->isProfileComplete() && (bool) $this->verified;
    }

    public function getCannotTopupReason(): ?string
    {
        if (! $this->isProfileComplete()) {
            $missing = implode(', ', $this->getMissingProfileFields());

            return "Harap lengkapi profil Anda ({$missing}) terlebih dahulu sebelum melakukan Top Up.";
        }

        if (! $this->verified) {
            return 'Akun Anda sedang menunggu verifikasi KTP oleh Admin. Anda belum dapat melakukan Top Up sebelum KTP diverifikasi.';
        }

        return null;
    }

    public function getCustomerHelpsCount(): int
    {
        return $this->helps()
            ->whereNotIn('status', ['cancelled', 'dibatalkan'])
            ->count();
    }

    public function getActiveCustomerHelpsCount(): int
    {
        return $this->getCustomerHelpsCount();
    }

    public function getMaxActiveCustomerHelpsLimit(): int
    {
        return $this->hasVerifiedEmail() ? PHP_INT_MAX : 2;
    }

    public function canCreateMoreHelps(): bool
    {
        if ($this->hasVerifiedEmail()) {
            return true;
        }

        return $this->getCustomerHelpsCount() < 2;
    }

    public function canWithdrawBalance(): bool
    {
        return $this->hasVerifiedEmail() && $this->isProfileComplete();
    }

    /**
     * Cek apakah user terkena batas penarikan saldo 1x dalam 24 jam.
     */
    public function getWithdraw24hLimitStatus(): array
    {
        $latest = \App\Models\WithdrawRequest::where('user_id', $this->id)
            ->whereNotIn('status', [\App\Models\WithdrawRequest::STATUS_FAILED, 'rejected'])
            ->where('created_at', '>=', now()->subHours(24))
            ->orderByDesc('created_at')
            ->first();

        if (! $latest) {
            return [
                'allowed' => true,
                'available_at' => null,
                'remaining_text' => null,
                'latest_withdraw' => null,
            ];
        }

        $availableAt = $latest->created_at->copy()->addHours(24);
        $diffMinutes = max(1, (int) now()->diffInMinutes($availableAt, false));
        $hours = intdiv($diffMinutes, 60);
        $minutes = $diffMinutes % 60;
        $remainingText = ($hours > 0 ? "{$hours} jam " : '') . "{$minutes} menit";

        return [
            'allowed' => false,
            'available_at' => $availableAt,
            'remaining_text' => $remainingText,
            'latest_withdraw' => $latest,
        ];
    }
}
