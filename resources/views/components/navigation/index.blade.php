<div class="w-[300px] h-screen bg-surface px-4 py-8 flex flex-col gap-4">
    <a href="{{ route('dashboard.index') }}">
        <img src="{{ asset('images/logo.png') }}" class="p-4" />
    </a>

    <x-navigation.item
        route="{{ route('dashboard.index') }}"
        :active="request()->routeIs('dashboard.index')"
        icon="heroicon-o-home"
        label="Dashboard"
    />

    <x-navigation.item
        route="{{ route('profile.edit') }}"
        :active="request()->routeIs('profile.edit')"
        icon="heroicon-o-user-circle"
        label="Profile"
    />

    <x-navigation.item
        route="{{ route('deck.index') }}"
        :active="request()->routeIs('deck.index')"
        icon="heroicon-o-square-3-stack-3d"
        label="Decks"
    />

    <x-navigation.item
        route="{{ route('server.index') }}"
        :active="request()->routeIs('server.index')"
        icon="heroicon-o-globe-alt"
        :label="'Server browser (' . \App\Models\Game::ongoing()->count() . ')'"
    />

    <div class="grow"></div>

    <x-navigation.item
        route="{{ route('logout.index') }}"
        :active="request()->routeIs('logout.index')"
        icon="heroicon-o-arrow-right-start-on-rectangle"
        label="Logout"
    />
</div>
