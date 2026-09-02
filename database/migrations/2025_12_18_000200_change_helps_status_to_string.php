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
        // Convert the ENUM status column to a VARCHAR to avoid future enum-value migrations.
        // This preserves existing values while allowing new statuses (like partner_cancel_requested).
        DB::statement("ALTER TABLE helps MODIFY COLUMN status VARCHAR(191) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate a permissive ENUM containing known status values used across the app.
        // Adjust this list if your app uses additional custom statuses.
        DB::statement("ALTER TABLE helps MODIFY COLUMN status ENUM('pending','approved','rejected','taken','in_progress','completed','cancelled','menunggu_mitra','memperoleh_mitra','selesai','partner_cancel_requested') DEFAULT 'pending'");
    }
};
