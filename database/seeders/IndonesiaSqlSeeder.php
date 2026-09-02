<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndonesiaSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sqlPath = __DIR__ . '/sql/indonesia.sql';
        if (! file_exists($sqlPath)) {
            $this->command->error("Missing file: {$sqlPath}");
            return;
        }

        DB::unprepared(file_get_contents($sqlPath));
        $this->command->info('Imported: ' . $sqlPath);
    }
}
