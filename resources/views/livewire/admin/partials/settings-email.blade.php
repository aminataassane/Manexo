{{-- EMAIL TAB --}}
<div x-show="tab === 'email'" x-cloak class="space-y-6">
    {{-- Config Card --}}
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Configuration Email (IMAP)</h2>
                    <p class="text-sm text-slate-500">Recevez et répondez aux tickets par email.</p>
                </div>
                @if ($mailboxIsActive)
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Actif
                    </span>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="saveMailbox" class="p-6 space-y-8">
            {{-- Mailbox Identity --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Adresse email</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="mailbox_email" value="Adresse email *" />
                        <input
                            id="mailbox_email"
                            type="email"
                            wire:model="mailboxEmail"
                            required
                            placeholder="support@exemple.com"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxEmail')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="mailbox_display_name" value="Nom d'affichage" />
                        <input
                            id="mailbox_display_name"
                            type="text"
                            wire:model="mailboxDisplayName"
                            placeholder="Support Entreprise"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- IMAP Settings --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Serveur IMAP</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="mailbox_imap_host" value="Hôte IMAP *" />
                        <input
                            id="mailbox_imap_host"
                            type="text"
                            wire:model="mailboxImapHost"
                            required
                            placeholder="imap.gmail.com"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxImapHost')" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="mailbox_imap_port" value="Port *" />
                            <input
                                id="mailbox_imap_port"
                                type="number"
                                wire:model="mailboxImapPort"
                                required
                                min="1"
                                max="65535"
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                @disabled(! $canManage)
                            />
                            <x-input-error :messages="$errors->get('mailboxImapPort')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="mailbox_imap_encryption" value="Chiffrement *" />
                            <x-select-input id="mailbox_imap_encryption" wire:model="mailboxImapEncryption" class="mt-1" :disabled="! $canManage">
                                <option value="ssl">SSL</option>
                                <option value="tls">TLS</option>
                                <option value="none">Aucun</option>
                            </x-select-input>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 mt-4">
                    <div>
                        <x-input-label for="mailbox_imap_username" value="Identifiant *" />
                        <input
                            id="mailbox_imap_username"
                            type="text"
                            wire:model="mailboxImapUsername"
                            required
                            placeholder="support@exemple.com"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxImapUsername')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="mailbox_imap_password" value="Mot de passe" />
                        <input
                            id="mailbox_imap_password"
                            type="password"
                            wire:model="mailboxImapPassword"
                            placeholder="Laisser vide pour conserver l'existant"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxImapPassword')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4 max-w-xs">
                    <x-input-label for="mailbox_imap_folder" value="Dossier IMAP" />
                    <input
                        id="mailbox_imap_folder"
                        type="text"
                        wire:model="mailboxImapFolder"
                        placeholder="INBOX"
                        class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                        @disabled(! $canManage)
                    />
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- SMTP Settings --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-semibold text-slate-900">Serveur SMTP (envoi)</h3>
                    <button
                        type="button"
                        x-on:click="
                            $wire.set('mailboxSmtpHost', $wire.mailboxImapHost.replace('imap.', 'smtp.'));
                            $wire.set('mailboxSmtpUsername', $wire.mailboxImapUsername);
                            $wire.set('mailboxSmtpEncryption', 'tls');
                            $wire.set('mailboxSmtpPort', 587);
                        "
                        class="text-xs font-medium text-[var(--accent)] hover:underline"
                        @disabled(! $canManage)
                    >
                        Copier depuis IMAP
                    </button>
                </div>
                <p class="text-xs text-slate-500 mb-4">Seul l'hôte SMTP est requis. L'identifiant et le mot de passe sont repris automatiquement depuis l'IMAP si laissés vides.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="mailbox_smtp_host" value="Hôte SMTP" />
                        <input
                            id="mailbox_smtp_host"
                            type="text"
                            wire:model="mailboxSmtpHost"
                            placeholder="smtp.gmail.com"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxSmtpHost')" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="mailbox_smtp_port" value="Port" />
                            <input
                                id="mailbox_smtp_port"
                                type="number"
                                wire:model="mailboxSmtpPort"
                                min="1"
                                max="65535"
                                placeholder="587"
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                @disabled(! $canManage)
                            />
                            <x-input-error :messages="$errors->get('mailboxSmtpPort')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="mailbox_smtp_encryption" value="Chiffrement" />
                            <x-select-input id="mailbox_smtp_encryption" wire:model="mailboxSmtpEncryption" class="mt-1" :disabled="! $canManage">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="none">Aucun</option>
                            </x-select-input>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 mt-4">
                    <div>
                        <x-input-label for="mailbox_smtp_username" value="Identifiant SMTP" />
                        <input
                            id="mailbox_smtp_username"
                            type="text"
                            wire:model="mailboxSmtpUsername"
                            placeholder="Vide = identifiant IMAP"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxSmtpUsername')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="mailbox_smtp_password" value="Mot de passe SMTP" />
                        <input
                            id="mailbox_smtp_password"
                            type="password"
                            wire:model="mailboxSmtpPassword"
                            placeholder="Vide = mot de passe IMAP"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                            @disabled(! $canManage)
                        />
                        <x-input-error :messages="$errors->get('mailboxSmtpPassword')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="testSmtpConnection"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all"
                        @disabled(! $canManage)
                    >
                        <span wire:loading.remove wire:target="testSmtpConnection">
                            <iconify-icon icon="solar:letter-opened-linear" width="18"></iconify-icon>
                            Tester l'envoi SMTP
                        </span>
                        <span wire:loading wire:target="testSmtpConnection">
                            <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="18"></iconify-icon>
                            Test en cours...
                        </span>
                    </button>
                </div>

                {{-- SMTP Test result --}}
                @if ($mailboxSmtpTestResult)
                    @if ($mailboxSmtpTestResult === 'success')
                        <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3">
                            <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                            Connexion SMTP réussie !
                        </div>
                    @else
                        <div class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center gap-3">
                            <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                            Échec SMTP : {{ str_replace('error:', '', $mailboxSmtpTestResult) }}
                        </div>
                    @endif
                @endif
            </div>

            <hr class="border-slate-100">

            {{-- Defaults --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Valeurs par défaut pour les tickets email</h3>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label value="Catégorie" />
                        <x-select-input wire:model="mailboxDefaultCategoryId" class="mt-1" :disabled="! $canManage">
                            <option value="">Aucune</option>
                            @foreach ($categories as $c)
                                @if ($c->is_active)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endif
                            @endforeach
                        </x-select-input>
                    </div>
                    <div>
                        <x-input-label value="Priorité" />
                        <x-select-input wire:model="mailboxDefaultPriorityId" class="mt-1" :disabled="! $canManage">
                            <option value="">Aucune</option>
                            @foreach ($priorities as $p)
                                @if ($p->is_active)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endif
                            @endforeach
                        </x-select-input>
                    </div>
                    <div>
                        <x-input-label value="Groupe" />
                        <x-select-input wire:model="mailboxDefaultGroupId" class="mt-1" :disabled="! $canManage">
                            <option value="">Aucun</option>
                            @foreach ($ticketGroups as $g)
                                @if ($g->is_active)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endif
                            @endforeach
                        </x-select-input>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="btn-primary" @disabled(! $canManage)>
                    <span wire:loading.remove wire:target="saveMailbox">Enregistrer</span>
                    <span wire:loading wire:target="saveMailbox"><iconify-icon icon="solar:refresh-linear" class="animate-spin"></iconify-icon></span>
                </button>

                <button
                    type="button"
                    wire:click="testMailboxConnection"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all"
                    @disabled(! $canManage)
                >
                    <span wire:loading.remove wire:target="testMailboxConnection">
                        <iconify-icon icon="solar:plug-circle-bolt-linear" width="18"></iconify-icon>
                        Tester la connexion
                    </span>
                    <span wire:loading wire:target="testMailboxConnection">
                        <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="18"></iconify-icon>
                        Test en cours...
                    </span>
                </button>

                @if ($mailboxIsActive)
                <button
                    type="button"
                    wire:click="fetchMailboxNow"
                    class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-100 transition-all"
                    @disabled(! $canManage)
                >
                    <span wire:loading.remove wire:target="fetchMailboxNow">
                        <iconify-icon icon="solar:inbox-line-bold-duotone" width="18"></iconify-icon>
                        Synchroniser maintenant
                    </span>
                    <span wire:loading wire:target="fetchMailboxNow">
                        <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="18"></iconify-icon>
                        Récupération...
                    </span>
                </button>
                @endif

                <button
                    type="button"
                    wire:click="toggleMailbox"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition-all {{ $mailboxIsActive ? 'border border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                    @disabled(! $canManage)
                >
                    <iconify-icon icon="{{ $mailboxIsActive ? 'solar:pause-bold' : 'solar:play-bold' }}" width="16"></iconify-icon>
                    {{ $mailboxIsActive ? 'Désactiver' : 'Activer' }}
                </button>
            </div>

            {{-- Test result --}}
            @if ($mailboxTestResult)
                @if ($mailboxTestResult === 'success')
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3">
                        <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
                        Connexion IMAP réussie !
                    </div>
                @else
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center gap-3">
                        <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                        Échec : {{ str_replace('error:', '', $mailboxTestResult) }}
                    </div>
                @endif
            @endif
        </form>
    </div>

    {{-- Status Card --}}
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
            <h2 class="text-lg font-bold text-slate-900">État de la réception</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Dernière récupération</span>
                        <span class="block text-sm font-medium text-slate-900">{{ $mailboxLastFetchedAt ?? 'Jamais' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl {{ $mailboxLastError ? 'bg-red-50 border border-red-100' : 'bg-slate-50 border border-slate-100' }}">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" width="20" class="{{ $mailboxLastError ? 'text-red-400' : 'text-slate-400' }} shrink-0"></iconify-icon>
                    <div class="min-w-0">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider {{ $mailboxLastError ? 'text-red-400' : 'text-slate-400' }}">Dernière erreur</span>
                        <span class="block text-sm font-medium {{ $mailboxLastError ? 'text-red-800' : 'text-slate-900' }} truncate">{{ $mailboxLastError ?? 'Aucune' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                <div class="flex items-start gap-3">
                    <iconify-icon icon="solar:info-circle-bold" class="text-blue-500 mt-0.5 shrink-0" width="18"></iconify-icon>
                    <div>
                        <p class="font-semibold">Comment ça fonctionne</p>
                        <ul class="mt-2 space-y-1 text-xs text-blue-700 list-disc list-inside">
                            <li>Les emails sont récupérés toutes les 2 minutes via IMAP.</li>
                            <li>Un email d'un membre connu crée un ticket en statut <strong>Ouvert</strong>.</li>
                            <li>Un email d'un inconnu crée un ticket en statut <strong>En attente</strong> (à valider par un agent).</li>
                            <li>Les réponses aux notifications email sont ajoutées à la conversation du ticket existant.</li>
                            <li>Les auto-réponses et bounces sont automatiquement ignorés.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
