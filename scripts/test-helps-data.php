<?php

$app = require_once __DIR__.'/bootstrap.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "========================================\n";
echo "📊 DATA BANTUAN DARI SEEDER\n";
echo "========================================\n\n";

$helps = App\Models\Help::latest()->take(10)->get();

echo 'Total Bantuan: '.App\Models\Help::count()."\n\n";

foreach ($helps as $index => $help) {
    echo ($index + 1).'. '.$help->title."\n";
    echo '   💰 Rp '.number_format($help->amount, 0, ',', '.')."\n";
    echo '   📍 '.$help->location."\n";
    echo '   📝 '.substr($help->description, 0, 80)."...\n";

    if ($help->latitude && $help->longitude) {
        echo '   🗺️  Lat: '.$help->latitude.', Lng: '.$help->longitude."\n";
    }

    if ($help->full_address) {
        echo '   🏠 '.$help->full_address."\n";
    }

    if ($help->photo) {
        echo '   📷 Foto: '.$help->photo."\n";
    }

    echo "\n";
}

echo "========================================\n";
echo "✅ Seeder berhasil membuat data lengkap!\n";
echo "========================================\n";
