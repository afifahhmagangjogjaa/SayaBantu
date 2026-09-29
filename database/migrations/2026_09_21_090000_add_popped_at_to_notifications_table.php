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
        Schema::table('notifications', function (Blueprint $table) {
            $table->timestamp('popped_at')->nullable()->after('read_at')->index();
        });

        // Set popped_at to created_at for all existing notifications so old historical notifications don't trigger mass popups
        DB::table('notifications')->update([
            'popped_at' => DB::raw('created_at')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['popped_at']);
            $table->dropColumn('popped_at');
        });
    }
};
