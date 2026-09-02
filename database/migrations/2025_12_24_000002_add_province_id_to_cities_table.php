<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'province_id')) {
                $table->unsignedBigInteger('province_id')->nullable()->after('province')->index();
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'province_id')) {
                $table->dropForeign(['province_id']);
                $table->dropColumn('province_id');
            }
        });
    }
};
