<?php

/**
 * Script PHP Runner Mandiri untuk Membersihkan Data Testing Database.
 * 
 * Penggunaan dari terminal:
 *   php clean-db.php
 *   php clean-db.php --force
 *   php clean-db.php --keep-admins
 *   php clean-db.php --clean-storage
 *   php clean-db.php --help
 * 
 * Atau bisa juga langsung melalui perintah Artisan:
 *   php artisan db:clean-test-data
 *   php artisan db:clean-test-data --force
 */

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Tangkap opsi dari argumen command line
$args = array_slice($argv, 1);
$options = ['command' => 'db:clean-test-data'];

if (in_array('--force', $args) || in_array('-f', $args)) {
    $options['--force'] = true;
}
if (in_array('--keep-admins', $args)) {
    $options['--keep-admins'] = true;
}
if (in_array('--clean-storage', $args)) {
    $options['--clean-storage'] = true;
}
if (in_array('--help', $args) || in_array('-h', $args)) {
    $options['--help'] = true;
}

$input = new Symfony\Component\Console\Input\ArrayInput($options);
$output = new Symfony\Component\Console\Output\ConsoleOutput();

$status = $kernel->handle($input, $output);

$kernel->terminate($input, $status);

exit($status);
