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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regency_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
            $table->foreign('regency_id')->references('id')->on('regencies')->onDelete('set null');
        });
    }
};
