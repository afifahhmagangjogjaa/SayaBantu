<?php

namespace Database\Seeders;

use App\Models\Help;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class HelpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Only pick users with customer role as help creators
        $userIds = User::whereIn('role', ['customer', 'kustomer'])->pluck('id')->toArray();
        $cityIds = City::pluck('id')->toArray();

        // If there are no customer users in DB, fallback to any available user
        if (empty($userIds)) {
            $userIds = User::pluck('id')->toArray() ?: [1];
        }
        if (empty($cityIds)) {
            $cityIds = [1];
        }

        $hasCategory = Schema::hasColumn('helps', 'category_id');

        for ($i = 0; $i < 25; $i++) {
            $data = [
                'user_id' => $faker->randomElement($userIds),
                'city_id' => $faker->randomElement($cityIds),
                'title' => ucfirst($faker->words($faker->numberBetween(2, 5), true)),
                'description' => $faker->paragraph(),
                'location' => $faker->address(),
                'amount' => $faker->numberBetween(10000, 200000),
                'photo' => null,
                'status' => 'menunggu_mitra',
            ];

            if ($hasCategory) {
                // pick an existing category id if available, otherwise skip
                $categoryIds = \DB::table('categories')->pluck('id')->toArray();
                if (!empty($categoryIds)) {
                    $data['category_id'] = $faker->randomElement($categoryIds);
                }
            }

            Help::create($data);
        }

        // Pastikan semua data bantuan terasosiasi dengan user customer yang valid dan user_id != mitra_id
        if (!empty($userIds)) {
            $invalidHelps = Help::whereNotIn('user_id', $userIds)
                ->orWhereColumn('user_id', 'mitra_id')
                ->get();

            foreach ($invalidHelps as $idx => $help) {
                $candidates = array_values(array_diff($userIds, [$help->mitra_id]));
                if (!empty($candidates)) {
                    $chosen = $candidates[$idx % count($candidates)];
                    $help->update(['user_id' => $chosen]);
                }
            }
        }
    }
}
