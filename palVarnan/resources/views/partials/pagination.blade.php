@if ($paginator->hasPages())
    <nav class="d-flex flex-column align-items-center gap-12" aria-label="Product catalog pagination">
        <ul class="custom-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Previous Page">
                    <span class="page-link" aria-hidden="true">
                        <i class="ph-bold ph-caret-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous Page">
                        <i class="ph-bold ph-caret-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ sprintf('%02d', $page) }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">
                                    {{ sprintf('%02d', $page) }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next Page">
                        <i class="ph-bold ph-caret-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Next Page">
                    <span class="page-link" aria-hidden="true">
                        <i class="ph-bold ph-caret-right"></i>
                    </span>
                </li>
            @endif
        </ul>

        <div class="text-neutral-500 text-xs fw-medium text-center">
            Page <span class="text-heading fw-semibold">{{ $paginator->currentPage() }}</span> of <span class="text-heading fw-semibold">{{ $paginator->lastPage() }}</span>
        </div>
    </nav>
@endif
