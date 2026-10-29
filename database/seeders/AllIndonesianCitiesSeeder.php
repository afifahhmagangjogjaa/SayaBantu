<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AllIndonesianCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi seluruh 514 Kota dan Kabupaten di 38 Provinsi se-Indonesia ke dalam tabel cities.
     * Mencegah duplikasi data, menyelaraskan kode Kemendagri, dan menjaga relasi data eksisting.
     */
    public function run(): void
    {
        $filePath = database_path('data/cities_master_indonesia.json');

        if (! File::exists($filePath)) {
            $this->command->error("File data master tidak ditemukan: {$filePath}");

            return;
        }

        $master = json_decode(File::get($filePath), true);

        if (! is_array($master) || empty($master)) {
            $this->command->error('Data master kota kosong atau format JSON salah.');

            return;
        }

        // 1. Bersihkan entry bogus lawas "Kota Lampung" (ID 76) jika tidak memiliki relasi data
        $bogus = City::where('id', 76)->where('name', 'Kota Lampung')->first();
        if ($bogus) {
            $userCount = DB::table('users')->where('city_id', 76)->count();
            $helpCount = DB::table('helps')->where('city_id', 76)->count();
            $adminCount = DB::table('admin_city')->where('city_id', 76)->count();
            if ($userCount === 0 && $helpCount === 0 && $adminCount === 0) {
                $bogus->delete();
            }
        }

        $provMap = [
            'Daerah Istimewa Yogyakarta' => 'DI Yogyakarta',
            'Kepulauan Bangka Belitung' => 'Bangka Belitung',
        ];

        $clean = function ($name) {
            $n = preg_replace('/^(KOTA\s+ADMINISTRASI|KABUPATEN\s+ADMINISTRASI|KOTA|KABUPATEN|KAB|ADM)\s+/i', '', trim($name));

            return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $n));
        };

        $existingCities = City::all();
        $matchedExistingIds = [];

        $insertedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($master as $m) {
                $mProvince = $provMap[$m['province']] ?? $m['province'];
                $mClean = $clean($m['name']);
                $mType = $m['type'] ?? (str_starts_with(strtolower($m['name']), 'kota') ? 'Kota' : 'Kabupaten');
                $mCode = $m['code'];

                // Cari kecocokan data eksisting berdasarkan jenis (Kota vs Kabupaten) dan nama inti
                $found = null;
                foreach ($existingCities as $ec) {
                    if (in_array($ec->id, $matchedExistingIds, true)) {
                        continue;
                    }

                    $ecClean = $clean($ec->name);
                    $ecType = str_starts_with(strtolower($ec->name), 'kota') ? 'Kota' : 'Kabupaten';

                    if ($ecClean === $mClean && $ecType === $mType) {
                        $found = $ec;
                        break;
                    }
                }

                if ($found) {
                    $matchedExistingIds[] = $found->id;
                    $found->update([
                        'code' => $mCode,
                        'type' => $mType,
                        'province' => $mProvince,
                        'is_active' => true,
                    ]);
                    $updatedCount++;
                } else {
                    City::create([
                        'name' => $m['name'],
                        'type' => $mType,
                        'province' => $mProvince,
                        'code' => $mCode,
                        'is_active' => true,
                    ]);
                    $insertedCount++;
                }
            }

            DB::commit();

            $total = City::count();
            $this->command->info("Sinkronisasi selesai! {$insertedCount} kota/kabupaten baru ditambahkan, {$updatedCount} kota diperbarui.");
            $this->command->info("Total kota/kabupaten aktif di database sekarang: {$total}");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Gagal sinkronisasi kota: {$e->getMessage()}");
            throw $e;
        }
    }
}
