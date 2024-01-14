<x-container>
    <ul>
        @foreach ($decks as $deck)
            <li>
                <a href="{{ $deck->route() }}">
                    {{ $deck->name}}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="w-full flex">
        <x-form.button
            wire:click="newDeck"
            text="Create Deck"
        />
    </div>
</x-container>
