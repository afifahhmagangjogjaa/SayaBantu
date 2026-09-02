<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$v = "1";
$verified = filter_var($v, FILTER_VALIDATE_BOOLEAN);
var_dump($verified);
