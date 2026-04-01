{{-- WEBHOOKS TAB --}}
<div x-show="tab === 'webhooks'" x-cloak class="space-y-6">
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
            <h2 class="text-lg font-bold text-slate-900">Webhooks sortants</h2>
            <p class="text-sm text-slate-500">Configurez des endpoints pour recevoir des notifications en temps réel.</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Secret just created --}}
            @if ($createdWebhookSecret)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-emerald-800 mb-2">Webhook créé. Copiez le secret maintenant, il ne sera plus affiché.</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 rounded-lg bg-white border border-emerald-200 px-3 py-2 text-sm font-mono text-slate-800 break-all select-all" x-ref="secretText">{{ $createdWebhookSecret }}</code>
                        <button type="button"
                            x-on:click="navigator.clipboard.writeText($refs.secretText.textContent); $dispatch('toast', {type:'success', message:'Secret copié !'})"
                            class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all">
                            <iconify-icon icon="solar:copy-bold" width="16"></iconify-icon>
                            Copier
                        </button>
                    </div>
                    <button type="button" wire:click="$set('createdWebhookSecret', null)" class="mt-2 text-xs text-emerald-700 underline">Fermer</button>
                </div>
            @endif

            {{-- Create form --}}
            @if ($canManage && ! $editingWebhookId)
                <form wire:submit.prevent="createWebhookEndpoint" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">URL du webhook</label>
                            <input type="url" wire:model.blur="newWebhookUrl" placeholder="https://example.com/webhook" class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                            @error('newWebhookUrl') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Description (optionnel)</label>
                            <input type="text" wire:model.blur="newWebhookDescription" placeholder="Ex: Notification ERP" class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Événements</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach (\App\Enums\WebhookEvent::labels() as $event => $label)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" wire:model="newWebhookEvents" value="{{ $event }}" class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        @error('newWebhookEvents') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <x-manexo.action-button type="submit" wire-target="createWebhookEndpoint" variant="primary" class="!font-semibold">
                        <iconify-icon icon="solar:link-round-bold" width="18"></iconify-icon>
                        Créer un webhook
                    </x-manexo.action-button>
                </form>
            @endif

            {{-- Edit form --}}
            @if ($editingWebhookId)
                <form wire:submit.prevent="updateWebhookEndpoint" class="space-y-4 rounded-xl border border-blue-200 bg-blue-50/30 p-4">
                    <h3 class="text-sm font-bold text-slate-900">Modifier le webhook</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">URL</label>
                            <input type="url" wire:model.blur="editingWebhookUrl" class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                            <input type="text" wire:model.blur="editingWebhookDescription" class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Événements</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach (\App\Enums\WebhookEvent::labels() as $event => $label)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" wire:model="editingWebhookEvents" value="{{ $event }}" class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="editingWebhookIsActive" class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                            Actif
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <x-manexo.action-button type="submit" wire-target="updateWebhookEndpoint" variant="primary" class="!font-semibold">
                            Enregistrer
                        </x-manexo.action-button>
                        <button type="button" wire:click="cancelEditWebhook" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">Annuler</button>
                    </div>
                </form>
            @endif

            {{-- Webhook list --}}
            @if (isset($webhookEndpoints) && $webhookEndpoints->count())
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">URL</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Événements</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Actif</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($webhookEndpoints as $ep)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900 max-w-xs truncate">{{ $ep->url }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($ep->events ?? [] as $ev)
                                                <span class="inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700">{{ $ev }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button type="button" wire:click="toggleWebhookEndpoint({{ $ep->id }})" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out {{ $ep->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $ep->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <button type="button" wire:click="startEditWebhook({{ $ep->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Modifier</button>
                                        <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: 'Supprimer ce webhook ? Cette action est irréversible.', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteWebhookEndpoint({{ $ep->id }}) })" class="text-red-600 hover:text-red-800 text-xs font-semibold">Supprimer</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500 italic">Aucun webhook configuré.</p>
            @endif

            {{-- Recent deliveries --}}
            @if (isset($webhookEndpoints) && $webhookEndpoints->count())
                @php
                    $recentDeliveries = \App\Models\WebhookDelivery::whereIn('webhook_endpoint_id', $webhookEndpoints->pluck('id'))
                        ->orderByDesc('created_at')
                        ->limit(20)
                        ->get();
                @endphp
                @if ($recentDeliveries->count())
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-3">Derniers envois</h3>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-slate-600">Événement</th>
                                        <th class="px-4 py-2 text-left font-semibold text-slate-600">Statut</th>
                                        <th class="px-4 py-2 text-left font-semibold text-slate-600">Tentatives</th>
                                        <th class="px-4 py-2 text-left font-semibold text-slate-600">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($recentDeliveries as $d)
                                        <tr>
                                            <td class="px-4 py-2 text-slate-700">{{ $d->event_type }}</td>
                                            <td class="px-4 py-2">
                                                @if ($d->status === 'success')
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Succès</span>
                                                @elseif ($d->status === 'failed')
                                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Échec</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">En attente</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-slate-500">{{ $d->attempt }}</td>
                                            <td class="px-4 py-2 text-slate-500">{{ $d->created_at?->translatedFormat('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
