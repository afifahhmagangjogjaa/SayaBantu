<?php

namespace App\Models\Concerns;

use App\Models\Rating;

/**
 * Statistik rating dan badge untuk customer maupun mitra.
 * Diekstrak dari App\Models\User agar model tidak terlalu besar.
 */
trait HasRatingStats
{
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

    public function getMitraAverageRatingAttribute()
    {
        $avg = \App\Models\Rating::forMitra($this->id)->avg('rating');
        return $avg ? round((float) $avg, 1) : 0;
    }

    public function getMitraRatingCountAttribute()
    {
        return \App\Models\Rating::forMitra($this->id)->count();
    }

    public function helpsAsMitra()
    {
        return $this->hasMany(\App\Models\Help::class, 'mitra_id');
    }

    public function helpsAsCustomer()
    {
        return $this->hasMany(\App\Models\Help::class, 'user_id');
    }

    public function getMitraCompletedJobsCountAttribute()
    {
        return \App\Models\Help::where('mitra_id', $this->id)->whereIn('status', ['selesai', 'completed'])->count();
    }

    public function getMitraBadgeAttribute(): array
    {
        $completedJobs = $this->mitra_completed_jobs_count;
        $rating = $this->mitra_average_rating;

        if ($completedJobs >= 10 && $rating >= 4.8) {
            return [
                'level' => 'top',
                'label' => 'Top Partner',
                'color' => 'bg-amber-100 text-amber-800 border-amber-300',
                'icon' => '👑',
            ];
        }

        if ($completedJobs >= 3 && $rating >= 4.5) {
            return [
                'level' => 'trusted',
                'label' => 'Mitra Terpercaya',
                'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'icon' => '⭐',
            ];
        }

        if ($this->verified) {
            return [
                'level' => 'verified',
                'label' => 'Mitra Terverifikasi',
                'color' => 'bg-blue-100 text-blue-800 border-blue-300',
                'icon' => '🛡️',
            ];
        }

        return [
            'level' => 'new',
            'label' => 'Mitra Baru',
            'color' => 'bg-gray-100 text-gray-700 border-gray-300',
            'icon' => '✨',
        ];
    }

    public function getAverageRatingAttribute()
    {
        return $this->isMitra() ? $this->mitra_average_rating : $this->customer_average_rating;
    }

    public function getRatingCountAttribute()
    {
        return $this->isMitra() ? $this->mitra_rating_count : $this->customer_rating_count;
    }
}
