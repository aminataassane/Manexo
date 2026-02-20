@php
    use App\Enums\TicketMessageType;
    $isSystem = $msg->isSystem();
    $isNote = $msg->isInternalNote();
    $isOwn = $msg->user_id && (int) $msg->user_id === (int) auth()->id();
    $time = $msg->created_at->format('H:i');
    $isCreator = isset($ticket) && (int) $ticket->created_by === (int) $msg->user_id;
    $roleLabel = $isCreator ? __('Client') : __('Agent');
    $avatarUrl = $msg->user
        ? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) . '&size=32&background=e2e8f0&color=475569'
        : 'https://ui-avatars.com/api/?name=U&size=32&background=e2e8f0&color=475569';
    $bodyEscaped = e($msg->body);
    $mentionPattern = '/@([\p{L}\p{N}_]+(?:\s+[\p{L}\p{N}_]+)*)/u';
    if ($isNote) {
        $bodyWithMentions = preg_replace($mentionPattern, '<span class="mention font-medium" style="color: color-mix(in srgb, var(--accent) 55%, black);">@$1</span>', $bodyEscaped);
    } elseif ($isOwn) {
        $bodyWithMentions = preg_replace($mentionPattern, '<span class="mention font-medium" style="color: color-mix(in srgb, var(--accent) 55%, black);">@$1</span>', $bodyEscaped);
    } else {
        $bodyWithMentions = preg_replace($mentionPattern, '<span class="mention font-medium text-slate-700">@$1</span>', $bodyEscaped);
    }
    $bodyFormatted = nl2br($bodyWithMentions);
    $messageAttachments = is_array($msg->attachments) ? $msg->attachments : [];
@endphp

@if($isSystem)
    <div class="flex justify-center">
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-medium" style="background: var(--accent-soft); color: var(--accent);">
            <iconify-icon icon="solar:user-plus-linear" width="12"></iconify-icon>
            {{ $msg->body }}
        </span>
    </div>
@elseif($isNote)
    <div class="flex items-start gap-3 justify-end group">
        <div class="flex flex-col gap-1 items-end max-w-[80%]">
            <div class="flex items-center gap-2 mb-0.5">
                <iconify-icon icon="solar:lock-keyhole-minimalistic-linear" class="text-xs" style="color: var(--accent);"></iconify-icon>
                <span class="text-[10px] font-medium uppercase tracking-wide" style="color: var(--accent);">{{ __('Note interne') }}</span>
                <span class="text-[10px] text-slate-400">{{ $time }}</span>
            </div>
            <div class="px-4 py-2.5 rounded-2xl rounded-br-sm text-sm leading-relaxed border" style="background: var(--accent-soft); border-color: color-mix(in srgb, var(--accent) 40%, transparent); color: var(--accent);">
                {!! $bodyFormatted !!}
                @foreach($messageAttachments as $att)
                    <a href="{{ is_array($att) ? ($att['url'] ?? '#') : '#' }}" target="_blank" rel="noopener" class="mt-2 flex items-center gap-2 rounded-lg border border-white/30 bg-white/20 px-2 py-1.5 text-xs">
                        <iconify-icon icon="solar:file-text-linear" width="14"></iconify-icon>
                        <span class="truncate">{{ is_array($att) ? ($att['name'] ?? 'Fichier') : 'Fichier' }}</span>
                    </a>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-slate-400">{{ __('Par') }} {{ $msg->user?->name }}</span>
                <img src="{{ $avatarUrl }}" alt="" class="w-5 h-5 rounded-full border border-slate-200 grayscale opacity-70">
            </div>
        </div>
    </div>
@elseif($isOwn)
    {{-- Message agent (moi) --}}
    <div class="flex items-end gap-3 justify-end group">
        <div class="flex flex-col gap-1 items-end max-w-[80%]">
            <div class="flex items-baseline gap-2 mb-0.5 flex-row-reverse">
                <span class="text-xs font-medium text-slate-900">{{ $msg->user?->name }}</span>
                <span class="text-[10px] text-slate-400">{{ $time }}</span>
            </div>
            <div class="text-white px-4 py-2.5 rounded-2xl rounded-br-sm shadow-md text-sm leading-relaxed" style="background-color: var(--accent);">
                {!! $bodyFormatted !!}
                @foreach($messageAttachments as $att)
                    <a href="{{ is_array($att) ? ($att['url'] ?? '#') : '#' }}" target="_blank" rel="noopener" class="mt-2 flex items-center gap-2 rounded-lg border border-white/30 bg-white/20 px-2 py-1.5 text-xs text-white">
                        <iconify-icon icon="solar:file-text-linear" width="14"></iconify-icon>
                        <span class="truncate">{{ is_array($att) ? ($att['name'] ?? 'Fichier') : 'Fichier' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <img src="{{ $avatarUrl }}" alt="" class="w-8 h-8 rounded-full border-2 border-white shadow-sm shrink-0" style="background: var(--accent-soft);">
    </div>
@else
    {{-- Message client ou autre agent --}}
    <div class="flex items-end gap-3 group">
        <img src="{{ $avatarUrl }}" alt="" class="w-8 h-8 rounded-full border border-slate-200 bg-white shrink-0">
        <div class="flex flex-col gap-1 max-w-[80%]">
            <div class="flex items-baseline gap-2 mb-0.5">
                <span class="text-xs font-medium text-slate-700">{{ $msg->user?->name }}</span>
                <span class="text-[10px] text-slate-400">{{ $time }}</span>
                <span class="text-[10px] text-slate-400">· {{ $roleLabel }}</span>
            </div>
            <div class="bg-white border border-slate-200 px-4 py-2.5 rounded-2xl rounded-bl-sm shadow-sm text-sm text-slate-600 leading-relaxed group-hover:shadow-md transition-shadow relative">
                {!! $bodyFormatted !!}
                @foreach($messageAttachments as $att)
                    <a href="{{ is_array($att) ? ($att['url'] ?? '#') : '#' }}" target="_blank" rel="noopener" class="mt-2 flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 text-xs text-slate-700 hover:bg-slate-100">
                        <iconify-icon icon="solar:file-text-linear" width="14"></iconify-icon>
                        <span class="truncate">{{ is_array($att) ? ($att['name'] ?? 'Fichier') : 'Fichier' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
