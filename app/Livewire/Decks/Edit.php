<?php

namespace App\Livewire\Decks;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Support\Collection;
use Livewire\Component;

class Edit extends Component
{
    public Deck $deck;

    public Collection $deckList;

    public string $name;

    public null | int $mainCardId = null;

    public function mount()
    {
        app('site')->title($this->deck->name);

        $this->deckList = $this->deck->list();

        if ($this->deck->user_id !== auth()->id()) {
            abort(403);
        }

        $this->name = $this->deck->name;
        $this->mainCardId = $this->deck->main_card_id;
    }

    public function render()
    {
        return view('livewire.decks.edit', [
            'cards' => Card::dudes()->get(),
            'cardList' => Card::dudes()
                ->get()
                ->mapWithKeys(fn (Card $card) => [$card->id => $card->toJavascript()]),
        ]);
    }

    public function saveDeck()
    {
        $this->validate([
            'name' => 'required',
        ]);

        $this->deck->update([
            'name' => $this->name,
            'main_card_id' => $this->mainCardId,
            'cards' => $this->deckList->pluck('amount', 'card.id'),
        ]);
    }
}
