<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'category_id',
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
        'partner_initial_lat',
        'partner_initial_lng',
        'partner_current_lat',
        'partner_current_lng',
        'partner_started_moving_at',
        'partner_cancel_requested_at',
        'partner_cancel_reason',
        'partner_cancel_prev_status',
        'completion_photo',
        'completion_notes',
        'complaint_photo',
        'complaint_reason',
        'complaint_submitted_at',
        'complaint_resolved_at',
        'complaint_resolution',
        'complaint_admin_notes',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'completed_at' => 'datetime',
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
        'partner_initial_lat' => 'decimal:8',
        'partner_initial_lng' => 'decimal:8',
        'partner_current_lat' => 'decimal:8',
        'partner_current_lng' => 'decimal:8',
        'partner_started_moving_at' => 'datetime',
        'partner_cancel_requested_at' => 'datetime',
        'complaint_submitted_at' => 'datetime',
        'complaint_resolved_at' => 'datetime',
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
}
