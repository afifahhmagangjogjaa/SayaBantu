<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('districts')) {
            Schema::create('districts', function (Blueprint $table) {
                $table->char('id', 6)->primary();
                $table->integer('regency_id');
                $table->string('name', 255);
                $table->timestamps();

                $table->foreign('regency_id')
                      ->references('id')
                      ->on('regencies')
                      ->onUpdate('cascade')
                      ->onDelete('restrict');

                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
}
