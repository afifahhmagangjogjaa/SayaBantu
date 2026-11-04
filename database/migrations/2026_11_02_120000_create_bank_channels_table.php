<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_channels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->enum('category', ['bank', 'ewallet'])->default('bank');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Seed bank dan e-wallet awal
        $defaultBanks = [
            ['code' => 'BCA', 'name' => 'BCA (Bank Central Asia)', 'category' => 'bank', 'is_active' => true, 'order' => 1],
            ['code' => 'BRI', 'name' => 'BRI (Bank Rakyat Indonesia)', 'category' => 'bank', 'is_active' => true, 'order' => 2],
            ['code' => 'BNI', 'name' => 'BNI (Bank Negara Indonesia)', 'category' => 'bank', 'is_active' => true, 'order' => 3],
            ['code' => 'MANDIRI', 'name' => 'Bank Mandiri', 'category' => 'bank', 'is_active' => true, 'order' => 4],
            ['code' => 'BSI', 'name' => 'BSI (Bank Syariah Indonesia)', 'category' => 'bank', 'is_active' => true, 'order' => 5],
            ['code' => 'CIMB', 'name' => 'CIMB Niaga', 'category' => 'bank', 'is_active' => true, 'order' => 6],
            ['code' => 'PERMATA', 'name' => 'Bank Permata', 'category' => 'bank', 'is_active' => false, 'order' => 7],
            ['code' => 'DANAMON', 'name' => 'Bank Danamon', 'category' => 'bank', 'is_active' => false, 'order' => 8],
            ['code' => 'SEABANK', 'name' => 'SeaBank', 'category' => 'bank', 'is_active' => true, 'order' => 9],
            ['code' => 'JAGO', 'name' => 'Bank Jago', 'category' => 'bank', 'is_active' => true, 'order' => 10],
            ['code' => 'DANA', 'name' => 'DANA', 'category' => 'ewallet', 'is_active' => true, 'order' => 11],
            ['code' => 'GOPAY', 'name' => 'GoPay', 'category' => 'ewallet', 'is_active' => true, 'order' => 12],
            ['code' => 'OVO', 'name' => 'OVO', 'category' => 'ewallet', 'is_active' => true, 'order' => 13],
            ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay', 'category' => 'ewallet', 'is_active' => true, 'order' => 14],
            ['code' => 'LINKAJA', 'name' => 'LinkAja', 'category' => 'ewallet', 'is_active' => false, 'order' => 15],
        ];

        foreach ($defaultBanks as $bank) {
            DB::table('bank_channels')->insert(array_merge($bank, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_channels');
    }
};

