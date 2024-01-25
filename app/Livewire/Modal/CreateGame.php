<?php

namespace App\Livewire\Modal;

use App\Models\Deck;
use App\Models\Game;
use Illuminate\Support\Collection;

class CreateGame extends Modal
{
    public Game $game;

    public Collection $decks;

    public array $fields = [
        'name' => 'New table',
    ];

    public function mount()
    {
        $this->decks = auth()->user()
            ->decks
            ->filter(fn (Deck $deck) => $deck->isLegal())
            ->pluck('name', 'id');
    }

    public function render()
    {
        return view('livewire.modal.create-game');
    }

    public function create()
    {
        $this->validate([
            'fields.name' => 'required|string',
            'fields.deck_id' => 'required|exists:decks,id',
        ], [
            'fields.name.required' => 'Please enter a name for the table.',
            'fields.deck_id.required' => 'Please select a deck.',
        ]);

        $game = Game::create([
            'name' => $this->fields['name'],
            'user_id_1' => auth()->id(),
            'data' => [
                'decks' => [
                    auth()->id() => $this->fields['deck_id'],
                ],
            ],
        ]);

        return redirect()->to($game->route());
    }
}
