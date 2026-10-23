<?php

namespace App\Console\Commands;

use App\Models\Help;
use Illuminate\Console\Command;

class AutoCancelHelps extends Command
{
    protected $signature = 'helps:auto-cancel';

    protected $description = 'Batalkan otomatis bantuan urgent yang melewati batas waktu pencarian mitra.';

    public function handle(): int
    {
        $count = Help::cancelExpiredUrgentHelps();

        $this->info("Berhasil membatalkan {$count} bantuan yang kedaluwarsa.");

        return self::SUCCESS;
    }
}
