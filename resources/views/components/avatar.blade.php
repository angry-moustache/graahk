<div {{ $attributes->except('user')->merge([
    'class' => 'w-16 h-16 bg-surface rounded-lg overflow-hidden',
]) }}>
    <div
        class="w-full pt-[100%] rounded-lg bg-background bg-cover bg-center"
        style="background-image: url('{{ $user?->avatar_url }}')"
    ></div>
</div>
