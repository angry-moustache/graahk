@props([
    'label' => $slot,
])

<x-form.button {{ $attributes->merge(['class' => '
    border-2 border-primary !text-primary
    bg-transparent hover:bg-primary-hover hover:!text-surface
    hover:border-primary-hover transition-all
']) }}>
    {{ $label }}
</x-form.button>
