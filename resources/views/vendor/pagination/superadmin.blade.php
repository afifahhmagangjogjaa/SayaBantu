@if ($paginator->hasPages())
<div class="flex items-center justify-between">
    <div class="text-sm text-gray-500">
        Showing
        <span class="font-medium">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-medium">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-medium">{{ $paginator->total() }}</span>
        results
    </div>

    <nav role="navigation" aria-label="Pagination" class="inline-flex items-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="Previous">
                <span class="relative inline-flex items-center px-3 py-1 rounded-md bg-white border border-gray-200 text-gray-400 cursor-default">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-1 rounded-md bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" aria-label="Previous">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <span class="mx-2 inline-flex items-center space-x-1">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="relative inline-flex items-center px-3 py-1 rounded-md bg-primary-600 text-white font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="relative inline-flex items-center px-3 py-1 rounded-md bg-white border border-gray-200 text-gray-600 hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-1 rounded-md bg-white border border-gray-200 text-gray-600 hover:bg-gray-50" aria-label="Next">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span aria-disabled="true" aria-label="Next">
                <span class="relative inline-flex items-center px-3 py-1 rounded-md bg-white border border-gray-200 text-gray-400 cursor-default">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </span>
        @endif
    </nav>
</div>
@endif
