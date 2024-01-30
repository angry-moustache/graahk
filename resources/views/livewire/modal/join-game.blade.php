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

        <div
            class="w-full grid grid-cols-3 gap-4 overflow-y-auto max-h-[55vh] p-2"
            x-data="{
                deck: @entangle('fields.deck_id'),
            }"
        >
            @foreach ($decks as $deck)
                <x-deck
                    :$deck
                    x-on:click.prevent="deck = {{ $deck->id }}"
                    x-bind:class="{
                        'opacity-25': deck !== {{ $deck->id }},
                        'opacity-100': deck === {{ $deck->id }},
                    }"
                />
            @endforeach
        </div>

        <div>
            <x-form.button
                wire:click="join"
                label="Join and play!"
            />
        </div>
    </x-slot>
</x-modal>
