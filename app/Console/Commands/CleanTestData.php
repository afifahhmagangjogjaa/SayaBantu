<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UserBalance;

class CleanTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-test-data 
                            {--force : Jalankan pembersihan tanpa konfirmasi interaktif}
                            {--keep-admins : Tetap simpan akun admin kota selain super_admin}
                            {--clean-storage : Hapus juga file upload pengujian (foto KTP, bukti bayar, foto bantuan)}';

    /**
     * Command aliases.
     *
     * @var array
     */
    protected $aliases = [
        'app:clean-test-data',
        'test:clean-data',
        'db:reset-test',
        'db:clean'
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan data transaksi & user testing di database dengan aman, mengecualikan data master sistem dan akun Super Admin.';

    /**
     * Tables that must NEVER be deleted (Master data sistem).
     *
     * @var array
     */
    protected array $protectedTables = [
        'migrations',
        'app_settings',
        'categories',
        'cities',
        'districts',
        'provinces',
        'reg_provinces',
        'reg_regencies',
        'reg_districts',
    ];

    /**
     * Transactional and test tables to purge (in order of child to parent).
     *
     * @var array
     */
    protected array $tablesToPurge = [
        'partner_activities',
        'partner_reports',
        'ratings',
        'chats',
        'helps',
        'notifications',
        'balance_transactions',
        'withdraw_requests',
        'registrations',
        'subscriptions',
        'activity_logs',
        'logs',
        'password_reset_tokens',
        'failed_jobs',
        'jobs',
        'job_batches',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $keepAdmins = (bool) $this->option('keep-admins');
        $cleanStorage = (bool) $this->option('clean-storage');

        $this->newLine();
        $this->line('<fg=cyan;options=bold>=============================================================</>');
        $this->line('<fg=cyan;options=bold>           SAYABANTU - PEMBERSIH DATA TESTING DATABASE       </>');
        $this->line('<fg=cyan;options=bold>=============================================================</>');
        $this->newLine();

        // 1. Tampilkan informasi keamanan
        $this->line('<fg=yellow;options=bold> [KEBIJAKAN PENGECUALIAN / DATA AMAN]</>');
        $this->line('  <fg=green>✓</> <fg=white>Data Master Sistem TIDAK AKAN DIHAPUS:</>');
        $this->line('    - Pengaturan website & banner (<fg=yellow>app_settings</>)');
        $this->line('    - Kategori layanan (<fg=yellow>categories</>)');
        $this->line('    - Wilayah & Kota (<fg=yellow>cities, reg_provinces, reg_regencies, reg_districts</>)');
        $this->line('    - Riwayat migrasi sistem (<fg=yellow>migrations</>)');
        $this->newLine();

        // Cari Super Admin yang akan dikecualikan
        $superAdminRoles = ['super_admin', 'superadmin'];
        $superAdmins = User::whereIn('role', $superAdminRoles)->get();

        if ($superAdmins->isEmpty()) {
            $this->error(' PERINGATAN: Tidak ditemukan akun dengan role super_admin di tabel users!');
            if (!$force && !$this->confirm('Apakah Anda ingin tetap melanjutkan? (Tidak ada akun super admin yang terdeteksi)', false)) {
                $this->warn('Operasi dibatalkan.');
                return Command::FAILURE;
            }
        } else {
            $this->line('  <fg=green>✓</> <fg=white>Akun Super Admin TETAP DIPERTAHANKAN (Bisa Langsung Login):</>');
            foreach ($superAdmins as $sa) {
                $this->line("    - <fg=bright-green>{$sa->name}</> ({$sa->email}) [Role: <fg=cyan>{$sa->role}</>]");
            }
        }

        $superAdminIds = $superAdmins->pluck('id')->toArray();
        $adminIds = [];

        if ($keepAdmins) {
            $admins = User::where('role', 'admin')->get();
            $adminIds = $admins->pluck('id')->toArray();
            $this->line('  <fg=green>✓</> <fg=white>Akun Admin Kota dipertahankan karena opsi --keep-admins aktif (' . $admins->count() . ' akun).</>');
        } else {
            $this->line('  <fg=gray>•</> <fg=gray>Akun testing customer, mitra, dan admin kota akan dibersihkan (tambahkan flag --keep-admins jika ingin admin kota tetap ada).</>');
        }

        $this->newLine();

        // 2. Konfirmasi interaktif jika bukan --force
        if (!$force) {
            $this->line('<fg=red;options=bold> PERHATIAN:</> Tindakan ini akan mengosongkan semua transaksi pengujian');
            $this->line('seperti order bantuan, riwayat chat, rating, transaksi saldo, dan user testing.');
            $this->newLine();

            if (!$this->confirm('Apakah Anda yakin ingin menghapus data pengujian tersebut sekarang?', false)) {
                $this->info(' Operasi dibatalkan oleh pengguna. Tidak ada data yang dihapus.');
                return Command::SUCCESS;
            }
        }

        $this->newLine();
        $this->info(' Memulai proses pembersihan data testing...');
        $this->newLine();

        $summary = [];

        // 3. Matikan pengecekan Foreign Key untuk mencegah FK constraint error
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $totalSteps = count($this->tablesToPurge) + 4 + ($cleanStorage ? 1 : 0);
            $bar = $this->output->createProgressBar($totalSteps);
            $bar->start();

            // A. Bersihkan tabel transaksional murni
            foreach ($this->tablesToPurge as $tableName) {
                if (Schema::hasTable($tableName)) {
                    $countBefore = DB::table($tableName)->count();
                    DB::table($tableName)->truncate();

                    // Reset auto increment ke 1 jika memungkinkan
                    try {
                        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1;");
                    } catch (\Throwable $e) {}

                    $summary[] = [
                        'name' => $tableName,
                        'status' => '<fg=green>Dibersihkan (Truncated)</>',
                        'count' => $countBefore . ' baris',
                    ];
                } else {
                    $summary[] = [
                        'name' => $tableName,
                        'status' => '<fg=gray>Tabel tidak ditemukan</>',
                        'count' => '-',
                    ];
                }
                $bar->advance();
            }

            // B. Bersihkan tabel relasi admin_city
            if (Schema::hasTable('admin_city')) {
                if ($keepAdmins) {
                    $deletedAc = DB::table('admin_city')
                        ->whereNotIn('user_id', array_merge($superAdminIds, $adminIds))
                        ->delete();
                    $summary[] = [
                        'name' => 'admin_city (relasi kota)',
                        'status' => '<fg=green>Dibersihkan (Non-Admin)</>',
                        'count' => $deletedAc . ' baris',
                    ];
                } else {
                    $acBefore = DB::table('admin_city')->count();
                    DB::table('admin_city')->truncate();
                    $summary[] = [
                        'name' => 'admin_city (relasi kota)',
                        'status' => '<fg=green>Dibersihkan</>',
                        'count' => $acBefore . ' baris',
                    ];
                }
            }
            $bar->advance();

            // C. Bersihkan tabel user_balances (pertahankan super admin)
            if (Schema::hasTable('user_balances')) {
                $preservedUserIds = $keepAdmins ? array_merge($superAdminIds, $adminIds) : $superAdminIds;
                $deletedBalances = DB::table('user_balances')
                    ->whereNotIn('user_id', $preservedUserIds)
                    ->delete();

                // Reset saldo super admin menjadi 0 (bersih dari transaksi lama)
                foreach ($superAdmins as $sa) {
                    UserBalance::updateOrCreate(
                        ['user_id' => $sa->id],
                        ['balance' => 0]
                    );
                }

                $summary[] = [
                    'name' => 'user_balances (saldo user)',
                    'status' => '<fg=green>Dibersihkan (Kecuali Super Admin)</>',
                    'count' => $deletedBalances . ' baris',
                ];
            }
            $bar->advance();

            // D. Bersihkan tabel sessions (Pertahankan sesi Super Admin agar tidak ter-logout)
            if (Schema::hasTable('sessions')) {
                $preservedUserIds = $keepAdmins ? array_merge($superAdminIds, $adminIds) : $superAdminIds;
                $deletedSessions = DB::table('sessions')
                    ->where(function ($q) use ($preservedUserIds) {
                        $q->whereNull('user_id')
                          ->orWhereNotIn('user_id', $preservedUserIds);
                    })
                    ->delete();

                $summary[] = [
                    'name' => 'sessions (sesi login)',
                    'status' => '<fg=green>Dibersihkan (Sesi SA Tersimpan)</>',
                    'count' => $deletedSessions . ' sesi',
                ];
            }
            $bar->advance();

            // E. Bersihkan tabel users (HANYA HAPUS NON-SUPERADMIN)
            $usersQuery = User::whereNotIn('role', $superAdminRoles);
            if ($keepAdmins) {
                $usersQuery->where('role', '!=', 'admin');
            }
            $deletedUsersCount = $usersQuery->count();
            $usersQuery->delete();

            $summary[] = [
                'name' => 'users (akun testing)',
                'status' => '<fg=green>Dihapus (Super Admin Aman)</>',
                'count' => $deletedUsersCount . ' akun',
            ];
            $bar->advance();

            // F. Bersihkan storage file pengujian jika opsi --clean-storage aktif
            if ($cleanStorage) {
                $storageDirectories = [
                    'complaint_photos',
                    'completion_photos',
                    'helps',
                    'ktp-photos',
                    'profile-photos',
                    'proof-of-payment',
                    'selfie-photos',
                    'topup-proofs',
                ];

                $deletedFilesCount = 0;
                foreach ($storageDirectories as $dir) {
                    if (Storage::disk('public')->exists($dir)) {
                        $files = Storage::disk('public')->allFiles($dir);
                        $deletedFilesCount += count($files);
                        Storage::disk('public')->delete($files);
                    }
                }

                $summary[] = [
                    'name' => 'storage (file upload test)',
                    'status' => '<fg=green>File dibersihkan</>',
                    'count' => $deletedFilesCount . ' file',
                ];
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

        } finally {
            // Hidupkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 4. Bersihkan Cache Laravel
        try {
            $this->callSilent('cache:clear');
            $this->callSilent('view:clear');
        } catch (\Throwable $e) {
            // Abaikan jika cache clear gagal
        }

        // 5. Tampilkan ringkasan tabel
        $tableData = [];
        foreach ($summary as $item) {
            $tableData[] = [
                $item['name'],
                $item['status'],
                $item['count'],
            ];
        }

        $this->table(['Tabel / Item', 'Status Eksekusi', 'Jumlah Terhapus'], $tableData);

        $this->newLine();
        $this->line('<fg=green;options=bold>=============================================================</>');
        $this->line('<fg=green;options=bold>  SUKSES! Database berhasil dibersihkan untuk pengujian baru. </>');
        $this->line('<fg=green;options=bold>=============================================================</>');
        $this->newLine();
        $this->line('  <fg=white;options=bold>Akun Super Admin tetap aman dan dapat langsung login:</>');
        foreach ($superAdmins as $sa) {
            $this->line("   • Email: <fg=bright-cyan>{$sa->email}</> (Role: <fg=yellow>{$sa->role}</>)");
        }
        $this->line('   • URL Login: <fg=bright-yellow>' . url('/admin/login') . '</>');
        $this->newLine();

        return Command::SUCCESS;
    }
}
