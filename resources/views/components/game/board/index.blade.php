<div {{ $attributes->except('data')->merge([
    'class' => 'flex flex-col h-[40vh] w-full justify-center',
]) }}>
    <div class="flex flex-wrap h-[30vh] w-full gap-4 items-center justify-evenly">
        @foreach ($data['board'] ?? [] as $dude)
            <x-game.board.dude :$dude />
        @endforeach
    </div>
</div>
