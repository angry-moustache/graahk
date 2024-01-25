@props([
    'main' => $slot,
    'footer' => null,
    'disableOverlayClick' => false,
    'fullScreen' => false,
    'width' => null,
])

<div class="modal">
    <div
        class="modal-overlay"
        @if (! $disableOverlayClick)
            x-on:click="window.closeModal()"
        @endif
    ></div>

    <div @class([
        'modal-content bg-background rounded-xl',
        '!min-w-[90%]' => $fullScreen,
        $width => ! $fullScreen,
    ])>
        <div class="!p-0" {{ $attributes->only('x-data') }}>
            <div wire:loading.flex>
                <x-loading />
            </div>

            <div {{ $attributes->except('x-data')->merge(['class' => 'flex flex-col gap-8 p-6']) }} wire:loading.remove>
                {{ $main }}
            </div>

            @if ($footer)
                <div class="
                    modal-footer flex w-full p-4 mt-4 gap-4 justify-end
                    border-t border-border bg-surface
                ">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
