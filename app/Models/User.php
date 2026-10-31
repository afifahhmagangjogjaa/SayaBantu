<?php

namespace App\Models;

use App\Models\Concerns\HasAccountStatus;
use App\Models\Concerns\HasBalanceWallet;
use App\Models\Concerns\HasOrderQuota;
use App\Models\Concerns\HasProfileCompleteness;
use App\Models\Concerns\HasRatingStats;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasAccountStatus;
    use HasBalanceWallet;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasOrderQuota;
    use HasProfileCompleteness;
    use HasRatingStats;

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
                    ->when(! empty($user->nik), function ($q) use ($user) {
                        $q->orWhere('nik', $user->nik);
                    })
                    ->when(! empty($oldNik) && $oldNik !== $user->nik, function ($q) use ($oldNik) {
                        $q->orWhere('nik', $oldNik);
                    })
                    ->first();

                if ($reg) {
                    $cityName = $user->city;
                    if (empty($cityName) && ! empty($user->city_id)) {
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

        static::updating(function (User $user) {
            if ($user->isDirty(['status', 'is_banned'])) {
                if ($user->isBanned() || in_array($user->status, ['blocked', 'inactive'])) {
                    $user->remember_token = null;
                }
            }
        });

        static::updated(function (User $user) {
            if ($user->wasChanged(['status', 'is_banned'])) {
                if ($user->isBanned() || in_array($user->status, ['blocked', 'inactive'])) {
                    $user->purgeSessions();
                }
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
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'google_id',
        'avatar',
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
        'warning_level',
        'is_banned',
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
            'warning_level' => 'integer',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Hapus total seluruh sesi aktif pengguna dari database dan file sesi, serta kosongkan remember_token
     */
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
    public function partnerActivities()
    {
        return $this->hasMany(PartnerActivity::class);
    }

    public function cancellationsCount(): int
    {
        return $this->partnerActivities()->where('activity_type', 'help_cancelled')->count();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function registration()
    {
        return $this->hasOne(Registration::class, 'email', 'email');
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
     */
    public function getAdminCityIds(): array
    {
        // Super Admin memiliki akses penuh ke seluruh wilayah kota di Indonesia
        if ($this->isSuperAdmin()) {
            return City::pluck('id')->toArray();
        }

        $cityIds = collect();

        if ($this->exists && ! empty($this->id)) {
            $managedDirect = City::where('admin_id', $this->id)->pluck('id');
            $managedPivot = $this->managedCities()->pluck('cities.id');
            $cityIds = $cityIds->merge($managedDirect)->merge($managedPivot);
        }

        if (! empty($this->city_id)) {
            $cityIds->push($this->city_id);
        }

        $cityIds = $cityIds->filter()->unique()->values();

        if ($cityIds->isNotEmpty()) {
            $codes = City::whereIn('id', $cityIds)
                ->whereNotNull('code')
                ->where('code', '!=', '')
                ->pluck('code')
                ->unique();

            if ($codes->isNotEmpty()) {
                $sameCodeIds = City::whereIn('code', $codes)->pluck('id');
                $cityIds = $cityIds->merge($sameCodeIds);
            }
        }

        return $cityIds->unique()->values()->toArray();
    }

    /**
     * Get all active admins managing a specific city.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAdminsForCity(?int $cityId)
    {
        if (! $cityId) {
            return collect();
        }

        $targetCity = City::find($cityId);
        $targetCityIds = [$cityId];
        if ($targetCity && ! empty($targetCity->code)) {
            $targetCityIds = City::where('code', $targetCity->code)->pluck('id')->toArray();
        }

        $directAdminIds = City::whereIn('id', $targetCityIds)->whereNotNull('admin_id')->pluck('admin_id')->toArray();

        return static::where('role', 'admin')
            ->where('status', 'active')
            ->where(function ($query) use ($targetCityIds, $directAdminIds) {
                $query->whereIn('city_id', $targetCityIds)
                    ->orWhereHas('managedCities', function ($q) use ($targetCityIds) {
                        $q->whereIn('cities.id', $targetCityIds);
                    });

                if (! empty($directAdminIds)) {
                    $query->orWhereIn('id', $directAdminIds);
                }
            })
            ->get();
    }

    public function helps()
    {
        return $this->hasMany(Help::class);
    }

    public function takenHelps()
    {
        return $this->hasMany(Help::class, 'mitra_id');
    }

    /**
     * Hitung total bantuan yang pernah diambil mitra (tidak termasuk yang dibatalkan)
     */

    /**
     * Hitung berapa order yang sedang/pernah diambil mitra (alias untuk kompatibilitas)
     */

    /**
     * Kuota maksimal order:
     * - Email terverifikasi = Banyak order tanpa batas (unlimited / 999)
     * - Belum verifikasi email = Maksimal 2 order
     */

    /**
     * Cek apakah mitra masih boleh mengambil order baru:
     * Jika email belum terverifikasi dan sudah mengambil 2 order, tidak boleh mengambil order lagi.
     */

    /**
     * Memeriksa apakah mitra diizinkan mengambil bantuan:
     * 1. Biodata profil wajib lengkap
     * 2. KTP sudah diverifikasi oleh admin (verified = true)
     * 3. Kuota order (belum verifikasi email = maks 2 order)
     */

    /**
     * Mengembalikan alasan spesifik jika mitra belum bisa mengambil bantuan.
     */

    /**
     * Memeriksa apakah pengguna diizinkan melakukan Top Up saldo:
     * 1. Biodata profil wajib lengkap
     * 2. KTP sudah diverifikasi oleh admin (verified = true)
     */

    /**
     * Mengembalikan alasan spesifik jika pengguna belum bisa melakukan Top Up.
     */

    /**
     * Hitung berapa bantuan yang telah dibuat customer (tidak termasuk bantuan yang dibatalkan)
     */

    /**
     * Alias untuk konsistensi view/komponen
     */

    /**
     * Batas maksimal bantuan yang dapat dibuat customer:
     * - Email terverifikasi = Banyak bantuan tanpa batas (999)
     * - Belum verifikasi email = Maksimal 2 bantuan
     */

    /**
     * Cek apakah customer masih boleh membuat permintaan bantuan baru:
     * Jika email belum terverifikasi, dibatasi maksimal 2 bantuan.
     * Jika email sudah terverifikasi, bebas membuat bantuan baru.
     */

    /**
     * Cek apakah mitra dapat melakukan penarikan saldo
     * Syarat: Email harus terverifikasi dan profil lengkap
     */
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

    public function withdrawRequests()
    {
        return $this->hasMany(\App\Models\WithdrawRequest::class);
    }

    public function customerAddresses()
    {
        return $this->hasMany(\App\Models\CustomerAddress::class);
    }

    public function primaryAddress()
    {
        return $this->hasOne(\App\Models\CustomerAddress::class)->where('is_primary', true);
    }

    /**
     * Accessor to get numeric balance conveniently via $user->balance
     */

    // Helper methods

    /**
     * Mendapatkan Enum UserRole untuk pengguna ini.
     */

    /**
     * Mendapatkan label manusiawi dari role (Super Admin, Admin Wilayah, Customer, Mitra).
     */

    /**
     * Return the display name for the city.
     * Prefer the related City model (if eager-loaded), fallback to users.city attribute, or registration.
     */
    public function getCityNameAttribute()
    {
        $rel = $this->getRelationValue('city');
        if ($rel && isset($rel->name)) {
            return $rel->name;
        }

        if (! empty($this->city_id)) {
            $city = \App\Models\City::find($this->city_id);
            if ($city) {
                return $city->name;
            }
        }

        if (isset($this->attributes['city']) && ! empty($this->attributes['city'])) {
            return $this->attributes['city'];
        }

        // Fallback dari data pendaftaran jika profil belum lengkap
        return optional($this->registration)->city;
    }

    public function getAddressAttribute($value)
    {
        return ! empty($value) ? $value : optional($this->registration)->address;
    }

    public function getKelurahanAttribute($value)
    {
        return ! empty($value) ? $value : optional($this->registration)->kelurahan;
    }

    public function getKecamatanAttribute($value)
    {
        return ! empty($value) ? $value : optional($this->registration)->kecamatan;
    }

    public function getProvinceAttribute($value)
    {
        return ! empty($value) ? $value : optional($this->registration)->province;
    }

    // Customer Rating Methods

    // Mitra Rating Methods

    /**
     * Lencana Reputasi Mitra Berdasarkan Jam Terbang & Rating:
     * - Top Partner / Mitra Unggulan: Selesai >= 10 order & Rating >= 4.8
     * - Terpercaya / Trusted Mitra: Selesai >= 3 order & Rating >= 4.5
     * - Mitra Aktif / Verified: Akun verified
     * - Mitra Baru: Belum ada order selesai
     */

    // Generic Aliases for profile & components

    /**
     * Memeriksa apakah data profil wajib pengguna sudah terisi lengkap.
     * Field wajib: Nama Lengkap, NIK (16 digit), No HP (10-13 digit), Kota Domisili, Alamat Lengkap.
     * Field opsional: RT, RW.
     */

    /**
     * Mengembalikan daftar field wajib yang belum terisi.
     */

    /**
     * Mengembalikan daftar field biodata yang belum terisi (tanpa dokumen KTP & Selfie).
     */

    /**
     * Get accessible URL for KTP photo
     */

    /**
     * Get accessible URL for Selfie photo
     */
}
