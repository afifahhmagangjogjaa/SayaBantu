<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;

class Notifications extends Component
{
    public $notifications;
    public $unreadCount = 0;
    public $recentUnread = [];

    protected $listeners = [
        'topupRequestCreated' => 'refreshNotifications',
        'notificationRead' => 'refreshNotifications',
        'refreshNotifications' => 'refreshNotifications'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();
        if (!$user) {
            $this->notifications = collect([]);
            $this->unreadCount = 0;
            $this->recentUnread = [];
            return;
        }

        $adminCityIds = $user->getAdminCityIds();

        // City scoping filter for city admins
        $allUserNotifs = $user->notifications()->latest()->get();
        $filteredNotifs = $allUserNotifs->filter(function ($notif) use ($adminCityIds) {
            $data = $notif->data ?? [];

            // If notification points to superadmin only, filter it out for city admin
            if (!empty($data['url']) && str_contains($data['url'], '/superadmin/')) {
                return false;
            }

            if (!empty($adminCityIds)) {
                // 1. Direct city_id in payload
                if (isset($data['city_id'])) {
                    return $data['city_id'] !== null && in_array((int)$data['city_id'], $adminCityIds);
                }

                // 2. Via help_id
                if (!empty($data['help_id'])) {
                    $help = \App\Models\Help::find($data['help_id']);
                    return $help && $help->city_id && in_array((int)$help->city_id, $adminCityIds);
                }

                // 3. Via user_id / mitra_id / customer_id
                $targetUserId = $data['mitra_id'] ?? $data['user_id'] ?? $data['customer_id'] ?? null;
                if ($targetUserId) {
                    $targetUser = \App\Models\User::find($targetUserId);
                    return $targetUser && $targetUser->city_id && in_array((int)$targetUser->city_id, $adminCityIds);
                }

                // 4. Via report_id
                if (!empty($data['report_id'])) {
                    $report = \App\Models\PartnerReport::find($data['report_id']);
                    $repCity = $report?->reporter?->city_id ?? $report?->reportedUser?->city_id;
                    return $repCity && in_array((int)$repCity, $adminCityIds);
                }

                // 5. Via withdraw_id
                if (!empty($data['withdraw_id'])) {
                    $withdraw = \App\Models\WithdrawRequest::with('user')->find($data['withdraw_id']);
                    $wCity = $withdraw?->user?->city_id;
                    return $wCity && in_array((int)$wCity, $adminCityIds);
                }

                // 6. Via transaction_id
                if (!empty($data['transaction_id'])) {
                    $trx = \App\Models\BalanceTransaction::with('user')->find($data['transaction_id']);
                    $tCity = $trx?->user?->city_id;
                    return $tCity && in_array((int)$tCity, $adminCityIds);
                }

                return false;
            }

            return true;
        });

        $this->notifications = $filteredNotifs->take(10);
        $this->unreadCount = $filteredNotifs->whereNull('read_at')->count();

        // Ambil notifikasi unread terbaru yang BELUM pernah di-pop-up (popped_at is null)
        $unreads = $filteredNotifs->whereNull('read_at')->whereNull('popped_at')->take(5);

        $this->recentUnread = $unreads->map(function ($notif) {
            $data = $notif->data ?? [];
            $type = $data['type'] ?? 'general';

            $title = $data['title'] ?? match($type) {
                'new_topup_request', 'topup_request_submitted' => '💰 Request Top-Up Saldo Baru',
                'new_withdraw_request', 'withdraw_status' => '💵 Permintaan Tarik Saldo Baru',
                'new_ktp_verification' => '🪪 Pengajuan Verifikasi KTP Baru',
                'new_registration', 'new_user' => '👤 Pendaftaran Pengguna Baru',
                'new_report', 'partner_report' => '⚠️ Laporan Aduan Baru',
                'help_taken' => '🤝 Bantuan Diambil',
                'help_status' => (isset($data['new_status']) && in_array($data['new_status'], ['komplain', 'disputed'])) 
                    ? '⚠️ Komplain Bantuan Diajukan' 
                    : '📢 Status Bantuan Diperbarui',
                default => '📢 Pemberitahuan Baru'
            };

            $message = $data['message'] ?? $data['body'] ?? 'Ada pembaruan baru yang memerlukan perhatian Anda.';

            $cityName = null;
            if (!empty($data['city_id'])) {
                $cityName = \App\Models\City::find($data['city_id'])?->name;
            }

            return [
                'id' => (string) $notif->id,
                'type' => (string) $type,
                'title' => (string) $title,
                'message' => (string) $message,
                'city_name' => (string) ($cityName ?? ''),
                'url' => (string) $this->resolveNotificationUrl($notif),
                'created_at_human' => $notif->created_at->diffForHumans(),
            ];
        })->values()->toArray();

        // Tandai notifikasi yang dipop-upkan sebagai sudah pernah muncul (popped_at = now())
        if ($unreads->isNotEmpty()) {
            DatabaseNotification::whereIn('id', $unreads->pluck('id'))->update(['popped_at' => now()]);
        }
    }

    public function resolveNotificationUrl($notification)
    {
        $data = $notification->data ?? [];
        $type = $data['type'] ?? '';

        // Jika pembatalan rekan jasa, rute langsung ke halaman detail mitra yang bersangkutan
        if ($type === 'help_status' && (!empty($data['new_status']) && $data['new_status'] === 'partner_cancelled_direct')) {
            $mitraId = $data['mitra_id'] ?? null;
            if (!$mitraId && !empty($data['help_id'])) {
                $help = \App\Models\Help::find($data['help_id']);
                $mitraId = $help?->mitra_id;
                if (!$mitraId) {
                    $activity = \App\Models\PartnerActivity::where('activity_type', 'help_cancelled')->where('description', 'like', "%#{$data['help_id']}%")->latest()->first();
                    $mitraId = $activity?->user_id;
                }
            }
            if ($mitraId) {
                return route('admin.mitra.show', $mitraId);
            }
        }

        if (!empty($data['url']) && !str_contains($data['url'], '/superadmin/')) {
            $url = $data['url'];
            if (str_contains($url, 'ngrok-free.app')) {
                $url = preg_replace('/https?:\/\/[^\/]+/', config('app.url', 'http://127.0.0.1:8000'), $url);
            }
            return $url;
        }

        return match ($type) {
            'new_topup_request', 'topup_request_submitted' => route('admin.topup.approvals'),
            'new_withdraw_request', 'withdraw_status' => route('admin.withdraws.index'),
            'new_ktp_verification' => route('admin.verifications'),
            'new_registration', 'new_user' => route('admin.customers'),
            'new_report', 'partner_report' => !empty($data['report_id']) ? route('admin.partners.reports.show', $data['report_id']) : route('admin.partners.report'),
            'help_status' => (!empty($data['new_status']) && $data['new_status'] === 'partner_cancelled_direct' && !empty($data['mitra_id']))
                ? route('admin.mitra.show', $data['mitra_id'])
                : (!empty($data['help_id']) ? route('admin.helps.show', $data['help_id']) : route('admin.helps')),
            default => route('admin.dashboard'),
        };
    }

    public function refreshNotifications()
    {
        $this->loadNotifications();
    }

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->update(['read_at' => now(), 'popped_at' => $notification->popped_at ?? now()]);
            $this->loadNotifications();
            $this->dispatch('notification-updated');
        }
    }

    public function openNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->update(['read_at' => now(), 'popped_at' => $notification->popped_at ?? now()]);
            $targetUrl = $this->resolveNotificationUrl($notification);
            $this->loadNotifications();
            $this->dispatch('notification-updated');
            
            return redirect()->to($targetUrl);
        }
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        if (!$user) return;

        $user->unreadNotifications()->update(['read_at' => now(), 'popped_at' => now()]);
        $this->loadNotifications();
        $this->dispatch('notification-updated');
        session()->flash('notification-success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }

    public function markAsPopped(array $ids)
    {
        if (!empty($ids)) {
            DatabaseNotification::whereIn('id', $ids)
                ->where('notifiable_id', auth()->id())
                ->update(['popped_at' => now()]);
        }
    }

    public function deleteNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->delete();
            $this->loadNotifications();
            $this->dispatch('notification-updated');
        }
    }

    public function deleteAllNotifications()
    {
        $user = auth()->user();
        if (!$user) return;

        $user->notifications()->delete();
        $this->loadNotifications();
        $this->dispatch('notification-updated');
        session()->flash('notification-success', 'Semua notifikasi berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.notifications');
    }
}
