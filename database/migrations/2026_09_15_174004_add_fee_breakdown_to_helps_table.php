<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            // Nominal dasar tawaran bantuan
            $table->decimal('base_amount', 12, 2)->default(0)->after('amount');
            
            // Sisi Customer (+ Biaya Layanan)
            $table->decimal('customer_fee_percent', 5, 2)->default(10);
            $table->decimal('customer_fee_amount', 12, 2)->default(0);
            $table->decimal('total_customer_paid', 12, 2)->default(0);

            // Sisi Mitra (- Biaya Platform)
            $table->decimal('mitra_fee_percent', 5, 2)->default(10);
            $table->decimal('mitra_fee_amount', 12, 2)->default(0);
            $table->decimal('net_mitra_amount', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->dropColumn([
                'base_amount',
                'customer_fee_percent',
                'customer_fee_amount',
                'total_customer_paid',
                'mitra_fee_percent',
                'mitra_fee_amount',
                'net_mitra_amount',
            ]);
        });
    }
};