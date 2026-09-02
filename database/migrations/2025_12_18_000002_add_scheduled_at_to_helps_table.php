<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('helps', function (Blueprint $table) {
            if (!Schema::hasColumn('helps', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable()->after('full_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('helps', function (Blueprint $table) {
            if (Schema::hasColumn('helps', 'scheduled_at')) {
                $table->dropColumn('scheduled_at');
            }
        });
    }
};
