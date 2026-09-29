<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Help;
use App\Models\UserBalance;
use App\Models\BalanceTransaction;
use App\Models\PartnerActivity;
use App\Notifications\HelpStatusNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoConfirmHelps extends Command
{
    protected $signature = 'helps:auto-confirm';
    protected $description = 'Auto-confirm helps that waited for customer confirmation for more than 24 hours.';

    public function handle()
    {
        $count = self::autoConfirmExpired($this);
        $this->info("Successfully processed {$count} helps.");
        return 0;
    }

    public static function autoConfirmExpired($console = null, ?int $userId = null, ?int $mitraId = null): int
    {
        $cutoff = Carbon::now()->subHours(24);

        $query = Help::where('status', 'waiting_customer_confirmation')
            ->where(function ($q) use ($cutoff) {
                $q->where(function ($sub) use ($cutoff) {
                    $sub->whereNotNull('service_completed_at')
                        ->where('service_completed_at', '<=', $cutoff);
                })->orWhere(function ($sub) use ($cutoff) {
                    $sub->whereNull('service_completed_at')
                        ->where('updated_at', '<=', $cutoff);
                });
            });

        // Filter opsional jika dipanggil spesifik untuk customer tertentu
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filter opsional jika dipanggil spesifik untuk mitra tertentu
        if ($mitraId) {
            $query->where('mitra_id', $mitraId);
        }

        $helps = $query->get();

        if ($console) {
            $console->info('Found ' . $helps->count() . ' helps to auto-confirm.');
        }

        $confirmed = 0;

        foreach ($helps as $help) {
            if (self::autoConfirmSingleHelp($help, $console)) {
                $confirmed++;
            }
        }

        return $confirmed;
    }

    public static function autoConfirmSingleHelp(Help $help, $console = null): bool
    {
        if ($help->status !== 'waiting_customer_confirmation') {
            return false;
        }

        try {
            $oldStatus = $help->status;

            DB::transaction(function () use ($help) {
                // 1. Update status bantuan menjadi 'selesai'
                // HelpObserver otomatis mencairkan saldo bersih ke akun mitra
                $adminNotes = trim(($help->admin_notes ? $help->admin_notes . '. ' : '') . 'Auto-confirm oleh sistem setelah 24 jam tanpa respon customer.');

                $help->update([
                    'status' => 'selesai',
                    'completed_at' => $help->completed_at ?? now(),
                    'admin_notes' => $adminNotes,
                ]);

                // 2. Safety Fallback: Jika belum dicairkan oleh HelpObserver, pastikan saldo tetap dicairkan
                if ($help->mitra_id) {
                    $payoutAmount = $help->getMitraPayoutAmount();

                    if ($payoutAmount > 0) {
                        $already = BalanceTransaction::where('user_id', $help->mitra_id)
                            ->where(function ($q) use ($help) {
                                $q->where('reference_id', $help->id)
                                  ->orWhere(function ($sq) use ($help) {
                                      if (!empty($help->order_id)) {
                                          $sq->where('order_id', $help->order_id);
                                      }
                                  })
                                  ->orWhere('description', 'like', 'Pendapatan Bantuan #' . $help->id . '%')
                                  ->orWhere('description', 'like', 'Auto-confirm Bantuan #' . $help->id . '%');
                            })
                            ->exists();

                        if (!$already) {
                            $userBalance = UserBalance::firstOrCreate(
                                ['user_id' => $help->mitra_id],
                                ['balance' => 0]
                            );

                            $feeText = ($help->mitra_fee_amount > 0)
                                ? " (Potongan Platform {$help->mitra_fee_percent}%: Rp " . number_format($help->mitra_fee_amount, 0, ',', '.') . ")"
                                : "";

                            $userBalance->addBalance($payoutAmount, "Auto-confirm Bantuan #{$help->id}" . $feeText, $help->id);
                        }
                    }
                }
            });

            // Notifikasi ke Mitra
            try {
                $mitra = $help->mitra ?? ($help->mitra_id ? \App\Models\User::find($help->mitra_id) : null);
                if ($mitra) {
                    $mitra->notify(new HelpStatusNotification($help, $oldStatus, 'selesai_auto_24h', $mitra));
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi auto-confirm ke mitra: ' . $e->getMessage());
            }

            // Notifikasi ke Customer
            try {
                $customer = $help->customer ?? ($help->user ?? \App\Models\User::find($help->user_id));
                if ($customer) {
                    $customer->notify(new HelpStatusNotification($help, $oldStatus, 'selesai_auto_24h', $customer));
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal kirim notifikasi auto-confirm ke customer: ' . $e->getMessage());
            }

            // Catat riwayat aktivitas PartnerActivity
            try {
                if ($help->mitra_id && class_exists(PartnerActivity::class)) {
                    PartnerActivity::create([
                        'user_id' => $help->mitra_id,
                        'activity_type' => 'help_auto_confirmed_24h',
                        'description' => 'Bantuan #' . $help->id . ' otomatis selesai dan saldo dicairkan setelah 24 jam tanpa respon customer',
                        'ip_address' => request()?->ip() ?? '127.0.0.1',
                        'user_agent' => request()?->header('User-Agent') ?? 'System/AutoConfirm',
                    ]);
                }
            } catch (\Throwable $e) {}

            if ($console) {
                $console->info('Auto-confirmed and credited help #' . $help->id);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to auto-confirm help #' . $help->id . ': ' . $e->getMessage());
            if ($console) {
                $console->error('Failed to auto-confirm help #' . $help->id . ': ' . $e->getMessage());
            }
            return false;
        }
    }
}