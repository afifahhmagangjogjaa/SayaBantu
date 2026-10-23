<?php

use App\Enums\HelpStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menormalkan nilai status `helps` yang masih memakai kosakata lama
 * (taken, completed, cancelled, approved, pending, ...) ke nilai kanonik enum.
 *
 * Pemetaan diambil langsung dari HelpStatus::aliases() supaya hanya ada satu
 * sumber kebenaran. Aman dijalankan berulang (idempotent).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('helps')) {
            return;
        }

        foreach (HelpStatus::aliases() as $legacy => $canonical) {
            DB::table('helps')
                ->where('status', $legacy)
                ->update(['status' => $canonical, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan: nilai legacy sudah tidak ditulis lagi oleh aplikasi.
    }
};
