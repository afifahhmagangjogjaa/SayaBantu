@props([
    'size' => 'md',
    'showTagline' => false,
    'tagline' => 'Jasa Serabutan, Mudah, Cepat, Terpercaya',
    'light' => false,
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
        'xs' => 'w-5 h-5 -mt-2',
        'sm' => 'w-6 h-6 -mt-2.5',
        'md' => 'w-7 h-7 -mt-3',
        'lg' => 'w-9 h-9 -mt-4',
        'xl' => 'w-11 h-11 -mt-5',
        default => 'w-7 h-7 -mt-3',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex flex-col items-center justify-center select-none']) }}>
    <div class="flex items-center justify-center font-black tracking-tight leading-none {{ $textClasses }}">
        <span class="{{ $light ? 'text-white' : 'text-[#0066FF]' }}">saya</span>
        
        <!-- Waving Palm Hand Icon on the letter 'b' -->
        <span class="relative inline-flex items-center justify-center">
            <span class="absolute -top-3 sm:-top-3.5 left-1/2 -translate-x-1/2 {{ $handSize }} text-amber-500 drop-shadow-xs animate-bounce" style="animation-duration: 2s;">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full transform -rotate-12">
                    <path d="M10 2a1.5 1.5 0 0 1 1.5 1.5v5.5a.5.5 0 0 0 1 0v-6a1.5 1.5 0 0 1 3 0v6a.5.5 0 0 0 1 0v-4.5a1.5 1.5 0 0 1 3 0V14c0 4.418-3.582 8-8 8s-8-3.582-8-8V8.5A1.5 1.5 0 0 1 5 7a1.5 1.5 0 0 1 1.5 1.5v3a.5.5 0 0 0 1 0v-8A1.5 1.5 0 0 1 9 2z"/>
                </svg>
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
