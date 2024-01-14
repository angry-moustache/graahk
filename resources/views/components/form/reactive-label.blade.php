@props([
    'label',
    'name' => strtolower($label),
    'activated' => false,
])

<div
    {{ $attributes->only('class')->merge(['class' => 'relative z-0 w-full']) }}
    x-data="{
        input: null,
        value: $wire.get('fields.{{ $name }}'),
        activated: @js($activated),
        init () {
            this.input = this.$el.querySelector('[data-label-target]')
            this.setLabelStatus()
        },
        setLabelStatus (activated = null) {
            this.value = this.input.value
            this.activated = (activated === null) ? !! this.input.value : activated
        },
        getValue () {
            return this.value
        },
        setValue (value, defer = true) {
            this.input.value = value
            this.setLabelStatus()

            $wire.set('fields.{{ $name }}', value, defer)
        },
    }"
>
    @if ($label)
        <x-form.label :$label :$name />
    @endif

    {{ $slot }}

    @error("fields.{$name}")
        <span class="inline-block pl-2 pt-2 text-red-500">{{ $message }}</span>
    @enderror
</div>
