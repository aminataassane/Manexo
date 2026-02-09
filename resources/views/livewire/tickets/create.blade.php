<div>
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Create ticket</h1>
            <a href="{{ route('tickets.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-gray-900 underline">
                Back to tickets
            </a>
        </div>

        <form wire:submit="submit" class="mt-6 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="category" value="Category" />
                    <select id="category" wire:model="ticket_category_id" class="mt-1 block w-full rounded-md border-gray-300">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="priority" value="Priority" />
                    <select id="priority" wire:model="ticket_priority_id" class="mt-1 block w-full rounded-md border-gray-300">
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('ticket_priority_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="subject" value="Subject" />
                <x-text-input id="subject" class="mt-1 block w-full" type="text" wire:model="subject" required />
                <x-input-error :messages="$errors->get('subject')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" wire:model="description" rows="6" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <x-primary-button>
                    Submit
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
