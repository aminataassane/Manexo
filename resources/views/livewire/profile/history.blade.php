<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-[#111827] tracking-tight">{{ __('Historique') }}</h1>
            <p class="text-sm text-[#6B7280] mt-1">{{ __("Tous vos événements (tickets créés / assignations).") }}</p>
        </div>
        <a href="{{ route('profile') }}"
           class="h-9 px-3 rounded-md border border-[#E5E7EB] bg-white text-[13px] font-semibold text-[#111827] hover:bg-[#F9FAFB] inline-flex items-center gap-2 transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" width="16"></iconify-icon>
            {{ __('Retour au profil') }}
        </a>
    </div>

    <div class="bg-white rounded-lg border border-[#E5E7EB] shadow-sm p-4 sm:p-6">
        <div class="flex flex-wrap gap-2">
            @php
                $filters = [
                    ['all', __('Tous')],
                    ['tickets', __('Tickets créés')],
                    ['assignations', __('Assignés à moi')],
                ];
            @endphp
            @foreach ($filters as [$key, $label])
                @php $isActive = $type === $key; @endphp
                <button
                    type="button"
                    wire:click="$set('type', '{{ $key }}')"
                    class="px-3 py-1.5 rounded-full text-[12px] font-semibold border transition-colors
                        {{ $isActive ? 'bg-white text-[#111827] border-[#E5E7EB] shadow-sm' : 'bg-[#F9FAFB] text-[#6B7280] border-[#E5E7EB] hover:text-[#111827]' }}"
                >{{ $label }}</button>
            @endforeach
        </div>

        <div class="mt-5 relative pl-4 border-l border-[#E5E7EB] space-y-5">
            @php
                $statusLabel = function (string $status): string {
                    return match ($status) {
                        'open' => __('Ouvert'),
                        'in_progress' => __('En cours'),
                        'pending' => __('En attente'),
                        'resolved' => __('Résolu'),
                        'closed' => __('Fermé'),
                        default => ucfirst(str_replace('_', ' ', $status)),
                    };
                };
            @endphp

            @forelse ($events as $e)
                <div class="relative">
                    <div class="absolute -left-[21px] top-0.5 w-2.5 h-2.5 rounded-full ring-4 ring-white" style="background: var(--accent);"></div>
                    <p class="text-xs text-[#111827]">
                        <span class="font-medium">{{ $e->label }}</span>
                        <span class="text-[#6B7280]">• Ticket #{{ $e->ticket_id }}</span>
                    </p>
                    <p class="text-[11px] text-[#6B7280] mt-1 truncate">{{ $e->subject }}</p>
                    <p class="text-[10px] text-[#6B7280] mt-1">
                        {{ __('Statut') }} : <span class="font-medium text-[#111827]">{{ $statusLabel((string) $e->status) }}</span>
                        • {{ \Illuminate\Support\Carbon::parse($e->at)->diffForHumans() }}
                    </p>
                </div>
            @empty
                <div class="rounded-md border border-[#E5E7EB] bg-[#F9FAFB] p-4 text-sm text-[#6B7280]">
                    {{ __('Aucun événement.') }}
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    </div>
</div>

