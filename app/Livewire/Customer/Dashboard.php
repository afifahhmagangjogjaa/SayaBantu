<?php

namespace App\Livewire\Customer;

use App\Models\Help;
use App\Models\UserBalance;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Dashboard extends Component
{
    use WithPagination;
    public $activeTab = 'latest'; // latest, all, history
    public $selectedHelp = null;
    public $selectedHelpData = null;

    #[On('balance-updated')]
    public function refreshBalance()
    {
        $this->dispatch('$refresh');
    }

    #[On('help-new-message')]
    public function refreshChatCount()
    {
        $this->dispatch('$refresh');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function showHelp($id)
    {
        $help = Help::with(['user', 'city', 'mitra', 'category'])->find($id);
        if (!$help) {
            $this->selectedHelp = null;
            $this->selectedHelpData = null;
            return;
        }

        $this->selectedHelp = $help;
        $this->selectedHelpData = [
            'id' => $help->id,
            'title' => $help->title,
            'description' => $help->description,
            'amount' => $help->amount,
            'photo' => $help->photo,
            'location' => $help->location,
            'status' => $help->status,
            'user_name' => $help->user?->name,
            'city_name' => $help->city?->name,
            'category_name' => $help->category?->name,
            'category_icon' => $help->category?->icon,
            'created_at_human' => $help->created_at?->diffForHumans(),
        ];
    }

    public function closeHelp()
    {
        $this->selectedHelp = null;
        $this->selectedHelpData = null;
    }

    public function render()
    {
        $user = auth()->user();

        $stats = [];

        // Get user balance
        $userBalance = UserBalance::where('user_id', $user->id)->first();
        $balance = $userBalance ? $userBalance->balance : 0;

        // Filter berdasarkan tab yang aktif
        if ($this->activeTab === 'latest') {
            // Hanya tampilkan bantuan yang MASIH MENUNGGU MITRA (belum diambil/diproses)
            $availableHelps = Help::where('user_id', $user->id)
                ->whereIn('status', [
                    'menunggu_pembayaran',
                    'mencari_mitra',
                    'menunggu_mitra',
                ])
                ->with(['user', 'city', 'mitra', 'category'])
                ->latest()
                ->take(10)
                ->get();
        } elseif ($this->activeTab === 'all') {
            // Ambil semua bantuan milik user sendiri (pakai pagination)
            $availableHelps = Help::where('user_id', $user->id)
                ->with(['user', 'city', 'mitra', 'category'])
                ->latest()
                ->paginate(10);
        } else { // history
            // Ambil riwayat transaksi user
            if ($user->isMitra()) {
                // Untuk mitra, tampilkan bantuan yang sudah dikerjakan
                $availableHelps = Help::where('mitra_id', $user->id)
                    ->with(['user', 'city', 'category'])
                    ->latest()
                    ->take(10)
                    ->get();
            } else {
                // Untuk customer, tampilkan bantuan yang sudah selesai atau dibatalkan
                $availableHelps = Help::where('user_id', $user->id)
                    ->whereIn('status', ['selesai', 'dibatalkan'])
                    ->with(['mitra', 'city', 'category'])
                    ->latest()
                    ->take(10)
                    ->get();
            }
        }

        if ($user->isCustomer()) {
            $stats = [
                'total_helps' => Help::where('user_id', $user->id)->count(),
                'pending_helps' => Help::where('user_id', $user->id)->where('status', 'menunggu_mitra')->count(),
                'completed_helps' => Help::where('user_id', $user->id)->where('status', 'selesai')->count(),
            ];

            $myHelps = Help::where('user_id', $user->id)
                ->with(['city', 'mitra', 'category'])
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->isMitra()) {
            $stats = [
                'total_helped' => Help::where('mitra_id', $user->id)->count(),
                'in_progress' => Help::where('mitra_id', $user->id)->where('status', 'memperoleh_mitra')->count(),
                'completed' => Help::where('mitra_id', $user->id)->where('status', 'selesai')->count(),
            ];

            $myHelps = Help::where('mitra_id', $user->id)
                ->with(['user', 'city', 'category'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            $myHelps = collect();
        }

        // Unread chats for customer (messages sent by mitra not yet read)
        $unreadChatCount = 0;
        try {
            $unreadChatCount = \App\Models\Chat::where('customer_id', $user->id)
                ->whereNull('read_at')
                ->where('sender_type', 'mitra')
                ->count();
        } catch (\Exception $e) {
            // ignore if Chat model or columns missing
        }

        return view('livewire.customer.dashboard', [
            'stats' => $stats,
            'myHelps' => $myHelps,
            'availableHelps' => $availableHelps,
            'balance' => $balance,
            'activeTab' => $this->activeTab,
            'unreadChatCount' => $unreadChatCount,
        ]);
    }
}
