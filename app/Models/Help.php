<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'category_id',
        'help_type',
        'title',
        'amount',
        'admin_fee',
        'total_amount',
        'description',
        'equipment_provided',
        'photo',
        'location',
        'full_address',
        'latitude',
        'longitude',
        'status',
        'mitra_id',
        'taken_at',
        'completed_at',
        'admin_notes',
        'order_id',
        'voucher_code',
        'discount_amount',
        'booking_fee',
        'mitra_assigned_at',
        'partner_started_at',
        'partner_arrived_at',
        'service_started_at',
        'service_completed_at',
        'scheduled_at',
        'auto_cancel_minutes',
        'auto_cancel_at',
        'partner_initial_lat',
        'partner_initial_lng',
        'partner_current_lat',
        'partner_current_lng',
        'partner_started_moving_at',
        'partner_cancel_requested_at',
        'partner_cancel_reason',
        'partner_cancel_prev_status',
        'customer_cancel_reason',
        'cancelled_by',
        'cancelled_at',
        'last_cancelled_mitra_id',
        'cancelled_mitra_ids',
        'completion_photo',
        'completion_notes',
        'complaint_photo',
        'complaint_reason',
        'complaint_submitted_at',
        'complaint_resolved_at',
        'complaint_resolution',
        'complaint_admin_notes',
        'base_amount',
        'customer_fee_percent',
        'customer_fee_amount',
        'total_customer_paid',
        'mitra_fee_percent',
        'mitra_fee_amount',
        'net_mitra_amount',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'discount_amount' => 'decimal:2',
        'booking_fee' => 'decimal:2',
        'mitra_assigned_at' => 'datetime',
        'partner_started_at' => 'datetime',
        'partner_arrived_at' => 'datetime',
        'service_started_at' => 'datetime',
        'service_completed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'auto_cancel_at' => 'datetime',
        'auto_cancel_minutes' => 'integer',
        'partner_initial_lat' => 'decimal:8',
        'partner_initial_lng' => 'decimal:8',
        'partner_current_lat' => 'decimal:8',
        'partner_current_lng' => 'decimal:8',
        'partner_started_moving_at' => 'datetime',
        'partner_cancel_requested_at' => 'datetime',
        'complaint_submitted_at' => 'datetime',
        'complaint_resolved_at' => 'datetime',
        'last_cancelled_mitra_id' => 'integer',
        'cancelled_mitra_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Alias untuk customer (sama dengan user)
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * The rating left by the owner of this help (if any).
     * This is useful to eager-load the single rating that belongs to the help's creator.
     */
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function messages()
    {
        return $this->hasMany(Chat::class);
    }

    public function scopeInMitraCity($query, $user)
    {
        if (!$user || empty($user->city_id)) {
            return $query;
        }

        $userCity = \App\Models\City::find($user->city_id);
        $userCityCode = $userCity->code ?? null;

        return $query->where(function ($q) use ($user, $userCityCode) {
            $q->where('helps.city_id', $user->city_id);

            if (!$userCityCode) {
                return;
            }

            // direct code match
            $q->orWhereHas('city', function ($cityQ) use ($userCityCode) {
                $cityQ->where('code', $userCityCode);
            });

            // Try to extract numeric regency id
            $regencyId = null;
            if (preg_match('/(\d+)/', (string) $userCityCode, $m)) {
                $regencyId = (int) $m[1];
            }

            if (!$regencyId) {
                return;
            }

            // Include helps whose city is a district belonging to that regency.
            if (\Illuminate\Support\Facades\Schema::hasTable('req_districts')) {
                $q->orWhereRaw(
                    "EXISTS (select 1 from cities c2 join req_districts rd on rd.id = CAST(SUBSTRING_INDEX(c2.code, '-', -1) as unsigned) where c2.id = helps.city_id and rd.regency_id = ?)",
                    [$regencyId]
                );
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('reg_districts')) {
                $q->orWhereRaw(
                    "EXISTS (select 1 from cities c2 join reg_districts rd on rd.id = CAST(SUBSTRING_INDEX(c2.code, '-', -1) as unsigned) where c2.id = helps.city_id and rd.regency_id = ?)",
                    [$regencyId]
                );
            }

            // Fallback: regency rows directly as cities
            $q->orWhereHas('city', function ($cityQ) use ($regencyId) {
                $cityQ->where('code', (string) $regencyId);
            });
        });
    }

    public function chatMessages()
    {
        return $this->hasMany(Chat::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'approved')->whereNull('mitra_id');
    }

    public function scopeTaken($query)
    {
        return $query->whereIn('status', ['taken', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isUrgent(): bool
    {
        return $this->help_type === 'urgent';
    }

    public function isScheduled(): bool
    {
        return $this->help_type === 'scheduled';
    }

    /**
     * Cek apakah mitra sudah diperbolehkan mulai menuju lokasi
     * Untuk pesanan terjadwal, mitra hanya bisa mulai berangkat H-1 jam (60 menit) sebelum jadwal pelaksanaan
     */
    public function canPartnerStartJourney(): bool
    {
        if ($this->isUrgent() || !$this->isScheduled() || empty($this->scheduled_at)) {
            return true;
        }

        return now()->gte(\Carbon\Carbon::parse($this->scheduled_at)->subHour());
    }

    /**
     * Waktu kapan mitra mulai diperbolehkan berangkat menuju lokasi (H-1 jam sebelum jadwal)
     */
    public function partnerJourneyAvailableAt(): ?\Carbon\Carbon
    {
        if (!$this->isScheduled() || empty($this->scheduled_at)) {
            return null;
        }

        return \Carbon\Carbon::parse($this->scheduled_at)->subHour();
    }

    /**
     * Cek apakah mitra belum berangkat/tidak ada pergerakan selama lebih dari 30 menit
     * Sesuai kondisi sebenarnya:
     * - Order harus aktif dan sudah ada mitra (status: taken atau memperoleh_mitra)
     * - Mitra belum mulai bergerak / bekerja (partner_started_moving_at dan partner_started_at null)
     * - Untuk pesanan terjadwal:
     *   Mitra belum wajib berangkat jika jadwal belum tiba (now() < scheduled_at).
     *   Mitra dianggap idle hanya jika jadwal sudah lewat >= 30 menit dan dalam batas waktu wajar (<= 12 jam).
     * - Untuk pesanan urgent/instan:
     *   Mitra dianggap idle jika waktu sejak diambil (taken_at) sudah >= 30 menit dan dalam batas wajar (<= 12 jam).
     */
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

    /**
     * Hitung waktu acuan mulai keberangkatan yang diharapkan untuk mitra
     */
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

    /**
     * Cek apakah mitra pernah/sedang mengalami keterlambatan keberangkatan >= 30 menit
     * Termasuk jika mitra baru mulai jalan setelah lewat 30 menit dari waktu yang diharapkan.
     */
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

    /**
     * Cek apakah customer berhak membatalkan / mengalihkan rekan jasa karena keterlambatan
     * Hak ini berlaku:
     * 1. Jika mitra idle >= 30 menit (belum berangkat)
     * 2. ATAU jika mitra terlambat berangkat (> 30 menit) dan saat ini masih dalam perjalanan (belum tiba di lokasi)
     * Hak ini otomatis tertutup begitu mitra sudah tiba di lokasi (partner_arrived) atau mulai bekerja.
     */
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

    public static function cancelExpiredUrgentHelps(): int
    {
        $expiredHelps = static::where('status', 'menunggu_mitra')
            ->where('help_type', 'urgent')
            ->whereNotNull('auto_cancel_at')
            ->where('auto_cancel_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredHelps as $help) {
            $help->update([
                'status' => 'dibatalkan',
                'customer_cancel_reason' => 'Batas waktu pencarian mitra telah habis (dibatalkan otomatis oleh sistem)',
                'cancelled_at' => now(),
            ]);
            // Refund saldo customer otomatis ditangani oleh HelpObserver
            $count++;
        }

        return $count;
    }

    /**
     * Hitung nominal bersih yang dicairkan ke mitra setelah potongan platform
     */
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

    /**
     * Batas waktu konfirmasi customer (24 jam sejak pekerjaan selesai)
     */
    public function getCustomerConfirmationDeadline(): ?\Carbon\Carbon
    {
        $base = $this->service_completed_at ?? $this->updated_at;
        return $base ? $base->copy()->addHours(24) : null;
    }

    /**
     * Cek apakah pesanan menunggu konfirmasi customer dan telah melewati 24 jam
     */
    public function isCustomerConfirmationExpired(): bool
    {
        if ($this->status !== 'waiting_customer_confirmation') {
            return false;
        }

        $deadline = $this->getCustomerConfirmationDeadline();
        return $deadline ? now()->greaterThanOrEqualTo($deadline) : false;
    }

    /**
     * Konfirmasi otomatis bantuan ini jika sudah melewati 24 jam menunggu konfirmasi
     */
    public function autoConfirmIfExpired(): bool
    {
        if (!$this->isCustomerConfirmationExpired()) {
            return false;
        }

        return (bool) \App\Console\Commands\AutoConfirmHelps::autoConfirmSingleHelp($this);
    }

    /**
     * Auto-confirm semua bantuan yang sudah lewat 24 jam menunggu konfirmasi customer
     */
    public static function autoConfirmExpiredCustomerHelps(?int $userId = null, ?int $mitraId = null): int
    {
        return \App\Console\Commands\AutoConfirmHelps::autoConfirmExpired(null, $userId, $mitraId);
    }


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

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('auto_cancel_at')
            ->orWhere('auto_cancel_at', '>', now());
        });
    }
}
