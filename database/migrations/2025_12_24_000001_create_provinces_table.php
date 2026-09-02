<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable()->index();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('provinces');
    }
};
