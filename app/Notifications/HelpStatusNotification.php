<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class HelpStatusNotification extends Notification
{
    use Queueable;

    protected $help;
    protected $oldStatus;
    protected $newStatus;
    protected $mitra;

    public function __construct($help, $oldStatus = null, $newStatus = null, $mitra = null)
    {
        $this->help = $help;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus ?? $help->status;
        $this->mitra = $mitra ?? $help->mitra;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $mitraName = $this->mitra?->name ?? 'Mitra';

        $isComplaintRefund = ($this->help->complaint_resolution === 'refunded' || in_array(strtolower($this->newStatus), ['refunded', 'dibatalkan']) && in_array(strtolower($this->oldStatus ?? ''), ['komplain', 'disputed']));
        $isComplaintMitraRelease = ($this->help->complaint_resolution === 'rejected' && in_array(strtolower($this->newStatus), ['selesai', 'completed']));

        $isAutoConfirm24h = (strtolower($this->newStatus) === 'selesai_auto_24h') ||
            (in_array(strtolower($this->newStatus), ['selesai', 'completed']) &&
             $this->help->admin_notes &&
             str_contains(strtolower($this->help->admin_notes), 'auto-confirm'));

        $formattedAmount = 'Rp ' . number_format($this->help->amount + ($this->help->admin_fee ?? 0), 0, ',', '.');
        $formattedMitraAmount = 'Rp ' . number_format($this->help->getMitraPayoutAmount(), 0, ',', '.');
        $helpTitle = $this->help->title ?? 'Bantuan #' . ($this->help->id ?? '');
        $customerName = $this->help->customer?->name ?? 'Customer';

        $title = match (true) {
            $isAutoConfirm24h => ($notifiable->role === 'mitra' ? '🎉 Pesanan Selesai Otomatis (24 Jam)' : '⏱️ Pesanan Selesai Otomatis'),
            $isComplaintRefund => ($notifiable->role === 'mitra' ? '⚠️ Mediasi Komplain: Refund Customer' : '💸 Pengajuan Pengembalian Dana Disetujui'),
            $isComplaintMitraRelease => ($notifiable->role === 'mitra' ? '🎉 Komplain Ditolak & Dana Dicairkan' : '⚖️ Hasil Mediasi Komplain Selesai'),
            in_array(strtolower($this->newStatus), ['komplain', 'disputed']) => '⚠️ Komplain Bantuan Diajukan',
            strtolower($this->newStatus) === 'rejected' => '❌ Bantuan Ditolak',
            strtolower($this->newStatus) === 'dibatalkan' || strtolower($this->newStatus) === 'cancelled' => '❌ Bantuan Dibatalkan',
            strtolower($this->newStatus) === 'partner_on_the_way' => "🚗 Mitra Dalam Perjalanan",
            strtolower($this->newStatus) === 'partner_arrived' => "📍 Mitra Telah Tiba di Lokasi",
            strtolower($this->newStatus) === 'in_progress' => "⚙️ Pekerjaan Dimulai",
            strtolower($this->newStatus) === 'waiting_customer_confirmation' => "✋ Menunggu Konfirmasi Anda",
            strtolower($this->newStatus) === 'partner_reassigned' => ($notifiable->role === 'mitra')
                ? "🔄 Pesanan Dialihkan ke Rekan Jasa Lain"
                : (($notifiable->role === 'admin' || $notifiable->role === 'super_admin')
                    ? "🔄 Pesanan Dialihkan (Mitra Tidak Bergerak >30m)"
                    : "🔄 Mencari Rekan Jasa Lain"),
            strtolower($this->newStatus) === 'partner_cancelled_direct' => ($notifiable->role === 'admin' || $notifiable->role === 'super_admin')
                ? "⚠️ Rekan Jasa Membatalkan Bantuan"
                : ($notifiable->role === 'mitra'
                    ? "🔄 Pesanan Dibatalkan"
                    : "🔄 Rekan Jasa Berhalangan Hadir"),
            strtolower($this->newStatus) === 'partner_cancel_requested' => "⚠️ Permintaan Pembatalan Mitra",
            strtolower($this->newStatus) === 'cancel_accepted' => "✅ Pembatalan Disetujui",
            strtolower($this->newStatus) === 'cancel_rejected' => "❌ Pembatalan Ditolak",
            in_array(strtolower($this->newStatus), ['selesai', 'completed']) => "🎉 Bantuan Selesai",
            default => "Update Status Bantuan"
        };

        $message = match (true) {
            $isAutoConfirm24h => ($notifiable->role === 'mitra')
                ? "Customer tidak mengonfirmasi dalam 24 jam. Bantuan '{$helpTitle}' diselesaikan otomatis oleh sistem dan saldo {$formattedMitraAmount} telah dicairkan ke akun Anda."
                : "Bantuan '{$helpTitle}' telah diselesaikan otomatis oleh sistem karena melewati batas konfirmasi 24 jam.",
            $isComplaintRefund => ($notifiable->role === 'mitra')
                ? "Mediasi komplain pada bantuan '{$helpTitle}' diselesaikan Admin dengan pengembalian dana ke customer."
                : "Pengajuan pengembalian dana untuk bantuan '{$helpTitle}' telah disetujui Admin. Saldo sebesar {$formattedAmount} telah dikembalikan ke dompet Anda.",
            $isComplaintMitraRelease => ($notifiable->role === 'mitra')
                ? "Komplain customer pada bantuan '{$helpTitle}' telah ditolak Admin. Dana sebesar {$formattedMitraAmount} telah dicairkan ke saldo dompet Anda."
                : "Komplain Anda pada bantuan '{$helpTitle}' telah ditinjau. Admin menetapkan pekerjaan telah selesai dan dana diteruskan ke rekan jasa.",
            in_array(strtolower($this->newStatus), ['komplain', 'disputed']) => ($notifiable->role === 'admin' || $notifiable->role === 'super_admin')
                ? "Customer {$customerName} mengajukan komplain pada '{$helpTitle}'. Mohon segera mediasi dalam 24 jam."
                : (($notifiable->role === 'mitra')
                    ? "Customer mengajukan komplain pada pesanan '{$helpTitle}'. Dana ditahan sementara menunggu mediasi Admin."
                    : "Komplain Anda pada '{$helpTitle}' telah diterima dan sedang ditinjau oleh Admin."),
            strtolower($this->newStatus) === 'rejected' => 'Permintaan bantuan "' . $helpTitle . '" ditolak oleh admin.' .
                ($this->help->admin_notes ? ' Alasan: ' . $this->help->admin_notes : ''),
            strtolower($this->newStatus) === 'dibatalkan' || strtolower($this->newStatus) === 'cancelled' => ($notifiable->role === 'mitra')
                ? "Customer telah membatalkan pesanan '{$helpTitle}'." . ($this->help->customer_cancel_reason ? " (Alasan: {$this->help->customer_cancel_reason})" : '')
                : "Bantuan '{$helpTitle}' telah dibatalkan. Saldo sebesar {$formattedAmount} telah dikembalikan ke dompet Anda.",
            strtolower($this->newStatus) === 'partner_on_the_way' => "$mitraName sedang menuju lokasi Anda",
            strtolower($this->newStatus) === 'partner_arrived' => "$mitraName telah tiba di lokasi Anda",
            strtolower($this->newStatus) === 'in_progress' => "$mitraName telah memulai pekerjaan",
            strtolower($this->newStatus) === 'waiting_customer_confirmation' => "$mitraName telah menyelesaikan pekerjaan. Silakan konfirmasi pesanan Anda.",
            strtolower($this->newStatus) === 'partner_reassigned' => ($notifiable->role === 'mitra')
                ? "Pesanan '{$helpTitle}' telah dialihkan oleh customer ke Rekan Jasa lain karena belum ada tanda keberangkatan menuju lokasi setelah lebih dari 30 menit."
                : (($notifiable->role === 'admin' || $notifiable->role === 'super_admin')
                    ? "Pesanan #{$this->help->id} '{$helpTitle}' dialihkan dari {$mitraName} ke antrean pencarian baru karena tidak bergerak >30 menit."
                    : "Sistem sedang mencarikan Rekan Jasa pengganti untuk pesanan '{$helpTitle}'."),
            strtolower($this->newStatus) === 'partner_cancelled_direct' => ($notifiable->role === 'admin' || $notifiable->role === 'super_admin')
                ? "Rekan Jasa {$mitraName} membatalkan pesanan #{$this->help->id} '{$helpTitle}' (Alasan: " . ($this->help->partner_cancel_reason ?: 'Kendala di jalan') . "). Pesanan telah dikembalikan ke antrean."
                : ($notifiable->role === 'mitra'
                    ? "Anda telah membatalkan pesanan '{$helpTitle}'."
                    : "Rekan Jasa $mitraName membatalkan pesanan (" . ($this->help->partner_cancel_reason ?: 'Kendala di jalan') . "). Sistem sedang mencarikan Rekan Jasa pengganti untuk Anda."),
            strtolower($this->newStatus) === 'partner_cancel_requested' => "$mitraName meminta pembatalan bantuan. Cek detail pesanan.",
            strtolower($this->newStatus) === 'cancel_accepted' => "Customer menerima pembatalan. Bantuan dibuka kembali untuk mitra lain.",
            strtolower($this->newStatus) === 'cancel_rejected' => "Customer menolak pembatalan. Silakan lanjutkan pekerjaan.",
            in_array(strtolower($this->newStatus), ['selesai', 'completed']) => "Bantuan '" . $helpTitle . "' telah dikonfirmasi selesai.",
            default => "Status bantuan diperbarui: {$this->newStatus}"
        };

        $url = null;
        $mitraId = $this->mitra?->id ?? $this->help->mitra_id ?? null;

        if (!empty($this->help->id)) {
            if ($notifiable->role === 'admin') {
                if (strtolower($this->newStatus) === 'partner_cancelled_direct' && $mitraId) {
                    $url = route('admin.mitra.show', $mitraId);
                } else {
                    $url = route('admin.helps.show', $this->help->id);
                }
            } elseif ($notifiable->role === 'super_admin') {
                if (strtolower($this->newStatus) === 'partner_cancelled_direct' && $mitraId) {
                    $url = route('superadmin.users') . '?search=' . urlencode($this->mitra?->email ?? $this->mitra?->name ?? '');
                } else {
                    $url = route('superadmin.helps.approved');
                }
            } elseif ($notifiable->role === 'mitra') {
                $url = route('mitra.helps.detail', $this->help->id);
            } else {
                $url = route('customer.helps.detail', $this->help->id);
            }
        }

        return [
            'type' => 'help_status',
            'title' => $title,
            'help_id' => $this->help->id ?? null,
            'help_title' => $this->help->title ?? null,
            'help_amount' => $this->help->amount ?? null,
            'city_id' => $this->help->city_id ?? null,
            'mitra_id' => $mitraId,
            'mitra_name' => $mitraName,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $message,
            'url' => $url,
        ];
    }
}
