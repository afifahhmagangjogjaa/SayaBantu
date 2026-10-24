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
     * baik di wilayah Jakarta Pusat maupun DI Yogyakarta.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Sayabantu123!');
        $columns = Schema::getColumnListing('users');
        $hasOccupation = in_array('occupation', $columns);

        // Ambil data kota referensi
        $jakartaPusat = City::where('name', 'like', '%Jakarta Pusat%')->first() ?: City::find(1);
        $jakartaSelatan = City::where('name', 'like', '%Jakarta Selatan%')->first() ?: City::find(4);
        $bogor = City::where('name', 'like', '%Bogor%')->first() ?: City::find(7);
        $kotaYogya = City::where('name', 'like', '%Kota Yogyakarta%')->first() ?: City::find(31);
        $sleman = City::where('name', 'like', '%Sleman%')->first() ?: City::find(32);
        $bantul = City::where('name', 'like', '%Bantul%')->first() ?: City::find(33);

        $jktId = $jakartaPusat ? $jakartaPusat->id : 1;
        $jktName = $jakartaPusat ? $jakartaPusat->name : 'Kota Jakarta Pusat';
        $jktProv = $jakartaPusat ? ($jakartaPusat->province ?? 'DKI Jakarta') : 'DKI Jakarta';

        $yogyaId = $kotaYogya ? $kotaYogya->id : 31;
        $yogyaName = $kotaYogya ? $kotaYogya->name : 'Kota Yogyakarta';
        $yogyaProv = $kotaYogya ? ($kotaYogya->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $slemanId = $sleman ? $sleman->id : 32;
        $slemanName = $sleman ? $sleman->name : 'Kabupaten Sleman';
        $slemanProv = $sleman ? ($sleman->province ?? 'DI Yogyakarta') : 'DI Yogyakarta';

        $accounts = [
            // =========================================================================
            // 1. SUPER ADMIN (Akses penuh ke seluruh sistem & kelola admin)
            // =========================================================================
            [
                'email' => 'superadmin@sayabantu.com',
                'name' => 'Super Admin',
                'password' => $defaultPassword,
                'role' => 'super_admin',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000001',
                'nik' => '3171010101900001',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'address' => 'Kantor Pusat SayaBantu, Jl. Sudirman No. 1, Jakarta',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // =========================================================================
            // 2. ADMIN WILAYAH / KOTA
            // =========================================================================
            [
                'email' => 'admin.jakarta@sayabantu.com',
                'name' => 'Admin Jakarta Pusat',
                'password' => $defaultPassword,
                'role' => 'admin',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567891',
                'nik' => '3171010101900011',
                'address' => 'Kantor Wilayah Jakarta Pusat, Jl. Thamrin No. 45',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],
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
            [
                'email' => 'admin.bogor@sayabantu.com',
                'name' => 'Admin Kota Bogor',
                'password' => $defaultPassword,
                'role' => 'admin',
                'city_id' => $bogor ? $bogor->id : 7,
                'city' => $bogor ? $bogor->name : 'Kota Bogor',
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
                'password' => $defaultPassword,
                'role' => 'admin',
                'city_id' => $jakartaSelatan ? $jakartaSelatan->id : 4,
                'city' => $jakartaSelatan ? $jakartaSelatan->name : 'Kota Jakarta Selatan',
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
                'password' => $defaultPassword,
                'role' => 'admin',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000002',
                'nik' => '3171010101900014',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'address' => 'Kantor Operasional Wilayah',
                'is_completed' => true,
                'email_verified_at' => now(),
            ],

            // =========================================================================
            // 3. CUSTOMER TERVERIFIKASI (Siap buat permintaan bantuan / saldo Rp 1.000.000)
            // =========================================================================
            // 3a. Customer Yogyakarta & Sekitarnya
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
            // 3b. Customer Jakarta Pusat
            [
                'email' => 'customer@sayabantu.com',
                'name' => 'Siti Nurhaliza (Customer)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'kecamatan' => 'Gambir',
                'kelurahan' => 'Gambir',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000004',
                'nik' => '3171015001950001',
                'address' => 'Jl. Kebon Sirih No. 12, Gambir, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],
            [
                'email' => 'budi@example.com',
                'name' => 'Budi Santoso (Customer Jakarta)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Laki-laki',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'kecamatan' => 'Gambir',
                'kelurahan' => 'Gambir',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567892',
                'nik' => '3171011505920002',
                'address' => 'Jl. Merdeka No. 123, Gambir, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 1000000,
            ],

            // =========================================================================
            // 4. MITRA TERVERIFIKASI (Siap ambil & proses bantuan / saldo Rp 250.000)
            // =========================================================================
            // 4a. Mitra Yogyakarta & Sleman (Radius dekat dengan Customer Jogja)
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
            // 4b. Mitra Jakarta Pusat
            [
                'email' => 'mitra@sayabantu.com',
                'name' => 'Rahmat Hidayat (Mitra)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'kecamatan' => 'Menteng',
                'kelurahan' => 'Menteng',
                'verified' => true,
                'status' => 'active',
                'phone' => '081200000005',
                'nik' => '3171012010910003',
                'address' => 'Jl. Cikini Raya No. 88, Menteng, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],
            [
                'email' => 'ahmad@example.com',
                'name' => 'Ahmad Relawan (Mitra)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'kecamatan' => 'Senen',
                'kelurahan' => 'Senen',
                'verified' => true,
                'status' => 'active',
                'phone' => '081234567893',
                'nik' => '3171012508930004',
                'address' => 'Jl. Kemanusiaan No. 456, Senen, Jakarta Pusat',
                'is_completed' => true,
                'email_verified_at' => now(),
                'initial_balance' => 250000,
            ],

            // =========================================================================
            // 5. CUSTOMER PENDING (Belum verifikasi KTP / Untuk testing approval Admin)
            // =========================================================================
            [
                'email' => 'customer.pending@sayabantu.com',
                'name' => 'Dewi Lestari (Pending)',
                'password' => $defaultPassword,
                'role' => 'customer',
                'gender' => 'Perempuan',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'verified' => false,
                'status' => 'active',
                'phone' => '081200000006',
                'nik' => '3171016012970005',
                'address' => 'Jl. Salemba Tengah No. 20, Jakarta Pusat',
                'is_completed' => false,
                'email_verified_at' => now(),
                'initial_balance' => 0,
            ],

            // =========================================================================
            // 6. MITRA PENDING (Belum verifikasi KTP / Menunggu approval Admin)
            // =========================================================================
            [
                'email' => 'mitra.pending@sayabantu.com',
                'name' => 'Fajar Pratama (Pending)',
                'password' => $defaultPassword,
                'role' => 'mitra',
                'gender' => 'Laki-laki',
                'city_id' => $jktId,
                'city' => $jktName,
                'province' => $jktProv,
                'kecamatan' => 'Kemayoran',
                'kelurahan' => 'Gunung Sahari Selatan',
                'verified' => false,
                'status' => 'inactive',
                'phone' => '081200000007',
                'nik' => '3171011804960006',
                'address' => 'Jl. Garuda No. 15, Kemayoran, Jakarta Pusat',
                'is_completed' => false,
                'email_verified_at' => now(),
                'initial_balance' => 0,
            ],
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
                'status' => 'inactive',
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

            // Hubungkan Admin ke kota-kota kelolaannya
            if ($user->role === 'admin' && ! empty($user->city_id)) {
                if ($user->email === 'admin.jogja@test.com') {
                    // Admin Jogja mengelola seluruh 5 wilayah DIY
                    $diyCityIds = [$yogyaId, $slemanId, ($bantul ? $bantul->id : 33)];
                    $user->managedCities()->sync($diyCityIds);
                } else {
                    $user->managedCities()->syncWithoutDetaching([$user->city_id]);
                }
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
            if (! $user->verified && in_array($user->email, ['mitra.pending@sayabantu.com', 'yusuf.mitra@test.com'])) {
                Registration::updateOrCreate(
                    ['email' => $user->email],
                    [
                        'role' => 'mitra',
                        'full_name' => $user->name,
                        'nik' => $user->nik,
                        'place_of_birth' => ($user->city_id == $yogyaId) ? 'Yogyakarta' : 'Jakarta',
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
