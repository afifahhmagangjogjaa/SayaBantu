@props([
    'title' => 'Belum Ada Data',
    'description' => null,
    'icon' => 'inbox',
    'actionText' => null,
    'actionUrl' => null,
    'actionWireClick' => null,
    'actionType' => 'primary',
    'compact' => false,
])

@php
    $paddingClass = $compact ? 'py-6 px-4' : 'py-10 px-6 sm:py-12 sm:px-8';
    $iconSize = $compact ? 'w-12 h-12' : 'w-16 h-16';
    $svgSize = $compact ? 'w-6 h-6' : 'w-8 h-8';
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col items-center justify-center text-center $paddingClass rounded-2xl bg-gradient-to-b from-blue-50/40 via-white to-gray-50/40 border border-dashed border-gray-200 shadow-xs transition-all duration-300"]) }}>
    <!-- Icon Container -->
    <div class="{{ $iconSize }} rounded-2xl bg-gradient-to-br from-blue-100 to-primary-100 text-primary-600 flex items-center justify-center shadow-xs ring-4 ring-white mb-3.5 transform transition-transform duration-300 hover:scale-105">
        @if ($icon === 'inbox')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @elseif ($icon === 'clipboard' || $icon === 'order' || $icon === 'help')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        @elseif ($icon === 'wallet' || $icon === 'money')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        @elseif ($icon === 'bell' || $icon === 'notification')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        @elseif ($icon === 'search')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        @elseif ($icon === 'users' || $icon === 'user')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        @elseif ($icon === 'star' || $icon === 'rating')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
        @elseif ($icon === 'check-circle' || $icon === 'verified')
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @else
            <svg class="{{ $svgSize }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>

    <!-- Title -->
    <h3 class="{{ $compact ? 'text-sm' : 'text-base sm:text-lg' }} font-bold text-gray-800 mb-1">
        {{ $title }}
    </h3>

    <!-- Description -->
    @if ($description)
        <p class="{{ $compact ? 'text-xs' : 'text-xs sm:text-sm' }} text-gray-500 max-w-sm mx-auto leading-relaxed mb-4">
            {{ $description }}
        </p>
    @endif

    <!-- Optional Slot Content -->
    {{ $slot }}

    <!-- Call to Action Button -->
    @if ($actionText)
        <div class="mt-2">
            @if ($actionWireClick)
                <button type="button" wire:click="{{ $actionWireClick }}"
                    class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition active:scale-95 shadow-sm hover:shadow
                        {{ $actionType === 'primary' ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-200' }}">
                    <span>{{ $actionText }}</span>
                </button>
            @elseif ($actionUrl)
                <a href="{{ $actionUrl }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition active:scale-95 shadow-sm hover:shadow
                        {{ $actionType === 'primary' ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-200' }}">
                    <span>{{ $actionText }}</span>
                </a>
            @endif
        </div>
    @endif
</div>
