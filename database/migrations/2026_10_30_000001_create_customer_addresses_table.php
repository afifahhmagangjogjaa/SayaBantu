<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label', 50)->default('Rumah'); // Rumah, Kantor, Kos, dll.
            $table->string('recipient_name');
            $table->string('phone', 20);
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('province', 100)->nullable();
            $table->string('city_name', 100)->nullable();
            $table->string('district', 100)->nullable(); // Kecamatan
            $table->string('village', 100)->nullable(); // Kelurahan
            $table->string('postal_code', 10)->nullable();
            $table->text('address'); // Alamat lengkap jalan, nomor rumah, RT/RW
            $table->string('benchmark', 255)->nullable(); // Patokan lokasi
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_primary']);
        });

        // Migrasikan alamat yang sudah ada pada user customer ke customer_addresses
        try {
            $existingCustomers = DB::table('users')
                ->where('role', 'customer')
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->get();

            foreach ($existingCustomers as $user) {
                DB::table('customer_addresses')->insert([
                    'user_id' => $user->id,
                    'label' => 'Rumah',
                    'recipient_name' => $user->name,
                    'phone' => $user->phone ?? '',
                    'city_id' => $user->city_id,
                    'city_name' => $user->city,
                    'province' => $user->province,
                    'district' => $user->kecamatan,
                    'village' => $user->kelurahan,
                    'postal_code' => null,
                    'address' => $user->address,
                    'benchmark' => null,
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel users belum memiliki data
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
