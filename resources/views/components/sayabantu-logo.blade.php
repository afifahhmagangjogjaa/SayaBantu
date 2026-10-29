@props([
    'size' => 'md',
    'showTagline' => false,
    'tagline' => 'Jasa Serabutan, Mudah, Cepat, Terpercaya',
    'light' => false,
    'customIconUrl' => null,
])

@php
    $textClasses = match($size) {
        'xs' => 'text-lg',
        'sm' => 'text-xl',
        'md' => 'text-2xl',
        'lg' => 'text-3xl',
        'xl' => 'text-4xl',
        default => 'text-2xl',
    };
    $handSize = match($size) {
        'xs' => 'w-6 h-6',
        'sm' => 'w-7 h-7',
        'md' => 'w-8 h-8',
        'lg' => 'w-10 h-10',
        'xl' => 'w-12 h-12',
        default => 'w-8 h-8',
    };
    $handOffset = match($size) {
        'xs' => '-top-5',
        'sm' => '-top-6',
        'md' => '-top-7',
        'lg' => '-top-8',
        'xl' => '-top-10',
        default => '-top-7',
    };
    $appIcon = \App\Models\AppSetting::get('app_icon');
    $iconUrl = $customIconUrl ?: ($appIcon && \Illuminate\Support\Facades\Storage::disk('public')->exists($appIcon) ? asset('storage/' . $appIcon) : null);
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex flex-col items-center justify-center select-none']) }}>
    <div class="flex items-center justify-center font-black tracking-tight leading-none {{ $textClasses }}">
        <span class="{{ $light ? 'text-white' : 'text-[#0066FF]' }}">saya</span>
        
        <!-- OK Hand Icon on the letter 'b' — matches reference logo -->
        <span class="relative inline-flex items-center justify-center">
            <span class="absolute {{ $handOffset }} left-1/2 -translate-x-1/2 {{ $handSize }} drop-shadow-sm pointer-events-none select-none">
                @if($iconUrl)
                    <img src="{{ $iconUrl }}" alt="Logo Icon" class="w-full h-full object-contain pointer-events-none select-none">
                @else
                    <svg viewBox="0 0 80 92" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full pointer-events-none select-none">
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
            </span>
            <span class="{{ $light ? 'text-white' : 'text-[#0066FF]' }}">b</span>
        </span>
        
        <span class="{{ $light ? 'text-white' : 'text-[#0066FF]' }}">antu</span>
        <span class="text-amber-500 font-extrabold">.com</span>
    </div>

    @if($showTagline)
        <p class="mt-1.5 text-xs font-medium {{ $light ? 'text-blue-100' : 'text-gray-500' }} text-center tracking-normal">
            {{ $tagline }}
        </p>
    @endif
</div>
