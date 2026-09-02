<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ChatMessageNotification extends Notification
{
    use Queueable;

    public $helpId;
    public $message;
    public $fromId;
    public $fromName;

    public function __construct($helpId, $message, $fromId = null, $fromName = null)
    {
        $this->helpId = $helpId;
        $this->message = $message;
        $this->fromId = $fromId;
        $this->fromName = $fromName;
    }

    public function via($notifiable)
    {
        $settings = $notifiable->notification_settings ?? [];
        if (isset($settings['generalNotification']) && !$settings['generalNotification']) {
            return [];
        }
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'chat_message',
            'help_id' => $this->helpId,
            'message' => $this->message,
            'from_id' => $this->fromId,
            'from_name' => $this->fromName,
            'url' => route('chat.show', ['help' => $this->helpId]) ?? route('customer.chat', ['help' => $this->helpId]),
        ];
    }
}
