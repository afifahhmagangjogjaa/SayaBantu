<?php

namespace App\Notifications;

use App\Models\PartnerReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReportNotification extends Notification
{
    use Queueable;

    public $report;

    public function __construct(PartnerReport $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $reporterName = $this->report->reporter?->name ?? ($this->report->category === 'dari_customer' ? 'Customer' : 'Mitra');
        $rolePrefix = $this->report->category === 'dari_customer' ? 'Customer' : 'Mitra';

        $url = $notifiable->role === 'super_admin'
            ? route('admin.partners.reports.show', $this->report->id)
            : route('admin.partners.reports.show', $this->report->id);

        return [
            'type' => 'new_report',
            'title' => '⚠️ Laporan Aduan Baru: ' . \Illuminate\Support\Str::limit($this->report->title, 35),
            'message' => "{$rolePrefix} {$reporterName} mengajukan laporan aduan: '{$this->report->message}'",
            'report_id' => $this->report->id,
            'report_type' => $this->report->report_type,
            'category' => $this->report->category,
            'reporter_id' => $this->report->reporter_id,
            'reporter_name' => $reporterName,
            'reported_user_id' => $this->report->reported_user_id,
            'reported_help_id' => $this->report->reported_help_id,
            'url' => $url,
        ];
    }
}
