<div class="discussion-composer shrink-0 border-t border-slate-200 bg-white/95 p-1.5 sm:p-2.5 z-10 safe-area-pb min-w-0 overflow-hidden backdrop-blur" data-composer>
    <div class="mx-auto max-w-3xl min-w-0">
        <div class="flex items-center gap-1.5 mb-1 sm:mb-1.5">
            <button type="button" wire:click="setAsInternalNote(false)" wire:loading.attr="disabled" wire:target="setAsInternalNote" class="text-[11px] sm:text-xs font-bold transition-colors border-b-2 pb-0.5 touch-manipulation {{ !$asInternalNote ? 'text-slate-900 border-[var(--accent)]' : 'text-slate-500 border-transparent hover:text-slate-900' }}">
                {{ __('Répondre') }}
            </button>
            @if($canWriteInternalNotes)
                <button type="button" wire:click="setAsInternalNote(true)" wire:loading.attr="disabled" wire:target="setAsInternalNote" class="text-[11px] sm:text-xs font-bold transition-colors border-b-2 pb-0.5 flex items-center gap-1 touch-manipulation {{ $asInternalNote ? 'text-amber-700 border-amber-500' : 'text-slate-500 border-transparent hover:text-slate-900' }}">
                    <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="10"></iconify-icon>
                    {{ __('Note interne') }}
                </button>
            @endif
        </div>

        <form wire:submit="sendMessage" x-on:submit="localStorage.removeItem('ticket-draft-{{ $ticketPublicId }}')" class="discussion-composer-box relative rounded-xl border border-slate-200 bg-white shadow-sm focus-within:ring-2 focus-within:ring-[var(--accent)]/25 focus-within:border-[var(--accent)] transition-all min-w-0"
        x-data="{
            users: {{ \Illuminate\Support\Js::from($mentionableUsers ?? []) }},
            mentionOpen: false,
            mentionQuery: '',
            mentionStart: 0,
            mentionCursor: 0,
            draftTimer: null,
            draftKey: 'ticket-draft-{{ $ticketPublicId }}',
            init() {
                try {
                    const saved = localStorage.getItem(this.draftKey);
                    if (saved && !this.$wire.get('body')) {
                        this.$wire.set('body', saved);
                    }
                } catch (e) {}
            },
            get filteredMentions() {
                if (!this.mentionQuery) return this.users.slice(0, 8);
                const q = this.mentionQuery.toLowerCase();
                return this.users.filter(u =>
                    (u.tag && u.tag.toLowerCase().startsWith(q)) ||
                    (u.name && u.name.toLowerCase().includes(q))
                ).slice(0, 8);
            },
            saveDraft(val) {
                clearTimeout(this.draftTimer);
                this.draftTimer = setTimeout(() => {
                    try {
                        if (val && val.trim()) localStorage.setItem(this.draftKey, val);
                        else localStorage.removeItem(this.draftKey);
                    } catch (e) {}
                }, 500);
            },
            onInput(ev) {
                const el = ev.target;
                const val = el.value;
                this.saveDraft(val);
                const pos = el.selectionStart || 0;
                const before = val.slice(0, pos);
                const lastAt = before.lastIndexOf('@');
                if (lastAt === -1) { this.mentionOpen = false; return; }
                const afterAt = before.slice(lastAt + 1);
                if (/[\s\n]/.test(afterAt)) { this.mentionOpen = false; return; }
                this.mentionStart = lastAt;
                this.mentionCursor = pos;
                this.mentionQuery = afterAt;
                this.mentionOpen = true;
            },
            pickUser(user) {
                const el = this.$refs.mentionInput;
                if (!el) return;
                const val = el.value;
                const newVal = val.slice(0, this.mentionStart) + '@' + user.tag + ' ' + val.slice(this.mentionCursor);
                this.$wire.set('body', newVal);
                this.mentionOpen = false;
                this.$nextTick(() => { el.focus(); el.setSelectionRange(this.mentionStart + user.tag.length + 2, this.mentionStart + user.tag.length + 2); });
            }
        }"
        @keydown.escape="mentionOpen = false">
            <div class="p-1.5 sm:p-2 relative">
                <textarea
                    x-ref="mentionInput"
                    wire:model.defer="body"
                    rows="2"
                    @input="onInput($event)"
                    @keydown.arrow-down.prevent="mentionOpen && filteredMentions.length && (mentionOpen = true)"
                    class="w-full bg-transparent border-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 resize-none text-sm p-1 min-h-[2.5rem] sm:min-h-[2.75rem] max-h-28"
                    placeholder="{{ $asInternalNote ? __('Ajouter une note visible uniquement par l\'équipe...') : __('Écrivez votre réponse ici...') }}"
                ></textarea>
                <div x-show="mentionOpen" x-cloak @click.outside="mentionOpen = false"
                    class="absolute left-1 right-1 sm:left-1.5 sm:right-1.5 bottom-full mb-1 py-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 max-h-36 overflow-y-auto"
                    style="display: none;" wire:ignore>
                    <template x-for="u in filteredMentions" :key="u.id">
                        <button type="button" @click="pickUser(u)"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 flex items-center gap-2">
                            <span class="font-medium text-slate-900" x-text="u.name"></span>
                            <span class="text-slate-400 text-xs" x-text="'@' + u.tag"></span>
                        </button>
                    </template>
                    <p x-show="filteredMentions.length === 0" class="px-3 py-2 text-xs text-slate-500">{{ __('Aucun utilisateur') }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-1.5 sm:gap-2 px-1.5 sm:px-2.5 py-1 sm:py-1.5 border-t border-slate-100 rounded-b-xl bg-slate-50/50">
                <div class="flex items-center gap-0.5 min-w-0">
                    <input type="file" wire:model="attachmentFiles" multiple class="hidden" id="composer-file-input-{{ $ticketId }}" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp,image/*">
                    <button type="button" onclick="document.getElementById('composer-file-input-{{ $ticketId }}').click()" wire:loading.attr="disabled" wire:target="attachmentFiles,sendMessage" class="p-1.5 min-h-[34px] min-w-[34px] sm:min-h-0 sm:min-w-0 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors touch-manipulation" title="{{ __('Joindre un fichier') }}">
                        <iconify-icon icon="solar:paperclip-linear" width="16"></iconify-icon>
                    </button>
                    <div x-data="{ emojiOpen: false }" class="relative hidden sm:block">
                        <button type="button" @click="emojiOpen = !emojiOpen" class="p-1.5 flex items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors touch-manipulation" title="{{ __('Emoji') }}">
                            <iconify-icon icon="solar:smile-circle-linear" width="16"></iconify-icon>
                        </button>
                        <div x-show="emojiOpen" @click.outside="emojiOpen = false" x-cloak class="absolute bottom-full left-0 mb-1 p-1.5 rounded-lg bg-white shadow-xl border border-slate-200 grid grid-cols-8 gap-1 max-h-36 overflow-y-auto z-50 w-48">
                            @foreach(['😀','😃','😄','😁','😅','😂','🤣','😊','😇','🙂','🙃','😉','😌','😍','🥰','😘','👍','👎','👏','🙌','👋','💪','✨','🔥','❤️','💯','✅','📎','📁','🔒'] as $emoji)
                                <button type="button" @click="$wire.set('body', ($wire.get('body') || '') + '{{ $emoji }}'); emojiOpen = false" class="p-1.5 hover:bg-slate-100 rounded-lg text-xl transition-colors">{{ $emoji }}</button>
                            @endforeach
                        </div>
                    </div>
                    @if(count($attachmentFiles ?? []) > 0)
                        <span class="ml-0.5 text-[10px] sm:text-xs font-medium text-[var(--accent)] bg-[var(--accent-soft)] px-1.5 py-0.5 rounded">{{ count($attachmentFiles) }}</span>
                    @endif
                    <span wire:loading wire:target="attachmentFiles" class="ml-0.5 text-[10px] text-slate-500">{{ __('Téléversement…') }}</span>
                    <p class="text-[10px] text-slate-400 hidden md:inline ml-2">{{ __('Markdown') }} · <kbd class="px-0.5 py-px rounded bg-slate-100 text-slate-600 font-mono text-[9px]">@</kbd> {{ __('pour mentionner') }}</p>
                </div>

                <x-manexo.action-button
                    type="submit"
                    wire-target="sendMessage"
                    variant="primary"
                    spinner-size="sm"
                    x-ref="submitBtn"
                    class="rounded-lg px-3 sm:px-3.5 py-1.5 sm:py-2 min-h-[34px] sm:min-h-0 text-xs touch-manipulation shrink-0"
                    style="background-color: {{ $asInternalNote ? '#d97706' : 'var(--accent)' }};"
                    :loading-label="__('ui.tickets.sending')"
                >
                    <iconify-icon icon="solar:plain-bold" width="12"></iconify-icon>
                    <span class="hidden sm:inline">{{ __('Envoyer') }}</span>
                </x-manexo.action-button>
            </div>
        </form>
        <x-input-error :messages="$errors->get('body')" class="mt-2" />
    </div>
</div>
