<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'help_id',
        'user_id',
        'mitra_id',
        'rater_id',
        'ratee_id',
        'type',
        'rating',
        'review',
        'is_anonymous',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_anonymous' => 'boolean',
    ];

    // Relationships
    public function help()
    {
        return $this->belongsTo(Help::class);
    }

    // Legacy: customer who gave the rating
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Legacy: mitra who received the rating
    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    // New: who gives the rating (can be customer or mitra)
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    // New: who receives the rating (can be customer or mitra)
    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }

    // Query Scopes
    public function scopeForMitra($query, $mitraId)
    {
        return $query->where(function ($q) use ($mitraId) {
            $q->where(function ($sub) use ($mitraId) {
                $sub->where('ratee_id', $mitraId)
                    ->where(function ($t) {
                        $t->where('type', 'customer_to_mitra')
                          ->orWhereNull('type');
                    });
            })->orWhere(function ($sub) use ($mitraId) {
                $sub->where('mitra_id', $mitraId)
                    ->where(function ($t) {
                        $t->where('type', 'customer_to_mitra')
                          ->orWhereNull('type');
                    })
                    ->where(function ($r) use ($mitraId) {
                        $r->where('rater_id', '!=', $mitraId)
                          ->orWhereNull('rater_id');
                    });
            });
        })->where('rating', '>', 0);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where(function ($q) use ($customerId) {
            $q->where(function ($sub) use ($customerId) {
                $sub->where('ratee_id', $customerId)
                    ->where('type', 'mitra_to_customer');
            })->orWhere(function ($sub) use ($customerId) {
                $sub->where('user_id', $customerId)
                    ->where('type', 'mitra_to_customer')
                    ->where(function ($r) use ($customerId) {
                        $r->where('rater_id', '!=', $customerId)
                          ->orWhereNull('rater_id');
                    });
            });
        })->where('rating', '>', 0);
    }

    public function scopeByRater($query, $raterId)
    {
        return $query->where('rater_id', $raterId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Check if user already rated this help
    public static function hasRated($helpId, $raterId, $type)
    {
        return self::where('help_id', $helpId)
                   ->where('rater_id', $raterId)
                   ->where('type', $type)
                   ->exists();
    }

    /**
     * Get display name of rater based on viewer permissions and anonymity
     */
    public function getRaterDisplayNameAttribute(): string
    {
        $viewer = auth()->user();

        // If viewer is admin, superadmin, or the rater themselves, always show real name
        if ($viewer && (in_array($viewer->role, ['superadmin', 'admin']) || $viewer->id === ($this->rater_id ?? $this->user_id))) {
            $name = optional($this->rater)->name ?? (optional($this->user)->name ?? 'Pengguna');
            return $this->is_anonymous ? "{$name} (Anonim)" : $name;
        }

        // If anonymous, return "Pengguna Anonim"
        if ($this->is_anonymous) {
            return 'Pengguna Anonim';
        }

        // If not anonymous, show the real name
        $fullName = trim(optional($this->rater)->name ?? (optional($this->user)->name ?? ''));
        if (empty($fullName)) {
            return 'Customer';
        }

        return $fullName;
    }
}
