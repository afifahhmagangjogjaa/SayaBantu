<?php

namespace App\Models\Concerns;

/**
 * Perhitungan jadwal, keterlambatan, dan kedaluwarsa order.
 * Diekstrak dari App\Models\Help agar model tidak terlalu besar.
 */
trait HasJourneyTimeline
{
    public function canPartnerStartJourney(): bool
    {
        if ($this->isUrgent() || !$this->isScheduled() || empty($this->scheduled_at)) {
            return true;
        }

        return now()->gte(\Carbon\Carbon::parse($this->scheduled_at)->subHour());
    }

    public function partnerJourneyAvailableAt(): ?\Carbon\Carbon
    {
        if (!$this->isScheduled() || empty($this->scheduled_at)) {
            return null;
        }

        return \Carbon\Carbon::parse($this->scheduled_at)->subHour();
    }

    public function isPartnerIdleOver30Minutes(): bool
    {
        if (!$this->mitra_id) {
            return false;
        }

        // Hanya berlaku saat mitra sudah ditugaskan tetapi belum berangkat/bekerja
        if (!in_array($this->status, ['taken', 'memperoleh_mitra'])) {
            return false;
        }

        // Jika mitra sudah mulai jalan atau mulai bekerja, maka tidak idle
        if (!empty($this->partner_started_moving_at) || !empty($this->partner_started_at)) {
            return false;
        }

        // 1. Pesanan Terjadwal (Scheduled)
        if ($this->isScheduled() || (!empty($this->scheduled_at) && $this->help_type !== 'urgent')) {
            if (empty($this->scheduled_at)) {
                return false;
            }

            // Jika belum masuk waktu berangkat (tombol berangkat pada mitra belum nyala/aktif), mitra belum terlambat
            if (!$this->canPartnerStartJourney()) {
                return false;
            }

            $scheduledAt = \Carbon\Carbon::parse($this->scheduled_at);
            $buttonAvailableAt = $this->partnerJourneyAvailableAt() ?? $scheduledAt->copy()->subHour();
            $takenAt = $this->taken_at 
                ? \Carbon\Carbon::parse($this->taken_at) 
                : ($this->mitra_assigned_at ? \Carbon\Carbon::parse($this->mitra_assigned_at) : $this->created_at);

            // Waktu awal di mana tombol berangkat sudah menyala DAN mitra sudah memegang pesanan
            $effectiveStartTime = ($takenAt && $takenAt->gt($buttonAvailableAt)) ? $takenAt : $buttonAvailableAt;

            // Tombol dan alert baru muncul jika sudah 30 menit sejak tombol nyala / pesanan diambil tanpa ada keberangkatan
            $isOver30Min = now()->gte($effectiveStartTime->copy()->addMinutes(30));
            $isWithinActiveWindow = now()->diffInHours($scheduledAt, false) <= 12;

            return $isOver30Min && $isWithinActiveWindow;
        }

        // 2. Pesanan Urgent / Langsung
        $takenTime = $this->taken_at 
            ? \Carbon\Carbon::parse($this->taken_at) 
            : ($this->mitra_assigned_at 
                ? \Carbon\Carbon::parse($this->mitra_assigned_at) 
                : null);

        if (!$takenTime) {
            return false;
        }

        $isOver30MinPastTaken = now()->gte($takenTime->copy()->addMinutes(30));
        // Batasi rentang wajar (maksimal 12 jam sejak diambil)
        $isWithinActiveWindow = now()->diffInHours($takenTime, false) <= 12;

        return $isOver30MinPastTaken && $isWithinActiveWindow;
    }

    public function getExpectedJourneyStartTime(): ?\Carbon\Carbon
    {
        if ($this->isScheduled() || (!empty($this->scheduled_at) && $this->help_type !== 'urgent')) {
            if (empty($this->scheduled_at)) {
                return null;
            }
            $scheduledAt = \Carbon\Carbon::parse($this->scheduled_at);
            $buttonAvailableAt = $this->partnerJourneyAvailableAt() ?? $scheduledAt->copy()->subHour();
            $takenAt = $this->taken_at 
                ? \Carbon\Carbon::parse($this->taken_at) 
                : ($this->mitra_assigned_at ? \Carbon\Carbon::parse($this->mitra_assigned_at) : $this->created_at);

            return ($takenAt && $takenAt->gt($buttonAvailableAt)) ? $takenAt : $buttonAvailableAt;
        }

        return $this->taken_at 
            ? \Carbon\Carbon::parse($this->taken_at) 
            : ($this->mitra_assigned_at 
                ? \Carbon\Carbon::parse($this->mitra_assigned_at) 
                : ($this->created_at ? \Carbon\Carbon::parse($this->created_at) : null));
    }

    public function wasPartnerLateDeparting(): bool
    {
        if (!$this->mitra_id) {
            return false;
        }

        // Jika saat ini masih diam / belum berangkat:
        if (empty($this->partner_started_moving_at) && empty($this->partner_started_at)) {
            return $this->isPartnerIdleOver30Minutes();
        }

        // Jika mitra SUDAH mulai berangkat:
        $departureTime = $this->partner_started_moving_at 
            ? \Carbon\Carbon::parse($this->partner_started_moving_at) 
            : \Carbon\Carbon::parse($this->partner_started_at);

        $expectedStartTime = $this->getExpectedJourneyStartTime();
        if (!$expectedStartTime) {
            return false;
        }

        // Apakah waktu berangkat mitra >= 30 menit setelah waktu acuan?
        $diffMinutes = $expectedStartTime->diffInMinutes($departureTime, false);
        return $diffMinutes >= 30;
    }

    public function canCustomerCancelDueToDelay(): bool
    {
        if (!$this->mitra_id) {
            return false;
        }

        // Jika mitra sudah tiba di lokasi atau sudah dalam tahap pengerjaan/selesai/batal, hak batal tertutup
        if (!empty($this->partner_arrived_at) || in_array($this->status, [
            'partner_arrived', 
            'in_progress', 
            'sedang_diproses', 
            'waiting_customer_confirmation', 
            'selesai', 
            'completed', 
            'dibatalkan', 
            'cancelled'
        ])) {
            return false;
        }

        // Hanya berlaku saat status taken, memperoleh_mitra, atau partner_on_the_way
        if (!in_array($this->status, ['taken', 'memperoleh_mitra', 'partner_on_the_way'])) {
            return false;
        }

        return $this->wasPartnerLateDeparting();
    }

    public function isExpired(): bool
    {
        if ($this->help_type === 'urgent' && $this->auto_cancel_at) {
            return now()->greaterThan($this->auto_cancel_at);
        }

        return false;
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('auto_cancel_at')
            ->orWhere('auto_cancel_at', '>', now());
        });
    }
}
