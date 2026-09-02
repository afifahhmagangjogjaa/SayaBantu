<?php

namespace App\Livewire\Admin\Helps;

use Livewire\Component;
use App\Models\Help;

class Show extends Component
{
    public $helpId;
    public $help;
    public $showDecisionModal = false;
    public $decisionType = ''; // 'refund' | 'reject_complaint'
    public $admin_notes = '';

    public function mount($id)
    {
        $this->helpId = $id;
        $this->loadHelp();
    }

    public function loadHelp()
    {
        $this->help = Help::with(['customer', 'mitra', 'category', 'city'])->findOrFail($this->helpId);
    }

    public function approveHelp()
    {
        $this->help->update(['status' => 'menunggu_mitra']);
        $this->loadHelp();
        session()->flash('message', 'Bantuan berhasil disetujui, menunggu mitra.');
    }

    public function rejectHelp()
    {
        $this->help->update(['status' => 'rejected']);
        $this->loadHelp();
        session()->flash('message', 'Bantuan telah ditolak.');
    }

    public function openDecisionModal($type)
    {
        $this->decisionType = $type;
        $this->admin_notes = '';
        $this->showDecisionModal = true;
    }

    public function closeDecisionModal()
    {
        $this->showDecisionModal = false;
        $this->admin_notes = '';
        $this->decisionType = '';
    }

    public function processDecision()
    {
        if ($this->decisionType === 'refund') {
            $this->approveRefund();
        } elseif ($this->decisionType === 'reject_complaint') {
            $this->rejectComplaint();
        }
    }

    public function approveRefund()
    {
        $oldStatus = $this->help->status;
        
        // Mengubah status menjadi 'dibatalkan' -> HelpObserver otomatis mengembalikan saldo 100% ke customer
        $this->help->update([
            'status' => 'dibatalkan',
            'complaint_resolved_at' => now(),
            'complaint_resolution' => 'refunded',
            'complaint_admin_notes' => $this->admin_notes,
        ]);

        // Notifikasi ke Customer
        try {
            $customer = $this->help->customer ?? $this->help->user;
            if ($customer) {
                $customer->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'dibatalkan', $customer));
            }
        } catch (\Throwable $e) {}

        // Notifikasi ke Mitra
        try {
            if ($this->help->mitra) {
                $this->help->mitra->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'dibatalkan', $this->help->mitra));
            }
        } catch (\Throwable $e) {}

        $this->showDecisionModal = false;
        $this->loadHelp();
        session()->flash('message', 'Komplain customer disetujui. Dana sebesar Rp ' . number_format($this->help->amount + ($this->help->admin_fee ?? 0), 0, ',', '.') . ' telah dikembalikan ke saldo customer.');
    }

    public function rejectComplaint()
    {
        $oldStatus = $this->help->status;

        // Mengubah status menjadi 'selesai' -> HelpObserver otomatis mencairkan saldo ke mitra
        $this->help->update([
            'status' => 'selesai',
            'completed_at' => $this->help->completed_at ?? now(),
            'complaint_resolved_at' => now(),
            'complaint_resolution' => 'rejected',
            'complaint_admin_notes' => $this->admin_notes,
        ]);

        // Notifikasi ke Customer
        try {
            $customer = $this->help->customer ?? $this->help->user;
            if ($customer) {
                $customer->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'selesai', $customer));
            }
        } catch (\Throwable $e) {}

        // Notifikasi ke Mitra
        try {
            if ($this->help->mitra) {
                $this->help->mitra->notify(new \App\Notifications\HelpStatusNotification($this->help, $oldStatus, 'selesai', $this->help->mitra));
            }
        } catch (\Throwable $e) {}

        $this->showDecisionModal = false;
        $this->loadHelp();
        session()->flash('message', 'Komplain ditolak. Pekerjaan dinyatakan selesai dan dana sebesar Rp ' . number_format($this->help->amount, 0, ',', '.') . ' telah dicairkan ke saldo mitra.');
    }

    public function render()
    {
        return view('admin.helps-show')
            ->layout('layouts.admin', ['pageTitle' => 'Detail Bantuan #' . $this->helpId]);
    }
}
