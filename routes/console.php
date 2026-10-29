<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('helps:auto-confirm')->everyTenMinutes();

// Batalkan order urgent yang lewat batas waktu pencarian mitra.
// Dulu ini dipanggil di dalam render() halaman; sekarang dijadwalkan agar tetap
// berjalan walau tidak ada yang membuka halaman.
Schedule::command('helps:auto-cancel')->everyMinute();
