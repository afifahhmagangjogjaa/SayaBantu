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
            if (!Schema::hasColumn('helps', 'completion_photo')) {
                $table->string('completion_photo')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('helps', 'completion_notes')) {
                $table->text('completion_notes')->nullable()->after('completion_photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            if (Schema::hasColumn('helps', 'completion_photo')) {
                $table->dropColumn('completion_photo');
            }
            if (Schema::hasColumn('helps', 'completion_notes')) {
                $table->dropColumn('completion_notes');
            }
        });
    }
};
