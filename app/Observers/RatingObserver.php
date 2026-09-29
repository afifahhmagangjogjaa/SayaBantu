<?php

namespace App\Observers;

use App\Models\Rating;
use App\Models\PartnerActivity;

class RatingObserver
{
    public function created(Rating $rating): void
    {
        try {
            $ip = null;
            $ua = null;
            if (function_exists('request')) {
                try {
                    $req = request();
                    $ip = $req->ip();
                    $ua = $req->userAgent();
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            PartnerActivity::create([
                'user_id' => $rating->user_id,
                'activity_type' => 'help_reviewed',
                'description' => 'Customer menilai bantuan #' . ($rating->help_id ?? ''),
                'ip_address' => $ip,
                'user_agent' => $ua,
            ]);

            // Notifikasi rating HANYA dikirimkan ke mitra (customer tidak menerima notifikasi rating)
            if ($rating->type === 'customer_to_mitra') {
                $ratee = $rating->ratee ?? $rating->mitra;
                if ($ratee && $ratee->role === 'mitra') {
                    $ratee->notify(new \App\Notifications\RatingReceivedNotification($rating));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RatingObserver error', ['error' => $e->getMessage()]);
        }
    }
}
