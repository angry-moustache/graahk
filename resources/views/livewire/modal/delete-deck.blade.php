<x-modal>
    <x-slot:main>
        <div class="w-full flex flex-col gap-8 p-2">
            <x-headers.h2 label="Are you sure?" />

            <p class="opacity-75">
                You are about to delete the deck "<span class="font-bold">{{ $deck->name }}</span>".<br>
                This action is irreversible!
            </p>
        </div>

        <div class="w-full flex gap-4">
            <x-form.button
                x-on:click="window.closeModal()"
                label="Wait what, no!"
            />

            <x-form.button
                wire:click="confirm"
                label="Yes, farewell"
            />
        </div>
    </x-slot>
</x-modal>
