@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('pagination.navigation') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between w-full min-w-0">
        <p class="pagination-manexo__summary">
            @if ($paginator->firstItem())
                {{ __('pagination.showing') }}
                <span class="font-semibold">{{ number_format($paginator->firstItem()) }}</span>
                {{ __('pagination.to') }}
                <span class="font-semibold">{{ number_format($paginator->lastItem()) }}</span>
                {{ __('pagination.of') }}
            @else
                {{ $paginator->count() }} {{ __('pagination.of') }}
            @endif
            <span class="font-semibold">{{ number_format($paginator->total()) }}</span>
            {{ __('pagination.results') }}
        </p>

        <div class="pagination-manexo__cluster order-1 sm:order-2 shrink-0">
            @if ($paginator->onFirstPage())
                <span class="pagination-manexo__btn pagination-manexo__btn--disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="20" class="opacity-60" aria-hidden="true"></iconify-icon>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="pagination-manexo__btn pagination-manexo__btn--link"
                   aria-label="{{ __('pagination.previous') }}">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="20"></iconify-icon>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-manexo__ellipsis" aria-hidden="true">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="pagination-manexo__btn pagination-manexo__btn--current">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="pagination-manexo__btn pagination-manexo__btn--link"
                               aria-label="{{ __('pagination.goto_page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="pagination-manexo__btn pagination-manexo__btn--link"
                   aria-label="{{ __('pagination.next') }}">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="20"></iconify-icon>
                </a>
            @else
                <span class="pagination-manexo__btn pagination-manexo__btn--disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="20" class="opacity-60" aria-hidden="true"></iconify-icon>
                </span>
            @endif
        </div>
    </nav>
@endif
