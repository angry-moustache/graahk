<div {{ $attributes->except('player')->merge([
    'class' => 'flex flex-col gap-4 p-4',
]) }}>
    <img
        src="{{ asset('images/logo.jpg') }}"
        class="w-full rounded-xl"
    >

    <h2 class="w-full text-center font-bold text-6xl">
        {{ $player->power }}
    </h2>

    <div>
        <p>{{ count($player->deck) }} (deck)</p>
        <p>{{ count($player->hand) }} (hand)</p>
        <p>{{ $player->energy }} (energy)</p>
    </div>
</div>
