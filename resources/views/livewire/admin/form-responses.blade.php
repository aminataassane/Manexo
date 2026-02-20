<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.forms') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" width="20"></iconify-icon>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ __('Réponses') }} - {{ $form->name }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $this->responses->total() }} {{ __('réponse(s)') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($this->responses as $response)
                <div>
                    <button
                        type="button"
                        wire:click="toggleResponse({{ $response->id }})"
                        class="w-full text-left px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors"
                    >
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $response->user?->name ?? __('Anonyme') }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $response->created_at->format('d/m/Y H:i') }}
                                · v{{ $response->form_version }}
                                @if($response->user?->email)
                                    · {{ $response->user->email }}
                                @endif
                            </p>
                        </div>
                        <iconify-icon
                            icon="{{ $expandedResponseId === $response->id ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear' }}"
                            width="16"
                            class="text-slate-400"
                        ></iconify-icon>
                    </button>

                    @if($expandedResponseId === $response->id)
                        <div class="px-6 pb-5 pt-1">
                            @php
                                $snapshot = $response->field_snapshot ?? [];
                                $answers = $response->responses ?? [];
                            @endphp
                            @if(count($snapshot) > 0)
                                <div class="space-y-3">
                                    @foreach($snapshot as $sf)
                                        @if(($sf['type'] ?? '') === 'section')
                                            <div class="pt-3 pb-1 border-t border-slate-100 first:border-t-0 first:pt-0">
                                                <h4 class="text-sm font-bold text-slate-800">{{ $sf['label'] ?? '' }}</h4>
                                            </div>
                                        @else
                                            @php $val = $answers[$sf['key'] ?? ''] ?? '—'; @endphp
                                            <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4 py-2 border-b border-slate-50 last:border-b-0">
                                                <span class="text-xs font-medium text-slate-500 sm:w-40 shrink-0">{{ $sf['label'] ?? $sf['key'] ?? '?' }}</span>
                                                <span class="text-sm text-slate-900">
                                                    @if(is_bool($val))
                                                        {{ $val ? __('Oui') : __('Non') }}
                                                    @else
                                                        {{ $val }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                {{-- Fallback: raw responses --}}
                                <div class="space-y-2">
                                    @foreach($answers as $key => $val)
                                        <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4 py-2 border-b border-slate-50 last:border-b-0">
                                            <span class="text-xs font-medium text-slate-500 sm:w-40 shrink-0 font-mono">{{ $key }}</span>
                                            <span class="text-sm text-slate-900">
                                                @if(is_bool($val))
                                                    {{ $val ? __('Oui') : __('Non') }}
                                                @else
                                                    {{ $val }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <iconify-icon icon="solar:inbox-linear" width="40" class="text-slate-300 mb-3"></iconify-icon>
                    <p class="text-sm font-medium text-slate-900">{{ __('Aucune réponse') }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ __('Les réponses apparaîtront ici une fois soumises.') }}</p>
                </div>
            @endforelse
        </div>

        @if($this->responses->hasPages())
            <div class="px-6 py-3 border-t border-slate-100">
                {{ $this->responses->links() }}
            </div>
        @endif
    </div>
</div>
