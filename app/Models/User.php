<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted()
    {
        static::saved(function ($user) {
            try {
                // Find matching registration by email, nik, or previous email/nik
                $oldEmail = $user->getOriginal('email');
                $oldNik = $user->getOriginal('nik');

                $reg = \App\Models\Registration::where('email', $user->email)
                    ->when($oldEmail && $oldEmail !== $user->email, function ($q) use ($oldEmail) {
                        $q->orWhere('email', $oldEmail);
                    })
                    ->when(!empty($user->nik), function ($q) use ($user) {
                        $q->orWhere('nik', $user->nik);
                    })
                    ->when(!empty($oldNik) && $oldNik !== $user->nik, function ($q) use ($oldNik) {
                        $q->orWhere('nik', $oldNik);
                    })
                    ->first();

                if ($reg) {
                    $cityName = $user->city;
                    if (empty($cityName) && !empty($user->city_id)) {
                        $cityName = \App\Models\City::find($user->city_id)?->name;
                    }

                    $normalizedRole = in_array($user->role, ['mitra', 'super_admin', 'admin']) ? ($user->role === 'mitra' ? 'mitra' : 'customer') : 'customer';

                    $reg->update([
                        'full_name' => $user->name,
                        'email' => $user->email,
                        'nik' => $user->nik ?: $reg->nik,
                        'role' => $normalizedRole,
                        'city_id' => $user->city_id ?: $reg->city_id,
                        'city' => $cityName ?: $reg->city,
                        'address' => $user->address ?: $reg->address,
                        'rt' => $user->rt ?: $reg->rt,
                        'rw' => $user->rw ?: $reg->rw,
                        'kelurahan' => $user->kelurahan ?: $reg->kelurahan,
                        'kecamatan' => $user->kecamatan ?: $reg->kecamatan,
                        'province' => $user->province ?: $reg->province,
                        'religion' => $user->religion ?: $reg->religion,
                        'marital_status' => $user->marital_status ?: $reg->marital_status,
                        'occupation' => $user->occupation ?: $reg->occupation,
                        'place_of_birth' => $user->place_of_birth ?: $reg->place_of_birth,
                        'date_of_birth' => $user->date_of_birth ?: $reg->date_of_birth,
                        'gender' => $user->gender ?: $reg->gender,
                        'status' => $user->verified ? 'approved' : ($reg->status ?: 'pending_verification'),
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore sync errors during save
            }
        });

        static::deleting(function ($user) {
            try {
                \App\Models\Registration::where('email', $user->email)->delete();
            } catch (\Exception $e) {
                // ignore
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_completed',
        'role',
        'city_id',
        'ktp_path',
        'verified',
        'status',
        'is_shadow_banned',
        'shadow_banned_at',
        'shadow_ban_reason',
        'phone',
        'address',
        // KTP Fields
        'nik',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'city',
        'province',
        'religion',
        'marital_status',
        'occupation',
        'ktp_photo',
        'selfie_photo',
        'profile_photo',
        'notification_settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified' => 'boolean',
            'is_shadow_banned' => 'boolean',
            'shadow_banned_at' => 'datetime',
            'date_of_birth' => 'date',
            'notification_settings' => 'array',
        ];
    }

    public function isShadowBanned(): bool
    {
        return (bool) ($this->is_shadow_banned ?? false);
    }

    protected function maritalStatus(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ($value === '' || $value === null) ? null : $value,
        );
    }

    protected function religion(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ($value === '' || $value === null) ? null : $value,
        );
    }

    protected function occupation(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ($value === '' || $value === null) ? null : $value,
        );
    }

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Cities managed by this admin (many-to-many)
    public function managedCities()
    {
        return $this->belongsToMany(City::class, 'admin_city', 'user_id', 'city_id')
                    ->withTimestamps();
    }

    // Alias for managedCities
    public function adminCities()
    {
        return $this->managedCities();
    }

    /**
     * Get all city IDs associated with/managed by this admin.
     *
     * @return array
     */
    public function getAdminCityIds(): array
    {
        return City::where('admin_id', $this->id)
            ->pluck('id')
            ->merge($this->managedCities()->pluck('cities.id'))
            ->push($this->city_id)
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    public function helps()
    {
        return $this->hasMany(Help::class);
    }

    public function takenHelps()
    {
        return $this->hasMany(Help::class, 'mitra_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'mitra_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'mitra_id');
    }

    // Ratings received as customer (from mitra)
    public function customerRatings()
    {
        return $this->hasMany(Rating::class, 'ratee_id')->where('type', 'mitra_to_customer');
    }

    // Ratings received as mitra (from customer)  
    public function mitraRatings()
    {
        return $this->hasMany(Rating::class, 'ratee_id')->where('type', 'customer_to_mitra');
    }

    public function partnerReports()
    {
        return $this->hasMany(\App\Models\PartnerReport::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function balance()
    {
        return $this->hasOne(UserBalance::class);
    }

    public function withdrawRequests()
    {
        return $this->hasMany(\App\Models\WithdrawRequest::class);
    }

    public function hasPendingOrProcessingWithdraws(): bool
    {
        return $this->withdrawRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
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

    /**
     * Accessor to get numeric balance conveniently via $user->balance
     */
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

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKustomer()
    {
        return $this->isCustomer();
    }

    public function isMitra()
    {
        return $this->role === 'mitra';
    }

    public function isCustomer()
    {
        // Accept both 'customer' (new) and 'kustomer' (legacy) values
        return in_array($this->role, ['customer', 'kustomer']);
    }

    public function isVerified()
    {
        return $this->verified;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Return the display name for the city.
     * Prefer the related City model (if eager-loaded), fallback to users.city attribute.
     */
    public function getCityNameAttribute()
    {
        $rel = $this->getRelationValue('city');
        if ($rel && isset($rel->name)) {
            return $rel->name;
        }

        if (!empty($this->city_id)) {
            $city = \App\Models\City::find($this->city_id);
            if ($city) {
                return $city->name;
            }
        }

        return isset($this->attributes['city']) && $this->attributes['city'] !== null
            ? $this->attributes['city']
            : null;
    }

    // Customer Rating Methods
    public function getCustomerAverageRatingAttribute()
    {
        $avg = Rating::forCustomer($this->id)->avg('rating');
        return $avg ? round((float) $avg, 1) : 0;
    }

    public function getCustomerRatingCountAttribute()
    {
        return Rating::forCustomer($this->id)->count();
    }

    public function getCustomerRatingBadgeAttribute()
    {
        $rating = $this->customer_average_rating;
        
        if ($rating >= 4.5) {
            return [
                'text' => 'Customer Terpercaya',
                'color' => 'green',
                'emoji' => '🌟'
            ];
        } elseif ($rating >= 4.0) {
            return [
                'text' => 'Customer Baik',
                'color' => 'blue',
                'emoji' => '⭐'
            ];
        } elseif ($rating >= 3.0) {
            return [
                'text' => 'Standar',
                'color' => 'yellow',
                'emoji' => '✓'
            ];
        } else {
            return [
                'text' => 'Perlu Perbaikan',
                'color' => 'red',
                'emoji' => '⚠️'
            ];
        }
    }

    // Mitra Rating Methods
    public function getMitraAverageRatingAttribute()
    {
        $avg = Rating::forMitra($this->id)->avg('rating');
        return $avg ? round((float) $avg, 1) : 0;
    }

    public function getMitraRatingCountAttribute()
    {
        return Rating::forMitra($this->id)->count();
    }

    // Generic Aliases for profile & components
    public function getAverageRatingAttribute()
    {
        return $this->isMitra() ? $this->mitra_average_rating : $this->customer_average_rating;
    }

    public function getRatingCountAttribute()
    {
        return $this->isMitra() ? $this->mitra_rating_count : $this->customer_rating_count;
    }

    /**
     * Memeriksa apakah data profil wajib pengguna sudah terisi lengkap.
     * Field wajib: Nama Lengkap, NIK (16 digit), No HP (10-13 digit), Kota Domisili, Alamat Lengkap, Agama, Status Pernikahan, dan Pekerjaan.
     * Field opsional: RT, RW.
     */
    public function isProfileComplete(): bool
    {
        return empty($this->getMissingProfileFields());
    }

    /**
     * Mengembalikan daftar field wajib yang belum terisi.
     */
    public function getMissingProfileFields(): array
    {
        $missing = [];

        if (empty(trim((string) $this->name))) {
            $missing['name'] = 'Nama Lengkap';
        }

        $cleanNik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        if (empty($cleanNik) || strlen($cleanNik) !== 16) {
            $missing['nik'] = 'NIK (16 Digit Angka)';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (empty($cleanPhone) || strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13) {
            $missing['phone'] = 'No. HP (10-13 Digit Angka)';
        }

        if (empty($this->city_id)) {
            $missing['city_id'] = 'Kota Domisili';
        }

        if (empty(trim((string) $this->address))) {
            $missing['address'] = 'Alamat Lengkap';
        }

        if (empty($this->religion)) {
            $missing['religion'] = 'Agama';
        }

        if (empty($this->marital_status)) {
            $missing['marital_status'] = 'Status Pernikahan';
        }

        if (empty(trim((string) $this->occupation))) {
            $missing['occupation'] = 'Pekerjaan';
        }

        if (empty($this->ktp_photo) && empty($this->ktp_path)) {
            $missing['ktp_photo'] = 'Foto KTP';
        }

        if (empty($this->selfie_photo)) {
            $missing['selfie_photo'] = 'Foto Selfie (Wajah & KTP)';
        }

        return $missing;
    }

    /**
     * Mengembalikan daftar field biodata yang belum terisi (tanpa dokumen KTP & Selfie).
     */
    public function getMissingBiodataFields(): array
    {
        $missing = [];

        if (empty(trim((string) $this->name))) {
            $missing['name'] = 'Nama Lengkap';
        }

        $cleanNik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        if (empty($cleanNik) || strlen($cleanNik) !== 16) {
            $missing['nik'] = 'NIK (16 Digit Angka)';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (empty($cleanPhone) || strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13) {
            $missing['phone'] = 'No. HP (10-13 Digit Angka)';
        }

        if (empty($this->city_id)) {
            $missing['city_id'] = 'Kota Domisili';
        }

        if (empty(trim((string) $this->address))) {
            $missing['address'] = 'Alamat Lengkap';
        }

        if (empty($this->religion)) {
            $missing['religion'] = 'Agama';
        }

        if (empty($this->marital_status)) {
            $missing['marital_status'] = 'Status Pernikahan';
        }

        if (empty(trim((string) $this->occupation))) {
            $missing['occupation'] = 'Pekerjaan';
        }

        return $missing;
    }
}
