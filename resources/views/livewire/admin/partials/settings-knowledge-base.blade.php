<!-- KNOWLEDGE BASE TAB -->
<div x-show="tab === 'knowledge_base'" x-cloak class="space-y-6">

    @php
        $kbIconChoices = [
            'solar:question-circle-bold-duotone' => 'FAQ',
            'solar:book-bold-duotone' => 'Guide',
            'solar:lightbulb-bolt-bold-duotone' => 'Astuces',
            'solar:bug-bold-duotone' => 'Problemes',
            'solar:settings-bold-duotone' => 'Config',
            'solar:shield-check-bold-duotone' => 'Securite',
            'solar:card-bold-duotone' => 'Paiement',
            'solar:user-bold-duotone' => 'Compte',
            'solar:letter-bold-duotone' => 'Email',
            'solar:chart-2-bold-duotone' => 'Rapports',
            'solar:rocket-2-bold-duotone' => 'Demarrage',
            'solar:widget-5-bold-duotone' => 'Modules',
            'solar:smartphone-bold-duotone' => 'Mobile',
            'solar:link-round-bold-duotone' => 'Liens',
            'solar:folder-bold-duotone' => 'General',
        ];
    @endphp

    {{-- ── Card 1 : Categories KB ─────────────────────────────────────── --}}
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
            <h2 class="text-lg font-bold text-slate-900">Categories</h2>
            <p class="text-sm text-slate-500">Organisez vos articles d'aide par themes.</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Create form --}}
            <form wire:submit="addKbCategory" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="new_kb_cat_name" value="Nom *" />
                        <input
                            id="new_kb_cat_name"
                            type="text"
                            wire:model="newKbCategoryName"
                            placeholder="Ex : FAQ Generale"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                        />
                        <x-input-error :messages="$errors->get('newKbCategoryName')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="new_kb_cat_desc" value="Description" />
                        <input
                            id="new_kb_cat_desc"
                            type="text"
                            wire:model="newKbCategoryDescription"
                            placeholder="Courte description (optionnel)"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                        />
                    </div>
                </div>

                {{-- Icon picker --}}
                <div>
                    <x-input-label value="Icone" />
                    <div class="mt-1.5 flex flex-wrap gap-2">
                        @foreach ($kbIconChoices as $iconName => $iconLabel)
                            <button
                                type="button"
                                wire:click="$set('newKbCategoryIcon', '{{ $iconName }}')"
                                class="relative flex flex-col items-center gap-1 rounded-xl border-2 px-3 py-2.5 text-center transition-all duration-150 cursor-pointer {{ $newKbCategoryIcon === $iconName ? 'border-[var(--accent)] bg-[var(--accent-soft)] ring-1 ring-[var(--accent)]/20' : 'border-slate-100 bg-white hover:border-slate-200 hover:bg-slate-50' }}"
                                title="{{ $iconLabel }}"
                            >
                                <iconify-icon icon="{{ $iconName }}" width="22" class="{{ $newKbCategoryIcon === $iconName ? 'text-[var(--accent)]' : 'text-slate-400' }} transition-colors"></iconify-icon>
                                <span class="text-[9px] font-medium {{ $newKbCategoryIcon === $iconName ? 'text-[var(--accent-dark)]' : 'text-slate-400' }} transition-colors leading-none">{{ $iconLabel }}</span>
                                @if ($newKbCategoryIcon === $iconName)
                                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-[var(--accent)] text-white">
                                        <iconify-icon icon="solar:check-read-linear" width="10"></iconify-icon>
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-manexo.action-button type="submit" wire-target="addKbCategory" variant="primary" class="shrink-0 !font-semibold !px-5">
                        <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                        Ajouter la categorie
                    </x-manexo.action-button>
                </div>
            </form>

            <hr class="border-slate-100">

            {{-- List --}}
            @if ($kbCategories->isEmpty())
                <div class="py-8 text-center">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                        <iconify-icon icon="solar:folder-open-linear" width="28" class="text-slate-400"></iconify-icon>
                    </div>
                    <p class="text-sm text-slate-500">Aucune categorie pour le moment.</p>
                    <p class="text-xs text-slate-400 mt-1">Creez votre premiere categorie pour organiser vos articles.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($kbCategories as $kbCat)
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                             wire:key="kb-cat-{{ $kbCat->id }}">

                            @if ($editingKbCategoryId === $kbCat->id)
                                {{-- Inline edit --}}
                                <form wire:submit="updateKbCategory" class="flex-1 space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <input
                                            type="text"
                                            wire:model="editingKbCategoryName"
                                            class="w-full rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                            placeholder="Nom"
                                            autofocus
                                        />
                                        <input
                                            type="text"
                                            wire:model="editingKbCategoryDescription"
                                            class="w-full rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                            placeholder="Description"
                                        />
                                    </div>

                                    {{-- Icon picker (edit) --}}
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($kbIconChoices as $iconName => $iconLabel)
                                            <button
                                                type="button"
                                                wire:click="$set('editingKbCategoryIcon', '{{ $iconName }}')"
                                                class="flex items-center justify-center rounded-lg border p-1.5 transition-all {{ $editingKbCategoryIcon === $iconName ? 'border-[var(--accent)] bg-[var(--accent-soft)]' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50' }}"
                                                title="{{ $iconLabel }}"
                                            >
                                                <iconify-icon icon="{{ $iconName }}" width="18" class="{{ $editingKbCategoryIcon === $iconName ? 'text-[var(--accent)]' : 'text-slate-400' }}"></iconify-icon>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="flex items-center gap-2 justify-end">
                                        <button type="button" wire:click="$set('editingKbCategoryId', null)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                                            Annuler
                                        </button>
                                        <x-manexo.action-button type="submit" wire-target="updateKbCategory" variant="primary-sm" class="!px-3 !font-semibold">
                                            <iconify-icon icon="solar:check-circle-bold" width="14"></iconify-icon>
                                            Enregistrer
                                        </x-manexo.action-button>
                                    </div>
                                    <x-input-error :messages="$errors->get('editingKbCategoryName')" class="mt-1" />
                                </form>
                            @else
                                {{-- Icon --}}
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                                    <iconify-icon icon="{{ $kbCat->icon ?: 'solar:folder-bold-duotone' }}" width="20"></iconify-icon>
                                </span>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-medium text-slate-900">{{ $kbCat->name }}</span>
                                        <span class="text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">{{ $kbCat->slug }}</span>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400">
                                            <iconify-icon icon="solar:document-text-linear" width="12"></iconify-icon>
                                            {{ $kbCat->articles()->count() }} articles
                                        </span>
                                    </div>
                                    @if ($kbCat->description)
                                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $kbCat->description }}</p>
                                    @endif
                                </div>

                                {{-- Active toggle --}}
                                <button
                                    type="button"
                                    wire:click="toggleKbCategory({{ $kbCat->id }})"
                                    class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors {{ $kbCat->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full {{ $kbCat->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $kbCat->is_active ? 'Actif' : 'Inactif' }}
                                </button>

                                {{-- Edit --}}
                                <button type="button" wire:click="editKbCategory({{ $kbCat->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="Modifier">
                                    <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                </button>

                                {{-- Delete --}}
                                <button type="button" wire:click="deleteKbCategory({{ $kbCat->id }})" wire:confirm="Supprimer cette categorie et tous ses articles ?" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="Supprimer">
                                    <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Card 2 : Articles KB ───────────────────────────────────────── --}}
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9; flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Articles</h2>
                <p class="text-sm text-slate-500">Creez et gerez vos articles d'aide.</p>
            </div>
            <button wire:click="openCreateArticle" class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">
                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                Nouvel article
            </button>
        </div>

        <div class="p-6">
            @if ($kbArticles->isEmpty())
                <div class="py-8 text-center">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                        <iconify-icon icon="solar:document-text-linear" width="28" class="text-slate-400"></iconify-icon>
                    </div>
                    <p class="text-sm text-slate-500">Aucun article pour le moment.</p>
                    <p class="text-xs text-slate-400 mt-1">Creez votre premier article d'aide pour vos utilisateurs.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($kbArticles as $article)
                        <div class="flex items-center gap-3 px-4 py-3.5 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                             wire:key="kb-art-{{ $article->id }}">

                            {{-- Icon --}}
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $article->status === 'published' ? 'bg-emerald-50 text-emerald-500' : 'bg-amber-50 text-amber-500' }}">
                                <iconify-icon icon="{{ $article->status === 'published' ? 'solar:document-bold-duotone' : 'solar:pen-new-square-bold-duotone' }}" width="18"></iconify-icon>
                            </span>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-slate-900 truncate">{{ $article->title }}</span>
                                </div>
                                <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                                    @if ($article->category)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500">
                                            <iconify-icon icon="{{ $article->category->icon ?: 'solar:folder-bold-duotone' }}" width="11" class="text-slate-400"></iconify-icon>
                                            {{ $article->category->name }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400">
                                        <iconify-icon icon="solar:eye-linear" width="11"></iconify-icon>
                                        {{ $article->view_count }} vues
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium {{ $article->visibility === 'public' ? 'text-blue-500' : 'text-slate-400' }}">
                                        <iconify-icon icon="{{ $article->visibility === 'public' ? 'solar:globe-linear' : 'solar:lock-linear' }}" width="11"></iconify-icon>
                                        {{ $article->visibility === 'public' ? 'Public' : 'Interne' }}
                                    </span>
                                </div>
                                @if (!empty($article->keywords))
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach (array_slice($article->keywords, 0, 5) as $kw)
                                            <span class="inline-block rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-500">{{ $kw }}</span>
                                        @endforeach
                                        @if (count($article->keywords) > 5)
                                            <span class="text-[10px] text-slate-400">+{{ count($article->keywords) - 5 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Status badge --}}
                            <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $article->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $article->status === 'published' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $article->status === 'published' ? 'Publie' : 'Brouillon' }}
                            </span>

                            {{-- Active toggle --}}
                            <button
                                type="button"
                                wire:click="toggleArticle({{ $article->id }})"
                                class="shrink-0 opacity-0 group-hover:opacity-100 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-all {{ $article->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                title="{{ $article->is_active ? 'Desactiver' : 'Activer' }}"
                            >
                                <iconify-icon icon="{{ $article->is_active ? 'solar:eye-bold' : 'solar:eye-closed-bold' }}" width="14"></iconify-icon>
                            </button>

                            {{-- Edit --}}
                            <button type="button" wire:click="openEditArticle({{ $article->id }})" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="Modifier">
                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                            </button>

                            {{-- Delete --}}
                            <button type="button" wire:click="deleteArticle({{ $article->id }})" wire:confirm="Supprimer cet article ?" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="Supprimer">
                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Modal : Create / Edit Article ──────────────────────────────── --}}
    @if ($showKbArticleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-transition>
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showKbArticleModal', false)"></div>

            {{-- Panel --}}
            <div class="relative content-card shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
                {{-- Header --}}
                <div class="shrink-0 px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9; rounded-t-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--accent-soft)] text-[var(--accent)]">
                            <iconify-icon icon="{{ $editingKbArticleId ? 'solar:pen-2-bold-duotone' : 'solar:add-circle-bold-duotone' }}" width="22"></iconify-icon>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ $editingKbArticleId ? 'Modifier l\'article' : 'Nouvel article' }}</h3>
                            <p class="text-xs text-slate-500">{{ $editingKbArticleId ? 'Modifiez le contenu et les parametres.' : 'Redigez un nouvel article d\'aide.' }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showKbArticleModal', false)" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                        <iconify-icon icon="solar:close-circle-bold" width="22"></iconify-icon>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit="saveArticle" class="flex-1 overflow-y-auto">
                    <div class="p-6 space-y-5">
                        {{-- Title --}}
                        <div>
                            <x-input-label for="kb_art_title" value="Titre *" />
                            <input
                                id="kb_art_title"
                                type="text"
                                wire:model="kbArticleTitle"
                                placeholder="Titre de l'article"
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            />
                            <x-input-error :messages="$errors->get('kbArticleTitle')" class="mt-1" />
                        </div>

                        {{-- Keywords (tag input) --}}
                        <div
                            x-data="{
                                raw: $wire.entangle('kbArticleKeywords'),
                                input: '',
                                get tags() {
                                    return this.raw ? this.raw.split(',').map(t => t.trim()).filter(Boolean) : [];
                                },
                                add() {
                                    let val = this.input.trim();
                                    if (!val) return;
                                    let current = this.tags;
                                    if (!current.includes(val)) {
                                        current.push(val);
                                        this.raw = current.join(', ');
                                    }
                                    this.input = '';
                                },
                                remove(index) {
                                    let current = this.tags;
                                    current.splice(index, 1);
                                    this.raw = current.join(', ');
                                },
                                onKeydown(e) {
                                    if (e.key === 'Enter' || e.key === ',') {
                                        e.preventDefault();
                                        this.add();
                                    }
                                    if (e.key === 'Backspace' && this.input === '' && this.tags.length) {
                                        this.remove(this.tags.length - 1);
                                    }
                                }
                            }"
                        >
                            <x-input-label value="Mots-cles" />
                            <div class="mt-1 flex flex-wrap items-center gap-1.5 min-h-[42px] rounded-xl border border-slate-200 bg-white py-2 px-3 shadow-sm focus-within:border-[var(--accent)] focus-within:ring-1 focus-within:ring-[var(--accent)] transition-all">
                                <template x-for="(tag, i) in tags" :key="i">
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-[var(--accent-soft)] px-2.5 py-1 text-xs font-medium text-[var(--accent-dark)]">
                                        <span x-text="tag"></span>
                                        <button type="button" @click="remove(i)" class="text-[var(--accent)] hover:text-red-500 transition-colors">
                                            <iconify-icon icon="solar:close-circle-bold" width="14"></iconify-icon>
                                        </button>
                                    </span>
                                </template>
                                <input
                                    type="text"
                                    x-model="input"
                                    @keydown="onKeydown"
                                    @blur="add()"
                                    placeholder="Tapez puis Entree..."
                                    class="flex-1 min-w-[120px] border-0 bg-transparent p-0 text-sm text-slate-900 placeholder-slate-400 focus:ring-0 focus:outline-none"
                                />
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Appuyez sur Entree ou virgule pour ajouter un mot-cle</p>
                            <x-input-error :messages="$errors->get('kbArticleKeywords')" class="mt-1" />
                        </div>

                        {{-- Category + Status + Visibility row --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="kb_art_cat" value="Categorie *" />
                                <x-select-input
                                    id="kb_art_cat"
                                    wire:model="kbArticleCategoryId"
                                >
                                    <option value="">-- Choisir --</option>
                                    @foreach ($kbCategories as $kbCat)
                                        <option value="{{ $kbCat->id }}">{{ $kbCat->name }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('kbArticleCategoryId')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="kb_art_status" value="Statut" />
                                <x-select-input
                                    id="kb_art_status"
                                    wire:model="kbArticleStatus"
                                >
                                    <option value="draft">Brouillon</option>
                                    <option value="published">Publie</option>
                                </x-select-input>
                            </div>
                            <div>
                                <x-input-label for="kb_art_vis" value="Visibilite" />
                                <x-select-input
                                    id="kb_art_vis"
                                    wire:model="kbArticleVisibility"
                                >
                                    <option value="internal">Interne</option>
                                    <option value="public">Public</option>
                                </x-select-input>
                            </div>
                        </div>

                        {{-- Rich Text Content --}}
                        <div
                            x-data="{
                                content: $wire.entangle('kbArticleContent'),
                                exec(cmd, val = null) {
                                    document.execCommand(cmd, false, val);
                                    this.sync();
                                },
                                sync() {
                                    this.content = this.$refs.editor.innerHTML;
                                },
                                init() {
                                    this.$nextTick(() => {
                                        this.$refs.editor.innerHTML = this.content || '';
                                    });
                                }
                            }"
                        >
                            <x-input-label value="Contenu *" />

                            {{-- Toolbar --}}
                            <div class="mt-1 flex flex-wrap items-center gap-0.5 rounded-t-xl border border-b-0 border-slate-200 bg-slate-50 px-2 py-1.5">
                                <button type="button" @mousedown.prevent @click="exec('bold')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Gras">
                                    <iconify-icon icon="solar:text-bold-bold" width="18"></iconify-icon>
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('italic')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Italique">
                                    <iconify-icon icon="solar:text-italic-bold" width="18"></iconify-icon>
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('underline')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Souligne">
                                    <iconify-icon icon="solar:text-underline-bold" width="18"></iconify-icon>
                                </button>

                                <span class="mx-1 h-5 w-px bg-slate-200"></span>

                                <button type="button" @mousedown.prevent @click="exec('formatBlock', 'h2')" class="flex h-8 items-center justify-center rounded-lg px-2 text-xs font-bold text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Titre">
                                    H2
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('formatBlock', 'h3')" class="flex h-8 items-center justify-center rounded-lg px-2 text-xs font-bold text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Sous-titre">
                                    H3
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('formatBlock', 'p')" class="flex h-8 items-center justify-center rounded-lg px-2 text-xs font-medium text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Paragraphe">
                                    <iconify-icon icon="solar:text-bold" width="16"></iconify-icon>
                                </button>

                                <span class="mx-1 h-5 w-px bg-slate-200"></span>

                                <button type="button" @mousedown.prevent @click="exec('insertUnorderedList')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Liste a puces">
                                    <iconify-icon icon="solar:list-bold" width="18"></iconify-icon>
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('insertOrderedList')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Liste numerotee">
                                    <iconify-icon icon="solar:list-1-bold" width="18"></iconify-icon>
                                </button>

                                <span class="mx-1 h-5 w-px bg-slate-200"></span>

                                <button type="button" @mousedown.prevent @click="exec('formatBlock', 'blockquote')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Citation">
                                    <iconify-icon icon="solar:chat-square-like-bold" width="18"></iconify-icon>
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('insertHorizontalRule')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-white hover:text-slate-900 transition-colors" title="Ligne separatrice">
                                    <iconify-icon icon="solar:minus-circle-bold" width="18"></iconify-icon>
                                </button>
                                <button type="button" @mousedown.prevent @click="exec('removeFormat')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-red-500 transition-colors ml-auto" title="Effacer la mise en forme">
                                    <iconify-icon icon="solar:eraser-bold" width="18"></iconify-icon>
                                </button>
                            </div>

                            {{-- Editable area --}}
                            <div
                                x-ref="editor"
                                contenteditable="true"
                                @input="sync()"
                                @blur="sync()"
                                class="block w-full min-h-[280px] max-h-[420px] overflow-y-auto rounded-b-xl border border-slate-200 bg-white py-3 px-4 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-1 focus:ring-[var(--accent)] outline-none transition-all prose prose-sm prose-slate max-w-none
                                    [&_h2]:text-base [&_h2]:font-bold [&_h2]:text-slate-900 [&_h2]:mt-4 [&_h2]:mb-2
                                    [&_h3]:text-sm [&_h3]:font-bold [&_h3]:text-slate-800 [&_h3]:mt-3 [&_h3]:mb-1
                                    [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:my-2
                                    [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:my-2
                                    [&_li]:my-0.5
                                    [&_blockquote]:border-l-4 [&_blockquote]:border-slate-300 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-slate-500 [&_blockquote]:my-3
                                    [&_hr]:border-slate-200 [&_hr]:my-4
                                    [&_p]:my-1.5"
                                data-placeholder="Redigez votre article ici..."
                                style="empty-cells: show;"
                            ></div>

                            {{-- Placeholder via CSS --}}
                            <style>
                                [contenteditable=true]:empty:before {
                                    content: attr(data-placeholder);
                                    color: #94a3b8;
                                    pointer-events: none;
                                    display: block;
                                }
                            </style>

                            <x-input-error :messages="$errors->get('kbArticleContent')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="shrink-0 px-6 py-4 bg-slate-50/30 rounded-b-2xl flex items-center justify-end gap-3" style="border-top: 1px solid #f1f5f9;">
                        <button type="button" wire:click="$set('showKbArticleModal', false)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                            Annuler
                        </button>
                        <x-manexo.action-button type="submit" wire-target="saveArticle" variant="primary" class="!px-5 !font-semibold" :loading-label="__('ui.action.saving')">
                            <iconify-icon icon="{{ $editingKbArticleId ? 'solar:pen-2-bold' : 'solar:add-circle-bold' }}" width="18"></iconify-icon>
                            {{ $editingKbArticleId ? 'Mettre a jour' : 'Creer l\'article' }}
                        </x-manexo.action-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
