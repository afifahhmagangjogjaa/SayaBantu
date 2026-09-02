<div wire:poll.3s class="{{ $selected_help_id ? 'h-screen overflow-hidden' : 'min-h-screen' }} bg-white">
    <div class="max-w-md mx-auto {{ $selected_help_id ? 'h-screen flex flex-col overflow-hidden' : '' }}">
        <!-- Header -->
        <div class="px-5 pt-5 pb-8 relative overflow-hidden flex-shrink-0" style="background: linear-gradient(to bottom right, #0098e7, #0077cc, #0060b0);">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

            <div class="relative z-10">
                @if(!$selected_help_id)
                    <div class="flex items-center justify-between text-white mb-3">
                        <a href="{{ route('customer.dashboard') }}" aria-label="Kembali ke Dashboard" class="p-2 hover:bg-white/20 rounded-lg transition text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <div class="text-center flex-1">
                            <h1 class="text-lg font-bold">Chat</h1>
                            <p class="text-xs text-white/90 mt-0.5">Percakapan antara Anda dan mitra</p>
                        </div>
                        <div class="w-8"></div>
                    </div>
                @else
                    <div class="flex items-center justify-between gap-2 text-white mb-2">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <a href="{{ route('customer.chat') }}" aria-label="Kembali ke Daftar Chat" class="p-1.5 hover:bg-white/20 rounded-lg transition text-white flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-white/60 bg-white/20 flex items-center justify-center font-bold text-white shadow-xs">
                                @if(optional($selected_help->mitra)->profile_photo)
                                    <img src="{{ asset('storage/' . optional($selected_help->mitra)->profile_photo) }}" alt="Mitra" class="w-full h-full object-cover">
                                @elseif(optional($selected_help->mitra)->photo)
                                    <img src="{{ asset('storage/' . optional($selected_help->mitra)->photo) }}" alt="Mitra" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(optional($selected_help->mitra)->name ?? 'M', 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h1 class="font-bold text-sm text-white truncate leading-tight">{{ optional($selected_help->mitra)->name ?? 'Mitra' }}</h1>
                                <p class="text-[11px] text-white/85 truncate mt-0.5">{{ Str::limit(optional($selected_help)->title ?? optional($selected_help)->description, 35) }}</p>
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

                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($latestReport && in_array($latestReport->status, ['pending', 'in_progress', 'processing']))
                                <a href="{{ route('customer.reports.show', $latestReport->id) }}" 
                                    class="px-2.5 py-1 text-xs font-semibold text-red-600 rounded-lg bg-white hover:bg-red-50 transition shadow-xs">
                                    Laporkan
                                </a>
                            @elseif(optional($selected_help->mitra)->id)
                                <a href="{{ route('customer.reports.create', ['user_id' => optional($selected_help->mitra)->id, 'help_id' => $selected_help->id, 'new' => 1]) }}" 
                                    class="px-2.5 py-1 text-xs font-semibold text-red-600 rounded-lg bg-white hover:bg-red-50 transition shadow-xs">
                                    Laporkan
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <svg class="absolute bottom-0 left-0 w-full" viewBox="0 0 1440 72" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,32 C360,72 1080,0 1440,40 L1440,72 L0,72 Z" fill="#ffffff"></path>
            </svg>
        </div>

        <div class="bg-white rounded-t-3xl -mt-6 px-5 pt-6 {{ $selected_help_id ? 'pb-24 flex-1 overflow-hidden flex flex-col' : 'pb-28 min-h-[60vh]' }}">
            <div class="{{ $selected_help_id ? 'flex-1 overflow-hidden flex flex-col' : 'space-y-4' }}">
                @if(!$selected_help_id)
                    <div class="relative">
                        <input type="text" wire:model.debounce.400ms="search" placeholder="Cari percakapan atau mitra..."
                            class="w-full px-4 py-2.5 rounded-xl bg-gray-50 text-gray-900 placeholder-gray-400 border border-gray-200 focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="space-y-3 overflow-y-auto hide-scrollbar">
                        @if($conversations && $conversations->count() > 0)
                            @foreach($conversations as $conversation)
                                <button wire:click="selectHelp({{ $conversation->id }})"
                                    class="w-full px-3 py-3 rounded-xl hover:shadow-md transition text-left {{ $selected_help_id === $conversation->id ? 'bg-primary-50 border border-primary-200' : 'bg-white border border-gray-100' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 flex items-center justify-center text-lg">
                                            @if(optional($conversation->mitra)->profile_photo)
                                                <img src="{{ asset('storage/' . optional($conversation->mitra)->profile_photo) }}" alt="Mitra" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($conversation->mitra->name ?? 'M', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-900 text-sm truncate">{{ optional($conversation->mitra)->name ?? 'Mitra' }}</span>
                                                <span class="text-xs text-gray-400">{{ optional($conversation->chatMessages->first())->created_at ? optional($conversation->chatMessages->first())->created_at->format('H:i') : '' }}</span>
                                            </div>
                                            <p class="text-xs text-gray-600 truncate mt-0.5">{{ Str::limit($conversation->title ?? $conversation->description, 40) }}</p>
                                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ optional($conversation->chatMessages->first())->message ?? 'Mulai percakapan...' }}</p>
                                        </div>

                                        @if(optional($conversation->chatMessages->first())->sender_type === 'mitra' && !optional($conversation->chatMessages->first())->read_at)
                                            <div class="ml-2 w-2 h-2 rounded-full bg-blue-500 mt-2 flex-shrink-0"></div>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        @else
                            <div class="text-center py-16">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="text-sm font-semibold text-gray-700">Tidak ada percakapan</p>
                                <p class="text-xs text-gray-500 mt-1">Percakapan akan muncul saat Anda membuat pesanan atau mitra menghubungi Anda</p>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Messages Body with scrolling -->
                    <div id="mitraMessagesWrapper" class="flex-1 overflow-y-auto hide-scrollbar px-1 py-1 space-y-3 pb-20"
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
                                <div class="flex {{ $msg->sender_type === 'customer' ? 'justify-end' : 'justify-start' }}">
                                    <div class="rounded-2xl px-4 py-2.5 max-w-[80%] shadow-xs {{ $msg->sender_type === 'customer' ? 'bg-blue-600 text-white rounded-tr-xs' : 'bg-gray-100 text-gray-900 rounded-tl-xs' }}">
                                        <p class="text-sm leading-snug break-words">{{ $msg->message }}</p>
                                        <div class="text-[11px] mt-1 text-right {{ $msg->sender_type === 'customer' ? 'text-white/70' : 'text-gray-400' }}">{{ $msg->created_at->format('H:i') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-gray-500 py-16">Mulai percakapan dengan mitra</div>
                        @endif
                    </div>

                    @if($selected_help && in_array($selected_help->status, ['selesai', 'completed', 'batal', 'cancelled', 'dibatalkan', 'cancel_accepted']))
                        <!-- Read-Only Banner when order is completed or cancelled -->
                        <div class="fixed left-1/2 transform -translate-x-1/2 bg-gray-100 border-t border-gray-200 z-40 shadow-sm px-4 py-2.5 flex items-center justify-center gap-2 text-center"
                            style="bottom: 64px; max-width: 448px; width: 100vw;">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-6V7a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2z" />
                            </svg>
                            <span class="text-xs text-gray-600 font-medium">
                                Pesanan telah {{ in_array($selected_help->status, ['selesai', 'completed']) ? 'selesai' : 'dibatalkan' }}. Percakapan ini telah diarsipkan (hanya-baca).
                            </span>
                        </div>
                    @elseif($selected_help && in_array($selected_help->status, ['partner_cancel_requested', 'cancel_requested']))
                        <!-- Banner when cancel requested -->
                        <div class="fixed left-1/2 transform -translate-x-1/2 bg-amber-50 border-t border-amber-200 z-40 shadow-sm px-4 py-2.5 flex items-center justify-center gap-2 text-center"
                            style="bottom: 64px; max-width: 448px; width: 100vw;">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-xs text-amber-700 font-medium">
                                Permintaan pembatalan pesanan sedang diajukan (menunggu persetujuan).
                            </span>
                        </div>
                    @else
                        <!-- Fixed Chat Input Bar: Docked flush right on top of bottom navigation -->
                        <div class="fixed left-1/2 transform -translate-x-1/2 bg-white border-t border-gray-200 z-40 shadow-md px-3.5 py-2"
                            style="bottom: 64px; max-width: 448px; width: 100vw;">
                            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                                <input type="text" wire:model.defer="message" placeholder="Tulis pesan..."
                                    class="flex-1 px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-300 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-blue-400 font-medium text-gray-900">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white px-4 py-2 rounded-xl font-semibold text-sm flex-shrink-0 shadow-xs">Kirim</button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Sembunyikan scrollbar di seluruh halaman percakapan (daftar nama & isi pesan) */
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
        @if($selected_help_id)
            /* Kunci dan hilangkan scroll panjang luar browser saat berada di dalam room chat */
            html, body {
                overflow: hidden !important;
                height: 100vh !important;
                max-height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            main {
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
                height: 100vh !important;
                max-height: 100vh !important;
                overflow: hidden !important;
            }
            body > div, body > div > div {
                height: 100vh !important;
                max-height: 100vh !important;
                min-height: 0 !important;
                overflow: hidden !important;
            }
        @endif
    </style>
</div>