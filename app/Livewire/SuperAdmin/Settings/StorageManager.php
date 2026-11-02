<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Enums\HelpStatus;
use App\Models\BalanceTransaction;
use App\Models\Help;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.superadmin')]
#[Title('Pembersihan Storage & Manajemen File - Super Admin')]
class StorageManager extends Component
{
    /**
     * Data statistik penggunaan storage
     *
     * @var array
     */
    public array $stats = [];

    /**
     * Jumlah dan ukuran file yatim (orphan)
     *
     * @var array
     */
    public array $orphanStats = [
        'count' => 0,
        'size_mb' => 0,
        'files' => [],
    ];

    /**
     * Status modal konfirmasi
     *
     * @var bool
     */
    public bool $showConfirmModal = false;

    /**
     * Tipe aksi yang sedang dikonfirmasi
     *
     * @var string|null
     */
    public ?string $pendingAction = null;

    /**
     * Judul modal konfirmasi
     *
     * @var string
     */
    public string $modalTitle = '';

    /**
     * Deskripsi modal konfirmasi
     *
     * @var string
     */
    public string $modalDescription = '';

    /**
     * Catatan / peringatan modal konfirmasi
     *
     * @var string
     */
    public string $modalWarning = '';

    /**
     * Filter periode untuk foto order selesai (hari)
     *
     * @var string
     */
    public string $helpsPeriod = '30';

    /**
     * Filter periode untuk bukti top up selesai (hari)
     *
     * @var string
     */
    public string $topupsPeriod = '30';

    /**
     * Hasil tindakan terakhir
     *
     * @var string|null
     */
    public ?string $lastActionMessage = null;

    public function mount()
    {
        $this->loadStorageStats();
    }

    /**
     * Pindai ulang dan hitung seluruh penggunaan storage
     */
    public function loadStorageStats()
    {
        $disk = Storage::disk('public');
        $allDirectories = $disk->directories();

        $categoryConfig = [
            'helps' => [
                'label' => 'Foto Permohonan Bantuan',
                'description' => 'Foto bantuan yang diunggah oleh customer saat membuat order',
                'icon' => '📦',
                'color' => 'blue',
                'protected' => false,
            ],
            'completion_photos' => [
                'label' => 'Foto Bukti Pengerjaan Mitra',
                'description' => 'Foto dokumentasi bukti penyelesaian tugas dari mitra',
                'icon' => '📸',
                'color' => 'indigo',
                'protected' => false,
            ],
            'before_photos' => [
                'label' => 'Foto Kondisi Awal Pengerjaan',
                'description' => 'Foto dokumentasi kondisi sebelum mulai dikerjakan oleh mitra',
                'icon' => '📷',
                'color' => 'cyan',
                'protected' => false,
            ],
            'complaint_photos' => [
                'label' => 'Foto Bukti Komplain / Kendala',
                'description' => 'Foto bukti pendukung laporan keluhan bantuan pelanggan',
                'icon' => '⚠️',
                'color' => 'amber',
                'protected' => false,
            ],
            'proof-of-payment' => [
                'label' => 'Bukti Transfer Top Up Saldo',
                'description' => 'Struk bukti transfer top up saldo dari customer atau mitra',
                'icon' => '💳',
                'color' => 'emerald',
                'protected' => false,
            ],
            'payment' => [
                'label' => 'Bukti Pembayaran Tambahan',
                'description' => 'Arsip struk bukti pembayaran saldo sekunder',
                'icon' => '💵',
                'color' => 'teal',
                'protected' => false,
            ],
            'ktp-photos' => [
                'label' => 'Dokumen Foto KTP Mitra',
                'description' => 'Dokumen identitas resmi verifikasi mitra (Terproteksi)',
                'icon' => '🪪',
                'color' => 'rose',
                'protected' => true,
            ],
            'selfie-photos' => [
                'label' => 'Foto Selfie Mitra bersama KTP',
                'description' => 'Foto verifikasi wajah mitra aktif (Terproteksi)',
                'icon' => '🤳',
                'color' => 'purple',
                'protected' => true,
            ],
            'promos' => [
                'label' => 'Banner Promosi Aplikasi',
                'description' => 'Banner visual yang tampil di aplikasi (Terproteksi)',
                'icon' => '🎨',
                'color' => 'pink',
                'protected' => true,
            ],
            'profile-photos' => [
                'label' => 'Foto Profil Pengguna',
                'description' => 'Avatar / foto profil akun customer dan mitra',
                'icon' => '👤',
                'color' => 'slate',
                'protected' => true,
            ],
        ];

        $folders = [];
        $totalBytes = 0;
        $totalFiles = 0;

        foreach ($allDirectories as $dir) {
            $files = $disk->allFiles($dir);
            $bytes = 0;
            foreach ($files as $f) {
                try {
                    $bytes += $disk->size($f);
                } catch (\Exception $e) {
                    // Abaikan file yang tidak dapat dibaca
                }
            }

            $totalBytes += $bytes;
            $count = count($files);
            $totalFiles += $count;

            $meta = $categoryConfig[$dir] ?? [
                'label' => 'Direktori ' . ucfirst(str_replace(['_', '-'], ' ', $dir)),
                'description' => 'Berkas tersimpan pada folder public/' . $dir,
                'icon' => '📁',
                'color' => 'slate',
                'protected' => false,
            ];

            $folders[$dir] = array_merge($meta, [
                'dir' => $dir,
                'files_count' => $count,
                'size_bytes' => $bytes,
                'size_mb' => round($bytes / 1024 / 1024, 2),
            ]);
        }

        // Cek juga temporary upload Livewire jika ada di storage/app/livewire-tmp
        $tmpFilesCount = 0;
        $tmpBytes = 0;
        if (Storage::exists('livewire-tmp')) {
            $tmpFiles = Storage::allFiles('livewire-tmp');
            $tmpFilesCount = count($tmpFiles);
            foreach ($tmpFiles as $tf) {
                try {
                    $tmpBytes += Storage::size($tf);
                } catch (\Exception $e) {}
            }
            $totalBytes += $tmpBytes;
            $totalFiles += $tmpFilesCount;
        }

        $this->stats = [
            'total_files' => $totalFiles,
            'total_bytes' => $totalBytes,
            'total_mb' => round($totalBytes / 1024 / 1024, 2),
            'folders' => $folders,
            'tmp_files' => $tmpFilesCount,
            'tmp_mb' => round($tmpBytes / 1024 / 1024, 2),
        ];

        // Hitung file yatim (orphan)
        $this->calculateOrphans();
    }

    /**
     * Hitung file di storage yang sudah tidak ada catatannya di database
     */
    protected function calculateOrphans()
    {
        $disk = Storage::disk('public');
        $orphanFiles = [];
        $orphanBytes = 0;

        // 1. Cek helps (foto bantuan)
        $dbHelpsPhotos = Help::whereNotNull('photo')->pluck('photo')->toArray();
        $diskHelps = $disk->allFiles('helps');
        foreach ($diskHelps as $file) {
            if (! in_array($file, $dbHelpsPhotos)) {
                $orphanFiles[] = $file;
                try { $orphanBytes += $disk->size($file); } catch (\Exception $e) {}
            }
        }

        // 2. Cek completion_photos
        $dbCompletion = Help::whereNotNull('completion_photo')->pluck('completion_photo')->toArray();
        $diskComp = $disk->allFiles('completion_photos');
        foreach ($diskComp as $file) {
            if (! in_array($file, $dbCompletion)) {
                $orphanFiles[] = $file;
                try { $orphanBytes += $disk->size($file); } catch (\Exception $e) {}
            }
        }

        // 3. Cek before_photos
        $dbBefore = Help::whereNotNull('before_photo')->pluck('before_photo')->toArray();
        $diskBefore = $disk->allFiles('before_photos');
        foreach ($diskBefore as $file) {
            if (! in_array($file, $dbBefore)) {
                $orphanFiles[] = $file;
                try { $orphanBytes += $disk->size($file); } catch (\Exception $e) {}
            }
        }

        // 4. Cek complaint_photos
        $dbComplaint = Help::whereNotNull('complaint_photo')->pluck('complaint_photo')->toArray();
        $diskComplaint = $disk->allFiles('complaint_photos');
        foreach ($diskComplaint as $file) {
            if (! in_array($file, $dbComplaint)) {
                $orphanFiles[] = $file;
                try { $orphanBytes += $disk->size($file); } catch (\Exception $e) {}
            }
        }

        // 5. Cek proof-of-payment & payment
        $dbTopups = BalanceTransaction::whereNotNull('proof_of_payment')->pluck('proof_of_payment')->toArray();
        $diskTopups = array_merge($disk->allFiles('proof-of-payment'), $disk->allFiles('payment'));
        foreach ($diskTopups as $file) {
            if (! in_array($file, $dbTopups)) {
                $orphanFiles[] = $file;
                try { $orphanBytes += $disk->size($file); } catch (\Exception $e) {}
            }
        }

        $this->orphanStats = [
            'count' => count($orphanFiles),
            'size_mb' => round($orphanBytes / 1024 / 1024, 2),
            'files' => $orphanFiles,
        ];
    }

    /**
     * Konfirmasi pembersihan file sampah (orphan files)
     */
    public function promptCleanOrphans()
    {
        $this->pendingAction = 'clean_orphans';
        $this->modalTitle = 'Bersihkan File Sampah (Orphan Files)';
        $this->modalDescription = "Sistem menemukan {$this->orphanStats['count']} berkas (" . $this->orphanStats['size_mb'] . " MB) di folder storage yang catatannya sudah tidak ada di database.";
        $this->modalWarning = 'Tindakan ini 100% aman karena hanya menghapus file bekas uji coba atau order lama yang datanya sudah terhapus. Data transaksi yang ada tidak akan terpengaruh.';
        $this->showConfirmModal = true;
    }

    /**
     * Konfirmasi pembersihan cache temporary upload Livewire
     */
    public function promptCleanTmp()
    {
        $this->pendingAction = 'clean_tmp';
        $this->modalTitle = 'Bersihkan Berkas Sementara (Cache Upload)';
        $this->modalDescription = 'Tindakan ini akan mengosongkan berkas sementara sisa unggahan gagal atau formulir yang dibatalkan pada folder temporary.';
        $this->modalWarning = 'Aman dilakukan. Berkas sementara yang belum tersimpan lebih dari 1 jam akan dihapus.';
        $this->showConfirmModal = true;
    }

    /**
     * Konfirmasi pembersihan foto order bantuan yang sudah selesai/dibatalkan
     */
    public function promptCleanCompletedHelps()
    {
        $periodLabel = match ($this->helpsPeriod) {
            '30' => 'lebih dari 30 hari yang lalu',
            '60' => 'lebih dari 60 hari yang lalu',
            '90' => 'lebih dari 90 hari yang lalu',
            'all' => 'semua order selesai / dibatalkan',
            default => 'lebih dari 30 hari yang lalu',
        };

        // Estimasi jumlah order
        $query = Help::whereIn('status', ['selesai', 'dibatalkan', 'completed', 'cancelled'])
            ->where(function ($q) {
                $q->whereNotNull('photo')
                    ->orWhereNotNull('before_photo')
                    ->orWhereNotNull('completion_photo')
                    ->orWhereNotNull('complaint_photo');
            });

        if ($this->helpsPeriod !== 'all') {
            $days = (int) $this->helpsPeriod;
            $cutoff = Carbon::now()->subDays($days);
            $query->where('updated_at', '<=', $cutoff);
        }

        $count = $query->count();

        $this->pendingAction = 'clean_completed_helps';
        $this->modalTitle = 'Bersihkan Foto Pesanan Selesai / Dibatalkan';
        $this->modalDescription = "Ditemukan {$count} pesanan berstatus Selesai / Dibatalkan ({$periodLabel}) yang memiliki berkas foto pengerjaan.";
        $this->modalWarning = 'PERHATIAN: Berkas foto bantuan, foto sebelum pengerjaan, dan foto bukti selesai akan dihapus permanen dari ruang disk untuk menghemat kapasitas. Riwayat transaksi, rincian biaya, dan rating pesanan TETAP TERSIMPAN AMAN di database.';
        $this->showConfirmModal = true;
    }

    /**
     * Konfirmasi pembersihan bukti transfer top up saldo lama
     */
    public function promptCleanTopupProofs()
    {
        $periodLabel = match ($this->topupsPeriod) {
            '30' => 'lebih dari 30 hari yang lalu',
            '60' => 'lebih dari 60 hari yang lalu',
            'all' => 'semua transaksi top up yang sudah selesai',
            default => 'lebih dari 30 hari yang lalu',
        };

        $query = BalanceTransaction::whereIn('status', ['completed', 'approved', 'rejected', 'failed', 'cancelled'])
            ->whereNotNull('proof_of_payment');

        if ($this->topupsPeriod !== 'all') {
            $days = (int) $this->topupsPeriod;
            $cutoff = Carbon::now()->subDays($days);
            $query->where('updated_at', '<=', $cutoff);
        }

        $count = $query->count();

        $this->pendingAction = 'clean_topup_proofs';
        $this->modalTitle = 'Bersihkan Bukti Transfer Top Up';
        $this->modalDescription = "Ditemukan {$count} transaksi top up ({$periodLabel}) yang berkas struk pembayarannya tersimpan di disk.";
        $this->modalWarning = 'Berkas struk transfer pembayaran akan dihapus dari storage. Catatan mutasi saldo dan riwayat transaksi pengguna TETAP AMAN dan tidak akan berubah.';
        $this->showConfirmModal = true;
    }

    /**
     * Eksekusi tindakan yang dikonfirmasi
     */
    public function executeAction()
    {
        $action = $this->pendingAction;
        $this->showConfirmModal = false;
        $this->pendingAction = null;

        $disk = Storage::disk('public');

        try {
            switch ($action) {
                case 'clean_orphans':
                    $files = $this->orphanStats['files'] ?? [];
                    $deletedCount = 0;
                    $deletedBytes = 0;

                    foreach ($files as $file) {
                        if ($disk->exists($file)) {
                            try { $deletedBytes += $disk->size($file); } catch (\Exception $e) {}
                            $disk->delete($file);
                            $deletedCount++;
                        }
                    }

                    $savedMb = round($deletedBytes / 1024 / 1024, 2);
                    $this->lastActionMessage = "Sukses! Berhasil membersihkan {$deletedCount} berkas sampah (orphan files), membebaskan ruang disk sebesar {$savedMb} MB.";
                    session()->flash('success', $this->lastActionMessage);
                    break;

                case 'clean_tmp':
                    $deletedTmp = 0;
                    $deletedTmpBytes = 0;
                    if (Storage::exists('livewire-tmp')) {
                        $files = Storage::allFiles('livewire-tmp');
                        $now = time();
                        foreach ($files as $file) {
                            try {
                                $lastMod = Storage::lastModified($file);
                                // Hanya hapus file yang berumur lebih dari 30 menit
                                if (($now - $lastMod) > 1800) {
                                    $deletedTmpBytes += Storage::size($file);
                                    Storage::delete($file);
                                    $deletedTmp++;
                                }
                            } catch (\Exception $e) {
                                Storage::delete($file);
                                $deletedTmp++;
                            }
                        }
                    }

                    $savedMb = round($deletedTmpBytes / 1024 / 1024, 2);
                    $this->lastActionMessage = "Sukses! Berhasil membersihkan {$deletedTmp} berkas temporary cache, membebaskan {$savedMb} MB.";
                    session()->flash('success', $this->lastActionMessage);
                    break;

                case 'clean_completed_helps':
                    $query = Help::whereIn('status', ['selesai', 'dibatalkan', 'completed', 'cancelled'])
                        ->where(function ($q) {
                            $q->whereNotNull('photo')
                                ->orWhereNotNull('before_photo')
                                ->orWhereNotNull('completion_photo')
                                ->orWhereNotNull('complaint_photo');
                        });

                    if ($this->helpsPeriod !== 'all') {
                        $days = (int) $this->helpsPeriod;
                        $cutoff = Carbon::now()->subDays($days);
                        $query->where('updated_at', '<=', $cutoff);
                    }

                    $helps = $query->get();
                    $deletedFiles = 0;
                    $deletedBytes = 0;

                    foreach ($helps as $help) {
                        $fields = ['photo', 'before_photo', 'completion_photo', 'complaint_photo'];
                        $updates = [];

                        foreach ($fields as $field) {
                            $filePath = $help->{$field};
                            if ($filePath && $disk->exists($filePath)) {
                                try { $deletedBytes += $disk->size($filePath); } catch (\Exception $e) {}
                                $disk->delete($filePath);
                                $deletedFiles++;
                            }
                            $updates[$field] = null;
                        }

                        $help->update($updates);
                    }

                    $savedMb = round($deletedBytes / 1024 / 1024, 2);
                    $this->lastActionMessage = "Sukses! Berhasil membersihkan {$deletedFiles} berkas foto dari " . $helps->count() . " pesanan selesai, membebaskan ruang sebesar {$savedMb} MB.";
                    session()->flash('success', $this->lastActionMessage);
                    break;

                case 'clean_topup_proofs':
                    $query = BalanceTransaction::whereIn('status', ['completed', 'approved', 'rejected', 'failed', 'cancelled'])
                        ->whereNotNull('proof_of_payment');

                    if ($this->topupsPeriod !== 'all') {
                        $days = (int) $this->topupsPeriod;
                        $cutoff = Carbon::now()->subDays($days);
                        $query->where('updated_at', '<=', $cutoff);
                    }

                    $txs = $query->get();
                    $deletedFiles = 0;
                    $deletedBytes = 0;

                    foreach ($txs as $tx) {
                        $filePath = $tx->proof_of_payment;
                        if ($filePath && $disk->exists($filePath)) {
                            try { $deletedBytes += $disk->size($filePath); } catch (\Exception $e) {}
                            $disk->delete($filePath);
                            $deletedFiles++;
                        }
                        $tx->update(['proof_of_payment' => null]);
                    }

                    $savedMb = round($deletedBytes / 1024 / 1024, 2);
                    $this->lastActionMessage = "Sukses! Berhasil membersihkan {$deletedFiles} berkas bukti transfer top up, membebaskan ruang sebesar {$savedMb} MB.";
                    session()->flash('success', $this->lastActionMessage);
                    break;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pembersihan storage: ' . $e->getMessage());
        }

        // Pindai ulang statistik
        $this->loadStorageStats();
    }

    /**
     * Batalkan modal konfirmasi
     */
    public function cancelConfirm()
    {
        $this->showConfirmModal = false;
        $this->pendingAction = null;
    }

    public function render()
    {
        return view('livewire.super-admin.settings.storage-manager');
    }
}

