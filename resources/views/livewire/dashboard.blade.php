<x-container class="py-12">
    <div class="flex flex-col gap-12">
        <x-headers.h1 label="Dashboard of random gobbledegook" />

        <div class="flex flex-col gap-6">
            <x-headers.h2 label="Players" />

            <div class="grid grid-cols-6 gap-4">
                @foreach ($users as $user)
                    <div class="flex gap-4 items-center justify-center">
                        <x-avatar :user="$user" />

                        <div class="flex flex-col">
                            <span class="font-bold">
                                {{ $user->username }}
                            </span>
                            <span class="opacity-50 text-sm">
                                @php $played = $user->gamesPlayed(); @endphp
                                Played {{ $played }} {{ Str::plural('game', $played) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <x-headers.h2 class="py-6" label="Sets" />

            <div class="flex gap-4">
                <div class="grid grid-cols-4 gap-4 w-2/3">
                    @foreach ($sets as $set)
                        <div
                            x-on:click="$wire.setSet({{ $set->id }})"
                            class="aspect-[2.5/3.5] bg-cover bg-center rounded-xl"
                            style="background-image: url('{{ $set->attachment->path() }}')"
                        ></div>
                    @endforeach
                </div>

                <div class="w-1/4">
                    <x-form.select
                        label="Level"
                        wire:model.live="level"
                        :options="[
                            '1' => 'Card level 1',
                            '2' => 'Card level 2',
                            '3' => 'Card level 3',
                            '4' => 'Card level 4',
                        ]"
                    />
                </div>
            </div>
        </div>

        @if ($currentSet)
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <x-headers.h2 :label="$currentSet->name" />

                    <p class="opacity-50">
                        Contains {{ $currentSet->cards()->dudes()->count() }} cards
                    </p>
                </div>

                <div class="grid grid-cols-6 gap-4">
                    @foreach ($currentSet->cards()->dudes()->get() as $card)
                        <x-card :$card :level="$level" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-container>
