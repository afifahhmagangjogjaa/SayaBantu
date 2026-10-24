<?php

$app = require_once __DIR__.'/../bootstrap.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$password = 'password';
$kotaYogya = 31;
$sleman = 32;
$bantul = 33;
$gunungkidul = 34;
$kulonprogo = 35;

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
$hasOccupation = in_array('occupation', $columns);

function makeUser($data, $password, $hasOccupation)
{
    $existing = User::where('email', $data['email'])->first();
    if ($existing) {
        echo "  [SKIP] {$data['email']} sudah ada (ID:{$existing->id})\n";

        return $existing;
    }
    $base = [
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($password),
        'role' => $data['role'],
        'city_id' => $data['city_id'],
        'status' => $data['status'] ?? 'active',
        'verified' => $data['verified'] ?? true,
        'email_verified_at' => $data['email_verified_at'] ?? now(),
        'phone' => $data['phone'],
        'gender' => $data['gender'],
        'address' => $data['address'] ?? 'Jl. Testing No. 1, Yogyakarta',
        'province' => 'DI Yogyakarta',
        'city' => $data['city_name'],
        'kecamatan' => $data['kecamatan'],
        'kelurahan' => $data['kelurahan'],
        'rt' => '01',
        'rw' => '02',
        'is_completed' => true,
        'balance' => $data['balance'] ?? 0,
    ];
    if ($hasOccupation) {
        $base['occupation'] = $data['occupation'] ?? 'Wiraswasta';
    }
    $user = User::create($base);
    echo "  [OK] ({$user->role}) {$user->name} | {$user->email} (ID:{$user->id})\n";

    return $user;
}

// ========================
// 5 CUSTOMER JOGJA
// ========================
echo "\n=== BUAT 5 CUSTOMER JOGJA ===\n";
makeUser([
    'name' => 'Dewi Rahayu (Customer Jogja)', 'email' => 'dewi.jogja@test.com',
    'role' => 'customer', 'city_id' => $kotaYogya, 'city_name' => 'Kota Yogyakarta',
    'phone' => '081234560001', 'gender' => 'Perempuan', 'balance' => 500000,
    'kecamatan' => 'Gondokusuman', 'kelurahan' => 'Terban', 'occupation' => 'Ibu Rumah Tangga',
], $password, $hasOccupation);

makeUser([
    'name' => 'Budi Hartono (Customer Sleman)', 'email' => 'budi.sleman@test.com',
    'role' => 'customer', 'city_id' => $sleman, 'city_name' => 'Kabupaten Sleman',
    'phone' => '081234560002', 'gender' => 'Laki-laki', 'balance' => 300000,
    'kecamatan' => 'Depok', 'kelurahan' => 'Caturtunggal', 'occupation' => 'Karyawan Swasta',
], $password, $hasOccupation);

makeUser([
    'name' => 'Sari Wulandari (Customer Bantul)', 'email' => 'sari.bantul@test.com',
    'role' => 'customer', 'city_id' => $bantul, 'city_name' => 'Kabupaten Bantul',
    'phone' => '081234560003', 'gender' => 'Perempuan', 'balance' => 750000,
    'kecamatan' => 'Bantul', 'kelurahan' => 'Bantul', 'occupation' => 'Guru',
], $password, $hasOccupation);

makeUser([
    'name' => 'Agus Prasetyo (Customer Gunungkidul)', 'email' => 'agus.gunungkidul@test.com',
    'role' => 'customer', 'city_id' => $gunungkidul, 'city_name' => 'Kabupaten Gunungkidul',
    'phone' => '081234560004', 'gender' => 'Laki-laki', 'balance' => 200000,
    'kecamatan' => 'Wonosari', 'kelurahan' => 'Wonosari', 'occupation' => 'Petani',
], $password, $hasOccupation);

makeUser([
    'name' => 'Ratna Kusuma (Customer Kulonprogo)', 'email' => 'ratna.kulonprogo@test.com',
    'role' => 'customer', 'city_id' => $kulonprogo, 'city_name' => 'Kabupaten Kulonprogo',
    'phone' => '081234560005', 'gender' => 'Perempuan', 'balance' => 400000,
    'kecamatan' => 'Wates', 'kelurahan' => 'Wates', 'occupation' => 'Pedagang',
], $password, $hasOccupation);

// ========================
// 5 MITRA JOGJA
// ========================
echo "\n=== BUAT 5 MITRA JOGJA ===\n";
makeUser([
    'name' => 'Hendra Wijaya (Mitra Jogja)', 'email' => 'hendra.mitra@test.com',
    'role' => 'mitra', 'city_id' => $kotaYogya, 'city_name' => 'Kota Yogyakarta',
    'phone' => '081234561001', 'gender' => 'Laki-laki', 'balance' => 0,
    'verified' => true, 'status' => 'active',
    'kecamatan' => 'Gondokusuman', 'kelurahan' => 'Kotabaru', 'occupation' => 'Teknisi',
], $password, $hasOccupation);

makeUser([
    'name' => 'Rina Susanti (Mitra Jogja)', 'email' => 'rina.mitra@test.com',
    'role' => 'mitra', 'city_id' => $kotaYogya, 'city_name' => 'Kota Yogyakarta',
    'phone' => '081234561002', 'gender' => 'Perempuan', 'balance' => 0,
    'verified' => true, 'status' => 'active',
    'kecamatan' => 'Mergangsan', 'kelurahan' => 'Brontokusuman', 'occupation' => 'Perawat',
], $password, $hasOccupation);

makeUser([
    'name' => 'Doni Setiawan (Mitra Sleman)', 'email' => 'doni.mitra@test.com',
    'role' => 'mitra', 'city_id' => $sleman, 'city_name' => 'Kabupaten Sleman',
    'phone' => '081234561003', 'gender' => 'Laki-laki', 'balance' => 0,
    'verified' => true, 'status' => 'active',
    'kecamatan' => 'Mlati', 'kelurahan' => 'Sinduadi', 'occupation' => 'Tukang',
], $password, $hasOccupation);

makeUser([
    'name' => 'Fitri Handayani (Mitra Bantul)', 'email' => 'fitri.mitra@test.com',
    'role' => 'mitra', 'city_id' => $bantul, 'city_name' => 'Kabupaten Bantul',
    'phone' => '081234561004', 'gender' => 'Perempuan', 'balance' => 0,
    'verified' => true, 'status' => 'active',
    'kecamatan' => 'Sewon', 'kelurahan' => 'Panggungharjo', 'occupation' => 'Guru',
], $password, $hasOccupation);

makeUser([
    'name' => 'Yusuf Pratama (Mitra - Belum Verif)', 'email' => 'yusuf.mitra@test.com',
    'role' => 'mitra', 'city_id' => $kotaYogya, 'city_name' => 'Kota Yogyakarta',
    'phone' => '081234561005', 'gender' => 'Laki-laki', 'balance' => 0,
    'verified' => false, 'status' => 'active',
    'kecamatan' => 'Umbulharjo', 'kelurahan' => 'Sorosutan', 'occupation' => 'Driver',
], $password, $hasOccupation);

// Buat data pendaftaran pending untuk testing menu Verifikasi KTP
\App\Models\Registration::firstOrCreate(
    ['email' => 'yusuf.mitra@test.com'],
    [
        'role' => 'mitra',
        'full_name' => 'Yusuf Pratama',
        'nik' => '3471010101950001',
        'place_of_birth' => 'Yogyakarta',
        'date_of_birth' => '1995-05-15',
        'gender' => 'Laki-laki',
        'address' => 'Jl. Umbulharjo No. 12',
        'rt' => '02',
        'rw' => '03',
        'kelurahan' => 'Sorosutan',
        'kecamatan' => 'Umbulharjo',
        'city' => 'Kota Yogyakarta',
        'city_id' => $kotaYogya,
        'province' => 'DI Yogyakarta',
        'status' => 'pending_verification',
    ]
);

// ========================
// 1 ADMIN JOGJA
// ========================
echo "\n=== BUAT 1 ADMIN JOGJA ===\n";
$adminJogja = makeUser([
    'name' => 'Admin Yogyakarta', 'email' => 'admin.jogja@test.com',
    'role' => 'admin', 'city_id' => $kotaYogya, 'city_name' => 'Kota Yogyakarta',
    'phone' => '081234562001', 'gender' => 'Laki-laki', 'balance' => 0,
    'verified' => true, 'status' => 'active',
    'kecamatan' => 'Gondokusuman', 'kelurahan' => 'Terban', 'occupation' => 'Admin',
], $password, $hasOccupation);

// Hubungkan seluruh 5 kota/kabupaten di DI Yogyakarta ke Admin Yogyakarta
$jogjaCityIds = [$kotaYogya, $sleman, $bantul, $gunungkidul, $kulonprogo];
$adminJogja->managedCities()->sync($jogjaCityIds);
City::whereIn('id', $jogjaCityIds)->update(['admin_id' => $adminJogja->id]);
echo '  [OK] Admin Yogyakarta dikaitkan dengan 5 kota DIY: '.implode(', ', $jogjaCityIds)."\n";

echo "\n=== RINGKASAN AKUN TESTING JOGJA ===\n";
echo "Password semua akun: {$password}\n\n";
$emails = [
    'dewi.jogja@test.com', 'budi.sleman@test.com', 'sari.bantul@test.com',
    'agus.gunungkidul@test.com', 'ratna.kulonprogo@test.com',
    'hendra.mitra@test.com', 'rina.mitra@test.com', 'doni.mitra@test.com',
    'fitri.mitra@test.com', 'yusuf.mitra@test.com',
    'admin.jogja@test.com',
];
foreach ($emails as $email) {
    $u = User::where('email', $email)->first(['id', 'name', 'email', 'role', 'city_id', 'verified', 'status']);
    if ($u) {
        $city = $u->city_id ? City::find($u->city_id) : null;
        echo sprintf("  [%-8s] %-40s | %-25s | verified:%s | %s\n",
            $u->role, $u->email, optional($city)->name, $u->verified ? 'Ya' : 'Tidak', $u->status);
    }
}
