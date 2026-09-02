<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            // Ubah tipe kolom status untuk menambahkan status baru
            DB::statement("ALTER TABLE helps MODIFY COLUMN status ENUM(
                'pending', 
                'approved', 
                'rejected', 
                'taken', 
                'partner_on_the_way',
                'partner_arrived', 
                'in_progress', 
                'completed', 
                'cancelled',
                'menunggu_mitra',
                'memperoleh_mitra',
                'selesai'
            ) DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            // Kembalikan ke status lama
            DB::statement("ALTER TABLE helps MODIFY COLUMN status ENUM(
                'pending', 
                'approved', 
                'rejected', 
                'taken', 
                'in_progress', 
                'completed', 
                'cancelled'
            ) DEFAULT 'pending'");
        });
    }
};
