<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_code',
        'bank_name',
        'account_number',
        'account_name',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tampilkan nomor rekening tersensor (misal: 1234••••90)
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        $num = (string) $this->account_number;
        $len = strlen($num);
        if ($len <= 4) {
            return $num;
        }

        $start = substr($num, 0, 3);
        $end = substr($num, -3);

        return $start . '••••' . $end;
    }
}

