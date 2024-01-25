@props([
    'label' => $slot,
])

<h1 {{ $attributes->merge([
    'class' => 'font-semibold text-2xl',
]) }}>
    {{ $label }}
</h1>
