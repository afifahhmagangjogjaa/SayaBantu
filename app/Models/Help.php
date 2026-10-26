<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\HelpStatus;
use App\Models\Concerns\HandlesMitraCancellation;
use App\Models\Concerns\HandlesPayoutAndConfirmation;
use App\Models\Concerns\HasJourneyTimeline;
use App\Models\Concerns\HasHelpStatus;

class Help extends Model
{
    use HasHelpStatus;
    use HasJourneyTimeline;
    use HandlesPayoutAndConfirmation;
    use HandlesMitraCancellation;
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
        'before_photo',
        'before_notes',
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

    /**
     * Waktu kapan mitra mulai diperbolehkan berangkat menuju lokasi (H-1 jam sebelum jadwal)
     */

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

    /**
     * Hitung waktu acuan mulai keberangkatan yang diharapkan untuk mitra
     */

    /**
     * Cek apakah mitra pernah/sedang mengalami keterlambatan keberangkatan >= 30 menit
     * Termasuk jika mitra baru mulai jalan setelah lewat 30 menit dari waktu yang diharapkan.
     */

    /**
     * Cek apakah customer berhak membatalkan / mengalihkan rekan jasa karena keterlambatan
     * Hak ini berlaku:
     * 1. Jika mitra idle >= 30 menit (belum berangkat)
     * 2. ATAU jika mitra terlambat berangkat (> 30 menit) dan saat ini masih dalam perjalanan (belum tiba di lokasi)
     * Hak ini otomatis tertutup begitu mitra sudah tiba di lokasi (partner_arrived) atau mulai bekerja.
     */

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
                'status' => \App\Enums\HelpStatus::DIBATALKAN->value,
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

    /**
     * Batas waktu konfirmasi customer (24 jam sejak pekerjaan selesai)
     */

    /**
     * Cek apakah pesanan menunggu konfirmasi customer dan telah melewati 24 jam
     */

    /**
     * Konfirmasi otomatis bantuan ini jika sudah melewati 24 jam menunggu konfirmasi
     */

    /**
     * Auto-confirm semua bantuan yang sudah lewat 24 jam menunggu konfirmasi customer
     */
    public static function autoConfirmExpiredCustomerHelps(?int $userId = null, ?int $mitraId = null): int
    {
        return \App\Console\Commands\AutoConfirmHelps::autoConfirmExpired(null, $userId, $mitraId);
    }

}
