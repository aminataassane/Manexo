<div class="builder-full-bleed h-builder flex flex-col min-w-0 bg-white overflow-hidden" x-data="{ mobileDetailOpen: false }">
    <!-- HEADER -->
    <div class="shrink-0 px-4 sm:px-5 lg:px-6 py-3 bg-white border-b border-slate-200/60">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('admin.forms') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors shrink-0">
                    <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                </a>
                <div class="min-w-0">
                    <h1 class="text-sm font-semibold text-slate-900 truncate">{{ $form->name }}</h1>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ trans_choice('forms_builder.response_count', $this->responses->total(), ['count' => $this->responses->total()]) }}
                    </p>
                </div>
            </div>
            <button type="button" wire:click="exportCsv"
                    class="h-8 px-3 rounded-lg border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition inline-flex items-center gap-1.5 shrink-0">
                <iconify-icon icon="solar:document-text-linear" width="15"></iconify-icon>
                {{ __('forms_builder.export_csv') }}
            </button>
        </div>

        <!-- FILTERS -->
        <div class="mt-2.5 flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[160px] max-w-xs">
                <iconify-icon icon="solar:magnifer-linear" width="14" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
                <input type="text" wire:model.live.debounce.300ms="filterSearch"
                       placeholder="{{ __('forms_builder.filter_search') }}"
                       class="input-builder w-full text-[11px] py-2 pl-8 pr-3">
            </div>
            <select wire:model.live="filterSource"
                    class="input-builder text-[11px] py-2 px-2.5 w-auto min-w-[120px]">
                <option value="">{{ __('forms_builder.all_sources') }}</option>
                <option value="public">{{ __('forms_builder.source_public') }}</option>
                <option value="internal_assignment">{{ __('forms_builder.source_internal_assignment') }}</option>
                <option value="internal_team">{{ __('forms_builder.source_internal_team') }}</option>
                <option value="internal_team_slug">{{ __('forms_builder.source_internal_team_slug') }}</option>
            </select>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px] font-medium text-slate-400 shrink-0">{{ __('forms_builder.filter_date_from') }}</label>
                <input type="date" wire:model.live="filterDateFrom"
                       class="input-builder text-[11px] py-1.5 px-2">
            </div>
            <div class="flex items-center gap-1.5">
                <label class="text-[10px] font-medium text-slate-400 shrink-0">{{ __('forms_builder.filter_date_to') }}</label>
                <input type="date" wire:model.live="filterDateTo"
                       class="input-builder text-[11px] py-1.5 px-2">
            </div>
        </div>
    </div>

    <!-- SPLIT VIEW -->
    <div class="flex-1 flex overflow-hidden">
        <!-- LEFT: Response list -->
        <div class="w-full lg:w-80 xl:w-96 shrink-0 border-r border-slate-200/60 bg-white overflow-y-auto custom-scrollbar">
            @forelse($this->responses as $response)
                @php
                    $isSelected = $selectedResponseId === $response->id;
                    $sourceBadge = match($response->submitted_from) {
                        'public' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => __('forms_builder.source_public')],
                        'internal_assignment' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'label' => __('forms_builder.source_internal_assignment')],
                        'internal_team' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team')],
                        'internal_team_slug' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team_slug')],
                        default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal')],
                    };
                @endphp
                <button
                    type="button"
                    wire:click="selectResponse({{ $response->id }})"
                    @click="if (window.innerWidth < 1024) mobileDetailOpen = true"
                    class="w-full text-left px-4 py-3 border-b border-slate-100/80 transition-all {{ $isSelected ? 'bg-slate-50 border-l-2 border-l-[var(--accent)]' : 'hover:bg-slate-50/50 border-l-2 border-l-transparent' }}"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-900 truncate">
                                {{ $response->respondent_name ?? $response->user?->name ?? __('Anonyme') }}
                            </p>
                            <p class="text-[10px] text-slate-500 mt-0.5 truncate">
                                {{ $response->respondent_email ?? $response->user?->email ?? '' }}
                            </p>
                        </div>
                        <span class="shrink-0 px-1.5 py-0.5 rounded-md text-[9px] font-medium {{ $sourceBadge['bg'] }} {{ $sourceBadge['text'] }}">
                            {{ $sourceBadge['label'] }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1.5 text-[10px] text-slate-400">
                        <span>{{ $response->created_at->format('d/m/Y H:i') }}</span>
                        <span class="text-slate-300">&middot;</span>
                        <span>v{{ $response->form_version }}</span>
                        @if($response->ticket_id)
                            <span class="text-slate-300">&middot;</span>
                            <span class="text-[var(--accent)]">#{{ $response->ticket_id }}</span>
                        @endif
                    </div>
                </button>
            @empty
                <div class="px-6 py-16 text-center">
                    <iconify-icon icon="solar:inbox-linear" width="32" class="text-slate-300 mb-3"></iconify-icon>
                    <p class="text-xs font-medium text-slate-600">{{ __('forms_builder.no_responses') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">{{ __('forms_builder.no_responses_hint') }}</p>
                </div>
            @endforelse

            @if($this->responses->hasPages())
                <div class="px-3 py-2.5 border-t border-slate-100">
                    {{ $this->responses->links() }}
                </div>
            @endif
        </div>

        <!-- RIGHT: Detail panel (desktop) -->
        <div class="hidden lg:flex flex-1 flex-col overflow-y-auto custom-scrollbar bg-slate-50/30">
            @if($this->selectedResponse)
                @include('livewire.admin.partials.form-response-detail', ['response' => $this->selectedResponse])
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center px-8">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                        <iconify-icon icon="solar:document-text-linear" class="text-slate-400" width="24"></iconify-icon>
                    </div>
                    <p class="text-xs font-medium text-slate-600">{{ __('forms_builder.select_response') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">{{ __('forms_builder.select_response_hint') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- MOBILE: Detail drawer -->
    <div class="lg:hidden fixed inset-0 z-40" x-show="mobileDetailOpen && @js($selectedResponseId)" x-cloak style="display:none;">
        <div class="absolute inset-0 bg-black/20 backdrop-blur-[2px]" @click="mobileDetailOpen = false"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-[24rem] bg-white shadow-xl border-l border-slate-200/60 flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="h-12 px-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <h3 class="text-xs font-semibold text-slate-900">{{ __('forms_builder.response_detail') }}</h3>
                <button type="button" @click="mobileDetailOpen = false" class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition">
                    <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @if($this->selectedResponse)
                    @include('livewire.admin.partials.form-response-detail', ['response' => $this->selectedResponse])
                @endif
            </div>
        </div>
    </div>
</div>
