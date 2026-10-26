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
            if (! Schema::hasColumn('helps', 'before_photo')) {
                $table->string('before_photo')->nullable()->after('photo');
            }
            if (! Schema::hasColumn('helps', 'before_notes')) {
                $table->text('before_notes')->nullable()->after('before_photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('helps', function (Blueprint $table) {
            if (Schema::hasColumn('helps', 'before_photo')) {
                $table->dropColumn('before_photo');
            }
            if (Schema::hasColumn('helps', 'before_notes')) {
                $table->dropColumn('before_notes');
            }
        });
    }
};
