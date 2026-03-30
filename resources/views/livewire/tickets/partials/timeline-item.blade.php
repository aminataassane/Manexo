{{-- Timeline item — receives a TimelineItem DTO ($item) --}}
@if($item->isSystem)
    <div id="message-{{ $item->id }}" class="message-row message-system flex justify-center py-3 min-w-0">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs">
            <iconify-icon icon="solar:info-circle-linear" width="14" class="shrink-0 text-slate-500"></iconify-icon>
            <span class="break-words max-w-[min(100%,28rem)]">{!! $item->body !!}</span>
            <span class="text-slate-400 shrink-0">· {{ $item->timeHuman }}</span>
        </div>
    </div>
@elseif($item->isInternal)
    <div id="message-{{ $item->id }}" class="message-row message-note flex gap-3 py-3 min-w-0 max-w-[85%]">
        <div class="w-8 h-8 shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
            <iconify-icon icon="solar:lock-keyhole-linear" width="14"></iconify-icon>
        </div>
        <div class="message-bubble flex-1 min-w-0 rounded-2xl rounded-tl-md bg-amber-50/90 border border-amber-200/80 shadow-sm overflow-hidden">
            <div class="px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 border-b border-amber-200/60 bg-amber-50/50">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wide">{{ __('Note interne') }}</span>
                    <span class="text-[10px] text-amber-600">{{ $item->authorName }}</span>
                </div>
                <span class="text-[10px] text-amber-600/90">{{ $item->timeHuman }}</span>
            </div>
            <div class="px-4 py-3 text-sm leading-relaxed text-amber-900 break-words">
                {!! $item->body !!}
            </div>
            @if($item->hasAttachments)
                <div class="px-4 py-3 pt-2 border-t border-amber-200/50 space-y-2 flex flex-wrap gap-2">
                    @foreach($item->attachments as $att)
                        @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'note'])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@else
    {{-- Regular message: own messages on the right, others on the left --}}
    @if($item->isOwn)
        <div id="message-{{ $item->id }}" class="message-row message-own flex justify-end py-3 min-w-0">
            <div class="flex items-end gap-3 max-w-[85%] min-w-0 flex-row-reverse">
                <div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                    <img src="{{ $item->avatarUrl }}" class="w-full h-full object-cover" alt="">
                </div>
                <div class="flex flex-col items-end min-w-0 max-w-full">
                    <div class="flex items-center gap-2 mb-1.5 flex-row-reverse">
                        <span class="text-xs font-semibold text-slate-800">{{ $item->authorName ?? '—' }}</span>

                        @if($item->hasEmailOrigin)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <iconify-icon icon="solar:letter-linear" width="10"></iconify-icon>
                                {{ $item->badgePrimary }}
                            </span>
                        @elseif($item->channel === 'api')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-violet-50 text-violet-600 border border-violet-200">
                                <iconify-icon icon="solar:code-square-linear" width="10"></iconify-icon>
                                {{ $item->badgePrimary }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-md bg-slate-700/10 text-[10px] font-medium text-slate-600">{{ $item->roleLabel }}</span>
                        @endif

                        <span class="text-[10px] text-slate-400">{{ $item->timeHuman }}</span>
                    </div>
                    <div class="message-bubble rounded-2xl rounded-br-md px-4 py-3 text-sm leading-relaxed shadow-md w-full max-w-full {{ $item->hasAttachments ? 'bg-white border border-slate-200 text-slate-700' : '' }}" @if(!$item->hasAttachments) style="background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, #1e293b) 100%); color: #fff;" @endif>
                        <div class="text-left break-words {{ !$item->hasAttachments ? 'text-white' : '' }}">
                            {!! $item->body !!}
                        </div>
                        @if($item->hasAttachments)
                            <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                @foreach($item->attachments as $att)
                                    @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div id="message-{{ $item->id }}" class="message-row message-incoming flex gap-3 py-3 min-w-0 max-w-[85%]">
            <div class="w-9 h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                <img src="{{ $item->avatarUrl }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="min-w-0 max-w-full w-fit">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="text-xs font-semibold text-slate-800">{{ $item->authorName ?? '—' }}</span>

                    @if($item->hasEmailOrigin)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium {{ $item->channel === 'email_inbound' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                            <iconify-icon icon="solar:letter-linear" width="10"></iconify-icon>
                            {{ $item->badgePrimary }}
                        </span>
                    @elseif($item->channel === 'api')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-violet-50 text-violet-600 border border-violet-200">
                            <iconify-icon icon="solar:code-square-linear" width="10"></iconify-icon>
                            {{ $item->badgePrimary }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-medium text-slate-600 border border-slate-200/80">{{ $item->roleLabel }}</span>
                    @endif

                    <span class="text-[10px] text-slate-400 ml-auto">{{ $item->timeHuman }}</span>
                </div>

                @if($item->hasEmailOrigin && $item->emailFrom)
                    <div class="text-[10px] text-slate-400 mb-1">via {{ $item->emailFrom }}</div>
                @endif

                <div class="message-bubble rounded-2xl rounded-bl-md px-4 py-3 text-sm leading-relaxed bg-white border border-slate-200 shadow-sm break-words w-fit max-w-full min-w-0">
                    {!! $item->body !!}
                    @if($item->hasAttachments)
                        <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                            @foreach($item->attachments as $att)
                                @include('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
