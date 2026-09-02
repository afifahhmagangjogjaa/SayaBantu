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
        Schema::create('admin_city', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('city_id')->constrained('cities')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['user_id', 'city_id']);
        });

        // Migrate existing data from cities.admin_id to admin_city table
        DB::statement('
            INSERT INTO admin_city (user_id, city_id, created_at, updated_at)
            SELECT admin_id, id, NOW(), NOW()
            FROM cities
            WHERE admin_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_city');
    }
};
