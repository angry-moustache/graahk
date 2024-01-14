@props([
    'label' => null,
    'options' => collect(),
    'nullable' => false,
    'value' => null,
    'name' => strtolower($label),
])

<x-form.reactive-label :$name :$label {{ $attributes->only('class') }}>
    <select
        data-label-target
        x-on:change="setLabelStatus()"
        class="bg-surface py-3 px-2 text-lg rounded-lg w-full outline-none"
        {{ $attributes->except('class', 'options') }}
    >
        @if ($nullable)
            <option value=""></option>
        @endif

        @foreach ($options as $key => $label)
            <option
                value="{{ $key }}"
                @if($key === $value) selected="selected" @endif
            >
                {{ $label}}
            </option>
        @endforeach
    </select>
</x-form.reactive-label>

@error($attributes->get('wire:model'))
    <div class="text-error">
        {{ $message }}
    </div>
@enderror
