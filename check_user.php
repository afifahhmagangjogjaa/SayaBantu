<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('nik', '3310198908908098')->first();
echo json_encode($u ? ['verified' => $u->verified, 'name' => $u->name] : 'Not Found');
