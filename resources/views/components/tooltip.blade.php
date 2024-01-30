<div class="flex flex-col bg-surface border-2 border-border rounded-lg max-w-[20rem] {{ isset($class) ? $class : '' }}">
    <div class="flex gap-4 items-center p-3">
        @if (isset($data['cost']))
            <span class="rounded-full flex w-8 h-8 text-xl font-bold items-center justify-center bg-primary text-surface">
                {{ $data['cost'] }}
            </span>
        @endif

        <div class="flex flex-col">
            <span class="font-bold">
                {!! $data['name'] !!}
            </span>

            @if (isset($data['tribes']))
                <span class="opacity-50 text-sm -mt-1">
                    {!! $data['tribes'] !!}
                </span>
            @endif
        </div>
    </div>

    @if (filled($data['text']))
        <div class="p-3 border-t border-border">
            {!! $data['text'] !!}
        </div>
    @endif

    @if (isset($data['power']))
        <div class="font-bold p-3 border-t border-border">
            {!! $data['power'] !!} power
        </div>
    @endif
</div>

@if (isset($data['extras']) && count($data['extras']) > 0)
    <div class="flex flex-col gap-1 mt-1">
        @foreach ($data['extras'] ?? [] as $extra)
            <x-tooltip
                :data="$extra"
                class="mx-4 w-[18rem]"
            />
        @endforeach
    </div>
@endif
