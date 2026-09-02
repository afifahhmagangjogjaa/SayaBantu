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
        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                if (!Schema::hasColumn('registrations', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('password');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('registrations')) {
            Schema::table('registrations', function (Blueprint $table) {
                if (Schema::hasColumn('registrations', 'rejection_reason')) {
                    $table->dropColumn('rejection_reason');
                }
            });
        }
    }
};
