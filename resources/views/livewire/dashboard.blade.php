<x-container>
    <div class="grid grid-cols-6 gap-4">
        @foreach ($cards as $card)
            <x-card :$card />
        @endforeach
    </div>
</x-container>
