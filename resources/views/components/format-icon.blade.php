@props([
    'format',
    'size' => 'md',
])

<div
    style="{{ $format->style() }}"
    {{ $attributes->except('format')->merge([
        'class' => implode(' ', [
            'flex justify-center items-center rounded-lg gap-2 aspect-square',
            match ($size) {
                'sm' => 'w-6 h-6',
                'md' => 'w-8 h-8',
                'lg' => 'w-11 h-11',
            },
        ]),
    ]) }}
>
    <x-dynamic-component
        :component="$format->icon()"
        class="{{ match ($size) {
            'sm' => 'w-4 h-4',
            'md' => 'w-6 h-6',
            'lg' => 'w-8 h-8',
        } }}"
    />
</div>
