<?php

namespace App\Livewire\Servers;

use App\Models\Game;
use Livewire\Component;

class Index extends Component
{
    public array $fields = [
        'name' => 'New table',
        'deck_id' => null,
    ];

    public function mount()
    {
        app('site')->title('Server Browser');
    }

    public function render()
    {
        $canCreate = Game::ongoing()
            ->where('user_id_1', auth()->id())
            ->orWhere('user_id_2', auth()->id())
            ->count() === 0;

        return view('livewire.servers.index', [
            'games' => Game::latest()->ongoing()->get(),
            'decks' => auth()->user()->decks->pluck('name', 'id'),
            'canCreate' => $canCreate,
        ]);
    }

    public function create()
    {
        $this->validate([
            'fields.name' => 'required|string',
            'fields.deck_id' => 'required|exists:decks,id',
        ]);

        Game::create([
            'name' => $this->fields['name'],
            'user_id_1' => auth()->id(),
            'data' => [
                'decks' => [
                    auth()->id() => $this->fields['deck_id'],
                ],
            ],
        ]);
    }

    public function joinGame(Game $game)
    {
        if ($game->player_id_2) {
            return;
        }

        $player1 = $game->user_id_1;
        $player2 = auth()->id();

        $gameData = $game->data;
        $gameData['decks'][$player2] = $this->fields['deck_id'];
        $gameData['current_player'] = rand(0, 1) ? $player2 : $player1;

        $game->update([
            'data' => $gameData,
            'user_id_1' => $player1,
            'user_id_2' => $player2,
        ]);
    }
}
