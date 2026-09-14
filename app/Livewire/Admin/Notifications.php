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

        $this->notifications = $user
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
        
        $this->unreadCount = $user
            ->unreadNotifications()
            ->count();

        // Ambil notifikasi unread terbaru untuk popup alert (notif apapun)
        $unreads = $user->unreadNotifications()
            ->latest()
            ->take(5)
            ->get();

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

            return [
                'id' => (string) $notif->id,
                'type' => (string) $type,
                'title' => (string) $title,
                'message' => (string) $message,
                'url' => (string) $this->resolveNotificationUrl($notif),
                'created_at_human' => $notif->created_at->diffForHumans(),
            ];
        })->values()->toArray();
    }

    public function resolveNotificationUrl($notification)
    {
        $data = $notification->data ?? [];
        $type = $data['type'] ?? '';

        if (!empty($data['url']) && !str_contains($data['url'], '/superadmin/')) {
            return $data['url'];
        }

        return match ($type) {
            'new_topup_request', 'topup_request_submitted' => route('admin.topup.approvals'),
            'new_withdraw_request', 'withdraw_status' => route('admin.withdraws.index'),
            'new_ktp_verification' => route('admin.verifications'),
            'new_registration', 'new_user' => route('admin.customers'),
            'new_report', 'partner_report' => !empty($data['report_id']) ? route('admin.partners.reports.show', $data['report_id']) : route('admin.partners.report'),
            'help_status' => !empty($data['help_id']) ? route('admin.helps.show', $data['help_id']) : route('admin.helps'),
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
            $notification->markAsRead();
            $this->loadNotifications();
            $this->dispatch('notification-updated');
        }
    }

    public function openNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        
        if ($notification && $notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
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

        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->loadNotifications();
        $this->dispatch('notification-updated');
        session()->flash('notification-success', 'Semua notifikasi telah ditandai sebagai dibaca');
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
