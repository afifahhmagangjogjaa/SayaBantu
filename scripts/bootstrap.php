<?php

/**
 * Bootstrap aplikasi untuk skrip manual di folder scripts/.
 *
 * Skrip di folder ini bukan bagian dari aplikasi: tidak di-autoload, tidak
 * dipanggil route/command/scheduler. Semua hanya dijalankan manual.
 *
 * Pemakaian:
 *   scripts/nama_skrip.php        -> $app = require_once __DIR__.'/bootstrap.php';
 *   scripts/legacy/nama_skrip.php -> $app = require_once __DIR__.'/../bootstrap.php';
 *
 * Setelah itu bootstrap kernel console satu kali:
 *   $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
 *
 * @return \Illuminate\Foundation\Application
 */
$basePath = dirname(__DIR__);

require_once $basePath.'/vendor/autoload.php';

return require $basePath.'/bootstrap/app.php';
