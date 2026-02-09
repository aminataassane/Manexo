<div>
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-3">
            <h3 class="font-semibold text-gray-900">Your organizations</h3>

            <div class="space-y-2">
                @forelse ($organizations as $org)
                    <button
                        type="button"
                        wire:click="selectOrganization({{ $org->id }})"
                        class="w-full text-left rounded-md border border-gray-200 px-4 py-3 hover:bg-gray-50"
                    >
                        <div class="font-medium text-gray-900">{{ $org->name }}</div>
                        <div class="text-xs text-gray-500">{{ $org->slug }}</div>
                    </button>
                @empty
                    <div class="text-sm text-gray-600">
                        You don't belong to any organization yet.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-3">
            <h3 class="font-semibold text-gray-900">Create an organization</h3>

            <form wire:submit="createOrganization" class="space-y-3">
                <div>
                    <x-input-label for="org_name" value="Organization name" />
                    <x-text-input
                        id="org_name"
                        class="mt-1 block w-full"
                        type="text"
                        wire:model="name"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="primary_color" value="Primary color (optional)" />
                    <x-text-input
                        id="primary_color"
                        class="mt-1 block w-full"
                        type="text"
                        wire:model="primary_color"
                        placeholder="#005F02"
                    />
                    <x-input-error :messages="$errors->get('primary_color')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        Create & continue
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
