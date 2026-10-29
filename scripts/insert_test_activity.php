<?php

$app = require_once __DIR__.'/bootstrap.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PartnerActivity;
use App\Models\User;

$mitra = User::where('role', 'mitra')->first();
if (! $mitra) {
    echo "No mitra user found. Create a user with role=mitra first.\n";
    exit(1);
}

$a = PartnerActivity::create([
    'user_id' => $mitra->id,
    'activity_type' => 'login',
    'description' => 'Test login recorded by script',
    'ip_address' => '127.0.0.1',
    'user_agent' => 'script-test',
]);

echo "Created activity id: {$a->id} for user {$mitra->email}\n";
