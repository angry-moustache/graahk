<div
    class="relative flex justify-center"
    x-on:click="() => {
        console.log('click', '{{ json_encode($dude) }}')
    }"
>
    <div
        style="background-image: url('{{ $dude['image'] }}')"
        class="
            w-[10rem] aspect-[2.5/3.5]
            border-[2px] border-black bg-cover bg-center rounded-xl overflow-hidden
        "
    ></div>

    <div class="
        absolute -bottom-[2rem] pb-1 pt-2 px-6 text-5xl font-bold bg-surface
        border-[2px] border-black rounded-2xl
    ">
        {{ $dude['power'] }}
    </div>
</div>
