<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\City;
use App\Models\User;
use App\Models\Registration;

$admin = User::where('email', 'admin.jogja@test.com')->first();
$adminCityIds = $admin->getAdminCityIds();

echo "=============================================\n";
echo "STATUS ADMIN JOGJA ({$admin->name})\n";
echo "=============================================\n";
echo "Primary City ID : {$admin->city_id}\n";
echo "Managed City IDs: " . implode(', ', $adminCityIds) . "\n";
echo "Daftar Kota yang dikelola:\n";
foreach (City::whereIn('id', $adminCityIds)->get() as $city) {
    echo "  - [ID: {$city->id}] {$city->name} (Provinsi: {$city->province})\n";
}

echo "\n--- 1. DATA CUSTOMER DI ADMIN JOGJA ---\n";
$customers = User::whereIn('role', ['customer', 'kustomer'])
    ->whereIn('city_id', $adminCityIds)
    ->get(['id', 'name', 'email', 'city_id']);
echo "Total Customer terlihat: " . $customers->count() . "\n";
foreach ($customers as $c) {
    $cityName = City::find($c->city_id)->name ?? '-';
    echo "  - {$c->name} ({$c->email}) -> {$cityName}\n";
}

echo "\n--- 2. DATA MITRA DI ADMIN JOGJA ---\n";
$mitras = User::where('role', 'mitra')
    ->whereIn('city_id', $adminCityIds)
    ->get(['id', 'name', 'email', 'city_id', 'verified']);
echo "Total Mitra terlihat: " . $mitras->count() . "\n";
foreach ($mitras as $m) {
    $cityName = City::find($m->city_id)->name ?? '-';
    $verif = $m->verified ? 'Terverifikasi' : 'Belum Verifikasi';
    echo "  - {$m->name} ({$m->email}) -> {$cityName} [{$verif}]\n";
}

echo "\n--- 3. DATA VERIFIKASI PENDING DI ADMIN JOGJA ---\n";
$pendingRegs = Registration::whereIn('city_id', $adminCityIds)
    ->whereNotIn('status', ['approved', 'rejected'])
    ->get(['id', 'full_name', 'email', 'city_id', 'status']);
echo "Total Registrasi Pending: " . $pendingRegs->count() . "\n";
foreach ($pendingRegs as $r) {
    $cityName = City::find($r->city_id)->name ?? '-';
    echo "  - {$r->full_name} ({$r->email}) -> {$cityName} [status: {$r->status}]\n";
}
echo "=============================================\n";
