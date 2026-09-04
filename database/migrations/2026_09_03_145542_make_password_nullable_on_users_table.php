<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            
            // Tambahkan kolom penanda jika belum ada
            if (!Schema::hasColumn('users', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 16)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'ktp_photo')) {
                $table->string('ktp_photo')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'selfie_photo')) {
                $table->string('selfie_photo')->nullable()->after('ktp_photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};