<?php

/**
 * =========================================================================
 * CLI SCRIPT: MEMPERCEPAT WAKTU TUNGGU PESANAN TERJADWAL (TESTING ROLE MITRA)
 * =========================================================================
 * 
 * Skrip ini digunakan untuk mempercepat jadwal pelaksanaan pesanan bantuan
 * yang bertipe "Terjadwal" sehingga syarat H-1 jam langsung terpenuhi dan
 * tombol "Menuju ke Lokasi Customer" langsung aktif di tampilan Role Mitra.
 * 
 * Cara Penggunaan di Terminal:
 *   1. Interaktif (menampilkan daftar pesanan):
 *      php percepat_jadwal.php
 * 
 *   2. Langsung sebutkan ID pesanan:
 *      php percepat_jadwal.php 45
 * 
 *   3. Custom menit jadwal dari sekarang (misal: 15 menit lagi):
 *      php percepat_jadwal.php 45 --minutes=15
 * 
 *   4. Mengembalikan jadwal ke kondisi terkunci (H-3 jam):
 *      php percepat_jadwal.php 45 --lock
 * 
 * Anda juga bisa menjalankan via php artisan:
 *   php artisan helps:fast-forward
 * =========================================================================
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$argv = $_SERVER['argv'];
array_shift($argv); // Buang nama file skrip ini
array_unshift($argv, 'artisan', 'helps:fast-forward');

$input = new \Symfony\Component\Console\Input\ArgvInput($argv);
$output = new \Symfony\Component\Console\Output\ConsoleOutput();

$status = $kernel->handle($input, $output);
$kernel->terminate($input, $status);

exit($status);
