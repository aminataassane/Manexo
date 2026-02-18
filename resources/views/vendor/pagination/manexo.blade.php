@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-center sm:justify-between" style="gap: 0.75rem;">
        {{-- Info "X à Y sur Z" --}}
        <p class="text-[13px] order-2 sm:order-1" style="color: #6B7280;">
            @if ($paginator->firstItem())
                <span style="color: #111827; font-weight: 500;">{{ $paginator->firstItem() }}</span>
                {{ __('to') }}
                <span style="color: #111827; font-weight: 500;">{{ $paginator->lastItem() }}</span>
                {{ __('of') }}
            @else
                {{ $paginator->count() }} {{ __('of') }}
            @endif
            <span style="color: #111827; font-weight: 500;">{{ $paginator->total() }}</span>
        </p>

        {{-- Contrôles : Précédent + numéros + Suivant --}}
        <div class="inline-flex items-center rounded-xl border bg-white p-1 order-1 sm:order-2" style="gap: 4px; border-color: #E5E7EB; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            {{-- Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center rounded-lg cursor-not-allowed" aria-hidden="true" style="width: 36px; height: 36px; color: #9CA3AF;">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="18"></iconify-icon>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center rounded-lg transition-colors hover:bg-[#F3F4F6]" style="width: 36px; height: 36px; color: #6B7280; text-decoration: none;" aria-label="{{ __('pagination.previous') }}">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="18"></iconify-icon>
                </a>
            @endif

            {{-- Numéros de page --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center px-2" style="min-width: 2.25rem; height: 36px; font-size: 13px; font-weight: 500; color: #9CA3AF;">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center rounded-lg font-semibold" style="min-width: 2.25rem; height: 36px; font-size: 13px; background: var(--accent-soft, #dcfce7); color: var(--accent, #005F02);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center rounded-lg transition-colors hover:bg-[#F3F4F6]" style="min-width: 2.25rem; height: 36px; font-size: 13px; font-weight: 500; color: #374151; text-decoration: none;" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center rounded-lg transition-colors hover:bg-[#F3F4F6]" style="width: 36px; height: 36px; color: #6B7280; text-decoration: none;" aria-label="{{ __('pagination.next') }}">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="18"></iconify-icon>
                </a>
            @else
                <span class="inline-flex items-center justify-center rounded-lg cursor-not-allowed" aria-hidden="true" style="width: 36px; height: 36px; color: #9CA3AF;">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="18"></iconify-icon>
                </span>
            @endif
        </div>
    </nav>
@endif
