<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Registration;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat akun-akun testing lengkap untuk semua peran (Customer, Mitra, Admin, Super Admin)
     * khusus di wilayah DI Yogyakarta (Kota Yogyakarta, Sleman, Bantul, Gunungkidul, Kulonprogo).
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Sayabantu123!');
        $columns = Schema::getColumnListing('users');
        $hasOccupation = in_array('occupation', $columns);

        // Ambil data kota referensi DI Yogyakarta
        $kotaYogya = City::where('name', 'like', '%Kota Yogyakarta%')->first() ?: City::find(31);
        $sleman = City::where('name', 'like', '%Sleman%')->first() ?: City::find(32);
        $bantul = City::where('name', 'like', '%Bantul%')->first() ?: City::find(33);
        $gunungkidul = City::where('name', 'like', '%Gunungkidul%')->first() ?: City::find(34);
        $kulonprogo = City::where('name', 'like', '%Kulonprogo%')->first() ?: City::find(35);

        $yogyaId = $kotaYogya ? $kotaYogya->id : 31;
        $yogyaName = $kotaYogya ? $kotaYogya->name : 'Kota Yogyakarta';
        $yogyaProv = $kotaYogya ? ($kotaYogya->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $slemanId = $sleman ? $sleman->id : 32;
        $slemanName = $sleman ? $sleman->name : 'Kabupaten Sleman';
        $slemanProv = $sleman ? ($sleman->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $bantulId = $bantul ? $bantul->id : 33;
        $bantulName = $bantul ? $bantul->name : 'Kabupaten Bantul';
        $bantulProv = $bantul ? ($bantul->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $gunungkidulId = $gunungkidul ? $gunungkidul->id : 34;
        $gunungkidulName = $gunungkidul ? $gunungkidul->name : 'Kabupaten Gunungkidul';
        $gunungkidulProv = $gunungkidul ? ($gunungkidul->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $kulonprogoId = $kulonprogo ? $kulonprogo->id : 35;
        $kulonprogoName = $kulonprogo ? $kulonprogo->name : 'Kabupaten Kulonprogo';
        $kulonprogoProv = $kulonprogo ? ($kulonprogo->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $diyCityIds = [$yogyaId, $slemanId, $bantulId, $gunungkidulId, $kulonprogoId];

        $accounts = [
            // =========================================================================
            // 1. SUPER ADMIN (Akses penuh nasional ke seluruh wilayah Indonesia & kelola admin)
            // =========================================================================
            [
                'email' => 'superadmin@sayabantu.com',
                'name' => 'Super Admin (Nasional)',
                'password' => $defaultPassword,
                'role' => 'super_admin',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000001',
                'nik' => '3171010101900001',
                'city_id' => $yogyaId,
                'city' => 'Nasional (Seluruh Indonesia)',
                'province' => 'Indonesia',
                'address' => 'Kantor Pusat SayaBantu Indonesia',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // =========================================================================
            // 2. ADMIN WILAYAH DI YOGYAKARTA (Mengelola seluruh 5 kabupaten/kota DIY)
            // =========================================================================
            [
                'email' => 'admin.jogja@test.com',
                'name' => 'Admin DI Yogyakarta',
                'password' => $defaultPassword,
                'role' => 'admin',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'verified' => true,
                'status' => 'active',
                'phone' => '081234562001',
                'nik' => '3471010101900015',
                'address' => 'Kantor Wilayah DIY, Jl. Malioboro No. 56, Yogyakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // =========================================================================
            // 3. CUSTOMER TERVERIFIKASI (Yogyakarta & Sekitarnya / Saldo Rp 1.000.000)
            // =========================================================================
            [
                'email' => 'dewi.jogja@test.com',
                'name' => 'Dewi Rahayu (Customer)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'kecamatan' => 'Gondokusuman',
                'kelurahan' => 'Terban',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234560001',
                'nik' => '3471015505950001',
                'address' => 'Jl. C. Simanjuntak No. 25, Terban, Gondokusuman, Kota Yogyakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],
            [
                'email' => 'budi.sleman@test.com',
                'name' => 'Budi Hartono (Customer Sleman)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Laki-laki',
                'city_id' => $slemanId,
                'city' => $slemanName,
                'province' => $slemanProv,
                'kecamatan' => 'Depok',
                'kelurahan' => 'Caturtunggal',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234560002',
                'nik' => '3404011505920002',
                'address' => 'Jl. Gejayan No. 88, Caturtunggal, Depok, Sleman',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],
            [
                'email' => 'sari.bantul@test.com',
                'name' => 'Sari Wulandari (Customer Bantul)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $bantulId,
                'city' => $bantulName,
                'province' => $bantulProv,
                'kecamatan' => 'Banguntapan',
                'kelurahan' => 'Baturetno',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234560003',
                'nik' => '3402015005940003',
                'address' => 'Jl. Gedongkuning No. 12, Banguntapan, Bantul',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],
            [
                'email' => 'agus.gunungkidul@test.com',
                'name' => 'Agus Prasetyo (Customer Gunungkidul)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Laki-laki',
                'city_id' => $gunungkidulId,
                'city' => $gunungkidulName,
                'province' => $gunungkidulProv,
                'kecamatan' => 'Wonosari',
                'kelurahan' => 'Kepek',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234560004',
                'nik' => '3403011005900004',
                'address' => 'Jl. KH. Agus Salim No. 5, Wonosari, Gunungkidul',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],
            [
                'email' => 'ratna.kulonprogo@test.com',
                'name' => 'Ratna Kusuma (Customer Kulon Progo)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $kulonprogoId,
                'city' => $kulonprogoName,
                'province' => $kulonprogoProv,
                'kecamatan' => 'Wates',
                'kelurahan' => 'Wates',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234560005',
                'nik' => '3401016005950005',
                'address' => 'Jl. Diponegoro No. 8, Wates, Kulon Progo',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],

            // =========================================================================
            // 4. MITRA TERVERIFIKASI (Yogyakarta & Sekitarnya / Saldo Rp 250.000)
            // =========================================================================
            [
                'email' => 'hendra.mitra@test.com',
                'name' => 'Hendra Wijaya (Mitra)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'kecamatan' => 'Gondokusuman',
                'kelurahan' => 'Kotabaru',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234561001',
                'nik' => '3471012010910001',
                'address' => 'Jl. Kotabaru No. 10, Gondokusuman, Kota Yogyakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],
            [
                'email' => 'rina.mitra@test.com',
                'name' => 'Rina Susanti (Mitra)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Perempuan',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'kecamatan' => 'Mergangsan',
                'kelurahan' => 'Brontokusuman',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234561002',
                'nik' => '3471016508930002',
                'address' => 'Jl. Kolonel Sugiyono No. 44, Brontokusuman, Yogyakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],
            [
                'email' => 'doni.mitra@test.com',
                'name' => 'Doni Setiawan (Mitra Sleman)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $slemanId,
                'city' => $slemanName,
                'province' => $slemanProv,
                'kecamatan' => 'Mlati',
                'kelurahan' => 'Sinduadi',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234561003',
                'nik' => '3404012508930003',
                'address' => 'Jl. Magelang Km 4.5, Sinduadi, Mlati, Sleman',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],
            [
                'email' => 'fitri.mitra@test.com',
                'name' => 'Fitri Handayani (Mitra Bantul)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Perempuan',
                'city_id' => $bantulId,
                'city' => $bantulName,
                'province' => $bantulProv,
                'kecamatan' => 'Sewon',
                'kelurahan' => 'Panggungharjo',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234561004',
                'nik' => '3402016508930004',
                'address' => 'Jl. Bantul Km 6, Panggungharjo, Sewon, Bantul',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],
            [
                'email' => 'budi.mitra@test.com',
                'name' => 'Budi Santoso (Mitra)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'kecamatan' => 'Jetis',
                'kelurahan' => 'Cokrodiningratan',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234561008',
                'nik' => '3471011505920008',
                'address' => 'Jl. AM Sangaji No. 12, Cokrodiningratan, Jetis, Kota Yogyakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],

            // =========================================================================
            // 5. CUSTOMER PENDING JOGJA — data uji onboarding + antrean verifikasi admin
            // =========================================================================
            [
                'email' => 'customer.pending@sayabantu.com',
                'name' => 'Dewi Lestari (Pending Jogja)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'kecamatan' => 'Danurejan',
                'kelurahan' => 'Suryatmajan',
                'verified' => false,
                'status' => 'active',
                'phone' => '081200000006',
                'nik' => '3471016012970005',
                'address' => 'Jl. Danurejan No. 15, Suryatmajan, Danurejan, Kota Yogyakarta',
                'is_completed' => false,
                'email_verified_at' => now(),
                'initial_balance' => 0,
            ],

            // =========================================================================
            // 6. MITRA PENDING JOGJA — data uji antrean verifikasi KTP admin
            // =========================================================================
            [
                'email' => 'yusuf.mitra@test.com',
                'name' => 'Yusuf Pratama (Pending Jogja)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $yogyaId,
                'city' => $yogyaName,
                'province' => $yogyaProv,
                'kecamatan' => 'Umbulharjo',
                'kelurahan' => 'Sorosutan',
                'verified' => false,
                'status' => 'active',
                'phone' => '081234561005',
                'nik' => '3471010101950001',
                'address' => 'Jl. Umbulharjo No. 12, Sorosutan, Umbulharjo, Kota Yogyakarta',
                'is_completed' => false,
                'email_verified_at' => now(),
                'initial_balance' => 0,
            ],
        ];

        foreach ($accounts as $data) {
            $initialBalance = $data['initial_balance'] ?? 0;
            unset($data['initial_balance']);

            if ($hasOccupation && ! isset($data['occupation'])) {
                $data['occupation'] = ($data['role'] === 'mitra') ? 'Teknisi / Rekan Jasa' : 'Karyawan Swasta';
            }

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Hubungkan Admin ke kota-kota kelolaannya (seluruh 5 wilayah DIY)
            if ($user->role === 'admin' && ! empty($user->city_id)) {
                $user->managedCities()->sync($diyCityIds);
            }

            // Pastikan saldo terisi di tabel user_balances, users.balance, dan balance_transactions
            if (in_array($user->role, ['customer', 'kustomer', 'mitra'])) {
                if ($initialBalance > 0) {
                    \App\Models\BalanceTransaction::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'topup',
                            'description' => 'Saldo Awal Akun Testing',
                        ],
                        [
                            'amount' => $initialBalance,
                            'status' => 'completed',
                            'processed_at' => now(),
                        ]
                    );
                }

                UserBalance::updateOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => $initialBalance]
                );
                $user->forceFill(['balance' => $initialBalance])->saveQuietly();
            }

            // Buat entri Registration untuk akun pending agar muncul di antrean verifikasi admin
            if (! $user->verified && in_array($user->email, ['yusuf.mitra@test.com'])) {
                Registration::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'role' => 'mitra',
                        'full_name' => $user->name,
                        'nik' => $user->nik,
                        'place_of_birth' => 'Yogyakarta',
                        'date_of_birth' => '1995-05-15',
                        'gender' => $user->gender ?: 'Laki-laki',
                        'address' => $user->address,
                        'city' => $user->city,
                        'city_id' => $user->city_id,
                        'province' => $user->province,
                        'status' => 'pending_verification',
                    ]
                );
            }
        }
    }
}
