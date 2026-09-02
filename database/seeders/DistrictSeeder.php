<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Minimal sample districts mapped to existing sample cities
        $map = [
            'Jakarta' => ['Jakarta Pusat','Jakarta Selatan','Jakarta Barat'],
            'Surabaya' => ['Sukolilo','Wonokromo','Genteng'],
            'Bandung' => ['Coblong','Sukasari','Cicendo'],
            'Medan' => ['Medan Kota','Medan Polonia','Medan Petisah'],
            'Semarang' => ['Semarang Tengah','Semarang Utara','Semarang Selatan'],
        ];

        foreach ($map as $cityName => $districts) {
            $city = City::where('name', $cityName)->first();
            if (! $city) continue;

            foreach ($districts as $d) {
                District::updateOrCreate([
                    'city_id' => $city->id,
                    'name' => $d,
                ], [
                    'code' => null,
                    'is_active' => true,
                ]);
            }
        }
    }
}
