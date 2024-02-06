<div {{ $attributes->merge(['class' => 'w-full h-screen overflow-y-scroll']) }}>
    {{ $slot }}

    <img
        class="z-[-1] fixed bottom-0 right-12 opacity-25"
        src="{{ asset('images/deck-helper/jack.png') }}"
    />
</div>
