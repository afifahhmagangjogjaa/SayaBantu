<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Chat as ChatModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RealtimeNotifications extends Component
{
    public $last_chat_id = 0;
    public $last_notification_check = null;
    public $processed_ids = [];
    public $sanctionModalOpen = false;
    public $currentSanction = null;

    public function mount()
    {
        if (auth()->check()) {
            $this->last_chat_id = ChatModel::where('customer_id', auth()->id())->max('id') ?? 0;
            $this->last_notification_check = now()->subSeconds(5);
            $this->processed_ids = auth()->user()->notifications()
                ->latest()
                ->take(30)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();

            $this->checkUnpoppedSanctions();
            $this->checkUnpoppedRejections();
            $this->checkUnpoppedReportStatus();
            $this->checkUnpoppedTopups();
        }
    }

    public function poll()
    {
        if (!auth()->check()) {
            return;
        }

        // Check for unpopped sanctions & report status & topup
        if (!$this->sanctionModalOpen) {
            $this->checkUnpoppedSanctions();
            $this->checkUnpoppedReportStatus();
            $this->checkUnpoppedTopups();
        }

        // Check for new chat messages
        $new = ChatModel::where('customer_id', auth()->id())
            ->where('sender_type', 'mitra')
            ->where('id', '>', $this->last_chat_id)
            ->orderBy('id', 'asc')
            ->first();

        if ($new) {
            Log::info('[Customer\RealtimeNotifications] found new chat id=' . $new->id . ' help_id=' . $new->help_id . ' message=' . Str::limit($new->message, 120));

            $this->last_chat_id = $new->id;

            $this->dispatch(
                'help-new-message',
                helpId: $new->help_id,
                message: Str::limit($new->message, 150),
                from: optional($new->mitra)->name ?? 'Mitra'
            );
        }

        // Check for new database notifications (help taken, status updates, etc)
        $newNotifications = auth()->user()->notifications()
            ->where('created_at', '>=', $this->last_notification_check)
            ->whereNotIn('id', $this->processed_ids)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($newNotifications->count() > 0) {
            $this->last_notification_check = $newNotifications->last()->created_at;
            foreach ($newNotifications as $n) {
                $this->processed_ids[] = (string) $n->id;
            }
            if (count($this->processed_ids) > 100) {
                $this->processed_ids = array_slice($this->processed_ids, -50);
            }
        }

        foreach ($newNotifications as $notification) {
            $data = $notification->data;

            Log::info('[Customer\\RealtimeNotifications] processing notification', [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $data
            ]);

            // Handle help_taken notification
            if (isset($data['type']) && $data['type'] === 'help_taken') {
                $this->dispatch(
                    'help-taken',
                    helpId: $data['help_id'] ?? null,
                    mitraName: $data['mitra_name'] ?? 'Mitra'
                );
                
                $this->js(sprintf(
                    "console.log('🔔 Dispatching help-taken event from backend'); window.dispatchEvent(new CustomEvent('help-taken', { detail: { helpId: %d, mitraName: '%s' } }))",
                    $data['help_id'] ?? 0,
                    addslashes($data['mitra_name'] ?? 'Mitra')
                ));
            }

            // Handle help status update notification
            if (isset($data['type']) && $data['type'] === 'help_status') {
                $helpId = $data['help_id'] ?? ($data['helpId'] ?? null);
                $helpTitle = $data['help_title'] ?? ($data['helpTitle'] ?? ($data['title'] ?? ''));
                $newStatus = $data['new_status'] ?? ($data['newStatus'] ?? ($data['status'] ?? null));

                $notifTitle = $data['title'] ?? '';
                $url = $data['url'] ?? route('customer.helps.detail', $helpId);

                $this->dispatch(
                    'status-changed',
                    [
                        'helpId' => $helpId,
                        'helpTitle' => $helpTitle,
                        'oldStatus' => $data['old_status'] ?? null,
                        'newStatus' => $newStatus,
                        'message' => $data['message'] ?? null,
                        'mitraName' => $data['mitra_name'] ?? ($data['mitraName'] ?? 'Mitra'),
                        'notifTitle' => $notifTitle,
                        'url' => $url,
                    ]
                );

                $this->js(sprintf(
                    "console.log('🔔 Dispatching help-status event from backend'); window.dispatchEvent(new CustomEvent('help-status-update', { detail: { helpId: %d, helpTitle: '%s', status: '%s', newStatus: '%s', message: '%s', mitraName: '%s', notifTitle: '%s', url: '%s' } }))",
                    $helpId ?? 0,
                    addslashes($helpTitle ?? ''),
                    addslashes($newStatus ?? ''),
                    addslashes($newStatus ?? ''),
                    addslashes($data['message'] ?? ''),
                    addslashes($data['mitra_name'] ?? ($data['mitraName'] ?? 'Mitra')),
                    addslashes($notifTitle),
                    addslashes($url)
                ));
            }

            // Handle topup_approved notification
            if ((isset($data['type']) && $data['type'] === 'topup_approved') || $notification->type === 'App\Notifications\TopupApproved') {
                $notification->update(['popped_at' => now()]);

                $this->dispatch('balance-updated');
                $this->js("window.dispatchEvent(new CustomEvent('balance-updated'))");

                $amount = isset($data['amount']) ? 'Rp ' . number_format((float)$data['amount'], 0, ',', '.') : '';
                $title = $data['title'] ?? 'Top-Up Saldo Disetujui! ✅';
                $message = $data['message'] ?? ("Request top-up saldo Anda sebesar $amount telah disetujui! Saldo sudah masuk.");
                $url = $data['url'] ?? route('customer.transactions.index', ['tab' => 'masuk']);

                $this->dispatch('customer-toast', [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'success',
                    'url' => $url,
                    'timeout' => 8000,
                ]);

                $this->js(sprintf(
                    "window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: 'success', url: '%s', timeout: 8000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                    addslashes($title),
                    addslashes($message),
                    addslashes($url)
                ));
            }

            // Handle topup_rejected notification
            if ((isset($data['type']) && $data['type'] === 'topup_rejected') || $notification->type === 'App\Notifications\TopupRejected') {
                $notification->update(['popped_at' => now()]);

                $title = $data['title'] ?? 'Top-Up Saldo Ditolak ❌';
                $reason = $data['rejection_reason'] ?? '';
                $message = $data['message'] ?? ("Request top-up saldo Anda ditolak." . ($reason ? " Alasan: $reason" : ''));
                $url = $data['url'] ?? route('customer.topup.history');

                $this->dispatch('customer-toast', [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'error',
                    'url' => $url,
                    'timeout' => 8000,
                ]);

                $this->js(sprintf(
                    "window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: 'error', url: '%s', timeout: 8000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                    addslashes($title),
                    addslashes($message),
                    addslashes($url)
                ));
            }

            // Handle topup_request_submitted notification
            if ((isset($data['type']) && $data['type'] === 'topup_request_submitted') || $notification->type === 'App\Notifications\TopupRequestSubmitted') {
                $notification->update(['popped_at' => now()]);

                $title = $data['title'] ?? '⏳ Request Top-Up Terkirim';
                $amount = isset($data['amount']) ? 'Rp ' . number_format((float)$data['amount'], 0, ',', '.') : '';
                $message = $data['message'] ?? "Request top-up saldo Anda sebesar $amount telah diterima dan menunggu verifikasi Admin.";
                $url = $data['url'] ?? route('customer.topup.history');

                $this->dispatch('customer-toast', [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'info',
                    'url' => $url,
                    'timeout' => 8000,
                ]);

                $this->js(sprintf(
                    "window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: 'info', url: '%s', timeout: 8000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                    addslashes($title),
                    addslashes($message),
                    addslashes($url)
                ));
            }

            // Handle idle_partner_alert notification
            if (isset($data['type']) && $data['type'] === 'idle_partner_alert') {
                $notification->update(['popped_at' => now()]);

                $title = $data['title'] ?? '⏳ Rekan Jasa Belum Berangkat';
                $message = $data['message'] ?? 'Rekan Jasa belum menuju lokasi Anda setelah 30 menit.';
                $url = $data['url'] ?? route('customer.helps.detail', $data['help_id'] ?? 0);

                $this->dispatch('customer-toast', [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'warning',
                    'url' => $url,
                    'timeout' => 10000,
                ]);

                $this->js(sprintf(
                    "window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: 'warning', url: '%s', timeout: 10000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                    addslashes($title),
                    addslashes($message),
                    addslashes($url)
                ));
            }

            // Handle report status update notification
            if (isset($data['type']) && $data['type'] === 'report_status') {
                $statusType = match ($data['new_status'] ?? '') {
                    'resolved' => 'completed',
                    'dismissed' => 'error',
                    'in_progress' => 'in_progress',
                    default => 'info',
                };

                $notification->update(['popped_at' => now()]);

                $this->dispatch('customer-toast', [
                    'title' => $data['title'] ?? 'Pembaruan Status Aduan',
                    'message' => $data['message'] ?? '',
                    'type' => $statusType,
                    'url' => $data['url'] ?? '#',
                    'timeout' => 8000,
                ]);

                $this->js(sprintf(
                    "window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: '%s', url: '%s', timeout: 8000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }",
                    addslashes($data['title'] ?? 'Pembaruan Status Aduan'),
                    addslashes($data['message'] ?? ''),
                    $statusType,
                    addslashes($data['url'] ?? '#')
                ));
            }
        }
    }

    public function checkUnpoppedReportStatus()
    {
        if (!auth()->check()) {
            return;
        }

        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->get()
            ->first(function ($n) {
                return ($n->data['type'] ?? '') === 'report_status';
            });

        if ($notif) {
            $data = $notif->data;
            $notif->update(['popped_at' => now()]);

            $statusType = match ($data['new_status'] ?? '') {
                'resolved' => 'completed',
                'dismissed' => 'error',
                'in_progress' => 'in_progress',
                default => 'info',
            };

            $this->js(sprintf(
                "setTimeout(() => { window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: '%s', url: '%s', timeout: 8000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); } }, 600);",
                addslashes($data['title'] ?? 'Pembaruan Status Aduan'),
                addslashes($data['message'] ?? ''),
                $statusType,
                addslashes($data['url'] ?? '#')
            ));
        }
    }

    public function checkUnpoppedTopups()
    {
        if (!auth()->check()) {
            return;
        }

        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->get()
            ->first(function ($n) {
                $type = $n->data['type'] ?? '';
                return in_array($type, ['topup_approved', 'topup_rejected', 'topup_request_submitted'])
                    || in_array($n->type, [
                        'App\Notifications\TopupApproved', 
                        'App\Notifications\TopupRejected',
                        'App\Notifications\TopupRequestSubmitted'
                    ]);
            });

        if ($notif) {
            $data = $notif->data;
            $notif->update(['popped_at' => now()]);

            $type = $data['type'] ?? '';
            $isApproved = $type === 'topup_approved' || $notif->type === 'App\Notifications\TopupApproved';
            $isRejected = $type === 'topup_rejected' || $notif->type === 'App\Notifications\TopupRejected';

            if ($isApproved) {
                $amount = isset($data['amount']) ? 'Rp ' . number_format((float)$data['amount'], 0, ',', '.') : '';
                $title = $data['title'] ?? 'Top-Up Saldo Disetujui! ✅';
                $message = $data['message'] ?? ("Request top-up saldo Anda sebesar $amount telah disetujui! Saldo sudah masuk.");
                $url = $data['url'] ?? route('customer.transactions.index', ['tab' => 'masuk']);
                $toastType = 'success';

                $this->dispatch('balance-updated');
                $this->js("window.dispatchEvent(new CustomEvent('balance-updated'))");
            } elseif ($isRejected) {
                $title = $data['title'] ?? 'Top-Up Saldo Ditolak ❌';
                $reason = $data['rejection_reason'] ?? '';
                $message = $data['message'] ?? ("Request top-up saldo Anda ditolak." . ($reason ? " Alasan: $reason" : ''));
                $url = $data['url'] ?? route('customer.topup.history');
                $toastType = 'error';
            } else {
                $title = $data['title'] ?? '⏳ Request Top-Up Terkirim';
                $amount = isset($data['amount']) ? 'Rp ' . number_format((float)$data['amount'], 0, ',', '.') : '';
                $message = $data['message'] ?? ("Request top-up saldo Anda sebesar $amount telah diterima dan menunggu verifikasi Admin.");
                $url = $data['url'] ?? route('customer.topup.history');
                $toastType = 'info';
            }

            $this->dispatch('customer-toast', [
                'title' => $title,
                'message' => $message,
                'type' => $toastType,
                'url' => $url,
                'timeout' => 8000,
            ]);

            $this->js(sprintf(
                "setTimeout(() => { window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '%s', message: '%s', type: '%s', url: '%s', timeout: 8000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); } }, 500);",
                addslashes($title),
                addslashes($message),
                $toastType,
                addslashes($url)
            ));
        }
    }

    public function checkUnpoppedRejections()
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['customer', 'kustomer', 'user'])) {
            return;
        }

        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->get()
            ->first(function ($n) {
                $data = $n->data;
                $status = strtolower($data['new_status'] ?? ($data['status'] ?? ''));
                return in_array($status, ['rejected', 'ditolak']);
            });

        if ($notif) {
            $data = $notif->data;
            $helpId = (int) ($data['help_id'] ?? 0);
            $helpTitle = $data['help_title'] ?? 'Bantuan';
            $message = $data['message'] ?? 'Permintaan bantuan Anda ditolak oleh admin.';
            $url = $data['url'] ?? ($helpId ? route('customer.helps.detail', $helpId) : route('customer.helps.index', ['statusFilter' => 'dibatalkan']));

            $notif->update(['popped_at' => now()]);

            $this->js(sprintf(
                "setTimeout(() => { window.dispatchEvent(new CustomEvent('help-status-update', { detail: { helpId: %d, helpTitle: '%s', status: 'rejected', newStatus: 'rejected', message: '%s', notifTitle: '❌ Bantuan Ditolak Admin', url: '%s' } })); }, 400);",
                $helpId,
                addslashes($helpTitle),
                addslashes($message),
                addslashes($url)
            ));
        }
    }

    public function checkUnpoppedSanctions()
    {
        if (!auth()->check()) {
            return;
        }

        // Cari notifikasi database sanksi/SP yang belum di-pop-up (popped_at null)
        $notif = auth()->user()->notifications()
            ->whereNull('popped_at')
            ->latest()
            ->get()
            ->first(function ($n) {
                $type = $n->data['type'] ?? '';
                return $type === 'sanction_warning' || str_contains($type, 'sanction') || $n->type === 'App\Notifications\SanctionNotification';
            });

        if ($notif) {
            $data = $notif->data;
            $level = (int) ($data['warning_level'] ?? 1);

            // Tandai sudah di-pop agar tidak muncul lagi
            $notif->update(['popped_at' => now()]);

            // SP 0 (cabut sanksi): JANGAN tampilkan modal merah, cukup toast hijau
            if ($level === 0) {
                $this->js("setTimeout(() => { window.dispatchEvent(new CustomEvent('customer-toast', { detail: { title: '✅ Surat Peringatan Dicabut', message: 'Status SP pada akun Anda telah resmi dicabut oleh Admin. Akun Anda kini kembali normal.', type: 'completed', timeout: 7000 } })); if (typeof window.playNotifChime === 'function') { window.playNotifChime(); } }, 400);");
                return;
            }

            $this->currentSanction = [
                'id' => (string) $notif->id,
                'title' => $data['title'] ?? ('⚠️ Surat Peringatan ' . $level),
                'message' => $data['message'] ?? 'Anda menerima Surat Peringatan dari admin.',
                'reason' => $data['reason'] ?? null,
                'warning_level' => $level,
                'url' => $data['url'] ?? route('profile'),
            ];
            $this->sanctionModalOpen = true;

            // Trigger notification sound / chime if browser permits
            $this->js("if (typeof window.playNotifChime === 'function') { window.playNotifChime(); }");
        }
    }

    public function dismissSanctionModal($notificationId = null)
    {
        $wasLevel3 = ($this->currentSanction['warning_level'] ?? 0) >= 3;
        $userId = auth()->id();

        $this->sanctionModalOpen = false;
        $this->currentSanction = null;

        // SP 3: Baru blokir akun sekarang (setelah user lihat pop-up peringatan terlebih dulu)
        if ($wasLevel3 && $userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->is_banned = true;
                $user->status = 'blocked';
                $user->save();
            }
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan/diblokir secara permanen dari sistem karena menerima Surat Peringatan 3.');
        }
    }

    public function viewSanctionDetail($notificationId = null)
    {
        $id = $notificationId ?: ($this->currentSanction['id'] ?? null);

        // Tutup modal (jangan trigger ban — ban hanya terjadi dari halaman detail untuk SP 3)
        $this->sanctionModalOpen = false;
        $this->currentSanction = null;

        if ($id && !str_starts_with($id, 'user_warning_level_')) {
            return redirect()->route('notifications.sanction', $id);
        }

        return redirect()->route('profile');
    }

    public function render()
    {
        return view('livewire.customer.realtime-notifications', [
            'sanctionModalOpen' => $this->sanctionModalOpen,
            'currentSanction' => $this->currentSanction,
        ]);
    }
}
