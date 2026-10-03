@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold flex items-center gap-3 shadow-2xs']) }}>
        <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div class="leading-relaxed flex-1">
            {{ $status }}
        </div>
    </div>
@endif
