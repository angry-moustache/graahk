<x-container class="flex flex-col gap-12 py-12">
    <div class="grid grid-cols-4 gap-4">
        <div class="p-2 flex w-full">
            <x-form.button
                x-on:click="window.openModal('create-deck')"
                class="
                    text-2xl bg-transparent border-text border-dashed
                    !text-text border-4 opacity-50 w-full
                    hover:bg-primary-hover hover:!text-surface hover:border-transparent
                    hover:opacity-100 transition-all duration-200
                "
            >
                <span class="inline-flex flex-col gap-4 items-center">
                    <x-heroicon-o-plus class="w-12 h-12" />
                    <span>Create new deck</span>
                </span>
            </x-form.button>
        </div>

        @foreach ($decks as $deck)
            <x-deck :$deck />
        @endforeach
    </div>
</x-container>
