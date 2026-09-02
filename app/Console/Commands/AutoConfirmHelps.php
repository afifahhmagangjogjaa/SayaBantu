<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Help;
use Carbon\Carbon;

class AutoConfirmHelps extends Command
{
    protected $signature = 'helps:auto-confirm';
    protected $description = 'Auto-confirm helps that waited for customer confirmation for more than 24 hours.';

    public function handle()
    {
        $cutoff = Carbon::now()->subHours(24);

        $helps = Help::where('status', 'waiting_customer_confirmation')
            ->where(function ($q) use ($cutoff) {
                $q->whereNotNull('service_completed_at')->where('service_completed_at', '<=', $cutoff)
                  ->orWhere('updated_at', '<=', $cutoff);
            })
            ->get();

        $this->info('Found ' . $helps->count() . ' helps to auto-confirm.');

        foreach ($helps as $help) {
            try {
                $old = $help->status;
                $help->update([
                    'status' => 'selesai',
                    'completed_at' => $help->completed_at ?? now(),
                ]);

                // Notify mitra that the help was auto-confirmed
                try {
                    $help->mitra?->notify(new \App\Notifications\HelpStatusNotification($help, $old, 'selesai', $help->mitra));
                } catch (\Throwable $e) {
                    // ignore notification errors
                }

                $this->info('Auto-confirmed help #' . $help->id);
            } catch (\Throwable $e) {
                $this->error('Failed to auto-confirm help #' . $help->id . ': ' . $e->getMessage());
            }
        }

        return 0;
    }
}
