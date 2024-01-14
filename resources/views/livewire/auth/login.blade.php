<x-container class="flex flex-col gap-12">
    <div class="flex flex-col gap-4">
        <x-headers.h1 text="Login" />

        <x-form wire:submit="login">
            <x-form.input wire:model="loginFields.username" label="Username" />
            <x-form.input wire:model="loginFields.password" type="password" label="Password" />
            <x-form.button text="Login" />
        </x-form>
    </div>

    <div class="flex flex-col gap-4">
        <x-headers.h1 text="Register" />

        <x-form wire:submit="register">
            <x-form.input autocomplete="one-time-code" wire:model="registerFields.username" type="text" label="Name" />
            <x-form.input autocomplete="one-time-code" wire:model="registerFields.email" type="email" label="Email" />
            <x-form.input autocomplete="one-time-code" wire:model="registerFields.password" type="password" label="Password" />
            <x-form.input autocomplete="one-time-code" wire:model="registerFields.password_confirmation" type="password" label="Password Confirmation" />
            <x-form.button text="Register" />
        </x-form>
    </div>
</x-container>
