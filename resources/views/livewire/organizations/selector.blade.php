<div class="z-10 w-full h-full max-w-5xl relative overflow-y-auto custom-scrollbar" style="--accent: {{ $primary_color ?: '#005F02' }};">
    <!-- Wrapper for centering and scrolling -->
    <div class="flex flex-col items-center justify-center min-h-full w-full px-6 py-12">

        <!-- Header Section -->
        <div class="text-center mb-10 fade-in">
            <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl mb-4" style="background-color: color-mix(in srgb, var(--accent) 8%, transparent); color: var(--accent);">
                <!-- Lucide Icon: Globe -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
            </div>
            <h1 class="font-serif text-3xl font-medium tracking-tight text-[#002e01] mb-2">Bienvenue sur MANEXO</h1>
            <p class="text-sm text-slate-500 max-w-md mx-auto">Pour continuer, créez votre propre structure ou rejoignez un espace de travail existant.</p>
        </div>

        <!-- Two Column Layout -->
        <div class="grid md:grid-cols-2 gap-6 w-full max-w-4xl items-start">

            <!-- 1. Create Enterprise Block (Primary Action) -->
            <div class="fade-in fade-in-delay-1 group relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity" style="color: var(--accent);">
                    <!-- Lucide Icon: Plus Square -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M8 12h8"></path><path d="M12 8v8"></path></svg>
                </div>

                <div class="mb-5 relative z-10">
                    <h2 class="text-base font-medium text-slate-900 flex items-center gap-2">
                        <!-- Lucide Icon: Plus Circle -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent);"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path><path d="M12 8v8"></path></svg>
                        Créer une entreprise
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-1">Configurez votre nouvel espace de travail.</p>
                </div>

                <form wire:submit.prevent="createOrganization" class="space-y-4 flex-1 flex flex-col relative z-10">
                    <div class="space-y-1.5">
                        <label for="company-name" class="block text-[11px] font-medium text-slate-700">Nom de la structure</label>
                        <input
                            type="text"
                            id="company-name"
                            wire:model.defer="name"
                            placeholder="Ex: Mon Agence Créative"
                            class="block w-full rounded-lg border-0 bg-slate-50 py-2.5 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset text-sm transition-all"
                            style="--tw-ring-color: var(--accent);"
                        >
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-medium text-slate-700">Couleur de l'espace</label>
                        <div class="flex items-center gap-3">
                            @php
                                $colorOptions = [
                                    ['id' => 'c1', 'value' => '#005F02', 'swatch' => 'bg-[#005F02]'],
                                    ['id' => 'c2', 'value' => '#2563EB', 'swatch' => 'bg-blue-600'],
                                    ['id' => 'c3', 'value' => '#7C3AED', 'swatch' => 'bg-violet-600'],
                                    ['id' => 'c4', 'value' => '#F97316', 'swatch' => 'bg-orange-500'],
                                ];
                            @endphp

                            @foreach ($colorOptions as $opt)
                                <div class="relative">
                                    <input
                                        type="radio"
                                        name="color"
                                        id="{{ $opt['id'] }}"
                                        class="color-radio peer sr-only"
                                        wire:model="primary_color"
                                        value="{{ $opt['value'] }}"
                                    >
                                    <label
                                        for="{{ $opt['id'] }}"
                                        class="cursor-pointer block h-6 w-6 rounded-full {{ $opt['swatch'] }} hover:opacity-90 transition-transform ring-2 ring-transparent ring-offset-2 ring-offset-white"
                                    ></label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 grid grid-cols-[auto,1fr] gap-3 items-center">
                            <input
                                type="color"
                                class="h-9 w-10 rounded-md border border-slate-200 bg-white p-1"
                                wire:model.live="primary_color"
                                aria-label="Choisir une couleur"
                            >
                            <div class="space-y-1">
                                <label for="primary-color" class="block text-[11px] font-medium text-slate-700">Code couleur (hex)</label>
                                <input
                                    id="primary-color"
                                    type="text"
                                    inputmode="text"
                                    autocomplete="off"
                                    placeholder="#005F02"
                                    class="block w-full rounded-lg border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset text-sm transition-all"
                                    style="--tw-ring-color: var(--accent);"
                                    wire:model.debounce.250ms="primary_color"
                                >
                                <p class="text-[10px] text-slate-500">Ex: <span class="font-mono">#005F02</span> ou <span class="font-mono">#2563EB</span></p>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->get('primary_color')" />
                    </div>

                    <div class="pt-4 mt-auto">
                        <button type="submit" class="group/btn flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white shadow-md transition-all duration-200 active:scale-[0.98]" style="background-color: var(--accent); box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
                            Créer et continuer
                            <!-- Lucide Icon: Arrow Right -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover/btn:translate-x-1"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Select / Join Block (Secondary Action) -->
            <div class="fade-in fade-in-delay-2 flex flex-col rounded-2xl border border-slate-200 bg-slate-50/50 backdrop-blur-sm p-6 hover:bg-white transition-colors duration-300 h-full">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h2 class="text-base font-medium text-slate-800 flex items-center gap-2">
                            <!-- Lucide Icon: Building 2 -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-600"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>
                            Vos entreprises
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-1">Sélectionnez un espace existant.</p>
                    </div>
                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                        {{ $organizations->count() }} disponible{{ $organizations->count() > 1 ? 's' : '' }}
                    </span>
                </div>

                <!-- List of existing companies -->
                <div class="space-y-2 flex-1 overflow-y-auto custom-scrollbar max-h-[180px] pr-1">
                    @forelse ($organizations as $org)
                        @php
                            $role = $org->pivot?->role ?? 'member';
                            $roleLabel = match ($role) {
                                'owner' => 'Propriétaire',
                                'admin' => 'Admin',
                                'agent' => 'Agent',
                                default => 'Membre',
                            };
                            $initial = mb_strtoupper(mb_substr((string) $org->name, 0, 1));
                        @endphp

                        <button
                            type="button"
                            wire:click="selectOrganization({{ $org->id }})"
                            class="w-full group flex items-center justify-between rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all duration-200 text-left"
                            style="--tw-ring-color: var(--accent);"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F2E3BB] font-serif font-medium text-sm" style="color: var(--accent);">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900 transition-colors group-hover:text-[color:var(--accent)]">{{ $org->name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $roleLabel }}</div>
                                </div>
                            </div>
                            <!-- Lucide Icon: Log In -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300 transition-colors group-hover:text-[color:var(--accent)]"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" x2="3" y1="12" y2="12"></line></svg>
                        </button>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-200 bg-white/60 p-4">
                            <div class="text-sm font-medium text-slate-700">Aucune entreprise pour le moment</div>
                            <div class="text-[11px] text-slate-500 mt-1">Créez votre première structure à gauche pour continuer.</div>
                        </div>
                    @endforelse
                </div>

                <!-- Join Action Footer -->
                <div class="pt-4 mt-auto border-t border-slate-200/60">
                    <button
                        type="button"
                        wire:click="openInviteModal"
                        class="w-full text-center text-xs font-medium text-slate-500 transition-colors flex items-center justify-center gap-1.5 py-1 hover:text-[color:var(--accent)]"
                    >
                        <!-- Lucide Icon: Key -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                        Rejoindre avec un code d'invitation
                    </button>
                </div>
            </div>
        </div>

        <!-- Logout / Cancel -->
        <div class="mt-12 fade-in fade-in-delay-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-[11px] font-medium text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-1.5">
                    <!-- Lucide Icon: Log Out -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line></svg>
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Invite Modal -->
@if ($showInviteModal)
    <div class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="closeInviteModal"></div>

        <div class="relative mx-auto flex min-h-full max-w-lg items-center justify-center px-6">
            <div class="w-full rounded-2xl border border-white/30 bg-white/95 p-6 shadow-2xl ring-1 ring-black/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Rejoindre une entreprise</h3>
                        <p class="mt-1 text-[11px] text-slate-500">Entrez le code d’invitation fourni par l’administrateur.</p>
                    </div>
                    <button type="button" wire:click="closeInviteModal" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" aria-label="Fermer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="joinWithInviteCode" class="mt-5 space-y-3">
                    <div class="space-y-1">
                        <label for="invite_code" class="block text-[11px] font-medium text-slate-700">Code d’invitation</label>
                        <input
                            id="invite_code"
                            type="text"
                            wire:model.defer="invite_code"
                            placeholder="Ex: MANEXO-8F3K2"
                            class="block w-full rounded-lg border-0 bg-slate-50 py-2.5 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset text-sm transition-all"
                            style="--tw-ring-color: var(--accent);"
                        >
                        <x-input-error :messages="$errors->get('invite_code')" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeInviteModal" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors" style="background-color: var(--accent);">
                            Rejoindre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
