
@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $pages = [];

        if ($last <= 5) {
            $pages = range(1, $last);
        } else {
            if ($current <= 2) {
                $pages = [1, 2, 3, $last];
            } elseif ($current === 3) {
                $pages = [1, 2, 3, 4, $last];
            } elseif ($current >= $last - 1) {
                $pages = [1, $last - 2, $last - 1, $last];
            } else {
                $pages = [1, $current - 1, $current, $current + 1, $last];
            }

            $pages = array_values(array_unique(array_filter($pages, function ($page) use ($last) {
                return $page >= 1 && $page <= $last;
            })));

            sort($pages);
        }
    @endphp

    <nav aria-label="Pagination">
        <ul class="pagination custom-pagination justify-content-start justify-content-sm-end flex-wrap gap-1 mw-100 mb-0">
            
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            
            @foreach ($pages as $index => $page)
                @if ($index > 0 && $page - $pages[$index - 1] > 1)
                    <li class="page-item disabled">
                        <span class="page-link pagination-ellipsis rounded-3 d-inline-flex align-items-center justify-content-center bg-transparent border-0 shadow-none">...</span>
                    </li>
                @endif

                @if ($page === $current)
                    <li class="page-item active" aria-current="page">
                        <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
