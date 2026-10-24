<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case MITRA = 'mitra';

    /**
     * Mendapatkan label manusiawi untuk role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin Wilayah',
            self::CUSTOMER => 'Customer',
            self::MITRA => 'Mitra',
        };
    }

    /**
     * Mendapatkan class warna badge Tailwind untuk role.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::ADMIN => 'bg-purple-100 text-purple-800 border-purple-200',
            self::CUSTOMER => 'bg-blue-100 text-blue-800 border-blue-200',
            self::MITRA => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    /**
     * Seluruh nilai string dari role.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
