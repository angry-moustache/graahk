<?php

namespace App\Livewire\Modal;

use App\Models\Deck;

class DeleteDeck extends Modal
{
    public Deck $deck;

    public function mount(array $params = [])
    {
        $this->deck = Deck::find($params['id'] ?? null);
    }

    public function render()
    {
        return view('livewire.modal.delete-deck');
    }

    public function confirm()
    {
        $this->deck->delete();

        return redirect()->to(route('deck.index'));
    }
}
