<?php

namespace App\Models\Concerns;

use App\Models\User;

/**
 * Pencatatan mitra yang membatalkan / dialihkan dari sebuah order.
 * Diekstrak dari App\Models\Help agar model tidak menjadi terlalu besar.
 */
trait HandlesMitraCancellation
{
    public function lastCancelledMitra()
    {
        return $this->belongsTo(User::class, 'last_cancelled_mitra_id');
    }

    public function recordMitraCancellation($mitraId): void
    {
        if (!$mitraId) {
            return;
        }
        $mitraIdInt = (int) $mitraId;
        $ids = $this->cancelled_mitra_ids ?? [];
        if (!is_array($ids)) {
            $ids = json_decode($ids, true) ?? [];
        }
        if (!in_array($mitraIdInt, array_map('intval', $ids), true)) {
            $ids[] = $mitraIdInt;
        }

        $this->cancelled_mitra_ids = array_values($ids);
        $this->last_cancelled_mitra_id = $mitraIdInt;
        $this->save();
    }

    /**
     * Lepaskan mitra saat ini dan kembalikan pesanan ke antrean pencarian mitra baru
     * (Mitra lama otomatis dicatat di cancelled_mitra_ids agar tidak bisa mengambil lagi)
     */
    public function reassignToNewPartner(string $reason = 'Customer memilih mencari rekan jasa lain karena tidak kunjung berangkat'): bool
    {
        $oldMitra = $this->mitra;
        $oldMitraId = $this->mitra_id;

        if (!$oldMitraId) {
            return false;
        }

        // Catat mitra lama ke blacklist order ini
        $this->recordMitraCancellation($oldMitraId);

        // Reset data mitra dan kembalikan status ke menunggu_mitra
        $oldStatus = $this->status;
        $this->update([
            'status' => 'menunggu_mitra',
            'mitra_id' => null,
            'partner_cancel_reason' => $reason,
            'partner_cancel_requested_at' => now(),
            'partner_current_lat' => null,
            'partner_current_lng' => null,
            'partner_initial_lat' => null,
            'partner_initial_lng' => null,
            'partner_started_moving_at' => null,
            'partner_arrived_at' => null,
        ]);

        // Catat PartnerActivity untuk mitra lama jika tabel tersedia
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('partner_activities')) {
                \App\Models\PartnerActivity::create([
                    'user_id' => $oldMitraId,
                    'help_id' => $this->id,
                    'activity_type' => 'help_reassigned',
                    'description' => "Pesanan #{$this->id} dialihkan oleh customer karena belum ada keberangkatan setelah 30 menit.",
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->header('User-Agent'),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log PartnerActivity on reassign: ' . $e->getMessage());
        }

        // Kirim notifikasi ke mitra lama
        try {
            if ($oldMitra) {
                $oldMitra->notify(new \App\Notifications\HelpStatusNotification(
                    $this,
                    $oldStatus,
                    'partner_reassigned',
                    $oldMitra
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to notify old mitra on reassign: ' . $e->getMessage());
        }

        return true;
    }

    public function wasCancelledByMitra($mitraId): bool
    {
        if (!$mitraId) {
            return false;
        }
        $mitraIdInt = (int) $mitraId;
        if ($this->last_cancelled_mitra_id && (int) $this->last_cancelled_mitra_id === $mitraIdInt) {
            return true;
        }
        $ids = $this->cancelled_mitra_ids ?? [];
        if (!is_array($ids)) {
            $ids = json_decode($ids, true) ?? [];
        }
        if (in_array($mitraIdInt, array_map('intval', $ids), true)) {
            return true;
        }

        // Fallback check from PartnerActivity if available
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('partner_activities')) {
                return \App\Models\PartnerActivity::where('activity_type', 'help_cancelled')
                    ->where('user_id', $mitraIdInt)
                    ->where(function ($q) {
                        $q->where('description', 'like', '%#' . $this->id . ' %')
                          ->orWhere('description', 'like', '%#' . $this->id . ' -%');
                    })
                    ->exists();
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return false;
    }

    public function scopeNotCancelledByMitra($query, $mitraId)
    {
        if (!$mitraId) {
            return $query;
        }

        $mitraIdInt = (int) $mitraId;

        return $query->where(function ($q) use ($mitraIdInt) {
            $q->whereNull('helps.last_cancelled_mitra_id')
              ->orWhere('helps.last_cancelled_mitra_id', '!=', $mitraIdInt);
        })->where(function ($q) use ($mitraIdInt) {
            $q->whereNull('helps.cancelled_mitra_ids')
              ->orWhereJsonDoesntContain('helps.cancelled_mitra_ids', $mitraIdInt);
        });
    }
}
