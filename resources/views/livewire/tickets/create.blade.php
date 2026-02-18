@php
    // Same input feel as login page (bg slate-50 + inset ring)
    $field = 'block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
    $select = $field . ' appearance-none pr-9';
    $textarea = 'block w-full rounded-md border-0 bg-slate-50 p-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
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
            <button
                type="submit"
                form="ticket-create-form"
                class="min-h-[44px] sm:min-h-0 h-10 px-4 text-white text-[13px] font-semibold rounded-xl shadow-sm transition-colors inline-flex items-center justify-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]"
            >
                <iconify-icon icon="solar:send-square-linear" width="16"></iconify-icon>
                {{ __('Envoyer') }}
            </button>
        </div>
    </div>

    <form id="ticket-create-form" wire:submit="submit" class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
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
                                <x-select-input id="category" wire:model="ticket_category_id">
                                    @foreach ($categories as $c)
                                        <option value="{{ (int) $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priorité *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                            <div class="mt-1">
                                <x-select-input id="priority" wire:model="ticket_priority_id">
                                    @foreach ($priorities as $p)
                                        <option value="{{ (int) $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <x-input-error :messages="$errors->get('ticket_priority_id')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="subject" :value="__('Sujet *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <input
                            id="subject"
                            type="text"
                            wire:model="subject"
                            required
                            class="mt-1 {{ $field }}"
                            placeholder="{{ __('Ex: Erreur 500 sur le checkout') }}"
                        >
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description *')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <textarea
                            id="description"
                            wire:model="description"
                            rows="8"
                            class="mt-1 {{ $textarea }}"
                            placeholder="{{ __('Donnez un maximum de détails (étapes, capture, contexte...)') }}"
                        ></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    @php
                        $steps = $formSteps ?? collect();
                        if ($steps->count() === 0 && (($formFields ?? collect())->count() > 0)) {
                            $steps = collect([(object) [
                                'id' => 0,
                                'number' => 1,
                                'title' => __('Informations supplémentaires'),
                                'description' => null,
                                'fields' => $formFields ?? collect(),
                            ]]);
                        }
                    @endphp

                    @if ($steps->count())
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

                            <div class="mt-4 space-y-5">
                                @foreach ($steps as $step)
                                    @php
                                        $fields = $step->fields ?? collect();
                                    @endphp

                                    <div class="rounded-2xl border border-[#E5E7EB] bg-[#F9FAFB] p-4 sm:p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-mono text-slate-400">#{{ (int) ($step->number ?? 1) }}</span>
                                                    <div class="text-[13px] font-semibold text-[#111827] truncate">{{ (string) ($step->title ?? __('Étape')) }}</div>
                                                </div>
                                                @if (!empty($step->description))
                                                    <div class="mt-1 text-[12px] text-[#6B7280]">{{ $step->description }}</div>
                                                @endif
                                            </div>
                                            <span class="shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full border border-[#E5E7EB] bg-white text-[#6B7280]">
                                                {{ (int) ($fields->count() ?? 0) }} {{ __('champ(s)') }}
                                            </span>
                                        </div>

                                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                            @foreach ($fields as $f)
                                                @php
                                                    $key = (string) $f->key;
                                                    $type = (string) $f->type;
                                                    $label = (string) $f->label;
                                                    $required = (bool) $f->required;
                                                @endphp

                                                @if ($type === 'textarea')
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-[11px] font-medium text-slate-700">
                                                            {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                                        </label>
                                                        <textarea
                                                            rows="4"
                                                            wire:model="custom.{{ $key }}"
                                                            class="mt-1 {{ $textarea }}"
                                                            placeholder="{{ (string) ($f->placeholder ?? '') }}"
                                                        ></textarea>
                                                        @if(!empty($f->help_text))
                                                            <p class="mt-1 text-[11px] text-[#6B7280]">{{ $f->help_text }}</p>
                                                        @endif
                                                        <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                                    </div>
                                                @elseif ($type === 'checkbox')
                                                    <div class="sm:col-span-2">
                                                        <label class="flex items-center justify-between gap-4 rounded-xl border border-[#E5E7EB] bg-white px-4 py-3">
                                                            <div class="min-w-0">
                                                                <div class="text-[13px] font-semibold text-[#111827]">
                                                                    {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                                                </div>
                                                                <div class="text-[12px] text-[#6B7280]">{{ __('Activer / désactiver') }}</div>
                                                            </div>
                                                            <input type="checkbox" wire:model="custom.{{ $key }}" class="h-5 w-5 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]" />
                                                        </label>
                                                        @if(!empty($f->help_text))
                                                            <p class="mt-1 text-[11px] text-[#6B7280]">{{ $f->help_text }}</p>
                                                        @endif
                                                        <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                                    </div>
                                                @elseif ($type === 'select')
                                                    <div>
                                                        <label class="block text-[11px] font-medium text-slate-700">
                                                            {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                                        </label>
                                                        <div class="mt-1">
                                                            <x-select-input wire:model="custom.{{ $key }}">
                                                                <option value="">{{ (string) ($f->placeholder ?? '—') }}</option>
                                                                @foreach ((array) ($f->options ?? []) as $opt)
                                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                                @endforeach
                                                            </x-select-input>
                                                        </div>
                                                        @if(!empty($f->help_text))
                                                            <p class="mt-1 text-[11px] text-[#6B7280]">{{ $f->help_text }}</p>
                                                        @endif
                                                        <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                                    </div>
                                                @else
                                                    <div>
                                                        <label class="block text-[11px] font-medium text-slate-700">
                                                            {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                                        </label>
                                                        <input
                                                            type="{{ $type === 'email' ? 'email' : ($type === 'number' ? 'number' : ($type === 'date' ? 'date' : 'text')) }}"
                                                            wire:model="custom.{{ $key }}"
                                                            class="mt-1 {{ $field }}"
                                                            placeholder="{{ (string) ($f->placeholder ?? '') }}"
                                                        >
                                                        @if(!empty($f->help_text))
                                                            <p class="mt-1 text-[11px] text-[#6B7280]">{{ $f->help_text }}</p>
                                                        @endif
                                                        <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
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
                                                    wire:model="checklistItems.{{ $idx }}.title"
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
                                                    wire:model="checklistItems.{{ $idx }}.due_date"
                                                    class="{{ $field }}"
                                                />
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" class="cursor-pointer h-8 w-8 rounded-lg border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-white hover:text-[color:var(--accent)] transition flex items-center justify-center" wire:click="moveChecklistItemUp({{ $idx }})" title="{{ __('Monter') }}" @if($idx === 0) disabled @endif>
                                                <iconify-icon icon="solar:alt-arrow-up-linear" width="14"></iconify-icon>
                                            </button>
                                            <button type="button" class="cursor-pointer h-8 w-8 rounded-lg border border-[#E5E7EB] bg-white text-[#6B7280] hover:bg-white hover:text-[color:var(--accent)] transition flex items-center justify-center" wire:click="moveChecklistItemDown({{ $idx }})" title="{{ __('Descendre') }}" @if($idx === count($checklistItems) - 1) disabled @endif>
                                                <iconify-icon icon="solar:alt-arrow-down-linear" width="14"></iconify-icon>
                                            </button>
                                            <button type="button" class="cursor-pointer h-8 w-8 rounded-lg border border-[#E5E7EB] bg-white text-red-600 hover:bg-red-50 transition flex items-center justify-center" wire:click="removeChecklistItem({{ $idx }})" title="{{ __('Supprimer') }}">
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
                                        <button type="button" class="h-8 w-8 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center" wire:click="removeFile({{ $i }})" title="{{ __('Supprimer') }}">
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
                                        <button type="button" class="h-8 w-8 rounded-md border border-[#E5E7EB] bg-white hover:bg-red-50 hover:border-red-200 text-[#6B7280] hover:text-red-700 transition flex items-center justify-center" wire:click="removeLink({{ $i }})" title="{{ __('Supprimer') }}">
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
            <div class="rounded-2xl border border-[#E5E7EB] bg-white shadow-sm overflow-visible">
                <div class="px-4 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">{{ __('Attribution') }}</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">{{ __('Optionnel: assignez dès la création.') }}</p>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <x-input-label for="assigned_to" :value="__('Assigné à')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                        <div class="mt-1">
                            <x-select-input id="assigned_to" wire:model="assigned_to">
                                <option value="">{{ __('—') }}</option>
                                @foreach ($assignees as $u)
                                    <option value="{{ (int) $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                    </div>
                </div>
            </div>

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
                                wire:model="start_date"
                                class="mt-1 {{ $field }}"
                            />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="due_date" :value="__('Deadline')" class="text-[#111827] block text-[11px] font-medium text-slate-700" />
                            <input
                                id="due_date"
                                type="date"
                                wire:model="due_date"
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
</div>
