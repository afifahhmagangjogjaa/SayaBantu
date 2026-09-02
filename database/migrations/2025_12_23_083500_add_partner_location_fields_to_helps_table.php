<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->decimal('partner_initial_lat', 10, 8)->nullable();
            $table->decimal('partner_initial_lng', 11, 8)->nullable();
            $table->decimal('partner_current_lat', 10, 8)->nullable();
            $table->decimal('partner_current_lng', 11, 8)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            $table->dropColumn([
                'partner_initial_lat',
                'partner_initial_lng',
                'partner_current_lat',
                'partner_current_lng',
            ]);
        });
    }
};
