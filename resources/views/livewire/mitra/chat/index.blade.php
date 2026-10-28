<div wire:poll.5s class="{{ $selected_help_id ? 'h-full overflow-hidden' : 'min-h-screen' }} bg-white">
    <div class="max-w-md mx-auto {{ $selected_help_id ? 'h-full flex flex-col overflow-hidden' : '' }}">
        <!-- Header - Brand Blue with Wave Separator -->
        <div class="px-5 pt-6 pb-10 relative overflow-hidden flex-shrink-0 text-white bg-[#0066FF]">
            <!-- Decorative circle overlays -->
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/10 rounded-full -ml-16 -mb-16 pointer-events-none"></div>

            <div class="relative z-10">
                @if(!$selected_help_id)
                    <div class="flex items-center justify-between text-white mb-2">
                        <a href="{{ route('mitra.dashboard') }}" wire:navigate aria-label="Kembali ke Dashboard" class="w-9 h-9 flex items-center justify-center p-2 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div class="text-center flex-1 px-2">
                            <h1 class="text-lg font-black text-white tracking-tight leading-tight">Chat</h1>
                            <p class="text-xs text-blue-100 font-medium mt-0.5">Percakapan dengan customer</p>
                        </div>
                        <div class="w-9 h-9 shrink-0"></div>
                    </div>
                @else
                    <div class="flex items-center justify-between gap-2 text-white mb-2">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <a href="{{ route('mitra.chat') }}" wire:navigate aria-label="Kembali ke Daftar Chat" class="w-9 h-9 flex items-center justify-center p-1.5 hover:bg-white/20 rounded-xl transition active:scale-95 text-white shrink-0 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <div class="w-9 h-9 rounded-full overflow-hidden shrink-0 ring-2 ring-white/60 bg-white/20 flex items-center justify-center font-bold text-white shadow-xs">
                                <x-user-avatar :user="$selected_help->user" class="h-full w-full" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h1 class="font-extrabold text-sm text-white truncate leading-tight">{{ trim(preg_replace('/\s*\((?:Mitra|Customer)[^)]*\)|\s*(?:Mitra|Customer)\b/i', '', optional($selected_help->user)->name ?? 'Customer')) }}</h1>
                                <p class="text-[11px] text-blue-100 truncate mt-0.5">{{ Str::limit(optional($selected_help)->title ?? optional($selected_help)->description, 35) }}</p>
                            </div>
                        </div>
                        @php
                            $chatReports = \App\Models\PartnerReport::where('reported_help_id', $selected_help->id)
                                ->where(function ($q) {
                                    $q->where('reporter_id', auth()->id())
                                      ->orWhere('reported_user_id', auth()->id());
                                })
                                ->orderBy('created_at', 'asc')
                                ->get();
                            $latestReport = $chatReports->last();
                        @endphp

                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($latestReport && in_array($latestReport->status, ['pending', 'in_progress', 'processing']))
                                <a href="{{ route('mitra.reports.show', $latestReport->id) }}" wire:navigate 
                                    class="px-3 py-1.5 text-xs font-bold text-red-600 rounded-xl bg-white hover:bg-red-50 transition shadow-xs active:scale-95">
                                    Laporkan
                                </a>
                            @elseif(optional($selected_help->user)->id)
                                <a href="{{ route('mitra.reports.create', ['user_id' => optional($selected_help->user)->id, 'help_id' => $selected_help->id, 'new' => 1]) }}" wire:navigate 
                                    class="px-3 py-1.5 text-xs font-bold text-red-600 rounded-xl bg-white hover:bg-red-50 transition shadow-xs active:scale-95">
                                    Laporkan
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Wave Separator (SVG) bergelombang -->
            <svg class="absolute bottom-0 left-0 w-full pointer-events-none" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true" style="display: block; height: 26px;">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <!-- Content container -->
        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-4 {{ $selected_help_id ? 'flex-1 min-h-0 flex flex-col' : 'pb-28 min-h-[60vh]' }}">
            <div class="{{ $selected_help_id ? 'flex-1 min-h-0 flex flex-col' : 'space-y-4' }}">
                @if(!$selected_help_id)
                    <!-- Search -->
                    <div class="relative mb-3">
                        <input type="text" wire:model.live.debounce.350ms="search" placeholder="Cari percakapan atau customer..."
                            class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-50 text-slate-900 placeholder-slate-400 border border-slate-200 focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 outline-none text-xs font-medium transition">
                        <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="space-y-2.5 overflow-y-auto hide-scrollbar">
                        @if($conversations && $conversations->count() > 0)
                            @foreach($conversations as $conversation)
                                @php
                                    $hasUnread = ($conversation->unread_messages_count ?? 0) > 0 || ($conversation->chatMessages->first()?->sender_type === 'customer' && !$conversation->chatMessages->first()?->read_at);
                                    $latestMsg = $conversation->chatMessages->first();
                                @endphp
                                <button wire:click="selectHelp({{ $conversation->id }})"
                                    class="w-full text-left p-3.5 rounded-2xl border border-slate-200/80 hover:border-blue-200 hover:bg-blue-50/20 active:scale-[0.99] transition-all bg-white shadow-2xs {{ $selected_help_id === $conversation->id ? 'ring-2 ring-[#0066FF] border-transparent' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 ring-2 ring-blue-100 bg-blue-50 flex items-center justify-center font-bold text-[#0066FF] shadow-xs">
                                            <x-user-avatar :user="$conversation->user" class="h-full w-full" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-0.5">
                                                <h3 class="font-extrabold text-xs text-slate-900 truncate">
                                                    {{ optional($conversation->user)->name ?? 'Customer' }}
                                                </h3>
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    @if(in_array($conversation->status, ['selesai', 'completed']))
                                                        <span class="text-[9px] bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded-md">Selesai</span>
                                                    @elseif(in_array($conversation->status, ['batal', 'cancelled', 'dibatalkan', 'cancel_accepted']))
                                                        <span class="text-[9px] bg-rose-50 text-rose-700 font-bold px-1.5 py-0.5 rounded-md">Batal</span>
                                                    @elseif(in_array($conversation->status, ['komplain', 'disputed']))
                                                        <span class="text-[9px] bg-red-50 text-red-700 font-bold px-1.5 py-0.5 rounded-md">Komplain</span>
                                                    @elseif(in_array($conversation->status, ['partner_cancel_requested', 'cancel_requested']))
                                                        <span class="text-[9px] bg-amber-50 text-amber-700 font-bold px-1.5 py-0.5 rounded-md">Ajukan Batal</span>
                                                    @endif
                                                    <span class="text-[10px] text-slate-400 font-medium">
                                                        {{ optional($latestMsg?->created_at ?? $conversation->updated_at)->format('H:i') }}
                                                    </span>
                                                    @if($hasUnread)
                                                        <div class="w-2 h-2 rounded-full bg-[#0066FF] flex-shrink-0 animate-pulse"></div>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-xs {{ $hasUnread ? 'text-slate-900 font-bold' : 'text-slate-500 font-medium' }} line-clamp-1">
                                                {{ $latestMsg?->message ?? 'Mulai percakapan...' }}
                                            </p>
                                        </div>
                                    </div>
                                </button>
                            @endforeach

                            @if(method_exists($conversations, 'hasPages') && $conversations->hasPages())
                                <div class="pt-2 pb-4">
                                    {{ $conversations->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-16">
                                <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-sm font-bold text-slate-700">Tidak ada percakapan</p>
                                <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Percakapan akan muncul saat Anda mulai membantu customer</p>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Messages Body with scrolling -->
                    <div id="mitraMessagesWrapper" class="flex-1 min-h-0 overflow-y-auto hide-scrollbar px-1 py-1 space-y-3 pb-3"
                        x-data="{
                            scrollToBottom() {
                                this.$el.scrollTop = this.$el.scrollHeight;
                            }
                        }"
                        x-init="
                            $nextTick(() => scrollToBottom());
                            let observer = new MutationObserver(() => { $nextTick(() => scrollToBottom()) });
                            observer.observe($el, { childList: true, subtree: true });
                        "
                    >
                        @if($messages && $messages->count() > 0)
                            @foreach($messages as $msg)
                                <div class="flex {{ $msg->sender_type === 'mitra' ? 'justify-end' : 'justify-start' }}">
                                    <div class="rounded-2xl px-4 py-2.5 max-w-[80%] shadow-2xs {{ $msg->sender_type === 'mitra' ? 'bg-[#0066FF] text-white rounded-tr-xs' : 'bg-slate-100 text-slate-900 rounded-tl-xs' }}">
                                        <p class="text-xs leading-relaxed break-words font-medium">{{ $msg->message }}</p>
                                        <div class="text-[10px] mt-1 text-right {{ $msg->sender_type === 'mitra' ? 'text-white/70' : 'text-slate-400' }}">{{ $msg->created_at->format('H:i') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-slate-400 text-xs py-16 font-medium">Mulai percakapan dengan customer</div>
                        @endif
                    </div>

                    @if($selected_help && $selected_help->chatClosedReason())
                        <!-- Read-Only Banner when chat is closed -->
                        <div class="flex-shrink-0 bg-slate-100 border-t border-slate-200 px-4 py-2.5 flex items-center justify-center gap-2 text-center rounded-xl my-1">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-6V7a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2z" />
                            </svg>
                            <span class="text-xs text-slate-600 font-medium">
                                {{ $selected_help->chatClosedReason() }}
                            </span>
                        </div>
                    @elseif($selected_help && in_array($selected_help->status, ['partner_cancel_requested', 'cancel_requested']))
                        <!-- Banner when cancel requested -->
                        <div class="flex-shrink-0 bg-amber-50 border border-amber-200 px-4 py-2.5 flex items-center justify-center gap-2 text-center rounded-xl my-1">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-xs text-amber-800 font-medium">
                                Permintaan pembatalan pesanan sedang diajukan (menunggu persetujuan).
                            </span>
                        </div>
                    @else
                        <!-- Chat Input Bar -->
                        <div class="flex-shrink-0 bg-white border-t border-slate-200/80 px-2 py-2.5 relative">
                            <form wire:submit.prevent="sendMessage" class="relative flex items-center gap-2">
                                @if($selected_help && $selected_help->isSelesai())
                                    <div class="absolute -top-7 left-0 right-0 text-center">
                                        <span class="text-[10px] text-slate-500 bg-slate-100 border border-slate-200 rounded-full px-2 py-0.5 font-medium">
                                            Percakapan ditutup otomatis 1 hari setelah pesanan selesai
                                        </span>
                                    </div>
                                @endif
                                <input type="text" wire:model.defer="message" placeholder="Tulis pesan..."
                                    class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs outline-none focus:bg-white focus:border-[#0066FF] focus:ring-2 focus:ring-[#0066FF]/20 font-medium text-slate-900 transition">
                                <button type="submit" class="bg-[#0066FF] hover:bg-[#0052CC] active:scale-95 text-white px-4 py-2.5 rounded-xl font-bold text-xs flex-shrink-0 shadow-md transition">Kirim</button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Sembunyikan scrollbar di seluruh halaman percakapan */
        .hide-scrollbar::-webkit-scrollbar,
        ::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .hide-scrollbar,
        * {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>
</div>
