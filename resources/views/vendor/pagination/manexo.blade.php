@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('pagination.navigation') }}" class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between w-full min-w-0">
        {{-- Résumé : "Affichage de X à Y sur Z résultats" --}}
        <p class="text-sm text-slate-500 order-2 sm:order-1">
            @if ($paginator->firstItem())
                {{ __('pagination.showing') }}
                <span class="font-semibold text-slate-700">{{ number_format($paginator->firstItem()) }}</span>
                {{ __('pagination.to') }}
                <span class="font-semibold text-slate-700">{{ number_format($paginator->lastItem()) }}</span>
                {{ __('pagination.of') }}
            @else
                {{ $paginator->count() }} {{ __('pagination.of') }}
            @endif
            <span class="font-semibold text-slate-700">{{ number_format($paginator->total()) }}</span>
            {{ __('pagination.results') }}
        </p>

        {{-- Stepper : Précédent | numéros | Suivant --}}
        <div class="inline-flex items-center gap-1 p-1.5 rounded-xl bg-white border border-slate-200 shadow-sm order-1 sm:order-2 shrink-0">
            {{-- Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-300 cursor-not-allowed" aria-hidden="true">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="18"></iconify-icon>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors"
                   aria-label="{{ __('pagination.previous') }}">
                    <iconify-icon icon="solar:alt-arrow-left-linear" width="18"></iconify-icon>
                </a>
            @endif

            {{-- Numéros de page --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 text-sm font-medium text-slate-400">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center min-w-[2.25rem] h-9 rounded-lg text-sm font-semibold bg-[var(--accent-soft)] text-[var(--accent)]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center min-w-[2.25rem] h-9 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors"
                               aria-label="{{ __('pagination.goto_page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors"
                   aria-label="{{ __('pagination.next') }}">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="18"></iconify-icon>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-300 cursor-not-allowed" aria-hidden="true">
                    <iconify-icon icon="solar:alt-arrow-right-linear" width="18"></iconify-icon>
                </span>
            @endif
        </div>
    </nav>
@endif
