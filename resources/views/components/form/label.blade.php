@props([
    'label',
    'name',
])

<label
    for="{{ $name }}"
    :class="{ '!-top-4 text-xs opacity-100' : activated }"
    {{ $attributes->only('class')->merge([
        'class' => "absolute top-0.5 pointer-events-none transition-all px-4 py-2 opacity-50",
    ]) }}
>
    {{ $label }}
</label>
