@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between w-full">
            {{-- Mobile Pagination --}}
            <div class="flex justify-between items-center w-full sm:hidden">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-default shadow-2xs">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition shadow-2xs">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif

                <span class="text-xs text-gray-600 font-medium">
                    Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition shadow-2xs">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-default shadow-2xs">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </div>

            {{-- Desktop Pagination --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-600 leading-5">
                        <span>Menampilkan</span>
                        <span class="font-semibold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
                        <span>sampai</span>
                        <span class="font-semibold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
                        <span>dari</span>
                        <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
                        <span>data</span>
                    </p>
                </div>

                <div>
                    <div class="inline-flex items-center rounded-lg border border-gray-200 bg-white shadow-2xs divide-x divide-gray-200 overflow-hidden">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <span class="relative inline-flex items-center justify-center w-9 h-9 text-gray-400 bg-white cursor-default select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        @else
                            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center justify-center w-9 h-9 text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-900 transition" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-sm font-medium text-gray-400 bg-white cursor-default select-none leading-none">
                                    {{ $element }}
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-sm font-bold text-blue-600 bg-white cursor-default select-none leading-none">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 transition leading-none" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center justify-center w-9 h-9 text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-900 transition" aria-label="{{ __('pagination.next') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <span class="relative inline-flex items-center justify-center w-9 h-9 text-gray-400 bg-white cursor-default select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    @endif
</div>
