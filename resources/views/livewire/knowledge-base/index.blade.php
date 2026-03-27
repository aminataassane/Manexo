<div class="w-full max-w-full min-w-0 mx-auto">

    {{-- ═══ HEADER ═══ --}}
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title">Base de connaissances</h1>
            <p class="page-subtitle">Trouvez des reponses a vos questions avant de creer un ticket.</p>
        </div>
        <div class="page-actions">
            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="inline-flex items-center gap-2 rounded-xl bg-white px-3 py-2.5 sm:px-4 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all touch-target sm:min-h-0 sm:min-w-0"
                style="border: 1px solid #e2e8f0;"
            >
                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                <span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>

    {{-- ═══ SEARCH BAR ═══ --}}
    <div class="mb-6 sm:mb-8">
        <div class="flex max-w-xl items-center gap-2 rounded-2xl bg-white px-3 py-1 shadow-sm transition focus-within:ring-2 focus-within:ring-[var(--accent)]/20" style="border: 1px solid #e2e8f0;">
            <span class="flex shrink-0 items-center justify-center pl-1 text-slate-400" aria-hidden="true">
                <iconify-icon icon="solar:magnifer-linear" width="20"></iconify-icon>
            </span>
            <input
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="Rechercher un article..."
                autocomplete="off"
                class="min-w-0 flex-1 border-0 bg-transparent py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0"
            />
            @if ($search)
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="flex shrink-0 items-center justify-center rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Effacer la recherche"
                >
                    <iconify-icon icon="solar:close-circle-bold" width="18"></iconify-icon>
                </button>
            @endif
        </div>
    </div>

    @if ($viewingArticle)
        {{-- ═══ ARTICLE DETAIL ═══ --}}
        <div class="mb-6">
            <button wire:click="closeArticle" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-[var(--accent)] transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
                Retour aux articles
            </button>
        </div>

        <div class="content-card">
            <div class="px-6 sm:px-8 py-6 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                <div class="flex items-center gap-3 mb-3">
                    @if ($viewingArticle->category)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[var(--accent-soft)] text-[var(--accent-dark)]">
                            @if ($viewingArticle->category->icon)
                                <iconify-icon icon="{{ $viewingArticle->category->icon }}" width="14"></iconify-icon>
                            @endif
                            {{ $viewingArticle->category->name }}
                        </span>
                    @endif
                    <span class="text-xs text-slate-400">{{ $viewingArticle->view_count }} vues</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $viewingArticle->title }}</h1>
                @if ($viewingArticle->creator)
                    <p class="mt-2 text-xs text-slate-500">
                        Par {{ $viewingArticle->creator->name }}
                        @if ($viewingArticle->published_at)
                            &middot; {{ $viewingArticle->published_at->translatedFormat('d M Y') }}
                        @endif
                    </p>
                @endif
            </div>
            <div class="px-6 sm:px-8 py-6">
                <div class="prose prose-slate max-w-none text-sm leading-relaxed
                    [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-slate-900 [&_h2]:mt-6 [&_h2]:mb-3
                    [&_h3]:text-base [&_h3]:font-bold [&_h3]:text-slate-800 [&_h3]:mt-5 [&_h3]:mb-2
                    [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:my-3
                    [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:my-3
                    [&_li]:my-1
                    [&_blockquote]:border-l-4 [&_blockquote]:border-slate-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-500 [&_blockquote]:my-4
                    [&_hr]:border-slate-200 [&_hr]:my-6
                    [&_p]:my-2
                    [&_strong]:font-bold [&_b]:font-bold
                    [&_em]:italic [&_i]:italic
                    [&_u]:underline"
                >{!! $viewingArticle->safeHtml() !!}</div>

                @if (!empty($viewingArticle->keywords))
                    <div class="mt-6 pt-5" style="border-top: 1px solid #f1f5f9;">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mots-cles</span>
                            @foreach ($viewingArticle->keywords as $kw)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600" style="border: 1px solid #f1f5f9;">
                                    <iconify-icon icon="solar:tag-linear" width="12" class="text-slate-400"></iconify-icon>
                                    {{ $kw }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- ═══ BROWSE VIEW ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
            {{-- Sidebar categories --}}
            <div class="lg:col-span-3">
                <div class="sidebar-panel sticky top-24">
                    <div class="sidebar-panel-header">
                        <h3>Categories</h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button
                            wire:click="filterByCategory(null)"
                            class="sidebar-item {{ !$categoryId ? 'sidebar-item-active' : 'sidebar-item-default' }}"
                        >
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="18" class="{{ !$categoryId ? 'text-[var(--accent)]' : 'text-slate-400' }}"></iconify-icon>
                                Toutes
                            </span>
                            <span class="sidebar-badge {{ !$categoryId ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $articles->count() }}</span>
                        </button>

                        @foreach ($categories as $cat)
                            <button
                                wire:click="filterByCategory({{ $cat->id }})"
                                class="sidebar-item {{ $categoryId === $cat->id ? 'sidebar-item-active' : 'sidebar-item-default' }}"
                            >
                                <span class="flex items-center gap-2.5 min-w-0">
                                    <iconify-icon icon="{{ $cat->icon ?: 'solar:folder-bold-duotone' }}" width="18" class="{{ $categoryId === $cat->id ? 'text-[var(--accent)]' : 'text-slate-400' }} shrink-0"></iconify-icon>
                                    <span class="truncate">{{ $cat->name }}</span>
                                </span>
                                <span class="sidebar-badge {{ $categoryId === $cat->id ? 'sidebar-badge-active' : 'sidebar-badge-default' }}">{{ $cat->articles_count }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Articles grid --}}
            <div class="lg:col-span-9">
                @if ($articles->isEmpty())
                    <div class="content-card">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <iconify-icon icon="solar:book-2-bold-duotone" width="28" class="text-slate-300"></iconify-icon>
                            </div>
                            <p class="empty-state-title">Aucun article trouve</p>
                            <p class="empty-state-text">
                                @if ($search)
                                    Essayez de modifier votre recherche.
                                @else
                                    Les articles apparaitront ici une fois publies.
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($articles as $article)
                            <button
                                wire:click="viewArticle({{ $article->id }})"
                                class="group rounded-2xl bg-white p-5 text-left transition-all duration-200 hover:-translate-y-0.5"
                                style="border: 1px solid #f1f5f9; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.03);"
                            >
                                <div class="flex items-center gap-2 mb-3">
                                    @if ($article->category)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                                            @if ($article->category->icon)
                                                <iconify-icon icon="{{ $article->category->icon }}" width="12"></iconify-icon>
                                            @endif
                                            {{ $article->category->name }}
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400">{{ $article->view_count }} vues</span>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors line-clamp-2">{{ $article->title }}</h4>
                                <p class="mt-2 text-xs text-slate-500 line-clamp-3">{{ $article->excerpt(120) }}</p>
                                @if (!empty($article->keywords))
                                    <div class="flex flex-wrap gap-1 mt-2.5">
                                        @foreach (array_slice($article->keywords, 0, 4) as $kw)
                                            <span class="inline-block rounded-md bg-slate-50 px-2 py-0.5 text-[10px] font-medium text-slate-500" style="border: 1px solid #f1f5f9;">{{ $kw }}</span>
                                        @endforeach
                                        @if (count($article->keywords) > 4)
                                            <span class="text-[10px] text-slate-400 self-center">+{{ count($article->keywords) - 4 }}</span>
                                        @endif
                                    </div>
                                @endif
                                <span class="mt-3 inline-flex items-center gap-1 text-xs font-medium text-[var(--accent)] opacity-0 group-hover:opacity-100 transition-opacity">
                                    Lire l'article
                                    <iconify-icon icon="solar:arrow-right-linear" width="14"></iconify-icon>
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
