@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="is-disabled">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
        @endif

        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $window = 2;
        @endphp

        @for ($page = 1; $page <= $last; $page++)
            @if ($page === 1 || $page === $last || ($page >= $current - $window && $page <= $current + $window))
                @if ($page === $current)
                    <span class="is-current">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
                @endif
            @elseif ($page === $current - $window - 1 || $page === $current + $window + 1)
                <span class="is-disabled">&hellip;</span>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
        @else
            <span class="is-disabled">&raquo;</span>
        @endif
    </nav>
@endif
