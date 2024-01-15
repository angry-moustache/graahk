<div
    class="flex h-screen w-screen overflow-hidden"
    x-data="{

    }"
>
    <div wire:poll.keepalive></div>

    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border">
        <x-game.player-info :data="$opponentData" />

        <div class="grow">
            {{-- TODO --}}
        </div>

        <x-game.player-info class="flex-col-reverse" :data="$playerData" />
    </div>

    <div class="flex flex-col grow">
        <x-game.board
            :data="$opponentData"
            @class(['bg-surface' => $opponentId !== $currentPlayerId])
        />

        <x-game.board
            :data="$playerData"
            @class(['bg-surface' => $playerId !== $currentPlayerId])
        />

        <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
            <div class="
                absolute left-0 right-0 -bottom-[4rem]
                flex justify-center items-center gap-2
            ">
                @foreach ($playerData['hand'] as $key => $card)
                    @php
                        $canPlay = ($card->cost <= $playerData['energy']);
                    @endphp

                    <x-card
                        :$card
                        wire:click="playCard({{ $key }})"
                        @class([
                            '!w-[10rem] transition-all duration-300 ease-in-out',
                            'hover:-translate-y-[4.5rem]',
                            'border border-green-500' => $canPlay,
                        ])
                    />
                @endforeach
            </div>
        </div>
    </div>

    {{-- Info columns --}}
    <div class="
        flex flex-col gap-4 h-screen w-[15rem] bg-surface border-l border-l-border
        justify-center items-center
    ">
        <x-form.button
            wire:click="triggerEndTurn"
            class="bg-green-500 hover:bg-green-600"
        >
            End Turn
        </x-form.button>
    </div>
</div>
