<x-container class="flex flex-col gap-12 py-8">
    @if ($canCreate)
        <div class="flex flex-col gap-6">
            <x-headers.h2 text="Open new table" />

            <div class="flex flex-col w-1/2 gap-4">
                <x-form.input
                    wire:model="fields.name"
                    label="Name"
                />

                <x-form.select
                    wire:model="fields.deck_id"
                    label="Deck"
                    :options="$decks"
                    nullable
                />

                <div class="w-full">
                    <x-form.button
                        wire:click="create"
                        text="Create table"
                    />
                </div>
            </div>
        </div>
    @endif

    <div wire:poll class="flex flex-col gap-8">
        <x-headers.h2 text="Available tables" />

        {{-- Active games --}}
        <ul>
            @foreach ($games as $game)
                <li>
                    {{ $game->name }}
                </li>
            @endforeach
        </ul>
    </div>
</x-container>
