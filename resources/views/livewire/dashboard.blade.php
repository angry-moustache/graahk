<x-container>
    <x-headers.h1 class="py-6" text="Sets" />
    <div class="grid grid-cols-6 gap-4">
        @foreach ($sets as $set)
            <div
                x-on:click="$wire.setSet({{ $set->id }})"
                class="aspect-[2.5/3.5] bg-cover bg-center rounded-xl"
                style="background-image: url('{{ $set->attachment->path() }}')"
            ></div>
        @endforeach
    </div>

    @if ($currentSet)
        <x-headers.h2 class="py-6" :text="$currentSet->name" />
        <div class="grid grid-cols-6 gap-4">
            @foreach ($currentSet->cards()->dudes()->get() as $card)
                <x-card :$card />
            @endforeach
        </div>
    @endif
</x-container>
