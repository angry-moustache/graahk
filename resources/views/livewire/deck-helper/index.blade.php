<div>
    @if ($step === 0)
        <x-deck-helper-section>
            <div class="flex flex-col gap-8 bg-surface p-8 rounded-xl w-1/2 mt-12 ml-12">
                <p class="text-xl text-stone-400 p-2">
                    Hey there bucko!<br><br>
                    First time here? Or need some inspiration for a new deck?<br><br>
                    Either way, Jack Gold Star is here, ready to help you out building a Graahk deck!
                </p>

                <x-form.button label="Let's go!" wire:click="next" />
            </div>

            <img
                class="z-[-1] absolute bottom-0 right-12 opacity-25"
                src="{{ asset('images/deck-helper/jack.png') }}"
            />
        </x-deck-helper-section>
    @elseif ($step === 1)
        <x-deck-helper-section class="flex flex-col gap-12 p-12">
            <div class="flex flex-col gap-8 bg-surface p-8 rounded-xl w-2/3">
                <p class="text-xl text-stone-400 p-2">
                    Let's start with a picking a card that interests you.<br><br>
                    Don't worry, you can always come back to this screen later.
                </p>
            </div>

            <div class="w-2/3 grid grid-cols-3 gap-8 px-8">
                @foreach ($helpers as $helper)
                    <x-card
                        :level="1"
                        :card="$helper->mainCard"
                        class="hover:scale-105 transition-transform duration-300 ease-in-out cursor-pointer"
                        wire:click="selectHelper({{ $helper->id }})"
                    />
                @endforeach
            </div>

            <div class="w-2/3">
                <x-form.button class="w-full" label="I don't like these, got some other ones?" wire:click="newHelpers" />
            </div>

            <img
                class="z-[-1] absolute bottom-0 right-12 opacity-25"
                src="{{ asset('images/deck-helper/jack.png') }}"
            />
        </x-deck-helper-section>
    @elseif ($step === 2)
        <x-deck-helper-section class="flex flex-col gap-12 p-12">
            <div class="flex gap-8 bg-surface p-8 rounded-xl w-2/3 items-center">
                <div class="w-1/5">
                    <x-card :level="1" :card="$chosenHelper->mainCard" />
                </div>

                <p class="w-4/5 text-xl text-stone-400 p-2">
                    Great choice! Now let's see what we can do with this card.<br><br>
                    {!! nl2br($chosenHelper->description) !!}<br><br>
                    Here are some other cards that go nicely with this one:
                </p>
            </div>

            <div class="w-2/3 grid grid-cols-3 gap-8 px-8">
                <x-card :level="1" :card="$chosenHelper->mainCard" />
                @foreach ($chosenHelper->cards as $card)
                    <x-card :level="1" :card="$card" />
                @endforeach
            </div>

            <div class="w-2/3 flex gap-8 justify-center">
                <x-form.button-secondary label="Ehh, maybe not, take me back" wire:click="back" />
                <x-form.button label="Sounds good to me!" wire:click="next" />
            </div>
        </x-deck-helper-section>
    @elseif ($step === 3)
        <x-deck-helper-section class="flex flex-col gap-12 p-12">
            <div class="flex gap-8 bg-surface p-8 rounded-xl w-2/3 items-center">
                <p class="text-xl text-stone-400 p-2">
                    Fantastic, we're almost done!<br><br>
                    I've made a small deck for you, based on the card you chose.<br><br>
                    You'll still need to fill up the rest of the deck, but this should give you a good start.<br>
                    Just remember to keep your deck structured, keeping in mind your energy curve. You only get 3 energy per turn, so don't go adding only 4+ cost cards or you'll find yourself quickly outnumbered!<br><br>
                    Finding a good balance of offense/defence is important, but just start playing, you'll get a feel for it pretty quickly!<br><br>
                    Good luck!
                </p>
            </div>

            <div class="w-2/3 flex gap-8">
                <x-form.button-secondary label="I changed my mind!" wire:click="back" />
                <x-form.button label="Take me to my deck!" wire:click="next" />
            </div>
        </x-deck-helper-section>
    @endif
</div>
