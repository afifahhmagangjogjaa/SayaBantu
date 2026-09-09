<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BalanceTransaction;
use Livewire\Livewire;

class UserBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->user->transactions();
    }

    /**
     * Recalculate and persist the true net balance for a user.
     * Formula: (Completed Inflows: topup + income + refund) - (Completed Outflows: deduction + withdraw + fee)
     *
     * @param int $userId
     * @return float
     */
    public static function recalculateForUser(int $userId): float
    {
        $inflow = (float) BalanceTransaction::where('user_id', $userId)
            ->whereIn('type', ['topup', 'income', 'refund'])
            ->whereRaw("LOWER(TRIM(status)) = 'completed'")
            ->sum('amount');

        $outflow = (float) BalanceTransaction::where('user_id', $userId)
            ->whereIn('type', ['deduction', 'withdraw', 'fee'])
            ->whereRaw("LOWER(TRIM(status)) = 'completed'")
            ->sum('amount');

        $netBalance = max(0, $inflow - $outflow);

        static::updateOrCreate(
            ['user_id' => $userId],
            ['balance' => $netBalance]
        );

        return $netBalance;
    }

    // Helper methods
    public function addBalance($amount, $description = null, $referenceId = null)
    {
        BalanceTransaction::create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'topup',
            'description' => $description,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        static::recalculateForUser($this->user_id);

        return $this;
    }

    public function deductBalance($amount, $description = null, $referenceId = null)
    {
        BalanceTransaction::create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'deduction',
            'description' => $description,
            'reference_id' => $referenceId,
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        static::recalculateForUser($this->user_id);

        // Emit Livewire event so dashboard components can refresh immediately
        try {
            if (class_exists(Livewire::class)) {
                Livewire::emit('balance-updated');
            }
        } catch (\Throwable $e) {
            // ignore if Livewire not available in this context
        }

        return $this;
    }
}

