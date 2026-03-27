<!-- SLA TAB -->
<div x-show="tab === 'sla'" x-cloak class="space-y-6">
    <form wire:submit.prevent="saveSlaSettings">
        {{-- Card 1: SLA General Settings --}}
        <div class="content-card">
            <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                <h2 class="text-lg font-bold text-slate-900">Accords de niveau de service (SLA)</h2>
                <p class="text-sm text-slate-500">Configurez les objectifs de temps de réponse et de résolution par priorité.</p>
            </div>

            <div class="p-6 space-y-6">
                {{-- Toggle SLA --}}
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-semibold text-slate-900">Activer le suivi SLA</label>
                        <p class="text-xs text-slate-500 mt-0.5">Les délais seront calculés automatiquement pour chaque nouveau ticket.</p>
                    </div>
                    <button
                        type="button"
                        wire:click="$toggle('slaEnabled')"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2"
                        :class="$wire.slaEnabled ? 'bg-[var(--accent)]' : 'bg-slate-200'"
                        role="switch"
                        :aria-checked="$wire.slaEnabled"
                    >
                        <span
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="$wire.slaEnabled ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                </div>

                {{-- At-risk threshold --}}
                <div>
                    <label for="slaAtRiskThreshold" class="block text-sm font-semibold text-slate-900">
                        Seuil "à risque" (%)
                    </label>
                    <p class="text-xs text-slate-500 mt-0.5 mb-2">
                        Quand ce pourcentage du temps SLA est écoulé, le ticket passe en statut "à risque".
                    </p>
                    <div class="flex items-center gap-2 max-w-xs">
                        <input
                            type="number"
                            id="slaAtRiskThreshold"
                            wire:model="slaAtRiskThreshold"
                            min="1"
                            max="99"
                            class="block w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                        >
                        <span class="text-sm text-slate-500">%</span>
                    </div>
                    @error('slaAtRiskThreshold')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Card 2: SLA Policies per Priority --}}
        <div class="content-card mt-6">
            <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                <h2 class="text-lg font-bold text-slate-900">Délais par priorité</h2>
                <p class="text-sm text-slate-500">Définissez les objectifs en minutes pour chaque niveau de priorité.</p>
            </div>

            <div class="p-6">
                @if (count($priorities) === 0)
                    <div class="text-center py-8 text-sm text-slate-500">
                        <iconify-icon icon="solar:flag-bold-duotone" width="32" class="text-slate-300 mb-2"></iconify-icon>
                        <p>Aucune priorité configurée. Créez d'abord des priorités dans l'onglet dédié.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="px-4 py-3">Priorité</th>
                                    <th class="px-4 py-3">Première réponse (min)</th>
                                    <th class="px-4 py-3">Résolution (min)</th>
                                    <th class="px-4 py-3 text-center">Actif</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($priorities as $priority)
                                    @php $pid = $priority->id; @endphp
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-xs font-bold text-slate-600">
                                                    {{ $priority->level }}
                                                </span>
                                                <span class="text-sm font-medium text-slate-900">{{ $priority->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="number"
                                                    wire:model="slaPolicies.{{ $pid }}.first_response_minutes"
                                                    min="1"
                                                    placeholder="ex: 60"
                                                    class="block w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                >
                                                <span class="text-xs text-slate-400 hidden sm:inline">= {{ isset($slaPolicies[$pid]['first_response_minutes']) && $slaPolicies[$pid]['first_response_minutes'] ? round($slaPolicies[$pid]['first_response_minutes'] / 60, 1) . 'h' : '—' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="number"
                                                    wire:model="slaPolicies.{{ $pid }}.resolution_minutes"
                                                    min="1"
                                                    placeholder="ex: 480"
                                                    class="block w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                >
                                                <span class="text-xs text-slate-400 hidden sm:inline">= {{ isset($slaPolicies[$pid]['resolution_minutes']) && $slaPolicies[$pid]['resolution_minutes'] ? round($slaPolicies[$pid]['resolution_minutes'] / 60, 1) . 'h' : '—' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input
                                                type="checkbox"
                                                wire:model="slaPolicies.{{ $pid }}.is_active"
                                                class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]"
                                            >
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="flex items-start gap-2">
                            <iconify-icon icon="solar:info-circle-bold" class="text-blue-400 mt-0.5 flex-shrink-0" width="16"></iconify-icon>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                <strong>Première réponse :</strong> temps maximum avant qu'un agent réponde au ticket.
                                <strong>Résolution :</strong> temps maximum pour résoudre le ticket.
                                Laissez vide pour ne pas appliquer de SLA à cette priorité.
                                Le timer de résolution se met automatiquement en pause quand le ticket est "En attente".
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Save button --}}
        <div class="flex justify-end mt-6">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
            >
                <iconify-icon icon="solar:diskette-bold" width="18" wire:loading.remove wire:target="saveSlaSettings"></iconify-icon>
                <iconify-icon icon="solar:refresh-bold" width="18" class="animate-spin" wire:loading wire:target="saveSlaSettings"></iconify-icon>
                Enregistrer les paramètres SLA
            </button>
        </div>
    </form>
</div>
