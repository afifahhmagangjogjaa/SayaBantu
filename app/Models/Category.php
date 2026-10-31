<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function helps()
    {
        return $this->hasMany(Help::class);
    }

    public function getSvgPathAttribute(): ?string
    {
        $map = [
            'full home cleaning' => 'images/categories/full-home-cleaning.svg',
            'basic daily cleaning' => 'images/categories/basic-daily-cleaning.svg',
            'buang sampah' => 'images/categories/buang-sampah.svg',
            'mengubur bangkai hewan' => 'images/categories/mengubur-bangkai.svg',
            'membersihkan kandang / kotoran hewan' => 'images/categories/kandang-hewan.svg',
            'antar / jemput' => 'images/categories/antar-jemput.svg',
            'angkut barang' => 'images/categories/angkut-barang.svg',
            'pindahan' => 'images/categories/pindahan.svg',
            'spy / mata - mata' => 'images/categories/spy.svg',
            'saba food' => 'images/categories/food.svg',
            'sansu food' => 'images/categories/food.svg',
            'nemenin olahraga' => 'images/categories/olahraga.svg',
            'jasa lainnya' => 'images/categories/lainnya.svg',
            'lainnya' => 'images/categories/lainnya.svg',
        ];

        $key = strtolower(trim($this->name));
        $file = $map[$key] ?? null;
        if ($file && file_exists(public_path($file))) {
            return $file;
        }

        return null;
    }

    public function getBadgeStyleAttribute(): array
    {
        $styles = [
            'full home cleaning' => ['bg' => 'bg-blue-50 text-blue-600 border-blue-100', 'color' => '#3B82F6'],
            'basic daily cleaning' => ['bg' => 'bg-cyan-50 text-cyan-600 border-cyan-100', 'color' => '#06B6D4'],
            'buang sampah' => ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'color' => '#10B981'],
            'mengubur bangkai hewan' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'color' => '#64748B'],
            'membersihkan kandang / kotoran hewan' => ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'color' => '#F59E0B'],
            'antar / jemput' => ['bg' => 'bg-purple-50 text-purple-600 border-purple-100', 'color' => '#8B5CF6'],
            'angkut barang' => ['bg' => 'bg-pink-50 text-pink-600 border-pink-100', 'color' => '#EC4899'],
            'pindahan' => ['bg' => 'bg-orange-50 text-orange-600 border-orange-100', 'color' => '#F97316'],
            'spy / mata - mata' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-100', 'color' => '#334155'],
            'saba food' => ['bg' => 'bg-red-50 text-red-600 border-red-100', 'color' => '#EF4444'],
            'sansu food' => ['bg' => 'bg-red-50 text-red-600 border-red-100', 'color' => '#EF4444'],
            'nemenin olahraga' => ['bg' => 'bg-teal-50 text-teal-600 border-teal-100', 'color' => '#14B8A6'],
            'jasa lainnya' => ['bg' => 'bg-gray-50 text-gray-700 border-gray-100', 'color' => '#6B7280'],
            'lainnya' => ['bg' => 'bg-gray-50 text-gray-700 border-gray-100', 'color' => '#6B7280'],
        ];

        $key = strtolower(trim($this->name));

        return $styles[$key] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-100', 'color' => $this->color ?: '#6B7280'];
    }
}
