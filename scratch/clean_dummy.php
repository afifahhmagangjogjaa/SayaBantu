<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// List tables (MySQL)
$tables = DB::select("SHOW TABLES");
echo "=== TABEL YANG ADA ===\n";
$allTables = [];
foreach ($tables as $t) {
    $arr = (array)$t;
    $tableName = reset($arr);
    $allTables[] = $tableName;
    $count = DB::table($tableName)->count();
    echo "  {$tableName}: {$count} baris\n";
}

// Tabel yang perlu dihapus datanya (dummy / transaksional)
$cleanTables = ['helps', 'help_applications', 'balance_transactions', 'user_balances', 'notifications'];

echo "\n=== HAPUS DATA DUMMY ===\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0');

// 1. Hapus semua data di tabel transaksional
foreach ($cleanTables as $table) {
    if (in_array($table, $allTables)) {
        $count = DB::table($table)->count();
        DB::table($table)->truncate();
        echo "  TRUNCATE {$table}: {$count} baris dihapus\n";
    } else {
        echo "  SKIP {$table}: tabel tidak ada\n";
    }
}

// 2. Hapus user dummy (bukan super_admin)
if (in_array('users', $allTables)) {
    $count = DB::table('users')->where('role', '!=', 'super_admin')->count();
    DB::table('users')->where('role', '!=', 'super_admin')->delete();
    echo "  HAPUS users (non super_admin): {$count} baris dihapus\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n=== DATA TERSISA SETELAH PEMBERSIHAN ===\n";
foreach ($allTables as $tableName) {
    if (in_array($tableName, ['migrations'])) continue;
    $count = DB::table($tableName)->count();
    echo "  {$tableName}: {$count} baris\n";
}

echo "\nSelesai!\n";
