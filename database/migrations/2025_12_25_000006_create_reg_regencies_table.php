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
        Schema::create('reg_regencies', function (Blueprint $table) {
            $table->char('id', 4)->primary();
            $table->char('province_id', 2);
            $table->string('name');

            $table->foreign('province_id')->references('id')->on('reg_provinces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reg_regencies');
    }
};
