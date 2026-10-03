<?php

namespace App\Livewire\Customer;

use App\Models\Help;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class IdlePartnerAlert extends Component
{
    public $showBanner = false;
    public $activeHelpId = null;

    public function mount()
    {
        $this->checkForIdlePartner();
    }

    public function checkForIdlePartner()
    {
        if (!auth()->check()) {
            $this->showBanner = false;
            $this->activeHelpId = null;
            return;
        }

        // Ambil semua kandidat bantuan customer yang sedang ditangani mitra tetapi belum berangkat
        $candidates = Help::where('user_id', auth()->id())
            ->whereIn('status', ['taken', 'memperoleh_mitra'])
            ->whereNotNull('mitra_id')
            ->whereNull('partner_started_moving_at')
            ->whereNull('partner_started_at')
            ->with(['mitra', 'category'])
            ->latest()
            ->get();

        $idleHelp = null;
        foreach ($candidates as $candidate) {
            // Evaluasi kondisi riil apakah mitra sudah telat berangkat >= 30 menit
            if ($candidate->isPartnerIdleOver30Minutes()) {
                $idleHelp = $candidate;
                break;
            }
        }

        if ($idleHelp) {
            $this->activeHelpId = $idleHelp->id;

            // Pastikan tersimpan juga di riwayat notifikasi database customer jika belum pernah dikirim untuk mitra ini
            $alreadyNotified = \Illuminate\Support\Facades\DB::table('notifications')
                ->where('notifiable_id', auth()->id())
                ->where('data->type', 'idle_partner_alert')
                ->where('data->help_id', $idleHelp->id)
                ->where('data->mitra_id', $idleHelp->mitra_id)
                ->exists();

            if (!$alreadyNotified) {
                try {
                    $idleHelp->user->notify(new \App\Notifications\IdlePartnerNotification($idleHelp));
                } catch (\Throwable $e) {
                    Log::warning('Gagal kirim IdlePartnerNotification: ' . $e->getMessage());
                }
            }

            // Kunci unik untuk menandai apakah notifikasi ini sudah diakui/ditutup di sesi ini
            $ackKey = 'idle_alert_ack_' . $idleHelp->id . '_' . $idleHelp->mitra_id;

            // Jika sudah pernah ditutup manual di sesi ini, jangan munculkan banner toast lagi
            if (session()->get($ackKey)) {
                $this->showBanner = false;
                return;
            }

            $this->showBanner = true;
        } else {
            $this->showBanner = false;
            $this->activeHelpId = null;
        }
    }

    public function goToDetail()
    {
        if (!$this->activeHelpId) {
            return;
        }
        $help = Help::find($this->activeHelpId);
        if ($help) {
            session()->put('idle_alert_ack_' . $help->id . '_' . $help->mitra_id, true);
        }
        $helpId = $this->activeHelpId;
        $this->showBanner = false;
        return redirect()->to(route('customer.helps.detail', $helpId) . '#customer-action-section');
    }

    public function dismissBanner()
    {
        if ($this->activeHelpId) {
            $help = Help::find($this->activeHelpId);
            if ($help) {
                session()->put('idle_alert_ack_' . $help->id . '_' . $help->mitra_id, true);
            }
        }
        $this->showBanner = false;
        $this->activeHelpId = null;
    }

    public function render()
    {
        $activeHelp = null;
        if ($this->showBanner && $this->activeHelpId) {
            $activeHelp = Help::with(['mitra', 'category'])->find($this->activeHelpId);
            if (!$activeHelp || !$activeHelp->isPartnerIdleOver30Minutes()) {
                $this->showBanner = false;
                $activeHelp = null;
            }
        }

        return view('livewire.customer.idle-partner-alert', [
            'activeHelp' => $activeHelp,
            'showBanner' => $this->showBanner && $activeHelp !== null,
        ]);
    }
}
