<x-container class="flex flex-col gap-12 py-12">
    <x-headers.h2 label="Decks" />

    <div class="grid grid-cols-4 gap-4">
        @foreach ($decks as $deck)
            <x-deck :$deck />
        @endforeach
    </div>

    <div class="w-full flex">
        <x-form.button
            wire:click="newDeck"
            label="Create Deck"
        />
    </div>
</x-container>
