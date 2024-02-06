<x-container class="flex flex-col gap-12 py-8">
    <div wire:poll class="flex flex-col gap-8">
        <x-headers.h2 label="Available tables" />

        {{-- Active games --}}
        <div class="w-full flex flex-wrap gap-8">
            @foreach ($games as $game)
                <div class="w-2/5 flex gap-2 relative bg-surface p-4 rounded-xl">
                    <x-avatar :user="$game->user1" />
                    <x-avatar :user="$game->user2" />

                    @if ($game->user2)
                        <img
                            class="absolute top-5 w-14 left-14 z-100"
                            src="{{ asset('images/swords.png') }}"
                        />
                    @endif

                    <div class="flex flex-col justify-center pl-4 gap-1">
                        <span class="text-lg font-bold">
                            {{ $game->name }}
                        </span>

                        <div class="flex items-center gap-2">
                            <x-format-icon :format="$game->format()" size="sm" />
                            <p class="opacity-50">
                                {{ $game->format()?->name() }}
                            </p>
                        </div>
                    </div>

                    <div class="grow"></div>

                    <div class="flex flex-col justify-center">
                        @if (! $game->user2 && $game->user1->id !== auth()->id())
                            <x-form.button
                                label="Join"
                                x-on:click="window.openModal('joinGame', {
                                    gameId: '{{ $game->id }}',
                                })"
                            />
                        @endif

                        @if (in_array(auth()->id(), [$game->user1?->id, $game->user2?->id]))
                            <x-form.button
                                label="Continue"
                                x-on:click="window.location.href = '{{ route('game.play', $game) }}'"
                            />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($canCreate)
            <div>
                <x-form.button
                    label="Create new table"
                    x-on:click="window.openModal('createGame')"
                />
            </div>
        @endif
    </div>
</x-container>
