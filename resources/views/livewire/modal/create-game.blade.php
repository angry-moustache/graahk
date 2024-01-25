<x-modal>
    <x-slot:main>
        <x-headers.h2 label="Create table" />

        <div class="flex flex-col w-1/2 gap-4">
            <x-form.input
                wire:model="fields.name"
                label="Table name"
                nullable
            />

            <x-form.select
                wire:model="fields.deck_id"
                :options="$decks"
                label="Deck"
                nullable
            />
        </div>

        <div>
            <x-form.button
                wire:click="create"
                label="Create new table"
            />
        </div>
    </x-slot>
</x-modal>
