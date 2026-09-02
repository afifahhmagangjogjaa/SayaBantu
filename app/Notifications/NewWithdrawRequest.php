<?php

namespace App\Notifications;

use App\Models\WithdrawRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewWithdrawRequest extends Notification
{
    use Queueable;

    public WithdrawRequest $withdraw;

    /**
     * Create a new notification instance.
     */
    public function __construct(WithdrawRequest $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $userName = $this->withdraw->user->name ?? 'Mitra';
        $amount = (float) $this->withdraw->amount;

        return [
            'type' => 'new_withdraw_request',
            'title' => 'Permintaan Tarik Saldo Baru',
            'withdraw_id' => $this->withdraw->id,
            'user_name' => $userName,
            'user_id' => $this->withdraw->user_id,
            'amount' => $amount,
            'bank_code' => $this->withdraw->bank_code,
            'account_number' => $this->withdraw->account_number,
            'message' => "Permintaan tarik saldo baru dari {$userName} sebesar Rp " . number_format($amount, 0, ',', '.'),
            'url' => route('superadmin.withdraws.index'),
        ];
    }
}
