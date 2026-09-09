<?php

namespace App\Observers;

use App\Models\BalanceTransaction;
use App\Models\PartnerActivity;
use App\Models\UserBalance;
use Illuminate\Support\Facades\Log;

class BalanceTransactionObserver
{
    public function created(BalanceTransaction $transaction): void
    {
        Log::info('BalanceTransactionObserver: created', [
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'type' => $transaction->type,
        ]);

        $this->handleCompletion($transaction, null);
    }

    public function updated(BalanceTransaction $transaction): void
    {
        $original = $transaction->getOriginal('status');

        Log::info('BalanceTransactionObserver: updated', [
            'transaction_id' => $transaction->id,
            'original' => $original,
            'current' => $transaction->status,
            'type' => $transaction->type,
        ]);

        $this->handleCompletion($transaction, $original);
    }

    protected function handleCompletion(BalanceTransaction $transaction, ?string $originalStatus): void
    {
        try {
            $normalize = fn ($s) => strtolower(trim($s ?? ''));
            $curr = $normalize($transaction->status);
            $orig = $normalize($originalStatus);

            // Only act when status is completed or transitioned to completed
            if ($curr !== 'completed') {
                return;
            }

            // If already completed previously and processed, skip to avoid double activity logging
            if ($orig === 'completed' && $transaction->processed_at) {
                return;
            }

            // Always recalculate net balance accurately for the user
            $newBalance = UserBalance::recalculateForUser($transaction->user_id);

            // Mark processed_at quietly without firing observer again
            if (!$transaction->processed_at) {
                $transaction->processed_at = now();
                $transaction->saveQuietly();
            }

            if ($transaction->type === 'topup') {
                try {
                    PartnerActivity::create([
                        'user_id' => $transaction->user_id,
                        'activity_type' => 'balance_topup',
                        'description' => sprintf(
                            'Top up berhasil Rp %s %s',
                            number_format((float)$transaction->amount, 0, ',', '.'),
                            $transaction->order_id ? '(Order #' . $transaction->order_id . ')' : ''
                        ),
                        'ip_address' => request()?->ip(),
                        'user_agent' => request()?->header('User-Agent'),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('BalanceTransactionObserver: failed to log PartnerActivity', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info("BalanceTransactionObserver: completed", [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'type' => $transaction->type,
                'balance_after' => $newBalance,
            ]);

        } catch (\Throwable $e) {
            Log::error('BalanceTransactionObserver ERROR', [
                'transaction_id' => $transaction->id,
                'msg' => $e->getMessage(),
            ]);
        }
    }
}

