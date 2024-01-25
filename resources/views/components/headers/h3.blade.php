@props([
    'label' => $slot,
])

<h3 {{ $attributes->merge([
    'class' => 'font-semibold text-md flex items-center',
]) }}>
    {{ $label }}
</h3>
