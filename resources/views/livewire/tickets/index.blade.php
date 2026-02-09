<div>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">My tickets</h1>
            <a href="{{ route('tickets.create') }}" wire:navigate>
                <x-primary-button>Create ticket</x-primary-button>
            </a>
        </div>

        <div class="mt-6 overflow-hidden bg-white shadow sm:rounded-lg">
            <div class="divide-y divide-gray-200">
                @forelse ($tickets as $ticket)
                    <div class="p-4 sm:p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ $ticket->subject }}</div>
                                <div class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium">Category:</span> {{ $ticket->category?->name }}
                                    <span class="mx-2">•</span>
                                    <span class="font-medium">Priority:</span> {{ $ticket->priority?->name }}
                                    <span class="mx-2">•</span>
                                    <span class="font-medium">Status:</span> {{ $ticket->status->value }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 whitespace-nowrap">
                                {{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-sm text-gray-600">
                        No tickets yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
