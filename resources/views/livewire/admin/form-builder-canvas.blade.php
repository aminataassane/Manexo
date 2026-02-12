{{-- CENTER: Canvas --}}
<div class="flex-1 bg-slate-50 bg-dot-pattern flex flex-col relative overflow-y-auto custom-scrollbar p-8">
    <div class="w-full max-w-xl mx-auto space-y-4 pb-20">
        @php
            $selected = $fb_selected_template_id
                ? $formTemplates->firstWhere('id', (int) $fb_selected_template_id)
                : null;
            $selectedFields = $selected ? $selected->fields : collect();
        @endphp

        @if ($selected)
            <div class="bg-white border-t-4 border-slate-900 rounded-lg shadow-sm border-x border-b border-slate-200 p-6 mb-6">
                <h1 class="text-2xl font-serif text-slate-900 mb-2">{{ $selected->name }}</h1>
                <p class="text-sm text-slate-500">
                    {{ $selected->category?->name ?? __('Toutes catégories') }}
                    ·
                    {{ $selected->targetUser ? __('Réservé à :name', ['name' => $selected->targetUser->name]) : __('Public') }}
                </p>
            </div>

            @forelse ($selectedFields as $f)
                @php
                    $isActive = (int) $fb_selected_field_id === (int) $f->id;
                    $isRequired = (bool) $f->required;
                    $label = (string) $f->label;
                    $type = (string) $f->type;
                @endphp
                <button type="button" wire:click="selectField({{ $f->id }})"
                        class="group relative w-full text-left bg-white rounded-lg shadow-sm p-5 transition-all border border-slate-200 hover:border-slate-300 hover:shadow-md {{ $isActive ? 'ring-2 ring-slate-900 shadow-lg border-slate-900/10' : '' }}">
                    <div class="absolute -left-8 top-1/2 -translate-y-1/2 p-1 text-slate-300 hover:text-slate-500 cursor-grab opacity-0 group-hover:opacity-100 transition-opacity">
                        <iconify-icon icon="solar:menu-dots-linear" width="20"></iconify-icon>
                    </div>
                    <div class="pointer-events-none {{ $isActive ? '' : 'opacity-80' }}">
                        <label class="block text-sm font-medium {{ $isActive ? 'text-slate-900' : 'text-slate-700' }} mb-1.5">
                            {{ $label }} @if($isRequired) <span class="text-red-500">*</span> @endif
                        </label>
                        @if ($type === 'select')
                            <div class="relative">
                                <div class="block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-400 shadow-sm ring-1 ring-inset ring-slate-200 text-sm flex justify-between items-center">
                                    {{ __('Sélectionnez une option') }}
                                    <iconify-icon icon="solar:alt-arrow-down-linear" class="text-slate-400"></iconify-icon>
                                </div>
                            </div>
                        @elseif ($type === 'textarea')
                            <div class="block w-full rounded-md border-0 bg-slate-50 py-3 px-3 text-slate-400 shadow-sm ring-1 ring-inset ring-slate-200 text-sm">
                                {{ __('Votre texte...') }}
                            </div>
                        @elseif ($type === 'checkbox')
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-slate-300 bg-white"></span>
                                {{ __('Activer') }}
                            </div>
                        @else
                            <div class="block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-400 shadow-sm ring-1 ring-inset ring-slate-200 text-sm">
                                {{ __('Exemple...') }}
                            </div>
                        @endif
                    </div>
                </button>
            @empty
                <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 flex flex-col items-center justify-center text-slate-400 bg-slate-50/50 hover:bg-slate-100/50 hover:border-slate-400 transition-all">
                    <iconify-icon icon="solar:add-circle-linear" class="mb-2 text-2xl"></iconify-icon>
                    <span class="text-xs font-medium">{{ __('Ajoute un champ depuis la palette') }}</span>
                </div>
            @endforelse
        @else
            <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 flex flex-col items-center justify-center text-slate-400 bg-slate-50/50">
                <iconify-icon icon="solar:widget-2-linear" class="mb-2 text-2xl"></iconify-icon>
                <span class="text-xs font-medium">{{ __('Crée ou sélectionne un formulaire') }}</span>
            </div>
        @endif
    </div>
</div>
