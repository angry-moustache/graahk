@props([
    'card',
])

<div
    style="background-image: url({{ $card->attachment->path() }})"
    class="
        graahk-card w-full rounded-xl overflow-hidden
        bg-cover bg-center relative
        text-black select-none aspect-[2.5/3.5]
    "
>
    <img src="{{ asset('images/cards/dude-1.svg')}}" />

    <h2 class="absolute top-[4%] left-[4%] text-center w-[14.5%] font-bold">{{ $card->cost }}</h3>
    <h3 class="absolute top-[5%] left-[21%] w-full font-bold">{{ $card->name }}</h3>

    <span class="absolute bottom-[36.5%] left-[8%] w-[80%] text-lg">
        {{ $card->getTribes()->join(', ') }}
    </span>

    <p class="absolute top-[65%] bottom-[14%] left-[9%] w-[82%] overflow-y-auto">
        {{ $card->getText() }}
    </p>

    <h4 class="absolute bottom-[2.6%] left-[4%] w-[29%] text-center font-bold">{{ $card->power }}</h4>
</div>
