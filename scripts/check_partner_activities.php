<?php

$app = require_once __DIR__.'/bootstrap.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PartnerActivity;

echo 'COUNT: '.PartnerActivity::count().PHP_EOL;
$rows = PartnerActivity::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
foreach ($rows as $r) {
    $time = $r->created_at ? $r->created_at->format('Y-m-d H:i:s') : '-';
    $user = $r->user ? $r->user->email : '-';
    $type = $r->activity_type;
    $desc = $r->description ? str_replace("\n", ' ', substr($r->description, 0, 120)) : '-';
    echo "$time | $user | $type | $desc".PHP_EOL;
}
