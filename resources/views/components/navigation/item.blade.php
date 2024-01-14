@props([
    'route' => '#',
    'icon' => '',
    'activeIcon' => Str::replace('-o-', '-s-', $icon),
    'active' => isset($route) && ($route === request()->url()),
    'label',
])

<a
    href="{{ $route }}"
    {{ $attributes->only('x-on:click.prevent') }}
    @class([
        'text-primary' => $active,
        'flex p-4 text-title hover:text-primary w-full select-none transition-colors',
        'flex-row justify-start relative gap-4',
    ])
>
    <div class="relative w-8 h-6">
        <x-dynamic-component
            :component="$active ? $activeIcon : $icon"
            class="w-8 h-6"
        />
    </div>

    <span class="block">
        {{ $label }}
    </span>
</a>
