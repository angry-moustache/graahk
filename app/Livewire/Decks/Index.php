<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $decks;

    public function mount()
    {
        app('site')->title('Decks');

        // Remove empty decks
        Deck::where('user_id', auth()->id())->get()
            ->each(function ($deck) {
                if (collect($deck->cards)->isEmpty()) {
                    $deck->delete();
                }
            });

        $this->decks = Deck::latest('updated_at')
            ->where('user_id', auth()->id())
            ->get();
    }

    public function render()
    {
        return view('livewire.decks.index');
    }
}
