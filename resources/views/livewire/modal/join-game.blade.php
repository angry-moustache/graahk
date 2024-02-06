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
            @forelse ($decks as $deck)
                <x-deck
                    :$deck
                    x-on:click.prevent="deck = {{ $deck->id }}"
                    x-bind:class="{
                        'opacity-25': deck !== {{ $deck->id }},
                        'opacity-100': deck === {{ $deck->id }},
                    }"
                />
            @empty
                <p class="col-span-3 opacity-75">
                    Oh dear, you have no legal decks available!<br>
                    Making sure you have a deck that in the <strong>{{ $game->format()?->name() }}</strong> format!
                </p>
            @endforelse
        </div>

        <div class="w-full flex gap-4">
            <x-form.button-secondary
                x-on:click="window.closeModal()"
                label="Cancel"
            />

            @if ($decks->isNotEmpty())
                <x-form.button
                    wire:click="join"
                    label="Join and play!"
                />
            @endif
        </div>
    </x-slot>
</x-modal>
