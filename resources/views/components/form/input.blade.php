@props([
    'id' => Str::random(16),
    'label' => null,
    'name' => strtolower($label),
])

<x-form.reactive-label :$name :$label {{ $attributes->only('class') }}>
    <input
        data-label-target
        x-init="setLabelStatus()"
        x-on:focus="setLabelStatus(true)"
        x-on:blur="setLabelStatus()"
        class="bg-surface px-4 py-2 text-lg rounded-lg w-full outline-none"
        {{ $attributes->except('class') }}
    />
</x-form.reactive-label>

@error($attributes->get('wire:model') ?? $attributes->get('wire:model.live'))
    <div class="text-error">
        {{ $message }}
    </div>
@enderror
