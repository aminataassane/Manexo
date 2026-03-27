@php
    use App\Enums\TicketMessageType;
    $isSystem = $msg->isSystem();
    $isNote = $msg->isInternalNote();
    $time = $msg->created_at->diffForHumans();
    $timeShort = $msg->created_at->format('H:i');
    $isCreator = isset($ticket) && (int) $ticket->created_by === (int) $msg->user_id;
    $isOwn = isset($ticket) && auth()->check() && (int) $msg->user_id === (int) auth()->id();
    $roleLabel = $isCreator ? __('Client') : __('Support');
    $avatarUrl = $msg->user
        ? 'https://ui-avatars.com/api/?name=' . urlencode($msg->user->name) . '&size=40&background=e2e8f0&color=475569'
        : 'https://ui-avatars.com/api/?name=U&size=40&background=e2e8f0&color=475569';
    $bodyEscaped = e($msg->body);
    $mentionPattern = '/@([\p{L}\p{N}_]+(?:\s+[\p{L}\p{N}_]+)*)/u';
    if ($isNote) {
        $bodyWithMentions = preg_replace($mentionPattern, '<span class="mention font-medium" style="color: color-mix(in srgb, var(--accent) 55%, black);">@$1</span>', $bodyEscaped);
    } elseif ($isOwn) {
        $bodyWithMentions = preg_replace($mentionPattern, '<span class="mention font-medium opacity-90">@$1</span>', $bodyEscaped);
    } else {
        $bodyWithMentions = preg_replace($mentionPattern, '<span class="mention font-medium text-slate-700">@$1</span>', $bodyEscaped);
    }
    $bodyFormatted = nl2br($bodyWithMentions);
    $messageAttachments = is_array($msg->attachments) ? $msg->attachments : [];
@endphp

@if($isSystem)
    <div id="message-{{ $msg->id }}" class="message-row message-system flex justify-center py-3 min-w-0">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs">
            <iconify-icon icon="solar:info-circle-linear" width="14" class="shrink-0 text-slate-500"></iconify-icon>
            <span class="break-words max-w-[min(100%,28rem)]">{!! $bodyFormatted !!}</span>
            <span class="text-slate-400 shrink-0">· {{ $time }}</span>
        </div>
    </div>
@elseif($isNote)
    <div id="message-{{ $msg->id }}" class="message-row message-note flex gap-3 py-3 min-w-0 max-w-[85%]">
        <div class="w-8 h-8 shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
            <iconify-icon icon="solar:lock-keyhole-linear" width="14"></iconify-icon>
        </div>
        <div class="message-bubble flex-1 min-w-0 rounded-2xl rounded-tl-md bg-amber-50/90 border border-amber-200/80 shadow-sm overflow-hidden">
            <div class="px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 border-b border-amber-200/60 bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wide">{{ __('Note interne') }}</span>
                    <span class="text-[10px] text-amber-600">{{ $msg->user?->name }}</span>
                </div>
                <span class="text-[10px] text-amber-600/90">{{ $time }}</span>
            </div>
            <div class="px-4 py-3 text-sm leading-relaxed text-amber-900 break-words">
                {!! $bodyFormatted !!}
            </div>
            @if(count($messageAttachments) > 0)
                <div class="px-4 py-3 pt-2 border-t border-amber-200/50 space-y-2 flex flex-wrap gap-2">
                    @foreach($messageAttachments as $att)
                        @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'note'])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@else
    {{-- Message reçu (Support / autres) : gauche. Message envoyé (Client / moi) : droite --}}
    @if($isOwn)
        @php $hasAttachments = count($messageAttachments) > 0; @endphp
        <div id="message-{{ $msg->id }}" class="message-row message-own flex justify-end py-3 min-w-0">
            <div class="flex items-end gap-3 max-w-[85%] min-w-0 flex-row-reverse">
                <div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                    <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="">
                </div>
                <div class="flex flex-col items-end min-w-0 max-w-full">
                    <div class="flex items-center gap-2 mb-1.5 flex-row-reverse">
                        <span class="text-xs font-semibold text-slate-800">{{ $msg->user?->name ?? '—' }}</span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-700/10 text-[10px] font-medium text-slate-600">{{ $roleLabel }}</span>
                        <span class="text-[10px] text-slate-400">{{ $time }}</span>
                    </div>
                    <div class="message-bubble rounded-2xl rounded-br-md px-4 py-3 text-sm leading-relaxed shadow-md w-full max-w-full {{ $hasAttachments ? 'bg-white border border-slate-200 text-slate-700' : '' }}" @if(!$hasAttachments) style="background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, #1e293b) 100%); color: #fff;" @endif>
                        <div class="text-left break-words {{ !$hasAttachments ? 'text-white' : '' }}">
                            {!! $bodyFormatted !!}
                        </div>
                        @if($hasAttachments)
                            <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                @foreach($messageAttachments as $att)
                                    @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div id="message-{{ $msg->id }}" class="message-row message-incoming flex gap-3 py-3 min-w-0 max-w-[85%]">
            <div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="min-w-0 max-w-full w-fit">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="text-xs font-semibold text-slate-800">{{ $msg->user?->name ?? '—' }}</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-medium text-slate-600 border border-slate-200/80">{{ $roleLabel }}</span>
                    <span class="text-[10px] text-slate-400 ml-auto">{{ $time }}</span>
                </div>
                <div class="message-bubble rounded-2xl rounded-bl-md px-4 py-3 text-sm leading-relaxed bg-white border border-slate-200 shadow-sm break-words w-fit max-w-full min-w-0">
                    {!! $bodyFormatted !!}
                    @if(count($messageAttachments) > 0)
                        <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                            @foreach($messageAttachments as $att)
                                @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
