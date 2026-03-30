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
        <nav role="navigation" aria-label="{{ __('pagination.navigation') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between w-full min-w-0">
            {{-- Mobile --}}
            <div class="flex flex-1 justify-between gap-3 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/90 bg-slate-50 px-4 text-sm font-medium text-slate-400 cursor-default">
                            {{ __('pagination.previous') }}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/95 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:shadow active:scale-[0.98]">
                            {{ __('pagination.previous') }}
                        </button>
                    @endif
                </span>
                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/95 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:shadow active:scale-[0.98]">
                            {{ __('pagination.next') }}
                        </button>
                    @else
                        <span class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200/90 bg-slate-50 px-4 text-sm font-medium text-slate-400 cursor-default">
                            {{ __('pagination.next') }}
                        </span>
                    @endif
                </span>
            </div>

            {{-- Desktop --}}
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between gap-4 w-full min-w-0">
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

                <div class="pagination-manexo__cluster shrink-0">
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="pagination-manexo__btn pagination-manexo__btn--disabled">
                            <iconify-icon icon="solar:alt-arrow-left-linear" width="20" class="opacity-60"></iconify-icon>
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="pagination-manexo__btn pagination-manexo__btn--link" aria-label="{{ __('pagination.previous') }}">
                            <iconify-icon icon="solar:alt-arrow-left-linear" width="20"></iconify-icon>
                        </button>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="pagination-manexo__ellipsis" aria-hidden="true">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page" class="pagination-manexo__btn pagination-manexo__btn--current">{{ $page }}</span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="pagination-manexo__btn pagination-manexo__btn--link" aria-label="{{ __('pagination.goto_page', ['page' => $page]) }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="pagination-manexo__btn pagination-manexo__btn--link" aria-label="{{ __('pagination.next') }}">
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="20"></iconify-icon>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="pagination-manexo__btn pagination-manexo__btn--disabled">
                            <iconify-icon icon="solar:alt-arrow-right-linear" width="20" class="opacity-60"></iconify-icon>
                        </span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>
