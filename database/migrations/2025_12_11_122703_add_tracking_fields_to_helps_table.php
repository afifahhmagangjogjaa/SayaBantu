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
            // Add order_id if not exists
            if (!Schema::hasColumn('helps', 'order_id')) {
                $table->string('order_id')->nullable()->after('id');
            }
            
            // Add voucher fields if not exists
            if (!Schema::hasColumn('helps', 'voucher_code')) {
                $table->string('voucher_code')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('helps', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('voucher_code');
            }
            if (!Schema::hasColumn('helps', 'booking_fee')) {
                $table->decimal('booking_fee', 10, 2)->default(3500)->after('discount_amount');
            }
            
            // Tracking timestamps
            if (!Schema::hasColumn('helps', 'mitra_assigned_at')) {
                $table->timestamp('mitra_assigned_at')->nullable();
            }
            if (!Schema::hasColumn('helps', 'partner_started_at')) {
                $table->timestamp('partner_started_at')->nullable();
            }
            if (!Schema::hasColumn('helps', 'partner_arrived_at')) {
                $table->timestamp('partner_arrived_at')->nullable();
            }
            if (!Schema::hasColumn('helps', 'service_started_at')) {
                $table->timestamp('service_started_at')->nullable();
            }
            if (!Schema::hasColumn('helps', 'service_completed_at')) {
                $table->timestamp('service_completed_at')->nullable();
            }
            
            // Schedule
            if (!Schema::hasColumn('helps', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            if (Schema::hasColumn('helps', 'order_id')) {
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('helps', 'voucher_code')) {
                $table->dropColumn('voucher_code');
            }
            if (Schema::hasColumn('helps', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('helps', 'booking_fee')) {
                $table->dropColumn('booking_fee');
            }
            if (Schema::hasColumn('helps', 'mitra_assigned_at')) {
                $table->dropColumn('mitra_assigned_at');
            }
            if (Schema::hasColumn('helps', 'partner_started_at')) {
                $table->dropColumn('partner_started_at');
            }
            if (Schema::hasColumn('helps', 'partner_arrived_at')) {
                $table->dropColumn('partner_arrived_at');
            }
            if (Schema::hasColumn('helps', 'service_started_at')) {
                $table->dropColumn('service_started_at');
            }
            if (Schema::hasColumn('helps', 'service_completed_at')) {
                $table->dropColumn('service_completed_at');
            }
            if (Schema::hasColumn('helps', 'scheduled_at')) {
                $table->dropColumn('scheduled_at');
            }
        });
    }
};
