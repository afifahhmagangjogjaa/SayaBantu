<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndonesiaRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $citiesPath = database_path('seeders/data/cities.json');
        $districtsPath = database_path('seeders/data/districts.json');

        if (! file_exists($citiesPath)) {
            $this->command->error("Missing file: {$citiesPath}");
            $this->command->line('Please download a JSON list of Indonesian cities/kabupaten and save it to that path.');
            $this->command->line('Expected format: [ { "id": 1, "name": "Ponorogo", "type": "Kabupaten", "province": "Jawa Timur", "code": "351" , "latitude": null, "longitude": null }, ... ]');
            return;
        }

        $citiesJson = json_decode(file_get_contents($citiesPath), true);
        if (! is_array($citiesJson)) {
            $this->command->error('Invalid JSON in cities file');
            return;
        }

        $this->command->info('Importing cities...');
        foreach ($citiesJson as $c) {
            City::updateOrCreate([
                'name' => $c['name'],
                'province' => $c['province'] ?? null,
            ], [
                'code' => $c['code'] ?? null,
                'type' => $c['type'] ?? null,
                'postal_code' => $c['postal_code'] ?? null,
                'latitude' => $c['latitude'] ?? null,
                'longitude' => $c['longitude'] ?? null,
                'is_active' => isset($c['is_active']) ? (bool)$c['is_active'] : true,
            ]);
        }

        if (! file_exists($districtsPath)) {
            $this->command->warn("Districts file not found: {$districtsPath} — skipping districts import.");
            return;
        }

        $districtsJson = json_decode(file_get_contents($districtsPath), true);
        if (! is_array($districtsJson)) {
            $this->command->error('Invalid JSON in districts file');
            return;
        }

        $this->command->info('Importing districts...');
        foreach ($districtsJson as $d) {
            // Try to find city by code or name/province
            $city = null;
            if (! empty($d['city_code'])) {
                $city = City::where('code', $d['city_code'])->first();
            }
            if (! $city && ! empty($d['city_name'])) {
                $city = City::where('name', $d['city_name'])->where('province', $d['province'] ?? null)->first();
            }
            if (! $city && ! empty($d['city_id'])) {
                $city = City::find($d['city_id']);
            }

            if (! $city) continue;

            District::updateOrCreate([
                'city_id' => $city->id,
                'name' => $d['name'],
            ], [
                'code' => $d['code'] ?? ($d['district_code'] ?? null),
                'is_active' => isset($d['is_active']) ? (bool)$d['is_active'] : true,
            ]);
        }

        $this->command->info('Indonesia regions import completed.');
    }
}
