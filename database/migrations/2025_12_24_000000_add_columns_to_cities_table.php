<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->string('type')->nullable()->after('name'); // e.g. "Kota" or "Kabupaten"
            $table->string('postal_code')->nullable()->after('province');
            $table->decimal('latitude', 10, 7)->nullable()->after('postal_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['code', 'type', 'postal_code', 'latitude', 'longitude']);
        });
    }
};
