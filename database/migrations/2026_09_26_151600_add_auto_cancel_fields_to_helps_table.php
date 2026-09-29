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
            if (!Schema::hasColumn('helps', 'auto_cancel_minutes')) {
                $table->integer('auto_cancel_minutes')->nullable()->after('scheduled_at');
            }
            if (!Schema::hasColumn('helps', 'auto_cancel_at')) {
                $table->timestamp('auto_cancel_at')->nullable()->after('auto_cancel_minutes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            if (Schema::hasColumn('helps', 'auto_cancel_at')) {
                $table->dropColumn('auto_cancel_at');
            }
            if (Schema::hasColumn('helps', 'auto_cancel_minutes')) {
                $table->dropColumn('auto_cancel_minutes');
            }
        });
    }
};
