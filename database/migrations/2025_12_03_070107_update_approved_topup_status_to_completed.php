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
        // Update existing 'approved' status to 'completed' for topup transactions
        // This ensures admin fees are counted in SuperAdmin panel analytics
        DB::table('balance_transactions')
            ->where('type', 'topup')
            ->where('status', 'approved')
            ->update(['status' => 'completed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: change 'completed' back to 'approved' for topup transactions
        // that were processed by this migration (approved_at is not null)
        DB::table('balance_transactions')
            ->where('type', 'topup')
            ->where('status', 'completed')
            ->whereNotNull('approved_at')
            ->update(['status' => 'approved']);
    }
};
