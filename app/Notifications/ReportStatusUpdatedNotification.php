<?php

namespace App\Notifications;

use App\Models\PartnerReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ReportStatusUpdatedNotification extends Notification
{
    use Queueable;

    public PartnerReport $report;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(PartnerReport $report, string $oldStatus, string $newStatus)
    {
        $this->report = $report;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $statusLabels = [
            'pending' => 'Menunggu Peninjauan',
            'in_progress' => 'Sedang Ditangani',
            'resolved' => 'Selesai',
            'dismissed' => 'Ditolak / Ditutup',
        ];

        $statusTitle = match ($this->newStatus) {
            'in_progress' => '🔍 Aduan Anda Sedang Ditangani Admin',
            'resolved' => '✅ Aduan Anda Selesai Ditangani',
            'dismissed' => '❌ Aduan Anda Ditutup / Ditolak',
            default => 'ℹ️ Pembaruan Status Laporan Aduan',
        };

        $statusMessage = match ($this->newStatus) {
            'in_progress' => 'Laporan aduan "' . Str::limit($this->report->title, 35) . '" sedang ditinjau dan diproses oleh tim Admin.',
            'resolved' => 'Laporan aduan "' . Str::limit($this->report->title, 35) . '" telah selesai ditindaklanjuti oleh tim Admin. Ketuk untuk melihat detail aduan.',
            'dismissed' => 'Laporan aduan "' . Str::limit($this->report->title, 35) . '" telah ditinjau dan ditutup oleh tim Admin.',
            default => 'Status laporan aduan Anda telah diperbarui menjadi ' . ($statusLabels[$this->newStatus] ?? $this->newStatus) . '.',
        };

        $url = ($notifiable->role === 'mitra')
            ? route('mitra.reports.show', $this->report->id)
            : route('customer.reports.show', $this->report->id);

        return [
            'type' => 'report_status',
            'report_id' => $this->report->id,
            'title' => $statusTitle,
            'message' => $statusMessage,
            'status' => $this->newStatus,
            'old_status' => $this->oldStatus,
            'url' => $url,
        ];
    }
}
