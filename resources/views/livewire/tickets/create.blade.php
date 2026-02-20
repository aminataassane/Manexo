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

                            <div class="mt-4 space-y-4">
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
                                    @endphp

                                    @if ($type === 'section')
                                        <div class="pt-3 pb-1 border-t border-slate-100 first:border-t-0 first:pt-0">
                                            <div class="text-[13px] font-bold text-[#111827]">{{ $label }}</div>
                                            @if($helpText)
                                                <p class="mt-0.5 text-[12px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                        </div>
                                    @elseif ($type === 'textarea')
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            <textarea
                                                rows="4"
                                                wire:model="custom.{{ $key }}"
                                                class="mt-1 {{ $textarea }}"
                                                placeholder="{{ $placeholder }}"
                                            ></textarea>
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @elseif ($type === 'checkbox')
                                        <div>
                                            <label class="flex items-center justify-between gap-4 rounded-xl border border-[#E5E7EB] bg-white px-4 py-3">
                                                <div class="min-w-0">
                                                    <div class="text-[13px] font-semibold text-[#111827]">
                                                        {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                                    </div>
                                                    <div class="text-[12px] text-[#6B7280]">{{ __('Activer / désactiver') }}</div>
                                                </div>
                                                <input type="checkbox" wire:model="custom.{{ $key }}" class="h-5 w-5 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]" />
                                            </label>
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @elseif (in_array($type, ['select', 'radio'], true))
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            @if($type === 'radio')
                                                <div class="mt-1 space-y-2">
                                                    @foreach((array) $options as $opt)
                                                        <label class="flex items-center gap-3 rounded-xl border border-[#E5E7EB] bg-white px-3 py-2.5 hover:border-[var(--accent)] cursor-pointer transition-colors">
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
                                        <div>
                                            <label class="block text-[11px] font-medium text-slate-700">
                                                {{ $label }}@if($required) <span class="text-red-600">*</span>@endif
                                            </label>
                                            <input type="file" wire:model="custom.{{ $key }}"
                                                   class="mt-1 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
                                                   @if(!empty($config['accept'])) accept="{{ $config['accept'] }}" @endif>
                                            @if($helpText)
                                                <p class="mt-1 text-[11px] text-[#6B7280]">{{ $helpText }}</p>
                                            @endif
                                            <x-input-error :messages="$errors->get('custom.'.$key)" class="mt-2" />
                                        </div>
                                    @else
                                        <div>
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
                                                wire:model="custom.{{ $key }}"
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
