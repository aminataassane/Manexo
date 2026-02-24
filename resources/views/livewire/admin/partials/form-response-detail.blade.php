@php
    $snapshot = $response->field_snapshot ?? [];
    $answers = $response->responses ?? [];
    $baseFields = $response->base_fields ?? [];
    $sourceBadge = match($response->submitted_from) {
        'public' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => __('forms_builder.source_public')],
        'internal_assignment' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'label' => __('forms_builder.source_internal_assignment')],
        'internal_team' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team')],
        'internal_team_slug' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal_team_slug')],
        default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'label' => __('forms_builder.source_internal')],
    };
@endphp

<div class="p-5 lg:p-6 xl:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="text-base font-semibold text-slate-900">
                {{ $response->respondent_name ?? $response->user?->name ?? __('Anonyme') }}
            </h3>
            @if($response->respondent_email ?? $response->user?->email)
                <p class="text-xs text-slate-500 mt-0.5">{{ $response->respondent_email ?? $response->user?->email }}</p>
            @endif
        </div>
        <span class="shrink-0 px-2 py-0.5 rounded-md text-[10px] font-medium {{ $sourceBadge['bg'] }} {{ $sourceBadge['text'] }}">
            {{ $sourceBadge['label'] }}
        </span>
    </div>

    <!-- Metadata -->
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
            <iconify-icon icon="solar:info-circle-linear" width="13"></iconify-icon>
            {{ __('forms_builder.metadata_section') }}
        </h4>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-xs">
            <div>
                <span class="text-slate-400 text-[10px]">{{ __('forms_builder.submitted_at') }}</span>
                <p class="font-medium text-slate-800 mt-0.5">{{ $response->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <span class="text-slate-400 text-[10px]">{{ __('forms_builder.form_version_label') }}</span>
                <p class="font-medium text-slate-800 mt-0.5">v{{ $response->form_version }}</p>
            </div>
            <div>
                <span class="text-slate-400 text-[10px]">{{ __('forms_builder.ip_address') }}</span>
                <p class="font-medium text-slate-800 mt-0.5 font-mono text-[11px]">{{ $response->ip_address ?? '—' }}</p>
            </div>
            @if($response->public_form_slug)
            <div>
                <span class="text-slate-400 text-[10px]">Slug</span>
                <p class="font-medium text-slate-800 mt-0.5 font-mono text-[11px]">{{ $response->public_form_slug }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-slate-100"></div>

    <!-- Base fields (subject, description, category) -->
    @if(!empty($baseFields))
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
            <iconify-icon icon="solar:document-text-linear" width="13"></iconify-icon>
            {{ __('forms_builder.base_fields_section') }}
        </h4>
        <div class="space-y-3">
            @if(!empty($baseFields['subject']))
                <div>
                    <span class="text-[10px] font-medium text-slate-400">{{ __('forms_builder.subject') }}</span>
                    <p class="text-sm text-slate-800 mt-0.5">{{ $baseFields['subject'] }}</p>
                </div>
            @endif
            @if(!empty($baseFields['description']))
                <div>
                    <span class="text-[10px] font-medium text-slate-400">{{ __('forms_builder.description') }}</span>
                    <p class="text-sm text-slate-800 mt-0.5 whitespace-pre-line">{{ $baseFields['description'] }}</p>
                </div>
            @endif
            @if(!empty($baseFields['category_name']))
                <div>
                    <span class="text-[10px] font-medium text-slate-400">{{ __('forms_builder.category') }}</span>
                    <p class="text-sm text-slate-800 mt-0.5">{{ $baseFields['category_name'] }}</p>
                </div>
            @endif
            @if(!empty($baseFields['guest_name']))
                <div>
                    <span class="text-[10px] font-medium text-slate-400">{{ __('forms_builder.respondent') }} (guest)</span>
                    <p class="text-sm text-slate-800 mt-0.5">{{ $baseFields['guest_name'] }} — {{ $baseFields['guest_email'] ?? '' }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="border-t border-slate-100"></div>
    @endif

    <!-- Custom fields (from snapshot) -->
    @if(count($snapshot) > 0)
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
            <iconify-icon icon="solar:list-check-linear" width="13"></iconify-icon>
            {{ __('forms_builder.custom_fields_section') }}
        </h4>
        <div class="space-y-2.5">
            @foreach($snapshot as $sf)
                @if(($sf['type'] ?? '') === 'section')
                    <div class="pt-3 mt-1">
                        <h5 class="text-xs font-semibold text-slate-700 border-b border-slate-100 pb-2">{{ $sf['label'] ?? '' }}</h5>
                    </div>
                @else
                    @php
                        $val = $answers[$sf['key'] ?? ''] ?? null;
                        $isFile = is_array($val) && ($val['type'] ?? '') === 'file';
                    @endphp
                    <div class="py-1.5">
                        <span class="text-[10px] font-medium text-slate-400">{{ $sf['label'] ?? $sf['key'] ?? '?' }}</span>
                        <div class="text-sm text-slate-800 mt-0.5">
                            @if($isFile)
                                <a href="{{ route('admin.forms.responses.file', ['response' => $response->id, 'fieldKey' => $sf['key']]) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 text-[var(--accent)] hover:opacity-80 text-xs font-medium">
                                    <iconify-icon icon="solar:download-minimalistic-linear" width="14"></iconify-icon>
                                    {{ $val['original_name'] ?? __('forms_builder.download_file') }}
                                    @if(!empty($val['size']))
                                        <span class="text-[10px] text-slate-400">({{ number_format($val['size'] / 1024, 0) }} KB)</span>
                                    @endif
                                </a>
                            @elseif($val === null || $val === '')
                                <span class="text-slate-300">—</span>
                            @elseif(is_bool($val))
                                {{ $val ? __('Oui') : __('Non') }}
                            @elseif(is_array($val))
                                {{ implode(', ', $val) }}
                            @else
                                {{ $val }}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @elseif(count($answers) > 0)
    {{-- Fallback: raw responses (old data without snapshot) --}}
    <div>
        <h4 class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5">{{ __('forms_builder.custom_fields_section') }}</h4>
        <div class="space-y-2.5">
            @foreach($answers as $key => $val)
                @php $isFile = is_array($val) && ($val['type'] ?? '') === 'file'; @endphp
                <div class="py-1.5">
                    <span class="text-[10px] font-medium text-slate-400 font-mono">{{ $key }}</span>
                    <div class="text-sm text-slate-800 mt-0.5">
                        @if($isFile)
                            <a href="{{ route('admin.forms.responses.file', ['response' => $response->id, 'fieldKey' => $key]) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 text-[var(--accent)] hover:opacity-80 text-xs font-medium">
                                <iconify-icon icon="solar:download-minimalistic-linear" width="14"></iconify-icon>
                                {{ $val['original_name'] ?? __('forms_builder.download_file') }}
                            </a>
                        @elseif(is_bool($val))
                            {{ $val ? __('Oui') : __('Non') }}
                        @elseif(is_array($val))
                            {{ implode(', ', $val) }}
                        @else
                            {{ $val }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Ticket link -->
    @if($response->ticket_id)
    <div class="pt-2 border-t border-slate-100">
        <a href="{{ route('tickets.discussion', $response->ticket_id) }}"
           class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-white rounded-lg hover:opacity-90 transition-all"
           style="background: var(--accent);">
            <iconify-icon icon="solar:ticket-linear" width="15"></iconify-icon>
            {{ __('forms_builder.open_ticket', ['id' => $response->ticket_id]) }}
        </a>
    </div>
    @endif
</div>
