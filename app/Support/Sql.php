<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Ekspresi SQL yang portabel antar driver.
 *
 * Beberapa halaman memakai YEAR(created_at) yang hanya ada di MySQL, sehingga
 * error "no such function: YEAR" saat aplikasi dijalankan di SQLite
 * (termasuk environment test).
 */
class Sql
{
    /**
     * Ekspresi tahun dari sebuah kolom tanggal, lengkap dengan alias "year".
     */
    public static function yearExpression(string $column = 'created_at', string $alias = 'year'): string
    {
        $expression = match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', {$column}) AS INTEGER)",
            'pgsql' => "EXTRACT(YEAR FROM {$column})",
            default => "YEAR({$column})",
        };

        return "{$expression} as {$alias}";
    }
}
