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
        if (Schema::hasTable('helps') && !Schema::hasColumn('helps', 'help_type')) {
            Schema::table('helps', function (Blueprint $table) {
                $table->string('help_type', 20)->default('scheduled')->after('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('helps') && Schema::hasColumn('helps', 'help_type')) {
            Schema::table('helps', function (Blueprint $table) {
                $table->dropColumn('help_type');
            });
        }
    }
};
