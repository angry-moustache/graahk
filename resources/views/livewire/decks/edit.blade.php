<x-container class="flex">
    <div
        class="w-full flex gap-4"
        x-data="{
            deckList: @entangle('deckList'),
            mainCardId: @entangle('mainCardId'),
            cardList: @js($cardList),
            addCard (id) {
                const card = this.deckList.find(card => card.card.id === id)

                if (card) {
                    if (card.amount < 4) {
                        card.amount++
                    }
                } else {
                    this.deckList.push({
                        amount: 1,
                        card: this.cardList[id],
                    })
                }
            },
            removeCard (id) {
                const card = this.deckList.find(card => card.card.id === id)

                if (card) {
                    if (card.amount > 1) {
                        card.amount--
                    } else {
                        this.deckList = this.deckList.filter(card => card.card.id !== id)
                    }
                }
            },
            getDeckList () {
                // Sort by cost, then by name
                return this.deckList.sort((a, b) => (a.card.cost - b.card.cost) || a.card.name.localeCompare(b.card.name))
            },
        }"
    >
        {{-- Decklist --}}
        <div class="w-96 flex flex-col gap-8 py-8">
            <x-form.input
                class="w-full"
                label="Deck name"
                wire:model="name"
            />

            <div class="w-full flex flex-col gap-2">
                <p
                    class="text-lg font-bold px-2"
                    x-bind:class="{
                        'text-red-500': deckList.reduce((a, c) => a + c.amount, 0) > 30,
                    }"
                >
                    Contains <span x-text="deckList.reduce((a, c) => a + c.amount, 0)"></span>/30 cards
                </p>

                <ul class="flex flex-col w-full gap-2">
                    <template x-for="card in getDeckList()">
                        <li class="cursor-pointer relative gap-2 w-full bg-surface hover:opacity-75 transition-all">
                            <div
                                class="absolute inset-0 bg-cover bg-[center_top_-4rem] bg-no-repeat"
                                x-bind:style="`background-image: url('${card.card.image}')`"
                            >
                                <div class="absolute inset-0 bg-black opacity-80"></div>
                            </div>

                            <div
                                class="relative flex gap-1 items-center justify-between"
                                x-on:click="removeCard(card.card.id)"
                            >
                                <span
                                    x-text="card.card.cost"
                                    class="p-4 w-16 flex-grow bg-surface inline-block text-xl font-extrabold text-center"
                                ></span>

                                <span
                                    class="p-2 inline-block w-full text-xl font-bold"
                                >
                                    <span class=" line-clamp-1" x-text="'x' + card.amount + ' ' + card.card.name"></span>
                                </span>
                            </div>

                            <div
                                x-on:click="mainCardId = card.card.id"
                                class="
                                    flex absolute right-0 top-0 bottom-0 w-12 justify-center items-center bg-surface
                                    opacity-0 hover:opacity-100 transition-all
                                "
                                x-bind:class="{
                                    '!opacity-100': mainCardId === card.card.id,
                                }"
                            >
                                <x-heroicon-o-bookmark
                                    x-show="mainCardId !== card.card.id"
                                    class="w-6 h-6"
                                />

                                <x-heroicon-s-bookmark
                                    x-show="mainCardId === card.card.id"
                                    class="w-6 h-6 text-primary"
                                />
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <x-form.button
                class="w-full"
                wire:click="saveDeck"
                text="Save changes"
            />
        </div>

        {{-- Cardpool --}}
        <div class="flex h-[95vh] overflow-y-scroll w-full p-8">
            <div class="flex flex-wrap w-full">
                @foreach ($cards as $card)
                    <div
                        x-on:click="addCard({{ $card->id }})"
                        class="w-1/4 p-2 hover:opacity-75 transition-all cursor-pointer"
                        x-bind:class="{
                            'opacity-25': deckList.find(card => card.card.id === {{ $card->id }})?.amount === 4,
                        }"
                    >
                        <x-card :$card />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-container>
