@php
    // Same input feel as login page (bg slate-50 + inset ring)
    $field = 'block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
    $select = $field . ' appearance-none pr-9';
    $textarea = 'block w-full rounded-md border-0 bg-slate-50 p-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
@endphp

<form wire:submit="submit" class="space-y-6">
    <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm">
        <div class="p-4 sm:p-6 space-y-6">
            <div>
                <h3 class="text-[13px] font-semibold text-[#111827]">{{ __('Détails du ticket') }}</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Choisissez une catégorie et une priorité, puis donnez un sujet clair.') }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="drawer_category" :value="__('Catégorie')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                    @php
                        $categoryOptions = $categories->map(fn ($c) => ['value' => (int) $c->id, 'label' => (string) $c->name])->values()->all();
                    @endphp
                    <div
                        class="relative mt-1"
                        :class="open ? 'z-[9999]' : ''"
                        x-data="{
                            open: false,
                            q: '',
                            selected: @entangle('ticket_category_id').live,
                            options: @js($categoryOptions),
                            get label() {
                                const hit = this.options.find(o => String(o.value) === String(this.selected));
                                return hit ? hit.label : '';
                            },
                            get filtered() {
                                const q = (this.q || '').toLowerCase().trim();
                                if (!q) return this.options;
                                return this.options.filter(o => (o.label || '').toLowerCase().includes(q));
                            }
                        }"
                        @keydown.escape.window="open=false"
                        @click.outside="open=false"
                    >
                        <button type="button" class="{{ $select }} text-left pr-10 cursor-pointer" @click="open = !open" :aria-expanded="open.toString()">
                            <span class="block truncate" x-text="label"></span>
                        </button>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14"></iconify-icon>

                        <div x-cloak x-show="open" x-transition class="absolute z-[9999] mt-2 w-full rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                            <div class="p-2 border-b border-slate-100">
                                <div class="relative">
                                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="14"></iconify-icon>
                                    <input type="text" x-model="q" class="w-full rounded-lg border-0 bg-slate-50 py-2 pl-9 pr-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200" placeholder="{{ __('Rechercher…') }}">
                                </div>
                            </div>
                            <div class="max-h-64 overflow-auto custom-scrollbar p-1">
                                <template x-for="opt in filtered" :key="opt.value">
                                    <button type="button" class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm text-left hover:bg-slate-50 transition" :class="String(opt.value)===String(selected) ? 'bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'text-slate-900'" @click="selected = opt.value; open=false; q='';">
                                        <span class="truncate" x-text="opt.label"></span>
                                        <iconify-icon x-show="String(opt.value)===String(selected)" icon="solar:check-circle-bold" width="16" style="color: var(--accent);"></iconify-icon>
                                    </button>
                                </template>
                                <div x-show="filtered.length===0" class="px-3 py-6 text-center text-[12px] text-slate-500">{{ __('Aucun résultat') }}</div>
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="drawer_priority" :value="__('Priorité')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                    @php
                        $priorityOptions = $priorities->map(fn ($p) => ['value' => (int) $p->id, 'label' => (string) $p->name])->values()->all();
                    @endphp
                    <div
                        class="relative mt-1"
                        :class="open ? 'z-[9999]' : ''"
                        x-data="{
                            open: false,
                            q: '',
                            selected: @entangle('ticket_priority_id').live,
                            options: @js($priorityOptions),
                            get label() {
                                const hit = this.options.find(o => String(o.value) === String(this.selected));
                                return hit ? hit.label : '';
                            },
                            get filtered() {
                                const q = (this.q || '').toLowerCase().trim();
                                if (!q) return this.options;
                                return this.options.filter(o => (o.label || '').toLowerCase().includes(q));
                            }
                        }"
                        @keydown.escape.window="open=false"
                        @click.outside="open=false"
                    >
                        <button type="button" class="{{ $select }} text-left pr-10 cursor-pointer" @click="open = !open" :aria-expanded="open.toString()">
                            <span class="block truncate" x-text="label"></span>
                        </button>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="14"></iconify-icon>

                        <div x-cloak x-show="open" x-transition class="absolute z-[9999] mt-2 w-full rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                            <div class="p-2 border-b border-slate-100">
                                <div class="relative">
                                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="14"></iconify-icon>
                                    <input type="text" x-model="q" class="w-full rounded-lg border-0 bg-slate-50 py-2 pl-9 pr-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200" placeholder="{{ __('Rechercher…') }}">
                                </div>
                            </div>
                            <div class="max-h-64 overflow-auto custom-scrollbar p-1">
                                <template x-for="opt in filtered" :key="opt.value">
                                    <button type="button" class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm text-left hover:bg-slate-50 transition" :class="String(opt.value)===String(selected) ? 'bg-[color:var(--accent-soft)] text-[color:var(--accent)]' : 'text-slate-900'" @click="selected = opt.value; open=false; q='';">
                                        <span class="truncate" x-text="opt.label"></span>
                                        <iconify-icon x-show="String(opt.value)===String(selected)" icon="solar:check-circle-bold" width="16" style="color: var(--accent);"></iconify-icon>
                                    </button>
                                </template>
                                <div x-show="filtered.length===0" class="px-3 py-6 text-center text-[12px] text-slate-500">{{ __('Aucun résultat') }}</div>
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('ticket_priority_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="drawer_subject" :value="__('Sujet')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                <input
                    id="drawer_subject"
                    type="text"
                    wire:model="subject"
                    required
                    class="mt-1 {{ $field }}"
                    placeholder="{{ __('Ex: Erreur 500 sur le checkout') }}"
                >
                <x-input-error :messages="$errors->get('subject')" class="mt-2" />
            </div>

            <div class="pt-2 border-t border-[#E5E7EB]">
                <h3 class="text-[13px] font-semibold text-[#111827]">{{ __('Description') }}</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Ajoute des détails: étapes, impact, captures d’écran, contexte…') }}</p>
            </div>

            <div>
                <x-input-label for="drawer_description" :value="__('Description')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                <textarea
                    id="drawer_description"
                    wire:model="description"
                    rows="5"
                    class="mt-1 {{ $textarea }}"
                    placeholder="{{ __('Donnez un maximum de détails (étapes, capture, contexte...)') }}"
                ></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Attachments -->
            <div class="pt-2 border-t border-[#E5E7EB]">
                <h3 class="text-[13px] font-semibold text-[#111827]">{{ __('Pièces jointes') }}</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Ajoutez des fichiers ou des liens (optionnel).') }}</p>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <!-- Files -->
                <div class="rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-[13px] font-semibold text-[#111827]">{{ __('Fichiers') }}</div>
                            <div class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('PDF, images, docs… (max 10MB / fichier, 5 fichiers).') }}</div>
                        </div>
                    </div>

                    <label
                        for="drawer_files"
                        class="mt-3 flex items-center justify-center gap-2 rounded-lg border border-dashed border-[#D1D5DB] bg-white px-3 py-4 text-[13px] font-medium text-[#111827] hover:bg-[#F9FAFB] transition cursor-pointer"
                    >
                        <iconify-icon icon="solar:upload-linear" width="18" class="text-[#6B7280]"></iconify-icon>
                        {{ __('Cliquer pour ajouter des fichiers') }}
                    </label>
                    <input
                        id="drawer_files"
                        type="file"
                        class="hidden"
                        multiple
                        accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip"
                        wire:model="files"
                    />

                    <div class="mt-2">
                        <x-input-error :messages="$errors->get('files')" />
                        <x-input-error :messages="$errors->get('files.*')" />
                    </div>

                    @if (!empty($files))
                        <div class="mt-3 space-y-2">
                            @foreach ($files as $i => $f)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-[#E5E7EB] bg-white px-3 py-2">
                                    <div class="min-w-0 flex items-start gap-2">
                                        <div class="mt-0.5 h-7 w-7 rounded-md border border-[#E5E7EB] bg-[#F9FAFB] flex items-center justify-center text-[#6B7280] shrink-0">
                                            <iconify-icon icon="solar:file-linear" width="14"></iconify-icon>
                                        </div>
                                        <div class="min-w-0">
                                        <div class="text-[12px] font-semibold text-[#111827] truncate">
                                            {{ method_exists($f, 'getClientOriginalName') ? $f->getClientOriginalName() : __('Fichier') }}
                                        </div>
                                        <div class="text-[11px] text-[#6B7280]">
                                            @if (method_exists($f, 'getSize') && $f->getSize())
                                                {{ number_format($f->getSize() / 1024, 0) }} KB
                                            @else
                                                —
                                            @endif
                                        </div>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="h-8 w-8 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center"
                                        wire:click="removeFile({{ $i }})"
                                        title="{{ __('Supprimer') }}"
                                    >
                                        <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="16"></iconify-icon>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-2 text-[11px] text-[#9CA3AF]" wire:loading wire:target="files">
                        {{ __('Téléversement en cours…') }}
                    </div>
                </div>

                <!-- Links -->
                <div class="rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4">
                    <div class="text-[13px] font-semibold text-[#111827]">{{ __('Liens') }}</div>
                    <div class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('Ajoutez un lien vers un drive, une page, une capture, etc.') }}</div>

                    <div class="mt-3 flex items-center gap-2">
                        <input
                            type="url"
                            class="flex-1 {{ $field }}"
                            placeholder="https://..."
                            wire:model.defer="linkUrl"
                        />
                        <button
                            type="button"
                            class="h-10 w-10 rounded-md border border-[#E5E7EB] bg-white text-[#6B7280] hover:text-[color:var(--accent)] hover:bg-[color:var(--accent-soft)] hover:border-[color:var(--accent-soft-2)] transition flex items-center justify-center"
                            wire:click="addLink"
                            title="{{ __('Ajouter') }}"
                        >
                            <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('linkUrl')" class="mt-2" />
                    <x-input-error :messages="$errors->get('links')" class="mt-2" />
                    <x-input-error :messages="$errors->get('links.*')" class="mt-2" />

                    @if (!empty($links))
                        <div class="mt-3 space-y-2">
                            @foreach ($links as $i => $url)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-[#E5E7EB] bg-white px-3 py-2">
                                    <div class="min-w-0 flex items-start gap-2">
                                        <div class="mt-0.5 h-7 w-7 rounded-md border border-[#E5E7EB] bg-[#F9FAFB] flex items-center justify-center text-[#6B7280] shrink-0">
                                            <iconify-icon icon="solar:link-linear" width="14"></iconify-icon>
                                        </div>
                                        <a href="{{ $url }}" target="_blank" class="min-w-0 text-[12px] font-medium text-[color:var(--accent)] hover:underline truncate">
                                            {{ $url }}
                                        </a>
                                    </div>
                                    <button
                                        type="button"
                                        class="h-8 w-8 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center"
                                        wire:click="removeLink({{ $i }})"
                                        title="{{ __('Supprimer') }}"
                                    >
                                        <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="sticky bottom-0 border-t border-[#E5E7EB] bg-white/95 backdrop-blur px-4 sm:px-6 py-4">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <button
                    type="button"
                    class="h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-semibold rounded-xl shadow-sm hover:bg-[#F9FAFB] transition flex items-center justify-center w-full sm:w-auto"
                    wire:click="$dispatch('tickets:closeCreateDrawer')"
                >
                    {{ __('Annuler') }}
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="h-10 px-4 text-white text-[13px] font-semibold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2 w-full sm:w-auto bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="submit"><iconify-icon icon="solar:send-square-linear" width="16"></iconify-icon></span>
                    <span wire:loading wire:target="submit" class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
                    <span wire:loading.remove wire:target="submit">{{ __('Envoyer') }}</span>
                </button>
            </div>
        </div>
    </div>
</form>

