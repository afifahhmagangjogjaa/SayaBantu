<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order')->orderBy('name');
    }

    public function scopeBanks($query)
    {
        return $query->where('category', 'bank');
    }

    public function scopeEwallets($query)
    {
        return $query->where('category', 'ewallet');
    }
}

