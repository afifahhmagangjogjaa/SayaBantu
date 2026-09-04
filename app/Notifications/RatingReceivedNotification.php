<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Rating;

class RatingReceivedNotification extends Notification
{
    use Queueable;

    public $rating;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rating $rating)
    {
        $this->rating = $rating->loadMissing(['rater', 'ratee', 'help']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $settings = $notifiable->notification_settings ?? [];
        if (isset($settings['generalNotification']) && !$settings['generalNotification']) {
            return [];
        }
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $help = $this->rating->help;
        $helpTitle = $help?->title ?? ('Bantuan #' . ($this->rating->help_id ?? ''));
        $stars = (int) $this->rating->rating;

        if ($this->rating->type === 'customer_to_mitra') {
            $raterName = $this->rating->rater_display_name;
            $title = "⭐ Ulasan Baru ({$stars}.0/5.0)";
            
            $reviewText = trim((string) $this->rating->review);
            if (!empty($reviewText)) {
                $truncated = mb_strlen($reviewText) > 80 ? mb_substr($reviewText, 0, 77) . '...' : $reviewText;
                $message = "{$raterName} memberi rating {$stars} bintang untuk '{$helpTitle}': \"{$truncated}\"";
            } else {
                $message = "{$raterName} memberi rating {$stars} bintang untuk bantuan '{$helpTitle}'";
            }
            
            $actionUrl = route('mitra.ratings');
        } else {
            $mitraName = optional($this->rating->rater)->name ?? 'Mitra';
            $title = "⭐ Rating Baru dari Mitra ({$stars}.0/5.0)";
            $message = "Mitra {$mitraName} memberikan rating {$stars} bintang untuk pesanan '{$helpTitle}'";
            $actionUrl = route('customer.helps.history');
        }

        return [
            'type' => 'rating_received',
            'title' => $title,
            'message' => $message,
            'rating_id' => $this->rating->id,
            'rating_value' => $stars,
            'help_id' => $this->rating->help_id,
            'help_title' => $helpTitle,
            'is_anonymous' => (bool) $this->rating->is_anonymous,
            'rater_name' => $this->rating->rater_display_name,
            'action_url' => $actionUrl,
        ];
    }
}
