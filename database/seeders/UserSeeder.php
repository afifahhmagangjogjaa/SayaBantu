<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
use App\Models\UserBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat akun-akun testing untuk semua peran (role) dan status verifikasi.
     */
    public function run(): void
    {
        $defaultCity = City::first();
        $cityId = $defaultCity ? $defaultCity->id : 1;
        $cityName = $defaultCity ? $defaultCity->name : 'Kota Jakarta Pusat';
        $provinceName = $defaultCity ? ($defaultCity->province ?? 'DKI Jakarta') : 'DKI Jakarta';

        $accounts = [
            // 1. SUPER ADMIN (Akses penuh ke seluruh sistem & kelola admin)
            [
                'email' => 'superadmin@sayabantu.com',
                'name' => 'Super Admin',
                'password' => 'Sayabantu123!',
                'role' => 'super_admin',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000001',
                'nik' => '3171010101900001',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'address' => 'Kantor Pusat SayaBantu, Jl. Sudirman No. 1, Jakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // 2. ADMIN WILAYAH / KOTA (Kelola verifikasi & operasional wilayah masing-masing)
            [
                'email' => 'admin.jakarta@sayabantu.com',
                'name' => 'Admin Jakarta Pusat',
                'password' => 'Sayabantu123!',
                'role' => 'admin',
                'city_id' => 1,
                'city' => 'Kota Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567891',
                'nik' => '3171010101900011',
                'address' => 'Kantor Wilayah Jakarta Pusat, Jl. Thamrin No. 45',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'admin.bogor@sayabantu.com',
                'name' => 'Admin Kota Bogor',
                'password' => 'Sayabantu123!',
                'role' => 'admin',
                'city_id' => 7,
                'city' => 'Kota Bogor',
                'province' => 'Jawa Barat',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567894',
                'nik' => '3271010101900012',
                'address' => 'Kantor Wilayah Bogor, Jl. Pajajaran No. 10, Bogor',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'admin.jaksel@sayabantu.com',
                'name' => 'Admin Jakarta Selatan',
                'password' => 'Sayabantu123!',
                'role' => 'admin',
                'city_id' => 4,
                'city' => 'Kota Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567895',
                'nik' => '3174010101900013',
                'address' => 'Kantor Wilayah Jakarta Selatan, Jl. Fatmawati No. 8',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'admin@sayabantu.com',
                'name' => 'Admin Utama',
                'password' => 'Sayabantu123!',
                'role' => 'admin',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000002',
                'nik' => '3171010101900014',
                'city_id' => 1,
                'city' => 'Kota Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'address' => 'Kantor Operasional Wilayah',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // 3. CUSTOMER TERVERIFIKASI (Siap buat permintaan bantuan / pesan mitra)
            [
                'email' => 'customer@sayabantu.com',
                'name' => 'Siti Nurhaliza (Customer)',
                'password' => 'Sayabantu123!',
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'kecamatan' => 'Gambir',
                'kelurahan' => 'Gambir',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000004',
                'nik' => '3171015001950001',
                'address' => 'Jl. Kebon Sirih No. 12, Gambir, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'budi@example.com',
                'name' => 'Budi Santoso',
                'password' => 'Sayabantu123!',
                'role' => 'customer',
                'gender' => 'Laki-laki',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'kecamatan' => 'Gambir',
                'kelurahan' => 'Gambir',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567892',
                'nik' => '3171011505920002',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // 4. MITRA TERVERIFIKASI (Siap terima tawaran pekerjaan / ambil bantuan)
            [
                'email' => 'mitra@sayabantu.com',
                'name' => 'Rahmat Hidayat (Mitra)',
                'password' => 'Sayabantu123!',
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'kecamatan' => 'Menteng',
                'kelurahan' => 'Menteng',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000005',
                'nik' => '3171012010910003',
                'address' => 'Jl. Cikini Raya No. 88, Menteng, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'ahmad@example.com',
                'name' => 'Ahmad Relawan',
                'password' => 'Sayabantu123!',
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'kecamatan' => 'Senen',
                'kelurahan' => 'Senen',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567893',
                'nik' => '3171012508930004',
                'address' => 'Jl. Kemanusiaan No. 456, Senen, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // 5. CUSTOMER BARU (Belum verifikasi KTP / Untuk testing alur verifikasi)
            [
                'email' => 'customer.pending@sayabantu.com',
                'name' => 'Dewi Lestari (Pending)',
                'password' => 'Sayabantu123!',
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'verified' => false,
                'status' => 'active',
                'phone' => '081200000006',
                'nik' => '3171016012970005',
                'address' => 'Jl. Salemba Tengah No. 20',
                'is_completed' => false,
                'email_verified_at' => now(),
            ],

            // 6. MITRA BARU (Belum verifikasi KTP / Menunggu approval Admin)
            [
                'email' => 'mitra.pending@sayabantu.com',
                'name' => 'Fajar Pratama (Pending)',
                'password' => 'Sayabantu123!',
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $cityId,
                'city' => $cityName,
                'province' => $provinceName,
                'kecamatan' => 'Kemayoran',
                'kelurahan' => 'Gunung Sahari Selatan',
                'verified' => false,
                'status' => 'inactive',
                'phone' => '081200000007',
                'nik' => '3171011804960006',
                'address' => 'Jl. Garuda No. 15, Kemayoran',
                'is_completed' => false,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($accounts as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Hubungkan Admin Kota ke pivot admin_city
            if ($user->role === 'admin' && !empty($user->city_id)) {
                $user->managedCities()->syncWithoutDetaching([$user->city_id]);
            }

            // Pastikan saldo awal ada untuk customer & mitra
            if (in_array($user->role, ['customer', 'kustomer', 'mitra'])) {
                UserBalance::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 150000] // Saldo awal Rp 150.000 untuk pengujian transaksi
                );
            }
        }
    }
}
