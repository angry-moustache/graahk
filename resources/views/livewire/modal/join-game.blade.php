<x-modal>
    <x-slot:main>
        <x-headers.h2 label="Join game" />

        <div class="flex gap-4 items-center">
            <x-avatar :user="$game->user1" />

            <div class="flex flex-col">
                <span class="text-lg font-bold">
                    {{ $game->name }}
                </span>
                <span class="opacity-50">
                    {{ $game->user1->username }} is looking for a worthy opponent!
                </span>
            </div>
        </div>

        <div class="flex flex-col w-1/2 gap-4">
            <x-form.select
                wire:model="fields.deck_id"
                :options="$decks"
                label="Deck"
                nullable
            />
        </div>

        <div>
            <x-form.button
                wire:click="join"
                label="Join and play!"
            />
        </div>
    </x-slot>
</x-modal>
