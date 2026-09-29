<?php

namespace App\Livewire\Customer;

use App\Models\Chat as ChatModel;
use App\Models\User;
use App\Notifications\ChatMessageNotification;
use App\Models\Help;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Chat extends Component
{
    use WithPagination;

    public $selected_help_id = null;
    public $message = '';
    public $search = '';

    protected $rules = [
        'message' => 'required|string|max:1000',
    ];

    public function mount($help = null)
    {
        $this->user = Auth::user();

        // If help is provided via route parameter, open that conversation immediately.
        if ($help) {
            $this->selected_help_id = $help;
            // mark mitra messages as read for that help
            ChatModel::where('help_id', $help)
                ->where('sender_type', 'mitra')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
                
            $this->markChatNotificationsAsRead($help);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getConversations()
    {
        $query = Help::where('user_id', Auth::id())
            ->where(function ($q) {
                $q->whereNotNull('mitra_id')
                    ->orWhereHas('chatMessages');
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('mitra', function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%');
            });
        }

        return $query->select('helps.*')
            ->selectSub(function ($q) {
                $q->from('chats')
                    ->whereColumn('chats.help_id', 'helps.id')
                    ->where('chats.sender_type', 'mitra')
                    ->whereNull('chats.read_at')
                    ->selectRaw('count(*)');
            }, 'unread_messages_count')
            ->with([
                'mitra:id,name,selfie_photo',
                'chatMessages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->orderByDesc('unread_messages_count')
            ->orderByRaw('COALESCE((SELECT MAX(created_at) FROM chats WHERE chats.help_id = helps.id), helps.updated_at) DESC')
            ->paginate(20);
    }

    public function getMessages()
    {
        if (!$this->selected_help_id) {
            return collect();
        }

        return ChatModel::where('help_id', $this->selected_help_id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['message' => $this->message],
            ['message' => 'required|string|max:1000'],
            []
        );

        $validator->validate();

        if (!$this->selected_help_id) {
            $this->dispatch('error', 'Pilih percakapan terlebih dahulu');
            return;
        }

        $help = Help::findOrFail($this->selected_help_id);

        if (in_array($help->status, ['selesai', 'completed', 'batal', 'cancelled', 'dibatalkan'])) {
            $this->dispatch('error', 'Pesanan telah ' . (in_array($help->status, ['selesai', 'completed']) ? 'selesai' : 'dibatalkan') . '. Percakapan ini telah diarsipkan.');
            return;
        }

        $chat = ChatModel::create([
            'help_id' => $this->selected_help_id,
            'mitra_id' => $help->mitra_id,
            'customer_id' => Auth::id(),
            'message' => $this->message,
            'sender_type' => 'customer',
        ]);

        $help->touch();

        // Notify the mitra (if exists) about the new message
        if ($help->mitra_id) {
            $mitra = User::find($help->mitra_id);
            if ($mitra) {
                $mitra->notify(new ChatMessageNotification($this->selected_help_id, Str::limit($this->message, 150), Auth::id(), optional(Auth::user())->name));
            }
        }

        $this->message = '';
        // Dispatch message-sent with helpId to allow redirecting to specific conversation
        $this->dispatch('message-sent', helpId: $this->selected_help_id);
    }

    public function selectHelp($help_id)
    {
        $this->selected_help_id = $help_id;

        ChatModel::where('help_id', $help_id)
            ->where('sender_type', 'mitra')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        $this->markChatNotificationsAsRead($help_id);
    }

    #[On('help-new-message')]
    public function refreshChat()
    {
        if ($this->selected_help_id) {
            ChatModel::where('help_id', $this->selected_help_id)
                ->where('sender_type', 'mitra')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
                
            $this->markChatNotificationsAsRead($this->selected_help_id);
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            ChatModel::where('customer_id', Auth::id())
                ->where('sender_type', 'mitra')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $notifications = auth()->user()->unreadNotifications;
            foreach ($notifications as $notification) {
                $data = $notification->data;
                if (isset($data['type']) && $data['type'] === 'chat_message') {
                    $notification->markAsRead();
                }
            }

            $this->dispatch('chat-all-read');
        }
    }

    private function markChatNotificationsAsRead($helpId)
    {
        if (auth()->check() && $helpId) {
            $notifications = auth()->user()->unreadNotifications;
            foreach ($notifications as $notification) {
                $data = $notification->data;
                if (isset($data['type']) && $data['type'] === 'chat_message' && isset($data['help_id']) && $data['help_id'] == $helpId) {
                    $notification->markAsRead();
                }
            }
        }
    }

    public function render()
    {
        $totalUnreadCount = 0;
        if (Auth::check()) {
            $totalUnreadCount = ChatModel::where('customer_id', Auth::id())
                ->where('sender_type', 'mitra')
                ->whereNull('read_at')
                ->count();
        }

        return view('livewire.customer.chat.index', [
            'conversations' => $this->getConversations(),
            'messages' => $this->getMessages(),
            'selected_help' => $this->selected_help_id ? Help::find($this->selected_help_id) : null,
            'totalUnreadCount' => $totalUnreadCount,
        ])->layout('layouts.app');
    }
}
