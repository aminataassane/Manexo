<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{ tab: 'branding' }">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#111827] tracking-tight">Paramètres entreprise</h1>
            <p class="mt-1 text-sm text-[#6B7280]">Branding, workflow, catalogue (catégories/priorités) et sécurité.</p>
        </div>

        <a
            href="{{ route('dashboard') }}"
            class="h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition flex items-center gap-2"
        >
            <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
            Retour
        </a>
    </div>

    @if (session('settings_status'))
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-[13px] text-emerald-800">
            {{ session('settings_status') }}
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- LEFT NAV -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E5E7EB]">
                    <div class="text-[11px] uppercase tracking-wider text-[#6B7280] font-semibold">Sections</div>
                </div>
                <div class="p-2 space-y-1">
                    @php
                        $navItem = "w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px] font-medium transition border border-transparent";
                    @endphp
                    <button type="button" class="{{ $navItem }}" :class="tab==='branding' ? 'text-[color:var(--accent)] bg-[color:var(--accent-soft)] border-[color:var(--accent-soft)]' : 'text-[#111827] hover:bg-[#F9FAFB]'" @click="tab='branding'">
                        <iconify-icon icon="solar:palette-linear" width="16"></iconify-icon>
                        Branding
                    </button>
                    <button type="button" class="{{ $navItem }}" :class="tab==='tickets' ? 'text-[color:var(--accent)] bg-[color:var(--accent-soft)] border-[color:var(--accent-soft)]' : 'text-[#111827] hover:bg-[#F9FAFB]'" @click="tab='tickets'">
                        <iconify-icon icon="solar:ticket-linear" width="16"></iconify-icon>
                        Réglages tickets
                    </button>
                    <button type="button" class="{{ $navItem }}" :class="tab==='categories' ? 'text-[color:var(--accent)] bg-[color:var(--accent-soft)] border-[color:var(--accent-soft)]' : 'text-[#111827] hover:bg-[#F9FAFB]'" @click="tab='categories'">
                        <iconify-icon icon="solar:tag-linear" width="16"></iconify-icon>
                        Catégories
                    </button>
                    <button type="button" class="{{ $navItem }}" :class="tab==='priorities' ? 'text-[color:var(--accent)] bg-[color:var(--accent-soft)] border-[color:var(--accent-soft)]' : 'text-[#111827] hover:bg-[#F9FAFB]'" @click="tab='priorities'">
                        <iconify-icon icon="solar:flag-linear" width="16"></iconify-icon>
                        Priorités / SLA
                    </button>
                    <button type="button" class="{{ $navItem }}" :class="tab==='integrations' ? 'text-[color:var(--accent)] bg-[color:var(--accent-soft)] border-[color:var(--accent-soft)]' : 'text-[#111827] hover:bg-[#F9FAFB]'" @click="tab='integrations'">
                        <iconify-icon icon="solar:plug-circle-linear" width="16"></iconify-icon>
                        Intégrations
                    </button>
                    <button type="button" class="{{ $navItem }}" :class="tab==='danger' ? 'text-red-700 bg-red-50 border-red-100' : 'text-[#111827] hover:bg-[#F9FAFB]'" @click="tab='danger'">
                        <iconify-icon icon="solar:danger-triangle-linear" width="16"></iconify-icon>
                        Zone danger
                    </button>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">Aide</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Infos rapides.</p>
                </div>
                <div class="p-4 sm:p-6 space-y-3 text-[12px] text-[#6B7280]">
                    <div class="flex gap-2">
                        <iconify-icon icon="solar:shield-check-linear" width="16"></iconify-icon>
                        <p>Modification réservée à <span class="font-medium text-[#111827]">Owner/Admin</span>.</p>
                    </div>
                    <div class="flex gap-2">
                        <iconify-icon icon="solar:upload-linear" width="16"></iconify-icon>
                        <p>Logo: si rien n’apparaît → <span class="font-mono">php artisan storage:link</span>.</p>
                    </div>
                    <div class="flex gap-2">
                        <iconify-icon icon="solar:link-linear" width="16"></iconify-icon>
                        <p>Gestion équipe: <a class="underline hover:opacity-80" style="color: var(--accent);" href="{{ route('admin.users') }}">/admin/users</a></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="lg:col-span-3 space-y-6">
            @if (! $canManage)
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-[13px] text-amber-900">
                    Accès limité. Seuls <span class="font-semibold">Owner</span> et <span class="font-semibold">Admin</span> peuvent modifier ces paramètres.
                </div>
            @endif

            <!-- BRANDING -->
            <div x-show="tab==='branding'" x-cloak class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">Branding</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Nom, slug, accent, logo.</p>
                </div>

                <form wire:submit="save" class="p-4 sm:p-6 space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="org_name" value="Nom de l’entreprise" class="text-[#111827]" />
                            <x-text-input id="org_name" class="mt-1" type="text" wire:model="name" required @disabled(! $canManage) />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="org_slug" value="Slug (URL)" class="text-[#111827]" />
                            <x-text-input id="org_slug" class="mt-1" type="text" wire:model="slug" required @disabled(! $canManage) />
                            <p class="mt-2 text-[12px] text-[#6B7280]">Ex: <span class="font-mono">quality-center</span></p>
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="primary_color" value="Couleur principale" class="text-[#111827]" />
                            <div class="mt-1 flex items-center gap-2">
                                <input
                                    id="primary_color"
                                    type="color"
                                    class="h-10 w-12 rounded-md border border-[#E5E7EB] bg-white"
                                    wire:model="primary_color"
                                    @disabled(! $canManage)
                                />
                                <input
                                    type="text"
                                    class="flex-1 h-10 px-3 rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] placeholder:text-[#9CA3AF] focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition"
                                    placeholder="#005F02"
                                    wire:model="primary_color"
                                    @disabled(! $canManage)
                                />
                            </div>
                            <x-input-error :messages="$errors->get('primary_color')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="logo" value="Logo" class="text-[#111827]" />
                            <input
                                id="logo"
                                type="file"
                                accept="image/*"
                                wire:model="logo"
                                @disabled(! $canManage)
                                class="mt-1 block w-full text-[13px] text-[#111827]
                                       file:mr-3 file:py-2 file:px-3 file:rounded-md file:border file:border-[#E5E7EB]
                                       file:bg-white file:text-[#111827] file:text-[13px] file:font-medium
                                       hover:file:bg-[#F9FAFB] transition"
                            />
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />

                            <div class="mt-3 flex items-center gap-3">
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="w-12 h-12 rounded-md border border-[#E5E7EB] bg-white object-cover" alt="Preview">
                                    <div class="text-[12px] text-[#6B7280]">Aperçu du nouveau logo.</div>
                                @elseif ($currentLogoUrl)
                                    <img src="{{ $currentLogoUrl }}" class="w-12 h-12 rounded-md border border-[#E5E7EB] bg-white object-cover" alt="Logo">
                                    <button
                                        type="button"
                                        class="h-9 px-3 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition"
                                        wire:click="removeLogo"
                                        @disabled(! $canManage)
                                    >
                                        Supprimer
                                    </button>
                                @else
                                    <div class="text-[12px] text-[#6B7280]">Aucun logo.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E5E7EB] flex justify-end">
                        <button
                            type="submit"
                            class="h-10 px-4 text-white text-[13px] font-semibold rounded-md shadow-sm transition-colors flex items-center justify-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] disabled:opacity-50 disabled:cursor-not-allowed"
                            @disabled(! $canManage)
                        >
                            <iconify-icon icon="solar:diskette-linear" width="16"></iconify-icon>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- TICKETS SETTINGS -->
            <div x-show="tab==='tickets'" x-cloak class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">Réglages tickets</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Valeurs par défaut et permissions.</p>
                </div>

                <form wire:submit="save" class="p-4 sm:p-6 space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="default_category" value="Catégorie par défaut" class="text-[#111827]" />
                            <div class="relative mt-1">
                                <select
                                    id="default_category"
                                    wire:model="default_category_id"
                                    @disabled(! $canManage)
                                    class="block w-full h-10 rounded-md border border-[#E5E7EB] bg-white text-[#111827] text-[13px] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] appearance-none pr-9 transition disabled:opacity-50"
                                >
                                    <option value="">—</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}{{ $c->is_active ? '' : ' (inactif)' }}</option>
                                    @endforeach
                                </select>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                            </div>
                            <x-input-error :messages="$errors->get('default_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="default_priority" value="Priorité par défaut" class="text-[#111827]" />
                            <div class="relative mt-1">
                                <select
                                    id="default_priority"
                                    wire:model="default_priority_id"
                                    @disabled(! $canManage)
                                    class="block w-full h-10 rounded-md border border-[#E5E7EB] bg-white text-[#111827] text-[13px] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] appearance-none pr-9 transition disabled:opacity-50"
                                >
                                    <option value="">—</option>
                                    @foreach ($priorities as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->level }}){{ $p->is_active ? '' : ' (inactif)' }}</option>
                                    @endforeach
                                </select>
                                <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                            </div>
                            <x-input-error :messages="$errors->get('default_priority_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="auto_close_days" value="Auto-clôture (jours)" class="text-[#111827]" />
                            <input
                                id="auto_close_days"
                                type="number"
                                min="1"
                                max="365"
                                wire:model="auto_close_days"
                                @disabled(! $canManage)
                                class="mt-1 block w-full h-10 px-3 rounded-md border border-[#E5E7EB] bg-white text-[13px] text-[#111827] placeholder:text-[#9CA3AF] focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition disabled:opacity-50"
                                placeholder="ex: 30"
                            />
                            <p class="mt-2 text-[12px] text-[#6B7280]">Optionnel. Utilisable plus tard pour automatiser la fermeture.</p>
                            <x-input-error :messages="$errors->get('auto_close_days')" class="mt-2" />
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3">
                                <div>
                                    <div class="text-[13px] font-medium text-[#111827]">Les membres peuvent modifier</div>
                                    <div class="text-[12px] text-[#6B7280]">Sur leurs propres tickets.</div>
                                </div>
                                <input type="checkbox" wire:model="members_can_edit" @disabled(! $canManage) class="h-5 w-5 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]" />
                            </div>
                            <div class="flex items-center justify-between gap-4 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3">
                                <div>
                                    <div class="text-[13px] font-medium text-[#111827]">Les membres peuvent supprimer</div>
                                    <div class="text-[12px] text-[#6B7280]">Sur leurs propres tickets.</div>
                                </div>
                                <input type="checkbox" wire:model="members_can_delete" @disabled(! $canManage) class="h-5 w-5 rounded border-[#E5E7EB] text-[color:var(--accent)] focus:ring-[color:var(--accent-ring)]" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E5E7EB] flex justify-end">
                        <button
                            type="submit"
                            class="h-10 px-4 text-white text-[13px] font-semibold rounded-md shadow-sm transition-colors flex items-center justify-center gap-2 bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)] disabled:opacity-50 disabled:cursor-not-allowed"
                            @disabled(! $canManage)
                        >
                            <iconify-icon icon="solar:diskette-linear" width="16"></iconify-icon>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- CATEGORIES / PRIORITIES / INTEGRATIONS placeholders -->
            <div x-show="tab==='categories'" x-cloak class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">Catégories</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Gestion rapide (bientôt CRUD complet).</p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="text-[13px] text-[#6B7280]">Pour l’instant, tu peux gérer via la DB/seeders. Je te fais le CRUD complet juste après la migration.</div>
                </div>
            </div>

            <div x-show="tab==='priorities'" x-cloak class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">Priorités / SLA</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Gestion rapide (bientôt CRUD complet).</p>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="text-[13px] text-[#6B7280]">Je te fais le CRUD complet (priorité + niveau + actif) juste après la migration.</div>
                </div>
            </div>

            <div x-show="tab==='integrations'" x-cloak class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-[#E5E7EB]">
                    <h2 class="text-[13px] font-semibold text-[#111827]">Intégrations</h2>
                    <p class="mt-1 text-[12px] text-[#6B7280]">Connexions externes (placeholder).</p>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="rounded-lg border border-[#E5E7EB] p-4 bg-[#F9FAFB]">
                        <div class="flex items-center justify-between">
                            <div class="text-[13px] font-semibold text-[#111827]">Slack</div>
                            <span class="text-[11px] text-[#6B7280]">Bientôt</span>
                        </div>
                        <p class="mt-1 text-[12px] text-[#6B7280]">Notifications tickets et mentions.</p>
                    </div>
                    <div class="rounded-lg border border-[#E5E7EB] p-4 bg-[#F9FAFB]">
                        <div class="flex items-center justify-between">
                            <div class="text-[13px] font-semibold text-[#111827]">Email</div>
                            <span class="text-[11px] text-[#6B7280]">Bientôt</span>
                        </div>
                        <p class="mt-1 text-[12px] text-[#6B7280]">Inbound email → ticket.</p>
                    </div>
                </div>
            </div>

            <div x-show="tab==='danger'" x-cloak class="rounded-xl border border-red-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-red-200 bg-red-50">
                    <h2 class="text-[13px] font-semibold text-red-900">Zone danger</h2>
                    <p class="mt-1 text-[12px] text-red-800/80">Actions irréversibles.</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[13px] text-red-900">
                        Suppression de l’entreprise = suppression des tickets et données liées (cascade DB).
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 items-end">
                        <div>
                            <x-input-label for="dangerConfirmName" value="Tape le nom exact de l’entreprise pour confirmer" class="text-[#111827]" />
                            <x-text-input id="dangerConfirmName" class="mt-1" type="text" wire:model="dangerConfirmName" placeholder="{{ $org?->name ?? '' }}" @disabled(! $isOwner) />
                            <x-input-error :messages="$errors->get('dangerConfirmName')" class="mt-2" />
                        </div>
                        <div class="flex sm:justify-end">
                            <button
                                type="button"
                                class="h-10 px-4 rounded-md border border-red-200 bg-red-600 text-white text-[13px] font-semibold shadow-sm hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:click="deleteOrganization"
                                @disabled(! $isOwner)
                            >
                                Supprimer l’entreprise
                            </button>
                        </div>
                    </div>

                    @if (! $isOwner)
                        <div class="text-[12px] text-[#6B7280]">Seul le rôle <span class="font-semibold">Owner</span> peut supprimer une entreprise.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

