<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PartnerReportController::storeSanction() menulis users.warning_reason dan
 * users.warning_applied_at, tetapi kedua kolom itu tidak pernah dibuat oleh
 * migration mana pun. Akibatnya pemberian Surat Peringatan gagal dengan
 * "Unknown column" (500).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'warning_reason')) {
                $table->text('warning_reason')->nullable()->after('warning_level');
            }

            if (! Schema::hasColumn('users', 'warning_applied_at')) {
                $table->timestamp('warning_applied_at')->nullable()->after('warning_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'warning_applied_at')) {
                $table->dropColumn('warning_applied_at');
            }

            if (Schema::hasColumn('users', 'warning_reason')) {
                $table->dropColumn('warning_reason');
            }
        });
    }
};
