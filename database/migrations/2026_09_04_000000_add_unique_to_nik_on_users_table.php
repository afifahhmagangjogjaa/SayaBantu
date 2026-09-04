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
        $existingIndexes = collect(Schema::getIndexes('users'))->pluck('name')->toArray();

        if (!in_array('users_nik_unique', $existingIndexes)) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('nik', 'users_nik_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $existingIndexes = collect(Schema::getIndexes('users'))->pluck('name')->toArray();

        if (in_array('users_nik_unique', $existingIndexes)) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_nik_unique');
            });
        }
    }
};
