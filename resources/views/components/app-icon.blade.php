@props([
    'class' => 'w-8 h-8',
    'customUrl' => null,
])

@php
    $appIcon = \App\Models\AppSetting::get('app_icon');
    $iconUrl = $customUrl ?: ($appIcon && \Illuminate\Support\Facades\Storage::disk('public')->exists($appIcon) ? asset('storage/' . $appIcon) : null);
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center ' . $class]) }}>
    @if($iconUrl)
        <img src="{{ $iconUrl }}" alt="App Icon" class="w-full h-full object-contain select-none pointer-events-none" />
    @else
        <svg viewBox="0 0 80 92" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full select-none pointer-events-none">
            <!-- Blue crescent/cuff at bottom -->
            <path d="M14 68 Q6 84 28 90 Q52 96 64 78 Q54 88 38 86 Q22 82 14 68Z" fill="#1565C0"/>

            <!-- OK hand (golden yellow, fill-rule evenodd creates white hole) -->
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M38 72
                   C26 72 16 62 16 50
                   C16 43 19 36 25 31
                   L18 12
                   C17 8 20 5 24 6
                   C28 7 30 10 31 14
                   L34 27
                   L36 10
                   C37 6 41 4 45 5
                   C49 6 51 10 50 14
                   L48 27
                   L51 13
                   C52 9 56 7 60 9
                   C64 11 65 15 63 19
                   L58 33
                   C64 29 70 31 72 37
                   C74 43 70 50 65 53
                   L54 63
                   C49 69 44 72 38 72
                   Z
                   M40 48
                   C35 48 31 52 31 57
                   C31 62 35 66 40 66
                   C45 66 49 62 49 57
                   C49 52 45 48 40 48
                   Z"
                fill="#FFC107"/>
        </svg>
    @endif
</div>

