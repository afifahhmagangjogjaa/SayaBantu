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
            $table->text('customer_cancel_reason')->nullable()->after('partner_cancel_prev_status');
            $table->string('cancelled_by')->nullable()->after('customer_cancel_reason');
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->dropColumn(['customer_cancel_reason', 'cancelled_by', 'cancelled_at']);
        });
    }
};
