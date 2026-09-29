<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            if (!Schema::hasColumn('helps', 'last_cancelled_mitra_id')) {
                $table->unsignedBigInteger('last_cancelled_mitra_id')->nullable()->after('customer_cancel_reason');
                $table->foreign('last_cancelled_mitra_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('helps', 'cancelled_mitra_ids')) {
                $table->json('cancelled_mitra_ids')->nullable()->after('last_cancelled_mitra_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            if (Schema::hasColumn('helps', 'last_cancelled_mitra_id')) {
                $table->dropForeign(['last_cancelled_mitra_id']);
                $table->dropColumn('last_cancelled_mitra_id');
            }
            if (Schema::hasColumn('helps', 'cancelled_mitra_ids')) {
                $table->dropColumn('cancelled_mitra_ids');
            }
        });
    }
};
