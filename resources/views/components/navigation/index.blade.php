<div class="w-[300px] h-screen bg-surface px-4 py-8 flex flex-col gap-12">
    <h1 class="text-4xl text-center w-full font-bold">GRAAHK</h1>

    <div class="flex flex-col gap-2">
        <x-navigation.item
            route="{{ route('dashboard.index') }}"
            :active="request()->routeIs('dashboard.index')"
            icon="heroicon-o-home"
            label="Dashboard"
        />

        <x-navigation.item
            route="{{ route('deck.index') }}"
            :active="request()->routeIs('deck.index')"
            icon="heroicon-o-square-3-stack-3d"
            label="Decks"
        />

        <x-navigation.item
            route="{{ route('logout.index') }}"
            :active="request()->routeIs('logout.index')"
            icon="heroicon-o-arrow-right-start-on-rectangle"
            label="Logout"
        />
    </div>
</div>
