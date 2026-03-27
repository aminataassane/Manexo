{{-- API TOKENS TAB --}}
<div x-show="tab === 'api'" x-cloak class="space-y-6">
    <div class="content-card">
        <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
            <h2 class="text-lg font-bold text-slate-900">Tokens API</h2>
            <p class="text-sm text-slate-500">Gérez les tokens d'accès à l'API REST de votre organisation.</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Token just created --}}
            @if ($createdTokenPlainText)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-emerald-800 mb-2">Token créé avec succès. Copiez-le maintenant, il ne sera plus affiché.</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 rounded-lg bg-white border border-emerald-200 px-3 py-2 text-sm font-mono text-slate-800 break-all select-all" x-ref="tokenText">{{ $createdTokenPlainText }}</code>
                        <button type="button"
                            x-on:click="navigator.clipboard.writeText($refs.tokenText.textContent); $dispatch('toast', {type:'success', message:'Token copié !'})"
                            class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all">
                            <iconify-icon icon="solar:copy-bold" width="16"></iconify-icon>
                            Copier
                        </button>
                    </div>
                    <button type="button" wire:click="$set('createdTokenPlainText', null)" class="mt-2 text-xs text-emerald-700 underline">Fermer</button>
                </div>
            @endif

            {{-- Create form --}}
            @if ($canManage)
                <form wire:submit.prevent="createApiToken" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nom du token</label>
                            <input type="text" wire:model="newTokenName" placeholder="Ex: ERP Integration" class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                            @error('newTokenName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Expiration (optionnel)</label>
                            <input type="date" wire:model="newTokenExpiresAt" class="w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Scopes</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach (\App\Enums\ApiTokenScope::labels() as $scope => $label)
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" wire:model="newTokenScopes" value="{{ $scope }}" class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        @error('newTokenScopes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">
                        <iconify-icon icon="solar:key-bold" width="18"></iconify-icon>
                        Créer un token
                    </button>
                </form>
            @endif

            {{-- Token list --}}
            @if (isset($apiTokens) && $apiTokens->count())
                <div class="w-full overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full min-w-full table-fixed divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="w-[16%] px-4 py-3 text-left font-semibold text-slate-600">Nom</th>
                                <th class="min-w-0 px-4 py-3 text-left font-semibold text-slate-600">Scopes</th>
                                <th class="w-[11%] px-4 py-3 text-left font-semibold text-slate-600">Créé le</th>
                                <th class="w-[18%] px-4 py-3 text-left font-semibold text-slate-600">Dernière utilisation</th>
                                <th class="w-[11%] px-4 py-3 text-left font-semibold text-slate-600">Expire le</th>
                                <th class="w-[10%] px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($apiTokens as $token)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $token->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ((is_array($token->scopes) ? $token->scopes : json_decode($token->scopes ?? '[]', true)) ?? [] as $s)
                                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">{{ $s }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">{{ $token->created_at?->translatedFormat('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $token->last_used_at?->translatedFormat('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $token->expires_at ? \Carbon\Carbon::parse($token->expires_at)->translatedFormat('d/m/Y') : '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="button" @click="$dispatch('confirm-action', { title: 'Révoquer', message: 'Révoquer ce token API ? Cette action est irréversible.', confirmLabel: 'Révoquer', variant: 'danger', onConfirm: () => $wire.revokeApiToken({{ $token->id }}) })" class="text-red-600 hover:text-red-800 text-xs font-semibold whitespace-nowrap">
                                            Révoquer
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500 italic">Aucun token API créé.</p>
            @endif
        </div>
    </div>
</div>
