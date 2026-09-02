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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_shadow_banned')->default(false)->after('status');
            $table->timestamp('shadow_banned_at')->nullable()->after('is_shadow_banned');
            $table->text('shadow_ban_reason')->nullable()->after('shadow_banned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_shadow_banned', 'shadow_banned_at', 'shadow_ban_reason']);
        });
    }
};
