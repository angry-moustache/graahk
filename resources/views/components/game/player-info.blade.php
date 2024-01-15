<div {{ $attributes->except('data')->merge([
    'class' => 'flex flex-col gap-4 p-4',
]) }}>
    <img
        {{-- TODO: player icon --}}
        src="{{ asset('images/logo.jpg') }}"
        class="w-full rounded-xl"
    >

    <h2 class="w-full text-center font-bold text-6xl">
        {{ $data['power'] }}
    </h2>

    <div>
        <p>{{ count($data['deck']) }} (deck)</p>
        <p>{{ count($data['hand']) }} (hand)</p>
        <p>{{ $data['energy'] }} (energy)</p>
    </div>
</div>
