<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('selfie_photo');
        });

        DB::statement("
            UPDATE users
            SET profile_photo = selfie_photo,
                selfie_photo = NULL
            WHERE selfie_photo LIKE 'profile-photos/%'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE users
            SET selfie_photo = profile_photo
            WHERE profile_photo IS NOT NULL
              AND (selfie_photo IS NULL OR selfie_photo NOT LIKE 'selfie-photos/%')
        ");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });
    }
};
