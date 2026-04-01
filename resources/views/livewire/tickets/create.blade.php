@php
    // Same input feel as login page (bg slate-50 + inset ring)
    $field = 'block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
    $select = $field . ' appearance-none pr-9';
    $textarea = 'block w-full rounded-md border-0 bg-slate-50 p-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';

    $ticketCreateCategoryOptions = $categories->map(fn ($c) => ['value' => (string) $c->id, 'label' => $c->name])->all();
    $ticketCreateCategoryLabel = $categories->firstWhere('id', (int) $ticket_category_id)?->name ?? '';
    $ticketCreatePriorityOptions = $priorities->map(fn ($p) => ['value' => (string) $p->id, 'label' => $p->name])->all();
    $ticketCreatePriorityLabel = $priorities->firstWhere('id', (int) $ticket_priority_id)?->name ?? '';
    $ticketCreateGroupOptions = [['value' => '', 'label' => __('— Aucun groupe')]];
    foreach ($ticketGroups ?? collect() as $tg) {
        $ticketCreateGroupOptions[] = ['value' => (string) $tg->id, 'label' => $tg->name];
    }
    $ticketCreateGroupLabel = $ticket_group_id
        ? (($ticketGroups ?? collect())->firstWhere('id', (int) $ticket_group_id)?->name ?? '')
        : __('— Aucun groupe');
@endphp

<div class="mx-auto w-full min-w-0 max-w-7xl 2xl:max-w-[90rem] min-[1920px]:max-w-[110rem] py-4 sm:py-6 lg:py-8 px-3 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-xl font-semibold text-[#111827] tracking-tight sm:text-2xl">{{ __('Créer un ticket') }}</h1>
            <p class="mt-1 text-xs sm:text-sm text-[#6B7280]">{{ __('Créez une demande complète avec dates, assignation et pièces jointes.') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:flex-nowrap sm:flex-shrink-0">
            <a
                href="{{ route('tickets.index') }}"
                class="min-h-[44px] sm:min-h-0 h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-semibold rounded-xl shadow-sm hover:bg-[#F9FAFB] transition inline-flex items-center justify-center gap-2"
            >
                <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
                {{ __('Retour') }}
            </a>
            <x-manexo.action-button
                type="submit"
                form="ticket-create-form"
                wire-target="submit"
                variant="primary"
                spinner-size="sm"
                class="min-h-[44px] sm:min-h-0 h-10 px-4 text-[13px] font-semibold !rounded-xl bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]"
                :loading-label="__('ui.tickets.sending_ticket')"
            >
                <iconify-icon icon="solar:send-square-linear" width="16"></iconify-icon>
                {{ __('Envoyer') }}
            </x-manexo.action-button>
        </div>
    </div>

    <form id="ticket-create-form" wire:submit="submit" class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6"
        x-data="{
            draftKey: 'ticket-draft-create-page',
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
        x-on:submit="localStorage.removeItem('ticket-draft-create-page')"
    >
        <!-- LEFT (main form) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-[#E5E7EB] bg-white shadow-sm overflow-visible">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Détails') }}</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Les champs marqués * sont obligatoires.') }}</p>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="category" :value="__('Catégorie *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                            <div class="mt-1">
                                <x-select-input
                                    id="category"
                                    :options="$ticketCreateCategoryOptions"
                                    :label="$ticketCreateCategoryLabel"
                                    :selected-value="(string) $ticket_category_id"
                                    wire:model="ticket_category_id"
                                    wire:loading.attr="disabled"
                                    wire:target="ticket_category_id"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priorité *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                            <div class="mt-1">
                            <x-select-input
                                    id="priority"
                                    :options="$ticketCreatePriorityOptions"
                                    :label="$ticketCreatePriorityLabel"
                                    :selected-value="(string) $ticket_priority_id"
                                    wire:model="ticket_priority_id"
                                wire:loading.attr="disabled"
                                wire:target="ticket_priority_id"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('ticket_priority_id')" class="mt-2" />
                        </div>
                    </div>

                    @if(($ticketGroups ?? collect())->isNotEmpty())
                    <div>
                        <x-input-label for="ticket_group" :value="__('Groupe')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <div class="mt-1">
                            <x-select-input
                                id="ticket_group"
                                :options="$ticketCreateGroupOptions"
                                :label="$ticketCreateGroupLabel"
                                :selected-value="$ticket_group_id !== null ? (string) $ticket_group_id : ''"
                                wire:model="ticket_group_id"
                                wire:loading.attr="disabled"
                                wire:target="ticket_group_id"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('ticket_group_id')" class="mt-2" />
                    </div>
                    @endif

                    <div>
                        <x-input-label for="subject" :value="__('Sujet *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <input
                            id="subject"
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

                        {{-- Browse KB button --}}
                        <div class="mt-2">
                            <button
                                type="button"
                                wire:click="openKbBrowser"
                                wire:loading.attr="disabled"
                                wire:target="openKbBrowser"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-[color:var(--accent)] hover:underline transition-colors"
                            >
                                <iconify-icon icon="solar:book-2-linear" width="14"></iconify-icon>
                                {{ __('Parcourir la base de connaissance') }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <textarea
                            id="description"
                            wire:model.defer="description"
                            @input="saveDraft()"
                            rows="8"
                            class="mt-1 {{ $textarea }}"
                            placeholder="{{ __('Donnez un maximum de détails (étapes, capture, contexte...)') }}"
                        ></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    @php
                        $allFields = $formFields ?? collect();
                    @endphp

                    @if ($allFields->count())
                        <div class="pt-4 border-t border-slate-100">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-[13px] font-semibold text-[#111827]">
                                        {{ __('Informations supplémentaires') }}
                                        @if (!empty($formTemplateName))
                                            <span class="ml-2 text-[11px] font-semibold px-2 py-0.5 rounded-full border border-[#E5E7EB] bg-white text-[#6B7280]">{{ $formTemplateName }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-[12px] text-[#6B7280]">{{ __('Ces champs dépendent de la catégorie / du client.') }}</div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-6 gap-4">
                                @foreach ($allFields->sortBy('sort_order') as $f)
                                    @php
                                        $key = (string) $f->key;
                                        $type = (string) $f->type;
                                        $label = (string) $f->label;
                                        $required = (bool) $f->required;
                                        $config = is_array($f->configuration) ? $f->configuration : [];
                                        $placeholder = $config['placeholder'] ?? $f->placeholder ?? '';
                                        $helpText = $config['help_text'] ?? $f->help_text ?? '';
                                        $options = $config['options'] ?? $f->options ?? [];
                                        $layout = $config['layout'] ?? 'full';
                                        $colSpan = match($layout) {
                                            'half' => 'col-span-6 sm:col-span-3',
                                            'third' => 'col-span-6 sm:col-span-2',
                                            default => 'col-span-6',
                                        };
                                    @endphp

                                    @if ($type === 'section')
                                        <div class="col-span-6 pt-3 pb-1 border-t border-slate-100 first:border-t-0 first:pt-0">
                                            <div class="text-[13px] font-bold text-[#111827]">{{ $label }}</div>
                                            @if($helpText)
                                                <p class="mt-0.5 text-[12px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                        </div>
                                    @elseif ($type === 'textarea')
                                        <div class="{{ $colSpan }}">
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            <textarea
                                                rows="4"
                                                wire:model.blur="custom.{{ $key }}"
                                                class="mt-1 {{ $textarea }}"
                                                placeholder="{{ $placeholder }}"
                                            ></textarea>
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @elseif ($type === 'checkbox')
                                        @php
                                            $displayMode = $config['display_mode'] ?? 'list';
                                            $optionsWrapClass = match($displayMode) {
                                                'inline' => 'mt-1 flex flex-wrap gap-2',
                                                'grid' => 'mt-1 grid grid-cols-2 gap-2',
                                                'card' => 'mt-1 space-y-2',
                                                default => 'mt-1 space-y-2',
                                            };
                                            $optionItemClass = match($displayMode) {
                                                'card' => 'flex items-center gap-3 rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3.5 hover:border-[var(--accent)] cursor-pointer transition-colors shadow-sm',
                                                default => 'flex items-center gap-3 rounded-xl border border-[#E5E7EB] bg-white px-3 py-2.5 hover:border-[var(--accent)] cursor-pointer transition-colors',
                                            };
                                        @endphp
                                        <div class="{{ $colSpan }}">
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            @if(count((array) $options) > 0)
                                                <div class="{{ $optionsWrapClass }}">
                                                    @foreach((array) $options as $opt)
                                                        <label class="{{ $optionItemClass }}">
                                                            <input type="checkbox" wire:model="custom.{{ $key }}" value="{{ $opt }}"
                                                                   class="text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]">
                                                            <span class="text-sm text-slate-700">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <label class="flex items-center justify-between gap-4 rounded-xl border border-[#E5E7EB] bg-white px-4 py-3 mt-1">
                                                    <div class="min-w-0">
                                                        <div class="text-[12px] text-[#6B7280]">{{ __('Activer / désactiver') }}</div>
                                                    </div>
                                                    <input type="checkbox" wire:model="custom.{{ $key }}" class="h-5 w-5 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]" />
                                                </label>
                                            @endif
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @elseif (in_array($type, ['select', 'radio'], true))
                                        @php
                                            $displayMode = $config['display_mode'] ?? 'list';
                                            $radioWrapClass = match($displayMode) {
                                                'inline' => 'mt-1 flex flex-wrap gap-2',
                                                'grid' => 'mt-1 grid grid-cols-2 gap-2',
                                                'card' => 'mt-1 space-y-2',
                                                default => 'mt-1 space-y-2',
                                            };
                                            $radioItemClass = match($displayMode) {
                                                'card' => 'flex items-center gap-3 rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3.5 hover:border-[var(--accent)] cursor-pointer transition-colors shadow-sm',
                                                default => 'flex items-center gap-3 rounded-xl border border-[#E5E7EB] bg-white px-3 py-2.5 hover:border-[var(--accent)] cursor-pointer transition-colors',
                                            };
                                        @endphp
                                        <div class="{{ $colSpan }}">
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            @if($type === 'radio')
                                                <div class="{{ $radioWrapClass }}">
                                                    @foreach((array) $options as $opt)
                                                        <label class="{{ $radioItemClass }}">
                                                            <input type="radio" wire:model="custom.{{ $key }}" value="{{ $opt }}"
                                                                   class="text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]">
                                                            <span class="text-sm text-slate-700">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="mt-1">
                                                    <x-select-input wire:model="custom.{{ $key }}">
                                                        <option value="">{{ $placeholder ?: '—' }}</option>
                                                        @foreach ((array) $options as $opt)
                                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                                        @endforeach
                                                    </x-select-input>
                                                </div>
                                            @endif
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @elseif ($type === 'file')
                                        <div class="{{ $colSpan }}" x-data="{ dragging: false }">
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            <label
                                                for="custom_file_{{ $key }}"
                                                class="mt-1 flex flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed px-4 py-5 cursor-pointer transition-colors"
                                                :class="dragging ? 'border-[var(--accent)] bg-[color-mix(in_srgb,var(--accent)_6%,white)]' : 'border-[#D1D5DB] bg-[#F9FAFB] hover:border-[var(--accent)] hover:bg-white'"
                                                x-on:dragover.prevent="dragging = true"
                                                x-on:dragleave.prevent="dragging = false"
                                                x-on:drop.prevent="dragging = false; $refs.fileInput_{{ $key }}.files = $event.dataTransfer.files; $refs.fileInput_{{ $key }}.dispatchEvent(new Event('change', { bubbles: true }))"
                                            >
                                                <div class="h-9 w-9 rounded-full flex items-center justify-center transition-colors"
                                                     :class="dragging ? 'bg-[color-mix(in_srgb,var(--accent)_15%,white)] text-[var(--accent)]' : 'bg-[#E5E7EB]/60 text-[#6B7280]'">
                                                    <iconify-icon icon="solar:upload-linear" width="20"></iconify-icon>
                                                </div>
                                                <span class="text-[12px] font-medium text-[#111827]">{{ __('Cliquer ou glisser un fichier') }}</span>
                                                @if($helpText)
                                                    <span class="text-[11px] text-[#6B7280]">{{ $helpText }}</span>
                                                @elseif(!empty($config['accept']))
                                                    <span class="text-[11px] text-[#6B7280]">{{ $config['accept'] }}</span>
                                                @endif
                                            </label>
                                            <input
                                                id="custom_file_{{ $key }}"
                                                x-ref="fileInput_{{ $key }}"
                                                type="file"
                                                wire:model="custom.{{ $key }}"
                                                class="hidden"
                                                @if(!empty($config['accept'])) accept="{{ $config['accept'] }}" @endif
                                            />

                                            @if(!empty($this->custom[$key]))
                                                @php $uploadedFile = $this->custom[$key]; @endphp
                                                <div class="mt-2 flex items-center justify-between gap-3 rounded-lg border border-[#E5E7EB] bg-white px-3 py-2">
                                                    <div class="min-w-0 flex items-center gap-2">
                                                        <div class="h-7 w-7 rounded-md border border-[#E5E7EB] bg-[#F9FAFB] flex items-center justify-center text-[#6B7280] shrink-0">
                                                            <iconify-icon icon="solar:file-linear" width="14"></iconify-icon>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="text-[12px] font-semibold text-[#111827] truncate">
                                                                {{ method_exists($uploadedFile, 'getClientOriginalName') ? $uploadedFile->getClientOriginalName() : __('Fichier') }}
                                                            </div>
                                                            <div class="text-[11px] text-[#6B7280]">
                                                                @if(method_exists($uploadedFile, 'getSize') && $uploadedFile->getSize())
                                                                    {{ number_format($uploadedFile->getSize() / 1024, 0) }} KB
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                            class="h-7 w-7 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center"
                                                            wire:click="$set('custom.{{ $key }}', null)">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="14"></iconify-icon>
                                                    </button>
                                                </div>
                                            @endif

                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @else
                                        <div class="{{ $colSpan }}">
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            @php
                                                $inputType = match($type) {
                                                    'email' => 'email',
                                                    'number' => 'number',
                                                    'date' => 'date',
                                                    'datetime' => 'datetime-local',
                                                    default => 'text',
                                                };
                                            @endphp
                                            <input
                                                type="{{ $inputType }}"
                                                wire:model.blur="custom.{{ $key }}"
                                                class="mt-1 {{ $field }}"
                                                placeholder="{{ $placeholder }}"
                                            >
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Checklist (plan d'exécution) --}}
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Checklist') }}</h2>
                                <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Optionnel : définissez les étapes à réaliser (ordre, responsable, échéance).') }}</p>
                            </div>
                            <button
                                type="button"
                                wire:click="addChecklistItem"
                                wire:loading.attr="disabled"
                                wire:target="addChecklistItem"
                                class="cursor-pointer h-9 px-3 rounded-lg border border-[#E5E7EB] bg-white text-[13px] font-medium text-[#111827] hover:bg-[#F9FAFB] transition inline-flex items-center gap-1.5"
                            >
                                <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                                {{ __('Ajouter un élément') }}
                            </button>
                        </div>
                        @if (count($checklistItems) > 0)
                            <div class="mt-4 space-y-3">
                                @foreach ($checklistItems as $idx => $item)
                                    <div class="flex flex-wrap items-start gap-2 rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-3" wire:key="checklist-{{ $idx }}">
                                        <div class="flex-1 min-w-0 grid gap-2 sm:grid-cols-1 md:grid-cols-3">
                                            <div class="md:col-span-2">
                                                <input
                                                    type="text"
                                                    wire:model.blur="checklistItems.{{ $idx }}.title"
                                                    class="{{ $field }}"
                                                    placeholder="{{ __('Intitulé de l’étape') }}"
                                                />
                                            </div>
                                            <div>
                                                <x-select-input wire:model="checklistItems.{{ $idx }}.assigned_to">
                                                    <option value="">{{ __('— Responsable') }}</option>
                                                    @foreach ($assignees as $u)
                                                        <option value="{{ (int) $u->id }}">{{ $u->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            </div>
                                            <div class="md:col-span-2">
                                                <input
                                                    type="date"
                                                    wire:model.blur="checklistItems.{{ $idx }}.due_date"
                                                    class="{{ $field }}"
                                                />
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" class="cursor-pointer h-8 w-8 rounded-lg border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-white hover:text-[color:var(--accent)] transition flex items-center justify-center" wire:click="moveChecklistItemUp({{ $idx }})" wire:loading.attr="disabled" wire:target="moveChecklistItemUp({{ $idx }})" title="{{ __('Monter') }}" @if($idx === 0) disabled @endif>
                                                <iconify-icon icon="solar:alt-arrow-up-linear" width="14"></iconify-icon>
                                            </button>
                                            <button type="button" class="cursor-pointer h-8 w-8 rounded-lg border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-white hover:text-[color:var(--accent)] transition flex items-center justify-center" wire:click="moveChecklistItemDown({{ $idx }})" wire:loading.attr="disabled" wire:target="moveChecklistItemDown({{ $idx }})" title="{{ __('Descendre') }}" @if($idx === count($checklistItems) - 1) disabled @endif>
                                                <iconify-icon icon="solar:alt-arrow-down-linear" width="14"></iconify-icon>
                                            </button>
                                            <button type="button" class="cursor-pointer h-8 w-8 rounded-lg border border-[#E5E7EB] bg-white text-red-600 hover:bg-red-50 transition flex items-center justify-center" wire:click="removeChecklistItem({{ $idx }})" wire:loading.attr="disabled" wire:target="removeChecklistItem({{ $idx }})" title="{{ __('Supprimer') }}">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="rounded-2xl border border-[#E5E7EB] bg-white shadow-sm overflow-visible">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Pièces jointes') }}</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Ajoutez des fichiers ou des liens (optionnel).') }}</p>
                </div>

                <div class="p-4 sm:p-6 grid gap-4 lg:grid-cols-2">
                    <!-- Files -->
                    <div class="rounded-xl border border-[#E5E7EB] bg-[#F9FAFB] p-4">
                        <div class="text-[13px] font-semibold text-[#111827]">{{ __('Fichiers') }}</div>
                        <div class="mt-0.5 text-[12px] text-[#6B7280]">{{ __('PDF, images, docs… (max 10MB / fichier, 5 fichiers).') }}</div>

                        <label
                            for="files"
                            class="mt-3 flex items-center justify-center gap-2 rounded-lg border border-dashed border-[#D1D5DB] bg-white px-3 py-4 text-[13px] font-medium text-[#111827] hover:bg-[#F9FAFB] transition cursor-pointer"
                        >
                            <iconify-icon icon="solar:upload-linear" width="18" class="text-[#6B7280]"></iconify-icon>
                            {{ __('Cliquer pour ajouter des fichiers') }}
                        </label>
                        <input id="files" type="file" class="hidden" multiple accept=".pdf,.png,.jpg,.jpeg,.webp,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" wire:model="files" />

                        <div class="mt-2">
                            <x-input-error :messages="$errors->get('files')" />
                            <x-input-error :messages="$errors->get('files.*')" />
                        </div>

                        <div class="mt-2 text-[11px] text-[#9CA3AF]" wire:loading wire:target="files">
                            {{ __('Téléversement en cours…') }}
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
                                        <button type="button" class="h-8 w-8 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center" wire:click="removeFile({{ $i }})" wire:loading.attr="disabled" wire:target="removeFile({{ $i }})" title="{{ __('Supprimer') }}">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="16"></iconify-icon>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                                        <button type="button" class="h-8 w-8 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center" wire:click="removeLink({{ $i }})" wire:loading.attr="disabled" wire:target="removeLink({{ $i }})" title="{{ __('Supprimer') }}">
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

        <!-- RIGHT (side panels) -->
        <div class="lg:col-span-1 space-y-6">
            @if($canAssignAtCreate ?? false)
            <div class="rounded-2xl border border-[#E5E7EB] bg-white shadow-sm overflow-visible">
                <div class="px-4 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Attribution') }}</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Optionnel: assignez dès la création (Admin/Agent).') }}</p>
                </div>
                <div class="p-4 space-y-4">
                    <div x-data="{
                        open: false,
                        search: '',
                        get filtered() {
                            const s = this.search.toLowerCase();
                            return this.$refs.userList ? Array.from(this.$refs.userList.querySelectorAll('[data-user]')).forEach(el => {
                                el.style.display = el.dataset.name.toLowerCase().includes(s) ? '' : 'none';
                            }) : null;
                        }
                    }">
                        <x-input-label :value="__('Assignés')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />

                        {{-- Selected chips --}}
                        <div class="mt-1 flex flex-wrap gap-1.5 min-h-[2.5rem] p-2 rounded-md border border-slate-200 bg-slate-50 cursor-pointer" @click="open = !open">
                            @forelse($assignees->whereIn('id', $assigned_to_ids) as $sel)
                                <span class="inline-flex items-center gap-1 pl-2 pr-1 py-1 rounded-md text-xs font-semibold bg-[var(--accent-soft)] text-[var(--accent)]">
                                    {{ $sel->name }}
                                    <button type="button" wire:click="$set('assigned_to_ids', {{ json_encode(array_values(array_diff($assigned_to_ids, [$sel->id]))) }})" class="ml-0.5 h-4 w-4 rounded-full hover:bg-[var(--accent)] hover:text-white flex items-center justify-center transition-colors" @click.stop>
                                        <iconify-icon icon="solar:close-circle-linear" width="12"></iconify-icon>
                                    </button>
                                </span>
                            @empty
                                <span class="text-sm text-slate-400 py-0.5">{{ __('— Cliquez pour assigner') }}</span>
                            @endforelse
                        </div>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.outside="open = false" x-cloak class="relative z-30">
                            <div class="absolute left-0 right-0 top-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-hidden">
                                <div class="p-2 border-b border-slate-100">
                                    <input type="text" x-model="search" @input="filtered" class="w-full h-8 px-2 rounded-lg border border-slate-200 bg-slate-50 text-sm placeholder:text-slate-400 focus:ring-1 focus:ring-[var(--accent)] focus:border-transparent" placeholder="{{ __('Rechercher...') }}" />
                                </div>
                                <div class="overflow-y-auto max-h-44 p-1" x-ref="userList">
                                    @foreach ($assignees as $u)
                                        @php $isSelected = in_array($u->id, $assigned_to_ids); @endphp
                                        <label data-user data-name="{{ $u->name }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg cursor-pointer transition-colors {{ $isSelected ? 'bg-[var(--accent-soft)]' : 'hover:bg-slate-50' }}">
                                            <input
                                                type="checkbox"
                                                value="{{ $u->id }}"
                                                wire:model="assigned_to_ids"
                                                wire:loading.attr="disabled"
                                                wire:target="assigned_to_ids"
                                                class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]"
                                            />
                                            <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                                                {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-slate-900 truncate">{{ $u->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('assigned_to_ids')" class="mt-2" />
                    </div>
                </div>
            </div>
            @endif

            <div class="rounded-2xl border border-[#E5E7EB] bg-white shadow-sm overflow-visible">
                <div class="px-4 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Dates') }}</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Optionnel: planifiez le traitement.') }}</p>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                        <div>
                            <x-input-label for="start_date" :value="__('Start date')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                            <input
                                id="start_date"
                                type="date"
                                wire:model.blur="start_date"
                                class="mt-1 {{ $field }}"
                            />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="due_date" :value="__('Deadline')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                            <input
                                id="due_date"
                                type="date"
                                wire:model.blur="due_date"
                                class="mt-1 {{ $field }}"
                            />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-[#E5E7EB] bg-white shadow-sm overflow-visible">
                <div class="px-4 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Aide') }}</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Conseils pour une demande claire.') }}</p>
                </div>
                <div class="p-4 text-[12px] text-[#6B7280] space-y-3">
                    <div class="flex gap-2">
                        <iconify-icon icon="solar:check-circle-linear" width="16"></iconify-icon>
                        <p>{{ __('Mettez un sujet court + précis.') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <iconify-icon icon="solar:paperclip-linear" width="16"></iconify-icon>
                        <p>{{ __('Ajoutez une capture ou un lien si possible.') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <iconify-icon icon="solar:calendar-search-linear" width="16"></iconify-icon>
                        <p>{{ __('Définissez une deadline si c’est urgent.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- KB Browser Modal --}}
    @if ($showKbBrowser)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ viewArticle: null }">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showKbBrowser', false)"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200 w-full max-w-2xl max-h-[85vh] flex flex-col">
                {{-- Header --}}
                <div class="shrink-0 px-6 py-4 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <iconify-icon icon="solar:book-2-bold-duotone" width="20"></iconify-icon>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ __('Base de connaissance') }}</h3>
                            <p class="text-xs text-slate-500">{{ __('Recherchez un article avant de creer un ticket.') }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showKbBrowser', false)" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                        <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                    </button>
                </div>

                {{-- Search --}}
                <div class="shrink-0 px-6 py-3 border-b border-slate-100">
                    <div class="relative">
                        <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="16"></iconify-icon>
                        <input
                            type="text"
                            wire:model.live.debounce.600ms="kbSearchTerm"
                            wire:keydown.enter="searchKbArticles"
                            class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-all"
                            placeholder="{{ __('Rechercher dans les articles...') }}"
                            autofocus
                        />
                    </div>
                </div>

                {{-- Results --}}
                <div class="flex-1 overflow-y-auto p-4">
                    @if (empty($kbSearchResults) && mb_strlen(trim($kbSearchTerm)) >= 2)
                        <div class="py-8 text-center" wire:loading.remove wire:target="searchKbArticles">
                            <iconify-icon icon="solar:document-text-linear" width="32" class="text-slate-300"></iconify-icon>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Aucun article trouve.') }}</p>
                        </div>
                    @elseif (empty($kbSearchResults))
                        <div class="py-8 text-center">
                            <iconify-icon icon="solar:magnifer-linear" width="32" class="text-slate-300"></iconify-icon>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Tapez un mot-cle pour rechercher.') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($kbSearchResults as $kIdx => $result)
                                <div class="rounded-xl border border-slate-200 hover:border-slate-300 transition-colors overflow-hidden">
                                    <button
                                        type="button"
                                        class="w-full flex items-start gap-3 px-4 py-3 text-left"
                                        @click="viewArticle = viewArticle === {{ $kIdx }} ? null : {{ $kIdx }}"
                                    >
                                        <iconify-icon
                                            :icon="viewArticle === {{ $kIdx }} ? 'solar:alt-arrow-down-bold' : 'solar:alt-arrow-right-bold'"
                                            width="14"
                                            class="text-slate-400 mt-0.5 shrink-0"
                                        ></iconify-icon>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-semibold text-slate-900">{{ $result['title'] }}</div>
                                            <p class="mt-0.5 text-xs text-slate-500 line-clamp-2" x-show="viewArticle !== {{ $kIdx }}">{{ $result['excerpt'] }}</p>
                                        </div>
                                        <span class="shrink-0 inline-flex items-center gap-1 text-[10px] text-slate-400 mt-0.5">
                                            <iconify-icon icon="solar:eye-linear" width="11"></iconify-icon>
                                            {{ $result['view_count'] }}
                                        </span>
                                    </button>
                                    <div
                                        x-show="viewArticle === {{ $kIdx }}"
                                        x-collapse
                                        class="px-4 pb-4 border-t border-slate-100"
                                    >
                                        <div class="pt-3 prose prose-sm prose-slate max-w-none text-sm text-slate-700 [&_h2]:text-base [&_h2]:font-bold [&_h3]:text-sm [&_h3]:font-bold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_blockquote]:border-l-4 [&_blockquote]:border-slate-300 [&_blockquote]:pl-4 [&_blockquote]:italic">
                                            {!! $result['safeHtml'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="py-4 text-center" wire:loading wire:target="searchKbArticles">
                        <iconify-icon icon="solar:refresh-circle-bold-duotone" width="24" class="text-slate-400 animate-spin"></iconify-icon>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="shrink-0 px-6 py-3 border-t border-slate-100 bg-slate-50/30 rounded-b-2xl flex justify-end">
                    <button
                        type="button"
                        wire:click="$set('showKbBrowser', false)"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all"
                    >
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
    {{-- end KB browser --}}
</div>
<div class="fixed inset-0 z-40 pointer-events-none" wire:loading.flex wire:target="submit">
    <div class="absolute inset-0 bg-slate-900/20"></div>
</div>
