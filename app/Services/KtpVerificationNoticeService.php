<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\KtpVerificationPromptNotification;
use Illuminate\Support\Facades\Log;

class KtpVerificationNoticeService
{
    /**
     * Pastikan pengguna yang belum verifikasi KTP memiliki notifikasi pengingat di lonceng/inbox.
     */
    public static function ensurePromptNotification(?User $user): void
    {
        if (!$user || (bool) $user->verified) {
            return;
        }

        try {
            // Cek apakah sudah ada notifikasi prompt yang belum dibaca
            $hasUnreadPrompt = $user->unreadNotifications()
                ->where('data->type', 'ktp_verification_prompt')
                ->exists();

            if (!$hasUnreadPrompt) {
                // Cek apakah baru saja dikirim dalam 24 jam terakhir agar tidak duplikat
                $recentPrompt = $user->notifications()
                    ->where('data->type', 'ktp_verification_prompt')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->exists();

                if (!$recentPrompt) {
                    $user->notify(new KtpVerificationPromptNotification());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('KtpVerificationNoticeService: gagal dispatch notifikasi prompt: ' . $e->getMessage());
        }
    }
}
