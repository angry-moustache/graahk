@props([
    'card',
    'text' => $card->toText(),
    'level' => $card->getLevel(),
])

<div
    style="background-image: url('{{ $card->attachment->path() }}')"
    {{ $attributes->merge(['class' => '
        graahk-card w-full rounded-xl overflow-hidden
        bg-cover bg-center relative
        text-black select-none aspect-[2.5/3.5]
        isolate z-[-2]
    ']) }}
>
    <div class="absolute inset-0 rounded-xl overflow-hidden">
        @if ($level >= 4) <div class="z-[-1] rounded-xl overflow-hidden animate-foil"></div> @endif
    </div>

    <img src="{{ asset('images/cards/dude-' . $level . '.svg')}}" />

    <h2 class="absolute top-[4%] left-[4%] text-center w-[14.5%] font-bold">{{ $card->cost }}</h3>
    <h3 class="absolute top-[5%] left-[21%] w-full font-bold">{{ $card->name }}</h3>

    <span
        @class([
            'absolute w-[80%] text-lg',
            'bottom-[36.5%] left-[8%]' => ($level <= 2),
            'bottom-[5.5%] left-[36.5%]' => ($level > 2),
        ])
    >
        @if (is_string($card->tribes))
            {{ $card->tribes }}
        @else
            {{ $card->getTribes()->join(', ') }}
        @endif
    </span>

    @if (filled($text))
        <p @class([
            'absolute bottom-[14%] overflow-y-auto',
            'left-[9%] w-[82%] top-[65%]' => ($level <= 2),
            'left-[4%] w-[92%]' => ($level > 2),
            'text-white text-border-hard' => ($level > 2),
            'bg-black p-2 bg-opacity-25 rounded-lg' => ($level >= 3),
        ])>
            {!! $text !!}
        </p>
    @endif

    <h4 class="absolute bottom-[2.6%] left-[4%] w-[29%] text-center font-bold">{{ $card->power }}</h4>
</div>
