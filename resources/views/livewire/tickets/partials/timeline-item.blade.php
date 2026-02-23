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
    <div id="message-{{ $msg->id }}" class="relative py-2 min-w-0 overflow-hidden">
        <div class="absolute left-0 sm:-left-[27px] top-1 w-5 h-5 rounded-full bg-[#F3F4F6] border-2 border-white flex items-center justify-center text-[#6B7280] shrink-0">
            <iconify-icon icon="solar:user-linear" width="10"></iconify-icon>
        </div>
        <div class="pl-7 sm:pl-2 flex flex-wrap items-center gap-2 text-sm text-slate-700 break-words min-w-0">
            {!! $bodyFormatted !!}
            <span class="text-slate-500 text-xs shrink-0">• {{ $time }}</span>
        </div>
    </div>
@elseif($isNote)
    <div id="message-{{ $msg->id }}" class="relative group py-3 min-w-0 overflow-hidden">
        <div class="absolute left-0 sm:-left-[27px] top-4 w-5 h-5 rounded-full bg-[#FFF7ED] border-2 border-white flex items-center justify-center text-amber-600 shrink-0">
            <iconify-icon icon="solar:lock-keyhole-linear" width="10"></iconify-icon>
        </div>
        <div class="pl-7 sm:pl-2 min-w-0 overflow-hidden">
            <div class="bg-[#FFF7ED] border border-amber-200 rounded-xl p-3 sm:p-4 min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2 min-w-0">
                    <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                        <span class="text-[11px] font-semibold text-[#9A3412]">{{ __('Note interne') }}</span>
                        <span class="text-[10px] text-amber-700">{{ __('Visible uniquement par l\'équipe') }}</span>
                    </div>
                    <span class="text-[10px] text-amber-600 shrink-0">{{ $msg->user?->name }} • {{ $time }}</span>
                </div>
                <div class="text-sm sm:text-base leading-relaxed text-amber-900 break-words min-w-0">
                    {!! $bodyFormatted !!}
                </div>
                @if(count($messageAttachments) > 0)
                    <div class="mt-2 pt-2 border-t border-amber-200/60 space-y-1.5">
                        @foreach($messageAttachments as $att)
                            @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'note'])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@else
    {{-- Message reçu (autres) : gauche avec cercle timeline. Message envoyé (moi) : droite, bulle accent --}}
    @if($isOwn)
        @php $hasAttachments = count($messageAttachments) > 0; @endphp
        <div id="message-{{ $msg->id }}" class="flex justify-end py-4 min-w-0">
            <div class="flex items-end gap-2 max-w-[85%] min-w-0">
                <div class="flex flex-col items-end min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 mb-2 flex-row-reverse">
                        <span class="text-[12px] font-semibold text-[#111827]">{{ $msg->user?->name ?? '—' }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-[#F3F4F6] border border-[#E5E7EB] text-[10px] font-medium text-[#4B5563]">{{ $roleLabel }}</span>
                        <span class="text-[10px] text-[#9CA3AF]">{{ $time }}</span>
                    </div>
                    {{-- Avec pièce jointe : fond neutre (pas d'orange) pour que l'image/fichier soit lisible --}}
                    <div class="rounded-2xl rounded-br-md px-4 py-3 text-sm sm:text-base leading-relaxed shadow-sm w-full @if($hasAttachments) bg-white border border-slate-200 text-slate-700 @endif" @if(!$hasAttachments) style="background-color: var(--accent);" @endif>
                        <div class="text-left break-words @if(!$hasAttachments) text-white @endif">
                            {!! $bodyFormatted !!}
                        </div>
                        @if($hasAttachments)
                            <div class="mt-2 pt-2 border-t border-[#E5E7EB] space-y-1.5">
                                @foreach($messageAttachments as $att)
                                    @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <img src="{{ $avatarUrl }}" class="w-8 h-8 rounded-full ring-2 ring-white shadow shrink-0 object-cover" alt="">
            </div>
        </div>
    @else
        <div id="message-{{ $msg->id }}" class="relative py-4 min-w-0 overflow-hidden">
            <div class="absolute left-0 sm:-left-[27px] top-3 w-8 h-8 rounded-full bg-white ring-2 ring-[#E5E7EB] overflow-hidden shrink-0 shadow-sm">
                <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="pl-10 sm:pl-2 pr-0 sm:pr-2 min-w-0">
                <div class="flex flex-wrap items-baseline justify-between gap-2 mb-2 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[12px] font-semibold text-[#111827]">{{ $msg->user?->name ?? '—' }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-[#F3F4F6] border border-[#E5E7EB] text-[10px] font-medium text-[#4B5563]">{{ $roleLabel }}</span>
                    </div>
                    <span class="text-[10px] text-[#9CA3AF] shrink-0">{{ $time }}</span>
                </div>
                <div class="rounded-2xl rounded-bl-md px-3 sm:px-4 py-3 text-sm sm:text-base leading-relaxed bg-white border border-slate-200 text-slate-700 shadow-sm break-words min-w-0 overflow-hidden">
                    {!! $bodyFormatted !!}
                    @if(count($messageAttachments) > 0)
                        <div class="mt-2 pt-2 border-t border-[#E5E7EB] space-y-1.5">
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
