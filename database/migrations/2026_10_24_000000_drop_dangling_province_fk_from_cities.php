<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel `provinces` sudah dihapus oleh 2025_12_25_000004, tetapi foreign key
 * `cities.province_id -> provinces.id` tertinggal karena constraint dimatikan
 * saat drop. Akibatnya insert ke `cities` gagal pada database baru
 * (SQLite: "no such table: main.provinces").
 *
 * Migration ini membersihkan sisa constraint tersebut.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cities') || ! Schema::hasColumn('cities', 'province_id')) {
            return;
        }

        if (Schema::hasTable('provinces')) {
            return;
        }

        try {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropForeign(['province_id']);
            });
        } catch (\Throwable $e) {
            // Constraint sudah tidak ada / driver tidak mendukung drop FK.
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan: tabel `provinces` memang sudah tidak dipakai.
    }
};
