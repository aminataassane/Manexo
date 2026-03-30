<div class="w-full max-w-full min-w-0 mx-auto">

    {{-- Header --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">{{ __('Base de connaissances') }}</h1>
            <p class="page-subtitle">{{ __('Trouvez des réponses à vos questions avant de créer un ticket.') }}</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-8 sm:mb-10 max-w-xl">
        <div class="relative">
            <iconify-icon icon="solar:magnifer-linear" width="18" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></iconify-icon>
            <input
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="{{ __('Rechercher un article...') }}"
                autocomplete="off"
                class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/15 transition-all"
            />
            @if($search)
                <button type="button" wire:click="$set('search', '')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" aria-label="{{ __('Effacer') }}">
                    <iconify-icon icon="solar:close-circle-bold" width="18"></iconify-icon>
                </button>
            @endif
        </div>
    </div>

    @if($viewingArticle)
        {{-- ═══ ARTICLE DETAIL ═══ --}}
        <div class="mb-6">
            <button wire:click="closeArticle" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
                {{ __('Retour aux articles') }}
            </button>
        </div>

        <article class="max-w-3xl">
            <div class="flex items-center gap-3 text-xs text-slate-500 mb-5">
                @if($viewingArticle->category)
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-700">
                        @if($viewingArticle->category->icon)
                            <iconify-icon icon="{{ $viewingArticle->category->icon }}" width="13" class="text-[var(--accent)]"></iconify-icon>
                        @endif
                        {{ $viewingArticle->category->name }}
                    </span>
                    <span class="text-slate-300">&middot;</span>
                @endif
                @if($viewingArticle->published_at)
                    <span>{{ $viewingArticle->published_at->translatedFormat('d M Y') }}</span>
                    <span class="text-slate-300">&middot;</span>
                @endif
                <span>{{ $viewingArticle->view_count }} {{ __('vues') }}</span>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $viewingArticle->title }}</h1>

            @if($viewingArticle->creator)
                <p class="mt-3 text-sm text-slate-500">{{ __('Par') }} <span class="font-medium text-slate-700">{{ $viewingArticle->creator->name }}</span></p>
            @endif

            <div class="mt-8 prose prose-slate prose-sm max-w-none
                [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-slate-900 [&_h2]:mt-8 [&_h2]:mb-3
                [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-slate-800 [&_h3]:mt-6 [&_h3]:mb-2
                [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:my-3
                [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:my-3
                [&_li]:my-1
                [&_blockquote]:border-l-2 [&_blockquote]:border-slate-200 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-500 [&_blockquote]:my-4
                [&_hr]:border-slate-100 [&_hr]:my-8
                [&_p]:my-3 [&_p]:leading-relaxed
                [&_a]:text-[var(--accent)] [&_a]:underline [&_a]:underline-offset-2
                [&_strong]:font-semibold
                [&_code]:text-sm [&_code]:bg-slate-50 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded"
            >{!! $viewingArticle->safeHtml() !!}</div>

            @if(!empty($viewingArticle->keywords))
                <div class="mt-10 pt-6 border-t border-slate-100 flex items-center gap-2 flex-wrap">
                    @foreach($viewingArticle->keywords as $kw)
                        <span class="text-xs text-slate-500 bg-slate-50 border border-slate-100 rounded-lg px-2.5 py-1">{{ $kw }}</span>
                    @endforeach
                </div>
            @endif
        </article>

    @else
        {{-- ═══ BROWSE ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            {{-- Sidebar catégories --}}
            <div class="lg:col-span-3">
                <div class="lg:sticky lg:top-24 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Catégories') }}</h3>
                    </div>
                    <div class="p-2">
                        <button
                            wire:click="filterByCategory(null)"
                            class="w-full flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 text-sm transition-all {{ !$categoryId ? 'bg-[var(--accent-soft)] text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"
                        >
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17" class="{{ !$categoryId ? 'text-[var(--accent)]' : 'text-slate-400' }}"></iconify-icon>
                                {{ __('Toutes') }}
                            </span>
                            <span class="text-xs font-medium tabular-nums {{ !$categoryId ? 'text-[var(--accent)]' : 'text-slate-400' }}">{{ $articles->count() }}</span>
                        </button>

                        @foreach($categories as $cat)
                            <button
                                wire:click="filterByCategory({{ $cat->id }})"
                                class="w-full flex items-center justify-between gap-2 rounded-xl px-3.5 py-2.5 text-sm transition-all {{ $categoryId === $cat->id ? 'bg-[var(--accent-soft)] text-slate-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}"
                            >
                                <span class="flex items-center gap-2.5 min-w-0">
                                    <iconify-icon icon="{{ $cat->icon ?: 'solar:folder-bold-duotone' }}" width="17" class="{{ $categoryId === $cat->id ? 'text-[var(--accent)]' : 'text-slate-400' }} shrink-0"></iconify-icon>
                                    <span class="truncate">{{ $cat->name }}</span>
                                </span>
                                <span class="text-xs font-medium tabular-nums {{ $categoryId === $cat->id ? 'text-[var(--accent)]' : 'text-slate-400' }}">{{ $cat->articles_count }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Articles --}}
            <div class="lg:col-span-9">
                @if($articles->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white py-20 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 mb-5">
                            <iconify-icon icon="solar:book-2-linear" width="26"></iconify-icon>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">{{ __('Aucun article trouvé') }}</p>
                        <p class="text-xs text-slate-500 mt-1.5 max-w-xs mx-auto">
                            @if($search)
                                {{ __('Essayez de modifier votre recherche.') }}
                            @else
                                {{ __('Les articles apparaîtront ici une fois publiés.') }}
                            @endif
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        @foreach($articles as $article)
                            <button
                                wire:click="viewArticle({{ $article->id }})"
                                class="group flex flex-col rounded-2xl border border-slate-200 bg-white text-left transition-all duration-200 hover:border-slate-300 hover:shadow-md"
                            >
                                {{-- Card body --}}
                                <div class="flex-1 p-5 sm:p-6">
                                    @if($article->category)
                                        <div class="mb-3">
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                                @if($article->category->icon)
                                                    <iconify-icon icon="{{ $article->category->icon }}" width="13" class="text-slate-400"></iconify-icon>
                                                @endif
                                                {{ $article->category->name }}
                                            </span>
                                        </div>
                                    @endif

                                    <h4 class="text-[15px] font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-[var(--accent)] transition-colors">{{ $article->title }}</h4>

                                    <p class="mt-2.5 text-[13px] text-slate-500 leading-relaxed line-clamp-3">{{ $article->excerpt(120) }}</p>
                                </div>

                                {{-- Card footer --}}
                                <div class="px-5 sm:px-6 py-3.5 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 text-[11px] text-slate-400 min-w-0">
                                        <span>{{ $article->view_count }} {{ __('vues') }}</span>
                                        @if(!empty($article->keywords))
                                            <span class="truncate hidden sm:inline">{{ implode(' · ', array_slice($article->keywords, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <iconify-icon icon="solar:arrow-right-linear" width="15" class="text-slate-300 group-hover:text-[var(--accent)] shrink-0 transition-colors"></iconify-icon>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
