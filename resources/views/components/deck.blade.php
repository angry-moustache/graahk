<a
    href="{{ $deck->route() }}"
    style="background-image: url('{{ $deck->image()?->path() }}')"
    class="
        relative flex flex-col aspect-[2.5/3.5]
        bg-surface bg-cover bg-center bg-no-repeat
        justify-end gap-2 text-white text-xl
    "
>
    <div class="bg-black p-4 bg-opacity-75">
        {{ $deck->name }}
    </div>

    <div class="absolute top-4 left-4 text-error flex items-center gap-4 bg-black py-2 px-4 rounded-lg bg-opacity-75">
        <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
        Not a legal deck
    </div>
</a>
