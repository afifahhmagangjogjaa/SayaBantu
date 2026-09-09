<?php

namespace App\Observers;

use App\Models\Help;
use App\Models\BalanceTransaction;
use App\Models\UserBalance;
use App\Notifications\HelpTakenNotification;
use App\Models\PartnerActivity;

class HelpObserver
{
    /**
     * Handle the Help "created" event.
     * Record activity when a customer creates a help (centralized)
     */
    public function created(Help $help): void
    {
        try {
            PartnerActivity::create([
                'user_id' => $help->user_id,
                'activity_type' => 'help_created',
                'description' => 'Customer membuat bantuan #' . $help->id,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Failed to record PartnerActivity on help created: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Help "updated" event.
     * Send notification when help is taken by a mitra
     */
    public function updated(Help $help): void
    {
        // Check if mitra_id was just set (help was taken)
        if ($help->wasChanged('mitra_id') && $help->mitra_id !== null && $help->getOriginal('mitra_id') === null) {
            // Load the mitra relationship
            $mitra = $help->mitra;

            // Send notification to the help requester
            if ($help->user && $mitra) {
                $help->user->notify(new HelpTakenNotification($help, $mitra));
            }

            // Record partner activity for taking the help
            try {
                PartnerActivity::create([
                    'user_id' => $mitra?->id,
                    'activity_type' => 'take_help',
                    'description' => 'Mengambil Bantuan #' . $help->id,
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->header('User-Agent'),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to record PartnerActivity on help taken: ' . $e->getMessage());
            }
        }

        // When help status changes
        if ($help->wasChanged('status')) {
            $newStatus = strtolower($help->status ?? '');
            $prevStatus = strtolower($help->getOriginal('status') ?? '');

            // Notify customer if help status is changed to 'rejected'
            if ($newStatus === 'rejected' && $prevStatus !== 'rejected') {
                $customer = $help->customer ?? ($help->user ?? \App\Models\User::find($help->user_id));
                if ($customer) {
                    try {
                        $customer->notify(new \App\Notifications\HelpStatusNotification($help, $prevStatus, 'rejected'));
                        \Log::info('Sent HelpStatusNotification (rejected) to customer id=' . $customer->id . ' for help_id=' . $help->id);
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to send rejection notification via HelpObserver: ' . $e->getMessage());
                    }
                }
            }

            // Automatic refund to customer when help is rejected or cancelled
            $rejectedOrCancelledStates = ['rejected', 'dibatalkan', 'cancelled'];
            if (in_array($newStatus, $rejectedOrCancelledStates) && !in_array($prevStatus, $rejectedOrCancelledStates)) {
                $customerId = $help->user_id;
                $refundAmount = (float) ($help->total_amount ?? ($help->amount + ($help->admin_fee ?? 0)));

                if ($customerId && $refundAmount > 0) {
                    $alreadyRefunded = BalanceTransaction::where('user_id', $customerId)
                        ->where('reference_id', $help->id)
                        ->where('description', 'like', '%Refund%')
                        ->exists();

                    if (!$alreadyRefunded) {
                        $description = ($help->complaint_resolution === 'refunded')
                            ? 'Pengembalian dana (Refund) Bantuan #' . $help->id . ' disetujui Admin'
                            : 'Pengembalian saldo (Refund) Bantuan #' . $help->id . ' (' . ucfirst($newStatus) . ')';
                        
                        BalanceTransaction::create([
                            'user_id' => $customerId,
                            'amount' => $refundAmount,
                            'type' => 'topup',
                            'description' => $description,
                            'status' => 'completed',
                            'reference_id' => $help->id,
                        ]);
                        \Log::info("Refunded Rp {$refundAmount} to customer id={$customerId} for help_id={$help->id}");
                    }
                }

                // Notify mitra if help had an assigned partner
                $mitra = $help->mitra ?? ($help->mitra_id ? \App\Models\User::find($help->mitra_id) : null);
                if ($mitra) {
                    try {
                        $mitra->notify(new \App\Notifications\HelpStatusNotification($help, $prevStatus, $newStatus, $mitra));
                        \Log::info("Sent cancellation notification to mitra id={$mitra->id} for help_id={$help->id}");
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to notify mitra of cancellation via HelpObserver: ' . $e->getMessage());
                    }
                }

                // Notify customer on cancellation (if not already handled)
                $customer = $help->customer ?? ($help->user ?? \App\Models\User::find($help->user_id));
                if ($customer && $newStatus !== 'rejected') {
                    try {
                        $customer->notify(new \App\Notifications\HelpStatusNotification($help, $prevStatus, $newStatus, $mitra));
                    } catch (\Throwable $e) {
                        \Log::warning('Failed to notify customer of cancellation via HelpObserver: ' . $e->getMessage());
                    }
                }
            }

            $completedStates = ['completed', 'selesai'];

            if (in_array($newStatus, $completedStates) && !in_array($prevStatus, $completedStates)) {
                // Only credit if a mitra was assigned and amount is positive
                if ($help->mitra_id && $help->amount > 0) {
                    $mitraId = $help->mitra_id;

                    // Avoid double-crediting by checking existing balance transaction for this help
                    $already = BalanceTransaction::where('user_id', $mitraId)
                        ->where('reference_id', $help->id)
                        ->exists();

                    if (!$already) {
                        // Ensure the mitra has a UserBalance row
                        $userBalance = UserBalance::firstOrCreate([
                            'user_id' => $mitraId,
                        ], [
                            'balance' => 0,
                        ]);

                        // Credit the mitra with a descriptive transaction
                        $description = 'Pendapatan Bantuan #' . $help->id;
                        $userBalance->addBalance($help->amount, $description, $help->id);
                    }
                }
            }
        }
    }
}
