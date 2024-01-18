<div
    class="flex h-screen w-screen overflow-hidden"
    x-data="{
        queue: [],
        animations: [],
        init () {
            window.setInterval(() => {
                if (this.queue.length === 0) return

                const job = this.queue.shift()
                job.job(job.object)
            }, 100)

            channel.bind('update-queue', (data) => {
                data.queue.forEach((_job) => {
                    this.queue.push({
                        'object': _job,
                        'job': (job) => {
                            window.setTimeout(() => this.animations.push(job.job), job.job.duration)
                        }
                    })
                })
            })
        },
    }"
>
    <div class="absolute inset-0 pointer-events-none z-90 overflow-hidden">
        <template x-for="animation in animations" :key="animation.uuid">
            <img
                x-key="animation.uuid"
                x-bind:src="'/images/animations/' + animation.name + '.png'"
                class="absolute top-0"
            />
        </template>
    </div>

    <div class="flex flex-col gap-4 h-screen w-[15rem] bg-surface border-r border-r-border">
        <x-game.player-info :player="$opponent" />

        <div class="grow">
            {{-- TODO --}}
        </div>

        <x-game.player-info class="flex-col-reverse" :player="$player" />
    </div>

    <div class="flex flex-col grow">
        <x-game.board
            :player="$opponent"
            @class(['bg-surface' => $yourTurn])
        />

        <x-game.board
            :player="$player"
            @class(['bg-surface' => ! $yourTurn])
        />

        <div class="relative flex justify-evenly h-[20vh] border-t border-t-border bg-surface">
            <div class="
                absolute left-0 right-0 -bottom-[4rem]
                flex justify-center items-center gap-2
            ">
                @foreach ($player->hand as $key => $card)
                    @php
                        $canPlay = ($card['cost'] <= $player->energy);
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
