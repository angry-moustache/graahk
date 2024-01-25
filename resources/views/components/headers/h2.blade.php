@props([
    'label' => $slot,
])

<h2 {{ $attributes->merge([
    'class' => 'font-semibold text-lg flex gap-8 items-center',
]) }}>
    <span>{{ $label }}</span>
    <div class="opacity-50 border-b border-border grow"></div>
    @if (isset($actions))
        {{ $actions }}
    @endif
</h2>
