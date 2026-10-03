<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurgeBannedSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:purge-banned-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus total seluruh sesi login aktif dan remember_token untuk semua pengguna yang berstatus diblokir/banned/nonaktif.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->line('<bg=red;fg=white;options=bold> ======================================================== </>');
        $this->line('<bg=red;fg=white;options=bold>      SAYABANTU - PURGE BANNED/BLOCKED USERS SESSIONS     </>');
        $this->line('<bg=red;fg=white;options=bold> ======================================================== </>');
        $this->newLine();

        // Cari seluruh user yang dibanned / diblokir / dinonaktifkan
        $bannedUserIds = User::where(function ($q) {
            $q->whereIn('status', ['blocked', 'inactive'])
              ->orWhere('is_banned', true);
        })->pluck('id')->toArray();

        $countUsers = count($bannedUserIds);
        $this->info("Ditemukan {$countUsers} pengguna dengan status diblokir/banned/nonaktif.");

        if ($countUsers === 0) {
            $this->info("Tidak ada pengguna yang diblokir saat ini.");
            return 0;
        }

        // 1. Kosongkan remember_token agar auto-login via cookie browser tidak bisa berfungsi
        $updatedTokens = User::whereIn('id', $bannedUserIds)
            ->whereNotNull('remember_token')
            ->update(['remember_token' => null]);

        $this->line(" - Reset remember_token : <fg=green>{$updatedTokens}</> pengguna dibersihkan token login otomatisnya.");

        // 2. Hapus seluruh data sesi aktif dari tabel sessions (database session driver)
        $deletedDbSessions = 0;
        if (Schema::hasTable('sessions')) {
            $deletedDbSessions = DB::table('sessions')
                ->whereIn('user_id', $bannedUserIds)
                ->delete();

            $this->line(" - Hapus tabel sessions  : <fg=green>{$deletedDbSessions}</> sesi aktif dihapus dari database.");
        }

        // 3. Hapus file sesi fisik jika menggunakan file driver
        $deletedFileSessions = 0;
        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            $files = @glob($sessionPath . '/*');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== '.gitignore') {
                        $content = @file_get_contents($file);
                        if ($content !== false) {
                            foreach ($bannedUserIds as $uid) {
                                if (strpos($content, (string) $uid) !== false) {
                                    if (preg_match('/login_[a-z0-9_]+[^\d]+' . $uid . '([^\d]|$)/', $content)) {
                                        @unlink($file);
                                        $deletedFileSessions++;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->line(" - Hapus file sessions   : <fg=green>{$deletedFileSessions}</> file sesi dihapus dari storage.");
        }

        $this->newLine();
        $this->line("<bg=green;fg=black;options=bold> SUKSES: Seluruh sesi pengguna yang dibanned telah dimusnahkan total! </>");
        $this->line("Pengguna yang diblokir tidak akan bisa masuk kembali meskipun mengandalkan cookie/riwayat login di browser.");
        $this->newLine();

        return 0;
    }
}
