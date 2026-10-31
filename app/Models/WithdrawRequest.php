<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $table = 'withdraw_requests';

    protected $fillable = [
        'user_id',
        'amount',
        'bank_code',
        'account_number',
        'status',
        'external_id',
        'description',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'processed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected static function booted()
    {
        static::creating(function ($withdraw) {
            if (empty($withdraw->external_id)) {
                $withdraw->external_id = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate ID unik otomatis untuk penarikan dana (format: WDR-YYYYMMDD-XXXX)
     */
    public static function generateUniqueCode($date = null): string
    {
        $dateStr = ($date ? \Carbon\Carbon::parse($date) : now())->format('Ymd');
        $prefix = "WDR-{$dateStr}-";

        $lastRecord = static::where('external_id', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->external_id, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $todayCount = static::whereDate('created_at', ($date ? \Carbon\Carbon::parse($date) : now())->toDateString())->count();
            $sequence = $todayCount + 1;
        }

        return sprintf('%s%04d', $prefix, $sequence);
    }

    /**
     * Accessor untuk kode penarikan unik yang konsisten
     */
    public function getWithdrawCodeAttribute(): string
    {
        if (! empty($this->external_id) && str_starts_with($this->external_id, 'WDR-')) {
            return $this->external_id;
        }

        $dateStr = ($this->created_at ?? now())->format('Ymd');

        return sprintf('WDR-%s-%04d', $dateStr, $this->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
