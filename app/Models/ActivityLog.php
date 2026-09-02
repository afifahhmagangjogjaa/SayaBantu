<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user role
     */
    public function scopeByRole($query, $role)
    {
        return $query->whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }
        return $query;
    }
}
