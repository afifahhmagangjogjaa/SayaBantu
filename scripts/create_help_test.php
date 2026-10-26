<?php

$app = require_once __DIR__.'/bootstrap.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Help;
use App\Models\PartnerActivity;
use App\Models\User;

$user = User::where('role', 'customer')->first() ?? User::first();
if (! $user) {
    echo "No user found\n";
    exit(1);
}

$help = Help::create([
    'user_id' => $user->id,
    'city_id' => 1,
    'title' => 'Script Test Help',
    'amount' => 15000,
    'description' => 'Created via script to test observer',
    'location' => 'Script location',
    'status' => 'menunggu_mitra',
]);

echo "Help created id: {$help->id}\n";

$activities = PartnerActivity::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();
foreach ($activities as $a) {
    echo $a->id.' | '.$a->activity_type.' | '.($a->description ?? '-').'\n';
}
