@props([
    'label' => $slot,
])

<button
    {{ $attributes->merge(['class' => '
        block bg-primary rounded px-4 py-2 font-bold text-surface
        hover:bg-primary-hover cursor-pointer
        flex items-center justify-center gap-2
    ']) }}
>
    {{ $label }}
</button>
