<form wire:submit="submit" class="space-y-6">
    <div class="rounded-xl border border-[#E5E7EB] bg-white shadow-sm">
        <div class="p-4 sm:p-6 space-y-6">
            <div>
                <h3 class="text-[13px] font-semibold text-[#111827]">Détails du ticket</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">Choisissez une catégorie et une priorité, puis donnez un sujet clair.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="drawer_category" value="Catégorie" class="text-[#111827]" />
                    <div class="relative mt-1">
                        <select
                            id="drawer_category"
                            wire:model="ticket_category_id"
                            class="block w-full h-10 rounded-md border border-[#E5E7EB] bg-white text-[#111827] text-[13px] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] appearance-none pr-9 transition"
                        >
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                    </div>
                    <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="drawer_priority" value="Priorité" class="text-[#111827]" />
                    <div class="relative mt-1">
                        <select
                            id="drawer_priority"
                            wire:model="ticket_priority_id"
                            class="block w-full h-10 rounded-md border border-[#E5E7EB] bg-white text-[#111827] text-[13px] shadow-sm focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] appearance-none pr-9 transition"
                        >
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                            @endforeach
                        </select>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280] pointer-events-none" width="14"></iconify-icon>
                    </div>
                    <x-input-error :messages="$errors->get('ticket_priority_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="drawer_subject" value="Sujet" class="text-[#111827]" />
                <x-text-input
                    id="drawer_subject"
                    type="text"
                    wire:model="subject"
                    required
                    class="mt-1"
                    placeholder="Ex: Erreur 500 sur le checkout"
                />
                <x-input-error :messages="$errors->get('subject')" class="mt-2" />
            </div>

            <div class="pt-2 border-t border-[#E5E7EB]">
                <h3 class="text-[13px] font-semibold text-[#111827]">Description</h3>
                <p class="mt-1 text-[12px] text-[#6B7280]">Ajoute des détails: étapes, impact, captures d’écran, contexte…</p>
            </div>

            <div>
                <x-input-label for="drawer_description" value="Description" class="text-[#111827]" />
                <textarea
                    id="drawer_description"
                    wire:model="description"
                    rows="5"
                    class="mt-1 block w-full rounded-md border border-[#E5E7EB] bg-white text-[#111827] text-[13px] placeholder:text-gray-400 focus:outline-none focus:border-[color:var(--accent)] focus:ring-2 focus:ring-[color:var(--accent-ring)] transition p-3 shadow-sm"
                    placeholder="Donnez un maximum de détails (étapes, capture, contexte...)"
                ></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <div class="sticky bottom-0 border-t border-[#E5E7EB] bg-white/95 backdrop-blur px-4 sm:px-6 py-4">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <button
                    type="button"
                    class="h-10 px-4 bg-white border border-[#E5E7EB] text-[#111827] text-[13px] font-medium rounded-md shadow-sm hover:bg-[#F9FAFB] transition flex items-center justify-center w-full sm:w-auto"
                    wire:click="$dispatch('tickets:closeCreateDrawer')"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="h-10 px-4 text-white text-[13px] font-semibold rounded-md shadow-sm transition-colors flex items-center justify-center gap-2 w-full sm:w-auto bg-[color:var(--accent)] hover:bg-[color:color-mix(in_srgb,var(--accent)_85%,black)]"
                >
                    <iconify-icon icon="solar:send-square-linear" width="16"></iconify-icon>
                    Envoyer
                </button>
            </div>
        </div>
    </div>
</form>

