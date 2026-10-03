<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Help;
use Carbon\Carbon;

class FastForwardSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helps:fast-forward 
                            {id? : ID atau Order ID pesanan bantuan} 
                            {--minutes=30 : Set jadwal pelaksanaan menjadi berapa menit dari sekarang (default: 30 menit)} 
                            {--lock : Mundurkan jadwal ke 3 jam ke depan untuk mengetes tombol terkunci kembali}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Percepat waktu tunggu pesanan terjadwal agar tombol "Menuju ke Lokasi" langsung aktif di akun Mitra.';

    /**
     * Command aliases.
     *
     * @var array
     */
    protected $aliases = ['help:fast-forward', 'help:speedup', 'helps:speedup'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->line('<bg=blue;fg=white;options=bold> ======================================================== </>');
        $this->line('<bg=blue;fg=white;options=bold>      SAYABANTU - FAST FORWARD SCHEDULE (TESTING TOOL)    </>');
        $this->line('<bg=blue;fg=white;options=bold> ======================================================== </>');
        $this->newLine();

        $helpId = $this->argument('id');
        $minutes = (int) $this->option('minutes');
        $isLock = (bool) $this->option('lock');

        // Cari pesanan bantuan
        $help = null;

        if ($helpId) {
            $help = Help::with(['user', 'mitra'])
                ->where('id', $helpId)
                ->orWhere('order_id', $helpId)
                ->first();

            if (!$help) {
                $this->error("Pesanan dengan ID atau Order ID '{$helpId}' tidak ditemukan!");
                return 1;
            }
        } else {
            // Tampilkan daftar pesanan terjadwal yang sedang aktif
            $activeHelps = Help::with(['user', 'mitra'])
                ->whereIn('status', ['memperoleh_mitra', 'taken', 'menunggu_mitra'])
                ->where('help_type', 'scheduled')
                ->orderByDesc('id')
                ->limit(10)
                ->get();

            // Jika tidak ada pesanan terjadwal spesifik, ambil pesanan aktif apapun
            if ($activeHelps->isEmpty()) {
                $activeHelps = Help::with(['user', 'mitra'])
                    ->whereIn('status', ['memperoleh_mitra', 'taken', 'menunggu_mitra'])
                    ->orderByDesc('id')
                    ->limit(10)
                    ->get();
            }

            // Jika masih kosong, ambil 5 pesanan terbaru
            if ($activeHelps->isEmpty()) {
                $activeHelps = Help::with(['user', 'mitra'])
                    ->orderByDesc('id')
                    ->limit(5)
                    ->get();
            }

            if ($activeHelps->isEmpty()) {
                $this->warn('Belum ada data pesanan bantuan di database.');
                return 0;
            }

            $tableData = [];
            foreach ($activeHelps as $idx => $h) {
                $isReady = $h->canPartnerStartJourney();
                $readyStatus = $isReady ? '<fg=green>AKTIF (Siap Berangkat)</>' : '<fg=yellow>TERKUNCI (Menunggu H-1 Jam)</>';

                $tableData[] = [
                    'No' => $idx + 1,
                    'ID' => $h->id,
                    'Order ID' => $h->order_id,
                    'Judul' => \Illuminate\Support\Str::limit($h->title, 25),
                    'Tipe' => $h->help_type === 'urgent' ? 'Urgent' : 'Terjadwal',
                    'Status' => $h->status,
                    'Mitra' => $h->mitra ? $h->mitra->name : '<fg=gray>Belum ada</>',
                    'Jadwal' => $h->scheduled_at ? Carbon::parse($h->scheduled_at)->format('d/m/Y H:i') : '-',
                    'Tombol Mitra' => $readyStatus,
                ];
            }

            $this->table(['No', 'ID', 'Order ID', 'Judul', 'Tipe', 'Status', 'Mitra', 'Jadwal', 'Tombol Mitra'], $tableData);

            if ($activeHelps->count() === 1) {
                $first = $activeHelps->first();
                if ($this->confirm("Percepat pesanan #{$first->id} ({$first->title})?", true)) {
                    $help = $first;
                } else {
                    $this->info('Dibatalkan.');
                    return 0;
                }
            } else {
                $selectedNo = $this->ask('Masukkan Nomor (1-' . $activeHelps->count() . ') atau ID pesanan yang ingin dipercepat');
                if (!$selectedNo) {
                    $this->info('Dibatalkan.');
                    return 0;
                }

                // Cek berdasarkan index (1, 2, 3...)
                if (is_numeric($selectedNo) && $selectedNo >= 1 && $selectedNo <= $activeHelps->count()) {
                    $help = $activeHelps[$selectedNo - 1];
                } else {
                    // Cek berdasarkan ID
                    $help = Help::with(['user', 'mitra'])->find($selectedNo);
                }

                if (!$help) {
                    $this->error('Pesanan tidak valid.');
                    return 1;
                }
            }
        }

        // Tampilkan info sebelum update
        $oldScheduled = $help->scheduled_at ? Carbon::parse($help->scheduled_at) : null;
        $this->newLine();
        $this->info("Target Pesanan:");
        $this->line(" - ID Pesanan      : #{$help->id} ({$help->order_id})");
        $this->line(" - Judul           : {$help->title}");
        $this->line(" - Customer        : " . ($help->user?->name ?? 'User #' . $help->user_id));
        $this->line(" - Mitra           : " . ($help->mitra?->name ?? '<fg=yellow>Belum diambil mitra</>'));
        $this->line(" - Status Saat Ini : {$help->status}");
        $this->line(" - Jadwal Semula   : " . ($oldScheduled ? $oldScheduled->translatedFormat('l, d F Y H:i') . ' WIB' : 'Belum diatur'));

        // Hitung jadwal baru
        if ($isLock) {
            // Mode lock: set ke 3 jam ke depan
            $newScheduled = Carbon::now()->addHours(3);
            $actionDesc = "dimundurkan ke 3 jam ke depan (tombol 'Menuju ke Lokasi' akan TERKUNCI kembali)";
        } else {
            // Mode fast-forward: set ke X menit ke depan (default 30 menit)
            // Karena tombol aktif pada H-1 jam (60 menit sebelum jadwal),
            // maka jika jadwal berjarak 30 menit dari sekarang, H-1 jam sudah lewat 30 menit yang lalu!
            $newScheduled = Carbon::now()->addMinutes(max(1, $minutes));
            $actionDesc = "dipercepat menjadi {$minutes} menit dari sekarang (tombol 'Menuju ke Lokasi' akan LANGSUNG AKTIF)";
        }

        // Simpan perubahan ke database
        $help->update([
            'scheduled_at' => $newScheduled->format('Y-m-d H:i:s'),
            'help_type'    => 'scheduled', // Pastikan bertipe scheduled agar sesuai skenario pengujian
        ]);

        $help->refresh();
        $canStart = $help->canPartnerStartJourney();

        $this->newLine();
        $this->line("<fg=green;options=bold>BERHASIL DIPERBARUI!</>");
        $this->line(" - Jadwal Baru     : <fg=cyan;options=bold>" . $newScheduled->translatedFormat('l, d F Y H:i:s') . " WIB</> ({$actionDesc})");
        $this->line(" - Waktu Buka Akses: " . $newScheduled->copy()->subHour()->format('H:i') . ' WIB (H-1 jam sebelum jadwal)');
        $this->line(" - Waktu Sekarang  : " . Carbon::now()->format('H:i:s') . ' WIB');
        
        if ($canStart) {
            $this->newLine();
            $this->line("<bg=green;fg=black;options=bold> STATUS TOMBOL MITRA: [ AKTIF / BISA DIKLIK ] </>");
            $this->line("Sekarang tombol <fg=cyan;options=bold>'Menuju ke Lokasi Customer'</> sudah muncul dan siap diklik di akun Mitra.");
        } else {
            $this->newLine();
            $this->line("<bg=yellow;fg=black;options=bold> STATUS TOMBOL MITRA: [ TERKUNCI / MENUNGGU JADWAL ] </>");
            $this->line("Tombol masih terkunci karena jadwal masih lebih dari 1 jam ke depan.");
        }

        $this->newLine();
        $this->line("<fg=gray>Tips: Silakan refresh (F5) halaman detail pesanan di browser Mitra untuk melihat perubahannya.</>");
        $this->newLine();

        return 0;
    }
}
