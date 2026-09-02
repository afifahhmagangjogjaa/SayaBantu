<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\Province;

class ImportReqRegencies extends Command
{
    protected $signature = 'import:req-regencies {--dry-run}';
    protected $description = 'Import or sync rows from req_regencies (+ req_provinces) into cities table';

    public function handle()
    {
        $dry = $this->option('dry-run');

        // Auto-detect available source tables and column shapes.
        $source = null;
        if (Schema::hasTable('req_regencies') && Schema::hasTable('req_provinces')) {
            $source = 'req';
        } elseif (Schema::hasTable('reg_regencies') && Schema::hasTable('reg_provinces')) {
            $source = 'reg_legacy';
        } elseif (Schema::hasTable('regencies') && Schema::hasTable('provinces')) {
            $source = 'regencies_legacy';
        }

        if (! $source) {
            $this->error('No supported source tables found. Checked: req_regencies, reg_regencies, regencies.');
            return 1;
        }

        $this->info('Using source: ' . $source);

        if ($source === 'req') {
            $rows = DB::table('req_regencies')
                ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
                ->select('req_regencies.id as regency_id', 'req_regencies.regency as name', 'req_regencies.type as type', 'req_provinces.province as province', 'req_regencies.latitude', 'req_regencies.longitude')
                ->orderBy('req_regencies.regency')
                ->get();
        } elseif ($source === 'reg_legacy') {
            $rows = DB::table('reg_regencies')
                ->join('reg_provinces', 'reg_regencies.province_id', '=', 'reg_provinces.id')
                ->select('reg_regencies.id as regency_id', 'reg_regencies.name as name', DB::raw('null as type'), 'reg_provinces.name as province', DB::raw('null as latitude'), DB::raw('null as longitude'))
                ->orderBy('reg_regencies.name')
                ->get();
        } else { // regencies_legacy
            $rows = DB::table('regencies')
                ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                ->select('regencies.id as regency_id', 'regencies.regency as name', 'regencies.type as type', 'provinces.province as province', DB::raw('null as latitude'), DB::raw('null as longitude'))
                ->orderBy('regencies.regency')
                ->get();
        }

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        $created = 0; $updated = 0;

        foreach ($rows as $r) {
            $bar->advance();

            $code = 'reqr-' . $r->regency_id;

            // find by code or by name+province as fallback
            $city = City::where('code', $code)->orWhere(function ($q) use ($r) {
                $q->where('name', $r->name)->where('province', $r->province);
            })->first();

            $provModel = null;
            if (Schema::hasTable('provinces')) {
                $provModel = Province::firstOrCreate(['name' => $r->province]);
            }

            $data = [
                'name' => $r->name,
                'province' => $r->province,
                'type' => $r->type ?? null,
                'code' => $code,
                'is_active' => true,
            ];
            if (! empty($r->latitude)) $data['latitude'] = $r->latitude;
            if (! empty($r->longitude)) $data['longitude'] = $r->longitude;
            if ($provModel) $data['province_id'] = $provModel->id;

            if ($city) {
                $city->update($data);
                $updated++;
            } else {
                if (! $dry) {
                    City::create($data);
                }
                $created++;
            }
        }

        $bar->finish();
        $this->line('');
        $this->info("Done. Created: {$created}, Updated: {$updated}");

        if ($dry) {
            $this->info('Dry-run mode: no writes were performed. Run without --dry-run to apply.');
        }

        return 0;
    }
}
