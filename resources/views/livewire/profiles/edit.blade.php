<x-container class="flex flex-col gap-12 pt-12">
    <div class="flex items-center gap-8">
        <x-avatar class="w-32 h-32" :user="$user" />

        <div class="flex flex-col">
            <p class="text-3xl font-extrabold">{{ $user->username }}</p>
            <p class="opacity-50">Joined {{ $user->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <div class="flex flex-col gap-4 w-full">
        <x-headers.h2 label="Upload new avatar" />

        <x-form.input
            label="avatar"
            wire:model.live="avatar"
            type="file"
        />
    </div>

    <form
        wire:submit.prevent="update"
        class="flex flex-col gap-6 w-full"
    >
        <x-headers.h2 label="General" />

        <x-form.input
            label="Username"
            wire:model="username"
        />

        <x-form.input
            label="E-mail"
            wire:model="email"
        />

        <div>
            <x-form.button label="Update profile" />
        </div>
    </form>
</x-container>
