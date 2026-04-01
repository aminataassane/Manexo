@php
    $field = 'block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
    $textarea = 'block w-full rounded-md border-0 bg-slate-50 p-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';

    $drawerCategoryOptions = $categories->map(fn ($c) => ['value' => (string) $c->id, 'label' => $c->name])->all();
    $drawerCategoryLabel = $categories->firstWhere('id', (int) $ticket_category_id)?->name ?? '';
    $drawerPriorityOptions = $priorities->map(fn ($p) => ['value' => (string) $p->id, 'label' => $p->name])->all();
    $drawerPriorityLabel = $priorities->firstWhere('id', (int) $ticket_priority_id)?->name ?? '';
@endphp

<div class="h-full min-h-0">
<form wire:submit="submit" class="flex h-full min-h-0 flex-col"
    x-data="{
        draftKey: 'ticket-draft-create-drawer',
        draftTimer: null,
        init() {
            try {
                const draft = JSON.parse(localStorage.getItem(this.draftKey) || '{}');
                if (draft.subject && !this.$wire.get('subject')) this.$wire.set('subject', draft.subject);
                if (draft.description && !this.$wire.get('description')) this.$wire.set('description', draft.description);
            } catch (e) {}
        },
        saveDraft() {
            clearTimeout(this.draftTimer);
            this.draftTimer = setTimeout(() => {
                try {
                    const data = {
                        subject: this.$wire.get('subject') || '',
                        description: this.$wire.get('description') || '',
                    };
                    if (data.subject || data.description) {
                        localStorage.setItem(this.draftKey, JSON.stringify(data));
                    } else {
                        localStorage.removeItem(this.draftKey);
                    }
                } catch (e) {}
            }, 500);
        }
    }"
    x-on:submit="localStorage.removeItem('ticket-draft-create-drawer')"
>
    <div class="flex-1 overflow-y-auto custom-scrollbar">
    <div class="mx-auto w-full max-w-5xl space-y-4 p-3 sm:p-5 lg:p-6">

        {{-- Détails du ticket --}}
        <div class="overflow-visible rounded-2xl border border-[#E5E7EB] bg-white shadow-sm">
            <div class="px-4 sm:px-5 py-3 border-b border-[#E5E7EB] bg-[#F9FAFB] rounded-t-2xl">
                <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Détails du ticket') }}</h2>
                <p class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('Choisissez une catégorie et une priorité, puis donnez un sujet clair.') }}</p>
            </div>

            <div class="space-y-4 p-4 sm:p-5">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="drawer_category" :value="__('Catégorie *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <div class="mt-1">
                            <x-select-input
                                id="drawer_category"
                                :options="$drawerCategoryOptions"
                                :label="$drawerCategoryLabel"
                                :selected-value="(string) $ticket_category_id"
                                wire:model="ticket_category_id"
                                wire:loading.attr="disabled"
                                wire:target="ticket_category_id"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="drawer_priority" :value="__('Priorité *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <div class="mt-1">
                            <x-select-input
                                id="drawer_priority"
                                :options="$drawerPriorityOptions"
                                :label="$drawerPriorityLabel"
                                :selected-value="(string) $ticket_priority_id"
                                wire:model="ticket_priority_id"
                                wire:loading.attr="disabled"
                                wire:target="ticket_priority_id"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('ticket_priority_id')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="drawer_subject" :value="__('Sujet *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                    <input
                        id="drawer_subject"
                        type="text"
                        wire:model.live.debounce.700ms="subject"
                        wire:loading.attr="disabled"
                        wire:target="subject"
                        @input="saveDraft()"
                        required
                        class="mt-1 {{ $field }}"
                        placeholder="{{ __('Ex: Erreur 500 sur le checkout') }}"
                    >
                    <x-input-error :messages="$errors->get('subject')" class="mt-2" />

                    @if (count($kbSuggestions))
                        <div class="mt-2 rounded-xl border border-blue-200 bg-blue-50 p-3" x-data="{ expanded: null }">
                            <div class="flex items-center gap-2 mb-2">
                                <iconify-icon icon="solar:book-2-bold-duotone" width="16" class="text-blue-600"></iconify-icon>
                                <span class="text-xs font-semibold text-blue-800">{{ __('Articles qui pourraient vous aider :') }}</span>
                            </div>
                            <ul class="space-y-1.5">
                                @foreach ($kbSuggestions as $idx => $suggestion)
                                    <li class="rounded-lg transition-colors" :class="expanded === {{ $idx }} ? 'bg-blue-100/60' : ''">
                                        <button
                                            type="button"
                                            class="w-full flex items-center gap-2 text-xs text-blue-700 hover:text-blue-900 transition-colors px-2 py-1.5 text-left"
                                            @click="expanded = expanded === {{ $idx }} ? null : {{ $idx }}"
                                        >
                                            <iconify-icon
                                                :icon="expanded === {{ $idx }} ? 'solar:alt-arrow-down-linear' : 'solar:arrow-right-linear'"
                                                width="12"
                                                class="shrink-0"
                                            ></iconify-icon>
                                            <span class="font-medium">{{ $suggestion['title'] }}</span>
                                        </button>
                                        <div
                                            x-show="expanded === {{ $idx }}"
                                            x-collapse
                                            class="px-6 pb-2 text-xs text-blue-900/80 leading-relaxed"
                                        >
                                            {{ $suggestion['excerpt'] }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="overflow-visible rounded-2xl border border-[#E5E7EB] bg-white shadow-sm">
            <div class="px-4 sm:px-5 py-3 border-b border-[#E5E7EB] bg-[#F9FAFB] rounded-t-2xl">
                <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Description') }}</h2>
                <p class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('Ajoute des détails: étapes, impact, captures d\'écran, contexte…') }}</p>
            </div>

            <div class="p-4 sm:p-5">
                <x-input-label for="drawer_description" :value="__('Description *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                <textarea
                    id="drawer_description"
                    wire:model.defer="description"
                    @input="saveDraft()"
                    rows="5"
                    class="mt-1 {{ $textarea }}"
                    placeholder="{{ __('Donnez un maximum de détails (étapes, capture, contexte...)') }}"
                ></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        {{-- Pièces jointes --}}
        <div class="overflow-visible rounded-2xl border border-[#E5E7EB] bg-white shadow-sm">
            <div class="px-4 sm:px-5 py-3 border-b border-[#E5E7EB] bg-[#F9FAFB] rounded-t-2xl">
                <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Pièces jointes') }}</h2>
                <p class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('Ajoutez des fichiers ou des liens (optionnel).') }}</p>
            </div>

            <div class="grid gap-4 p-4 sm:p-5 md:grid-cols-2">
                {{-- Files --}}
                <div class="rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-3.5 sm:p-4">
                    <div class="text-[13px] font-semibold text-[#111827]">{{ __('Fichiers') }}</div>
                    <div class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('PDF, images, docs… (max 10MB / fichier, 5 fichiers).') }}</div>

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

                {{-- Links --}}
                <div class="rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-3.5 sm:p-4">
                    <div class="text-[13px] font-semibold text-[#111827]">{{ __('Liens') }}</div>
                    <div class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('Ajoutez un lien vers un drive, une page, une capture, etc.') }}</div>

                    <div class="mt-3 flex items-center gap-2">
                        <input
                            type="url"
                            class="flex-1 {{ $field }}"
                            placeholder="https://..."
                            wire:model.blur="linkUrl"
                        />
                        <button
                            type="button"
                            class="h-10 w-10 rounded-md border border-[#E5E7EB] bg-white text-[#6B7280] hover:text-[color:var(--accent)] hover:bg-[color:var(--accent-soft)] hover:border-[color:var(--accent-soft-2)] transition flex items-center justify-center"
                            wire:click="addLink"
                            wire:loading.attr="disabled"
                            wire:target="addLink"
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
    </div>
    </div>

    {{-- Footer sticky --}}
    <div class="sticky bottom-0 border-t border-[#E5E7EB] bg-white/95 px-3 py-3 backdrop-blur sm:px-5 lg:px-6">
        <div class="mx-auto flex w-full max-w-5xl flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button
                type="button"
                class="flex h-10 w-full items-center justify-center rounded-xl border border-[#E5E7EB] bg-white px-4 text-[13px] font-semibold text-[#111827] shadow-sm transition hover:bg-[#F9FAFB] sm:w-auto"
                wire:click="$dispatch('tickets:closeCreateDrawer')"
                wire:loading.attr="disabled"
                wire:target="submit"
            >
                {{ __('Annuler') }}
            </button>
            <x-manexo.action-button
                type="submit"
                wire-target="submit"
                variant="primary"
                spinner-size="sm"
                class="flex h-10 w-full sm:w-auto items-center justify-center gap-2 !rounded-xl bg-[color:var(--accent)] px-4 text-[13px] font-semibold hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]"
                :loading-label="__('ui.tickets.sending_ticket')"
            >
                <iconify-icon icon="solar:send-square-linear" width="16"></iconify-icon>
                {{ __('Envoyer') }}
            </x-manexo.action-button>
        </div>
    </div>
</form>
</div>
