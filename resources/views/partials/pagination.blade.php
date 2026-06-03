@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between flex-col sm:flex-row gap-4">

    <div class="text-sm text-slate-500 font-medium">
        Tampilkan
        <span class="font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</span>
        sampai
        <span class="font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</span>
        dari <span class="font-bold text-slate-800">{{ $paginator->total() }}</span> data
    </div>

    <div class="inline-flex items-center -space-x-px rounded-xl bg-white shadow-sm border border-slate-200 p-1">

        @if ($paginator->onFirstPage())
        <span class="px-2.5 py-1.5 text-slate-300 cursor-not-allowed text-sm"><i class="bi bi-chevron-left"></i></span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-2.5 py-1.5 text-slate-600 hover:bg-slate-50 hover:text-secondary rounded-lg transition-colors text-sm"><i class="bi bi-chevron-left"></i></a>
        @endif

        @foreach ($elements as $element)
        @if (is_string($element))
        <span class="px-3 py-1.5 text-slate-400 text-sm font-medium">{{ $element }}</span>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <span class="px-3 py-1.5 bg-secondary text-white text-sm font-bold rounded-lg shadow-sm shadow-secondary/20 z-10">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="px-3 py-1.5 text-slate-600 hover:bg-slate-50 hover:text-secondary rounded-lg transition-colors text-sm font-semibold">{{ $page }}</a>
        @endif
        @endforeach
        @endif
        @endforeach

        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-2.5 py-1.5 text-slate-600 hover:bg-slate-50 hover:text-secondary rounded-lg transition-colors text-sm"><i class="bi bi-chevron-right"></i></a>
        @else
        <span class="px-2.5 py-1.5 text-slate-300 cursor-not-allowed text-sm"><i class="bi bi-chevron-right"></i></span>
        @endif

    </div>
</nav>
@endif