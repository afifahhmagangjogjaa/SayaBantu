@php
    $avatar = $help->user->profile_photo ?? $help->user->photo ?? null;
    $name = $help->user->name ?? 'Pengguna';
    $cardImage = $help->photo ?? $avatar;
    $catId = optional($help->category)->id ?? 0;
    $colors = ['bg-pink-100 text-pink-600','bg-green-100 text-green-600','bg-yellow-100 text-yellow-600','bg-blue-100 text-blue-600'];
    $mitraFeePercent = (float)($help->mitra_fee_percent ?? 10);
    $baseWage = (float)($help->base_amount > 0 ? $help->base_amount : ($help->amount ?? 0));
    $price = $help->net_mitra_amount > 0 ? $help->net_mitra_amount : max(0, $baseWage - round($baseWage * $mitraFeePercent / 100));
@endphp

<div class="block w-full bg-white rounded-xl border border-gray-100 shadow-sm p-3">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ explode(' ', $color)[0] }}">
            @if($cardImage)
                <img src="{{ asset('storage/' . $cardImage) }}" alt="image" class="w-10 h-10 object-cover rounded-lg">
            @else
                <div class="w-8 h-8 rounded-md bg-white/30 flex items-center justify-center text-sm font-bold text-gray-700">{{ strtoupper(substr($name,0,1)) }}</div>
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 min-w-0">
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $help->title ?? Str::limit($help->description, 60) }}</div>
                    @if($help->isUrgent())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-700 uppercase tracking-wider flex-shrink-0">⚡ Urgent</span>
                    @endif
                </div>
                <div class="text-sm font-bold text-gray-900 ml-3 whitespace-nowrap">Rp {{ number_format($price, 0, ',', '.') }}</div>
            </div>
            <div class="flex items-center justify-between mt-1">
                <div class="text-xs text-gray-500 truncate">{{ $help->city->name ?? ($help->location ?? '-') }}</div>
                <div class="text-xs text-gray-400">{{ $help->created_at->diffForHumans() }}</div>
            </div>
            @php
                $displayDate = $help->scheduled_at ?? $help->created_at;
                $isCancelledByMe = auth()->check() && $help->wasCancelledByMitra(auth()->id());
            @endphp
            @if($displayDate)
                <div class="mt-2 text-xs {{ $help->isUrgent() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                    {{ $help->isUrgent() ? '⚡ ' : '📅 ' }}{{ \Carbon\Carbon::parse($displayDate)->translatedFormat('d M Y, H:i') }}
                </div>
            @endif
            @if($isCancelledByMe)
                <div class="mt-2 p-1.5 bg-amber-50 border border-amber-200/80 rounded-lg text-amber-800 text-[10px] flex items-center gap-1 font-medium leading-tight">
                    <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Anda tidak bisa mengambil bantuan ini karena sudah pernah dibatalkan</span>
                </div>
            @endif
        </div>
    </div>
</div>