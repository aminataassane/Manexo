{{-- Timeline item — receives a TimelineItem DTO ($item) --}}
@if($item->isSystem)
    <div id="message-{{ $item->id }}" class="message-row message-system flex justify-center py-2 sm:py-3 min-w-0">
        <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full bg-slate-100 text-slate-600 text-[11px] sm:text-xs max-w-full">
            <iconify-icon icon="solar:info-circle-linear" width="14" class="shrink-0 text-slate-500"></iconify-icon>
            <span class="break-words min-w-0 max-w-[min(100%,28rem)]">{!! $item->body !!}</span>
            <span class="text-slate-400 shrink-0">· {{ $item->timeHuman }}</span>
        </div>
    </div>
@elseif($item->isInternal)
    <div id="message-{{ $item->id }}" class="message-row message-note flex gap-2 sm:gap-3 py-2 sm:py-3 min-w-0 max-w-[95%] sm:max-w-[85%]">
        <div class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
            <iconify-icon icon="solar:lock-keyhole-linear" width="14"></iconify-icon>
        </div>
        <div class="message-bubble flex-1 min-w-0 rounded-2xl rounded-tl-md bg-amber-50/90 border border-amber-200/80 shadow-sm overflow-hidden">
            <div class="px-3 py-2 sm:px-4 sm:py-2.5 flex flex-wrap items-center justify-between gap-1.5 sm:gap-2 border-b border-amber-200/60 bg-amber-50/50">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <span class="text-[10px] sm:text-[11px] font-bold text-amber-800 uppercase tracking-wide">{{ __('Note interne') }}</span>
                    <span class="text-[10px] text-amber-600">{{ $item->authorName }}</span>
                </div>
                <span class="text-[10px] text-amber-600/90">{{ $item->timeHuman }}</span>
            </div>
            <div class="px-3 py-2.5 sm:px-4 sm:py-3 text-[13px] sm:text-sm leading-relaxed text-amber-900 break-words">
                {!! $item->body !!}
            </div>
            @if($item->hasAttachments)
                <div class="px-3 sm:px-4 pb-3">
                    @include('livewire.tickets.partials.attachments-group', ['attachments' => $item->attachments, 'variant' => 'note'])
                </div>
            @endif
        </div>
    </div>
@else
    {{-- Regular message: own messages on the right, others on the left --}}
    @if($item->isOwn)
        <div id="message-{{ $item->id }}" class="message-row message-own flex justify-end py-2 sm:py-3 min-w-0">
            <div class="flex items-end gap-2 sm:gap-3 max-w-[95%] sm:max-w-[85%] min-w-0 flex-row-reverse">
                <div class="w-7 h-7 sm:w-9 sm:h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                    <img src="{{ $item->avatarUrl }}" class="w-full h-full object-cover" alt="">
                </div>
                <div class="flex flex-col items-end min-w-0 max-w-full">
                    <div class="flex items-center gap-1.5 sm:gap-2 mb-1 sm:mb-1.5 flex-row-reverse flex-wrap justify-end">
                        <span class="text-[11px] sm:text-xs font-semibold text-slate-800">{{ $item->authorName ?? '—' }}</span>

                        @if($item->hasEmailOrigin)
                            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <iconify-icon icon="solar:letter-linear" width="10"></iconify-icon>
                                {{ $item->badgePrimary }}
                            </span>
                        @elseif($item->channel === 'api')
                            <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-violet-50 text-violet-600 border border-violet-200">
                                <iconify-icon icon="solar:code-square-linear" width="10"></iconify-icon>
                                {{ $item->badgePrimary }}
                            </span>
                        @else
                            <span class="hidden sm:inline px-2 py-0.5 rounded-md bg-slate-700/10 text-[10px] font-medium text-slate-600">{{ $item->roleLabel }}</span>
                        @endif

                        <span class="text-[10px] text-slate-400">{{ $item->timeHuman }}</span>
                    </div>
                    <div class="message-bubble rounded-2xl rounded-br-md text-[13px] sm:text-sm leading-relaxed shadow-md w-full max-w-full overflow-hidden"
                         style="{{ $item->hasAttachments
                             ? 'background: color-mix(in srgb, var(--accent) 8%, white); border: 1px solid color-mix(in srgb, var(--accent) 18%, #e2e8f0);'
                             : 'background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 85%, #1e293b) 100%); color: #fff;' }}">
                        <div class="px-3 py-2.5 sm:px-4 sm:py-3 text-left break-words {{ $item->hasAttachments ? 'text-slate-800' : 'text-white' }}">
                            {!! $item->body !!}
                        </div>
                        @if($item->hasAttachments)
                            <div class="px-3 sm:px-4 pb-3">
                                @include('livewire.tickets.partials.attachments-group', ['attachments' => $item->attachments, 'variant' => 'mine'])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div id="message-{{ $item->id }}" class="message-row message-incoming flex gap-2 sm:gap-3 py-2 sm:py-3 min-w-0 max-w-[95%] sm:max-w-[85%]">
            <div class="w-7 h-7 sm:w-9 sm:h-9 shrink-0 rounded-full ring-2 ring-white shadow-md overflow-hidden bg-slate-100">
                <img src="{{ $item->avatarUrl }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="min-w-0 max-w-full w-fit">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1 sm:mb-1.5">
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-800">{{ $item->authorName ?? '—' }}</span>

                    @if($item->hasEmailOrigin)
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium {{ $item->channel === 'email_inbound' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                            <iconify-icon icon="solar:letter-linear" width="10"></iconify-icon>
                            {{ $item->badgePrimary }}
                        </span>
                    @elseif($item->channel === 'api')
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-violet-50 text-violet-600 border border-violet-200">
                            <iconify-icon icon="solar:code-square-linear" width="10"></iconify-icon>
                            {{ $item->badgePrimary }}
                        </span>
                    @else
                        <span class="hidden sm:inline px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-medium text-slate-600 border border-slate-200/80">{{ $item->roleLabel }}</span>
                    @endif

                    <span class="text-[10px] text-slate-400 ml-auto">{{ $item->timeHuman }}</span>
                </div>

                @if($item->hasEmailOrigin && $item->emailFrom)
                    <div class="text-[10px] text-slate-400 mb-1">via {{ $item->emailFrom }}</div>
                @endif

                <div class="message-bubble rounded-2xl rounded-bl-md text-[13px] sm:text-sm leading-relaxed bg-white border border-slate-200 shadow-sm break-words w-fit max-w-full min-w-0 overflow-hidden">
                    <div class="px-3 py-2.5 sm:px-4 sm:py-3">
                        {!! $item->body !!}
                    </div>
                    @if($item->hasAttachments)
                        <div class="px-3 sm:px-4 pb-3">
                            @include('livewire.tickets.partials.attachments-group', ['attachments' => $item->attachments, 'variant' => 'theirs'])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
