<?php

namespace App\Services;

use App\Models\Help;
use App\Models\PartnerActivity;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationUrlResolver
{
    /**
     * Normalize any URL to ensure it points to the current application domain/host.
     * Prevents stale hostnames (e.g. 127.0.0.1:8000, localhost, ngrok) from hijacking user navigation.
     */
    public static function normalizeUrl(?string $url, string $default = '/'): string
    {
        if (empty($url) || $url === '#' || $url === 'javascript:void(0);') {
            return $default;
        }

        $parsed = parse_url($url);
        if (! isset($parsed['path']) || empty($parsed['path'])) {
            return $default;
        }

        $path = $parsed['path'];
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return url($path . $query . $fragment);
    }

    /**
     * Resolve the accurate target destination URL for any notification based on user role and notification payload.
     */
    public static function resolveUrl($notification, ?User $user = null): string
    {
        $user = $user ?? auth()->user();
        $role = $user?->role ?? 'customer';

        if (is_array($notification)) {
            $data = $notification;
        } elseif ($notification instanceof DatabaseNotification) {
            $data = $notification->data ?? [];
        } elseif (is_object($notification) && isset($notification->data)) {
            $data = is_string($notification->data) ? json_decode($notification->data, true) : (array) $notification->data;
        } else {
            $data = [];
        }

        $notificationId = is_object($notification) && isset($notification->id)
            ? (string) $notification->id
            : ($data['id'] ?? null);

        $type = (string) ($data['type'] ?? '');
        $helpId = $data['help_id'] ?? null;
        $reportId = $data['report_id'] ?? null;
        $withdrawId = $data['withdraw_id'] ?? null;
        $rawUrl = $data['url'] ?? null;

        // 1. Surat Peringatan (Sanction) - All roles go to sanction detail if notificationId is present
        if ($type === 'sanction_warning' || str_contains($type, 'sanction')) {
            if ($notificationId) {
                return route('notifications.sanction', $notificationId);
            }
        }

        // 2. Customer Role Destinations
        if (in_array($role, ['customer', 'kustomer'])) {
            // Chat
            if ($type === 'chat_message') {
                return $helpId ? route('customer.chat', ['help' => $helpId]) : route('customer.chat');
            }

            // Top-Up
            if ($type === 'topup_approved') {
                return route('customer.transactions.index', ['tab' => 'masuk']);
            }
            if (in_array($type, ['topup_rejected', 'topup_request_submitted'])) {
                return route('customer.topup.history');
            }

            // Help Order Statuses (in_progress, partner_on_the_way, completed, taken, rejected, idle, etc.)
            if ($helpId || str_contains($type, 'help') || $type === 'idle_partner_alert') {
                if ($helpId) {
                    return route('customer.helps.detail', $helpId);
                }
                return route('customer.helps.index');
            }

            // KTP / Verification
            if (str_contains($type, 'ktp') || str_contains($type, 'verification')) {
                if ($type === 'ktp_approved') {
                    return route('customer.dashboard');
                }
                return route('profile.settings.verification');
            }

            // Reports / Complaints
            if (str_contains($type, 'report')) {
                if ($reportId) {
                    return route('customer.reports.show', $reportId);
                }
                return route('customer.reports.index');
            }

            // Fallback for customer
            if (! empty($rawUrl)) {
                return self::normalizeUrl($rawUrl, route('customer.dashboard'));
            }
            return route('customer.dashboard');
        }

        // 3. Mitra Role Destinations
        if ($role === 'mitra') {
            // Chat
            if ($type === 'chat_message') {
                return $helpId ? route('mitra.chat', ['help' => $helpId]) : route('mitra.chat');
            }

            // Withdraw / Penarikan Saldo
            if (str_contains($type, 'withdraw')) {
                $status = $data['status'] ?? '';
                if ($withdrawId && $status === 'success') {
                    return route('mitra.withdraw.success', $withdrawId);
                }
                if ($withdrawId && $status === 'failed') {
                    return route('mitra.withdraw.rejected', $withdrawId);
                }
                return route('mitra.withdraw.history');
            }

            // Rating / Review Received
            if ($type === 'rating_received') {
                return route('mitra.ratings');
            }

            // Help Order (taken, status, request, completed, cancelled)
            if ($helpId || str_contains($type, 'help')) {
                if ($helpId) {
                    return route('mitra.helps.detail', $helpId);
                }
                return route('mitra.helps.processing');
            }

            // KTP / Verification
            if (str_contains($type, 'ktp') || str_contains($type, 'verification')) {
                if ($type === 'ktp_approved') {
                    return route('mitra.dashboard');
                }
                return route('profile.settings.verification');
            }

            // Reports / Complaints
            if (str_contains($type, 'report')) {
                if ($reportId) {
                    return route('mitra.reports.show', $reportId);
                }
                return route('mitra.reports.index');
            }

            // Fallback for mitra
            if (! empty($rawUrl)) {
                return self::normalizeUrl($rawUrl, route('mitra.dashboard'));
            }
            return route('mitra.dashboard');
        }

        // 4. Admin Role Destinations
        if ($role === 'admin') {
            // Mitra direct cancellation -> redirect directly to mitra detail
            if ($type === 'help_status' && (! empty($data['new_status']) && $data['new_status'] === 'partner_cancelled_direct')) {
                $mitraId = $data['mitra_id'] ?? null;
                if (! $mitraId && $helpId) {
                    $help = Help::find($helpId);
                    $mitraId = $help?->mitra_id;
                    if (! $mitraId) {
                        $activity = PartnerActivity::where('activity_type', 'help_cancelled')->where('description', 'like', "%#{$helpId}%")->latest()->first();
                        $mitraId = $activity?->user_id;
                    }
                }
                if ($mitraId) {
                    return route('admin.mitra.show', $mitraId);
                }
            }

            if (in_array($type, ['new_topup_request', 'topup_request_submitted'])) {
                return route('admin.topup.approvals');
            }
            if (in_array($type, ['new_withdraw_request', 'withdraw_status'])) {
                return route('admin.withdraws.index');
            }
            if ($type === 'new_ktp_verification' || str_contains($type, 'ktp')) {
                return route('admin.verifications');
            }
            if (in_array($type, ['new_registration', 'new_user'])) {
                return route('admin.customers');
            }
            if (str_contains($type, 'report')) {
                return ! empty($data['report_id']) ? route('admin.partners.reports.show', $data['report_id']) : route('admin.partners.report');
            }
            if ($helpId || str_contains($type, 'help')) {
                return $helpId ? route('admin.helps.show', $helpId) : route('admin.helps');
            }

            // Fallback for admin
            if (! empty($rawUrl) && ! str_contains($rawUrl, '/superadmin/')) {
                return self::normalizeUrl($rawUrl, route('admin.dashboard'));
            }
            return route('admin.dashboard');
        }

        // 5. Super Admin Role Destinations
        if ($role === 'super_admin') {
            if (in_array($type, ['new_topup_request', 'topup_request_submitted'])) {
                return route('superadmin.topup.approvals');
            }
            if (in_array($type, ['new_withdraw_request', 'withdraw_status'])) {
                return route('superadmin.withdraws.index');
            }
            if (in_array($type, ['new_registration', 'new_user', 'new_ktp_verification']) || str_contains($type, 'ktp')) {
                return route('superadmin.users');
            }
            if (str_contains($type, 'report')) {
                return route('superadmin.reports.index');
            }
            if ($helpId || str_contains($type, 'help')) {
                return route('superadmin.helps.approved');
            }

            // Fallback for superadmin
            if (! empty($rawUrl)) {
                return self::normalizeUrl($rawUrl, route('superadmin.dashboard'));
            }
            return route('superadmin.dashboard');
        }

        // Global fallback
        return self::normalizeUrl($rawUrl, url('/'));
    }
}
