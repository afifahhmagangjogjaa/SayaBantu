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

    public function getMaxActiveOrdersLimit(): int
    {
        return $this->hasVerifiedEmail() ? 999 : 2;
    }

    public function canTakeMoreOrders(): bool
    {
        return $this->getMitraHelpsCount() < $this->getMaxActiveOrdersLimit();
    }

    public function canTakeHelp(): bool
    {
        return $this->isProfileComplete() && (bool) $this->verified && $this->canTakeMoreOrders();
    }

    public function getCannotTakeHelpReason(): ?string
    {
        if (!$this->isProfileComplete()) {
            $missing = implode(', ', $this->getMissingProfileFields());
            return "Harap lengkapi biodata profil Anda ({$missing}) terlebih dahulu sebelum mengambil bantuan.";
        }

        if (!$this->verified) {
            return "Akun Anda belum terverifikasi KTP oleh Admin. Silakan tunggu proses verifikasi selesai sebelum dapat mengambil bantuan.";
        }

        if (!$this->canTakeMoreOrders()) {
            if (!$this->hasVerifiedEmail()) {
                return "Akun Anda belum verifikasi email dan telah mencapai batas maksimal 2 bantuan. Silakan verifikasi email Anda terlebih dahulu untuk mengambil bantuan lagi.";
            }
            return "Anda telah mencapai batas maksimal order bantuan.";
        }

        return null;
    }

    public function canTopup(): bool
    {
        return $this->isProfileComplete() && (bool) $this->verified;
    }

    public function getCannotTopupReason(): ?string
    {
        if (!$this->isProfileComplete()) {
            $missing = implode(', ', $this->getMissingProfileFields());
            return "Harap lengkapi profil Anda ({$missing}) terlebih dahulu sebelum melakukan Top Up.";
        }

        if (!$this->verified) {
            return "Akun Anda sedang menunggu verifikasi KTP oleh Admin. Anda belum dapat melakukan Top Up sebelum KTP diverifikasi.";
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
        return $this->hasVerifiedEmail() ? 999 : 2;
    }

    public function canCreateMoreHelps(): bool
    {
        return $this->getCustomerHelpsCount() < $this->getMaxActiveCustomerHelpsLimit();
    }

    public function canWithdrawBalance(): bool
    {
        return $this->hasVerifiedEmail() && $this->isProfileComplete();
    }
}
