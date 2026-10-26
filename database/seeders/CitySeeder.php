<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mencoba load dari cities.json, fallback ke data default jika tidak ada.
     */
    public function run(): void
    {
        // Coba load dari file JSON lengkap
        $jsonPath = database_path('seeders/data/cities.json');
        if (file_exists($jsonPath)) {
            $cities = json_decode(file_get_contents($jsonPath), true);
            if (is_array($cities) && count($cities) > 0) {
                foreach ($cities as $c) {
                    City::firstOrCreate(
                        ['name' => $c['name'], 'province' => $c['province'] ?? null],
                        [
                            'code'     => $c['code'] ?? null,
                            'type'     => $c['type'] ?? null,
                            'latitude' => $c['latitude'] ?? null,
                            'longitude'=> $c['longitude'] ?? null,
                            'is_active'=> true,
                        ]
                    );
                }
                return;
            }
        }

        // Fallback: data kota-kota utama jika file JSON tidak tersedia
        $cities = [
            ['name' => 'Jakarta',    'province' => 'DKI Jakarta'],
            ['name' => 'Surabaya',   'province' => 'Jawa Timur'],
            ['name' => 'Bandung',    'province' => 'Jawa Barat'],
            ['name' => 'Medan',      'province' => 'Sumatera Utara'],
            ['name' => 'Semarang',   'province' => 'Jawa Tengah'],
            ['name' => 'Yogyakarta', 'province' => 'DI Yogyakarta'],
            ['name' => 'Makassar',   'province' => 'Sulawesi Selatan'],
            ['name' => 'Palembang',  'province' => 'Sumatera Selatan'],
            ['name' => 'Denpasar',   'province' => 'Bali'],
            ['name' => 'Malang',     'province' => 'Jawa Timur'],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(['name' => $city['name']], $city);
        }
    }
}
