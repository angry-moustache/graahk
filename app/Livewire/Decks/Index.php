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

        $this->decks = Deck::latest()
            ->where('user_id', auth()->id())
            ->get();
    }

    public function render()
    {
        return view('livewire.decks.index');
    }

    public function newDeck()
    {
        $deck = Deck::create([
            'name' => 'New Deck',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('deck.edit', $deck);
    }
}
