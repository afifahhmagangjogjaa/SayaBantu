<?php

namespace App\Models\Concerns;

use App\Enums\HelpStatus;

/**
 * Scope dan predikat status untuk model Help.
 * Diekstrak dari App\Models\Help agar model tidak menjadi terlalu besar.
 */
trait HasHelpStatus
{
    public function scopeApproved($query)
    {
        return $query->whereIn('status', HelpStatus::availableValues());
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', HelpStatus::availableValues())->whereNull('mitra_id');
    }

    public function scopeTaken($query)
    {
        return $query->whereIn('status', HelpStatus::activeValues());
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', HelpStatus::expand(HelpStatus::SELESAI));
    }

    /**
     * Bantuan yang sedang aktif ditangani mitra (belum tuntas).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', HelpStatus::activeValues());
    }

    /**
     * Bantuan yang sudah berakhir (selesai / dibatalkan / ditolak).
     */
    public function scopeFinished($query)
    {
        return $query->whereIn('status', HelpStatus::finalValues());
    }

    /**
     * Filter berdasarkan satu atau banyak status, sekaligus mengenali nilai
     * legacy ('taken', 'completed', 'cancelled', 'approved', ...).
     *
     * Contoh: Help::withStatus(HelpStatus::SELESAI, HelpStatus::DIBATALKAN)
     */
    public function scopeWithStatus($query, string|HelpStatus|array ...$statuses)
    {
        $flat = [];
        foreach ($statuses as $status) {
            foreach ((array) $status as $item) {
                $flat[] = $item instanceof HelpStatus ? $item->value : (string) $item;
            }
        }

        return $query->whereIn('status', HelpStatus::expand($flat));
    }

    public function statusEnum(): ?HelpStatus
    {
        return HelpStatus::resolve($this->status);
    }

    public function getStatusLabelAttribute(): string
    {
        return HelpStatus::labelFor($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return HelpStatus::badgeClassFor($this->status);
    }

    /*
    |--------------------------------------------------------------------------
    | Predikat status (dipakai di Blade supaya tidak menulis string literal)
    |--------------------------------------------------------------------------
    */

    public function isMenungguMitra(): bool
    {
        return in_array($this->status, HelpStatus::availableValues(), true);
    }

    public function isSelesai(): bool
    {
        return in_array($this->status, HelpStatus::expand(HelpStatus::SELESAI), true);
    }

    public function isDibatalkan(): bool
    {
        return in_array($this->status, HelpStatus::expand(HelpStatus::DIBATALKAN), true);
    }

    public function isDitolak(): bool
    {
        return in_array($this->status, HelpStatus::expand(HelpStatus::REJECTED), true);
    }

    /** Mitra sudah mengambil order ini. */
    public function isMemperolehMitra(): bool
    {
        return $this->statusEnum() === HelpStatus::MEMPEROLEH_MITRA;
    }

    /** Aktif: mitra sudah terhubung dan pekerjaan belum tuntas. */
    public function isAktif(): bool
    {
        return $this->statusEnum()?->isActive() ?? false;
    }

    /** Tidak ada transisi status lagi (selesai / dibatalkan / ditolak). */
    public function isBerakhir(): bool
    {
        return $this->statusEnum()?->isFinal() ?? false;
    }

    /** Menunggu konfirmasi penyelesaian dari customer. */
    public function isMenungguKonfirmasi(): bool
    {
        return $this->statusEnum() === HelpStatus::WAITING_CUSTOMER_CONFIRMATION;
    }

    /** Mitra mengajukan pembatalan, menunggu keputusan customer/admin. */
    public function isPartnerMintaBatal(): bool
    {
        return $this->statusEnum() === HelpStatus::PARTNER_CANCEL_REQUESTED;
    }
}
