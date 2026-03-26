@if ($paginator->hasPages())
    <nav class="px-8 py-4 flex items-center justify-between border-t border-slate-200" role="navigation"
        aria-label="Pagination Navigation">
        <p class="text-sm text-slate-500">
            Showing
            <span class="font-medium text-slate-700">{{ $paginator->firstItem() ?? 0 }}</span>
            to
            <span class="font-medium text-slate-700">{{ $paginator->lastItem() ?? 0 }}</span>
            of
            <span class="font-medium text-slate-700">{{ $paginator->total() }}</span>
            customers
        </p>

        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="btn-ghost opacity-50 cursor-not-allowed">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-ghost">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-slate-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-300 bg-slate-100 px-3 text-sm font-semibold text-slate-800">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 hover:bg-slate-50">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-ghost">Next</a>
            @else
                <span class="btn-ghost opacity-50 cursor-not-allowed">Next</span>
            @endif
        </div>
    </nav>
@endif
