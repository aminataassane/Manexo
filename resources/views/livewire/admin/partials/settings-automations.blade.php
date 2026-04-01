<!-- AUTOMATIONS TAB -->
<div x-show="tab === 'automations'" x-cloak class="space-y-6">
    {{-- Card 1: Activation --}}
    <form wire:submit.prevent="saveAutomationsSettings">
        <div class="content-card">
            <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                <h2 class="text-lg font-bold text-slate-900">Automatisations</h2>
                <p class="text-sm text-slate-500">Configurez des regles pour automatiser le tri, l'assignation et le suivi des tickets.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-semibold text-slate-900">Activer les automatisations</label>
                        <p class="text-xs text-slate-500 mt-0.5">Les regles actives seront executees automatiquement lors des evenements configurees.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$toggle('automationsEnabled')"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2"
                        :class="$wire.automationsEnabled ? 'bg-[var(--accent)]' : 'bg-slate-200'"
                        role="switch"
                        :aria-checked="$wire.automationsEnabled"
                    >
                        <span
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="$wire.automationsEnabled ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                </div>
            </div>

            <div class="px-6 pb-6 flex justify-end">
                <x-manexo.action-button type="submit" wire-target="saveAutomationsSettings" variant="primary" class="!px-5 !font-semibold" :loading-label="__('ui.action.saving')">
                    <iconify-icon icon="solar:diskette-bold" width="18"></iconify-icon>
                    Enregistrer
                </x-manexo.action-button>
            </div>
        </div>
    </form>

    {{-- Card 2: Rules list --}}
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9; flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Regles d'automatisation</h2>
                <p class="text-sm text-slate-500">Les regles sont executees dans l'ordre affiche.</p>
            </div>
            <button
                type="button"
                wire:click="openCreateRule"
                class="inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all"
            >
                <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                Ajouter une regle
            </button>
        </div>

        <div class="p-6">
            @if ($automationRules->isEmpty())
                <div class="text-center py-12">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="48" class="text-slate-300 mb-3"></iconify-icon>
                    <p class="text-sm text-slate-500">Aucune regle d'automatisation configuree.</p>
                    <p class="text-xs text-slate-400 mt-1">Creez votre premiere regle pour automatiser le traitement des tickets.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="px-4 py-3 w-16">Ordre</th>
                                <th class="px-4 py-3">Nom</th>
                                <th class="px-4 py-3">Declencheur</th>
                                <th class="px-4 py-3 text-center">Actif</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($automationRules as $rule)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <button type="button" wire:click="moveRuleUp({{ $rule->id }})" class="p-1 text-slate-400 hover:text-slate-600 transition-colors" title="Monter">
                                                <iconify-icon icon="solar:alt-arrow-up-linear" width="16"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="moveRuleDown({{ $rule->id }})" class="p-1 text-slate-400 hover:text-slate-600 transition-colors" title="Descendre">
                                                <iconify-icon icon="solar:alt-arrow-down-linear" width="16"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-medium text-slate-900">{{ $rule->name }}</span>
                                        @if ($rule->description)
                                            <p class="text-xs text-slate-500 mt-0.5">{{ \Illuminate\Support\Str::limit($rule->description, 60) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $triggerLabels = [
                                                'ticket_created' => 'Ticket cree',
                                                'status_changed' => 'Statut modifie',
                                                'priority_changed' => 'Priorite modifiee',
                                                'sla_at_risk' => 'SLA a risque',
                                                'sla_breached' => 'SLA depasse',
                                            ];
                                            $triggerColors = [
                                                'ticket_created' => 'bg-blue-100 text-blue-700',
                                                'status_changed' => 'bg-amber-100 text-amber-700',
                                                'priority_changed' => 'bg-purple-100 text-purple-700',
                                                'sla_at_risk' => 'bg-orange-100 text-orange-700',
                                                'sla_breached' => 'bg-red-100 text-red-700',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $triggerColors[$rule->trigger_type] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $triggerLabels[$rule->trigger_type] ?? $rule->trigger_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            wire:click="toggleRule({{ $rule->id }})"
                                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                            :class="{{ $rule->is_active ? 'true' : 'false' }} ? 'bg-[var(--accent)]' : 'bg-slate-200'"
                                        >
                                            <span
                                                class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                :class="{{ $rule->is_active ? 'true' : 'false' }} ? 'translate-x-4' : 'translate-x-0'"
                                            ></span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" wire:click="openEditRule({{ $rule->id }})" class="p-1.5 text-slate-400 hover:text-[var(--accent)] transition-colors rounded-lg hover:bg-slate-100" title="Modifier">
                                                <iconify-icon icon="solar:pen-bold" width="16"></iconify-icon>
                                            </button>
                                            <button
                                                type="button"
                                                @click="$dispatch('confirm-action', { title: 'Supprimer', message: 'Supprimer cette regle d\'automatisation ?', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteRule({{ $rule->id }}) })"
                                                class="p-1.5 text-slate-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50"
                                                title="Supprimer"
                                            >
                                                <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: Rule editor --}}
    @if ($showRuleModal)
        <div
            x-data="automationRuleEditor(@js($editingRule))"
            x-show="true"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 backdrop-blur-sm pt-12 pb-12"
            @keydown.escape.window="$wire.set('showRuleModal', false)"
        >
            <div
                @click.outside="$wire.set('showRuleModal', false)"
                class="content-card shadow-xl w-full max-w-3xl mx-4"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                {{-- Header --}}
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9; rounded-t-2xl flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $editingRuleId ? 'Modifier la regle' : 'Nouvelle regle d\'automatisation' }}
                    </h3>
                    <button type="button" wire:click="$set('showRuleModal', false)" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                        <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    {{-- Name & Description --}}
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-900 mb-1">Nom de la regle *</label>
                            <input type="text" wire:model="editingRule.name" class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" placeholder="Ex: Assigner tickets urgents">
                            @error('editingRule.name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-900 mb-1">Description</label>
                            <input type="text" wire:model="editingRule.description" class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" placeholder="Optionnel">
                        </div>
                    </div>

                    {{-- QUAND (trigger) --}}
                    <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4">
                        <h4 class="text-sm font-bold text-blue-900 flex items-center gap-2 mb-3">
                            <iconify-icon icon="solar:clock-circle-bold" width="18"></iconify-icon>
                            QUAND (declencheur)
                        </h4>
                        <x-select-input wire:model.live="editingRule.trigger_type">
                            <option value="ticket_created">Ticket cree</option>
                            <option value="status_changed">Statut modifie</option>
                            <option value="priority_changed">Priorite modifiee</option>
                            <option value="sla_at_risk">SLA a risque</option>
                            <option value="sla_breached">SLA depasse</option>
                        </x-select-input>
                    </div>

                    {{-- SI (conditions) --}}
                    <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4">
                        <h4 class="text-sm font-bold text-amber-900 flex items-center gap-2 mb-3">
                            <iconify-icon icon="solar:filter-bold" width="18"></iconify-icon>
                            SI (conditions) — toutes doivent etre vraies
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Categorie</label>
                                <x-select-input wire:model="editingRule.conditions.category_id">
                                    <option value="">— Toutes —</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Priorite</label>
                                <x-select-input wire:model="editingRule.conditions.priority_id">
                                    <option value="">— Toutes —</option>
                                    @foreach ($priorities as $prio)
                                        <option value="{{ $prio->id }}">{{ $prio->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Groupe</label>
                                <x-select-input wire:model="editingRule.conditions.group_id">
                                    <option value="">— Tous —</option>
                                    @foreach ($ticketGroups as $grp)
                                        <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Source</label>
                                <x-select-input wire:model="editingRule.conditions.source">
                                    <option value="">— Toutes —</option>
                                    <option value="platform">Plateforme</option>
                                    <option value="form">Formulaire</option>
                                    <option value="email">Email</option>
                                </x-select-input>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Statut</label>
                                <x-select-input wire:model="editingRule.conditions.status">
                                    <option value="">— Tous —</option>
                                    <option value="open">Ouvert</option>
                                    <option value="in_progress">En cours</option>
                                    <option value="pending">En attente</option>
                                    <option value="resolved">Resolu</option>
                                    <option value="closed">Ferme</option>
                                </x-select-input>
                            </div>
                            <div class="flex items-center gap-2 pt-5">
                                <input type="checkbox" wire:model="editingRule.conditions.is_unassigned" id="cond_unassigned" class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]">
                                <label for="cond_unassigned" class="text-sm text-slate-700">Non assigne</label>
                            </div>
                        </div>
                    </div>

                    {{-- ALORS (actions) --}}
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4" x-data>
                        <h4 class="text-sm font-bold text-emerald-900 flex items-center gap-2 mb-3">
                            <iconify-icon icon="solar:play-bold" width="18"></iconify-icon>
                            ALORS (actions)
                        </h4>

                        <div class="space-y-3">
                            @foreach ($editingRule['actions'] ?? [] as $idx => $act)
                                <div class="flex items-start gap-2 bg-white rounded-xl border border-slate-200 p-3">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <x-select-input wire:model.live="editingRule.actions.{{ $idx }}.type">
                                                <option value="">— Type d'action —</option>
                                                <option value="assign_responsible">Assigner responsable</option>
                                                <option value="add_collaborators">Ajouter collaborateurs</option>
                                                <option value="change_priority">Changer priorite</option>
                                                <option value="change_status">Changer statut</option>
                                                <option value="change_group">Deplacer vers groupe</option>
                                                <option value="notify_users">Notifier utilisateurs</option>
                                                <option value="add_checklist">Ajouter checklist</option>
                                            </x-select-input>
                                        </div>
                                        <div>
                                            @if (($act['type'] ?? '') === 'assign_responsible')
                                                <x-select-input wire:model="editingRule.actions.{{ $idx }}.user_id">
                                                    <option value="">— Choisir —</option>
                                                    @foreach ($members as $m)
                                                        <option value="{{ $m->user?->id }}">{{ $m->user?->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            @elseif (($act['type'] ?? '') === 'add_collaborators' || ($act['type'] ?? '') === 'notify_users')
                                                <x-select-input wire:model="editingRule.actions.{{ $idx }}.user_ids" multiple>
                                                    @foreach ($members as $m)
                                                        <option value="{{ $m->user?->id }}">{{ $m->user?->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            @elseif (($act['type'] ?? '') === 'change_priority')
                                                <x-select-input wire:model="editingRule.actions.{{ $idx }}.priority_id">
                                                    <option value="">— Choisir —</option>
                                                    @foreach ($priorities as $prio)
                                                        <option value="{{ $prio->id }}">{{ $prio->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            @elseif (($act['type'] ?? '') === 'change_status')
                                                <x-select-input wire:model="editingRule.actions.{{ $idx }}.status">
                                                    <option value="">— Choisir —</option>
                                                    <option value="open">Ouvert</option>
                                                    <option value="in_progress">En cours</option>
                                                    <option value="pending">En attente</option>
                                                    <option value="resolved">Resolu</option>
                                                    <option value="closed">Ferme</option>
                                                </x-select-input>
                                            @elseif (($act['type'] ?? '') === 'change_group')
                                                <x-select-input wire:model="editingRule.actions.{{ $idx }}.group_id">
                                                    <option value="">— Choisir —</option>
                                                    @foreach ($ticketGroups as $grp)
                                                        <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                                                    @endforeach
                                                </x-select-input>
                                            @elseif (($act['type'] ?? '') === 'add_checklist')
                                                <input type="text" wire:model="editingRule.actions.{{ $idx }}.items_text" class="block w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" placeholder="Items separes par des virgules">
                                                <p class="text-xs text-slate-400 mt-0.5">Ex: Verifier identite, Creer compte</p>
                                            @else
                                                <span class="text-xs text-slate-400 pt-2 block">Selectionnez un type d'action</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="$set('editingRule.actions', {{ json_encode(array_values(collect($editingRule['actions'] ?? [])->forget($idx)->all())) }})"
                                        class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors mt-0.5"
                                    >
                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button
                            type="button"
                            wire:click="$set('editingRule.actions', {{ json_encode(array_merge($editingRule['actions'] ?? [], [['type' => '', 'user_id' => null, 'user_ids' => [], 'priority_id' => null, 'status' => '', 'group_id' => null, 'items_text' => '']])) }})"
                            class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 hover:text-emerald-900 transition-colors"
                        >
                            <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                            Ajouter une action
                        </button>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-slate-50/50 rounded-b-2xl flex items-center justify-end gap-3" style="border-top: 1px solid #f1f5f9;">
                    <button type="button" wire:click="$set('showRuleModal', false)" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        Annuler
                    </button>
                    <button
                        type="button"
                        wire:click="saveRule"
                        class="inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                    >
                        <iconify-icon icon="solar:diskette-bold" width="18" wire:loading.remove wire:target="saveRule"></iconify-icon>
                        <iconify-icon icon="solar:refresh-bold" width="18" class="animate-spin" wire:loading wire:target="saveRule"></iconify-icon>
                        {{ $editingRuleId ? 'Mettre a jour' : 'Creer la regle' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('automationRuleEditor', (initialRule) => ({
        rule: initialRule,
    }));
</script>
@endscript
