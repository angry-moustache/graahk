<?php

namespace App\Livewire\Modal;

use App\Models\Card;
use App\Models\Deck;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JoinGame extends Modal
{
    public Game $game;

    public Collection $decks;

    public array $fields = [];

    public function mount(array $params = [])
    {
        $this->game = Game::find($params['gameId'] ?? null);
        $this->decks = auth()->user()
            ->decks
            ->filter(fn (Deck $deck) => $deck->isLegal())
            ->pluck('name', 'id');
    }

    public function render()
    {
        return view('livewire.modal.join-game');
    }

    public function join()
    {
        $this->validate(
            ['fields.deck_id' => 'required|exists:decks,id'],
            ['fields.deck_id.required' => 'Please select a deck.']
        );

        if ($this->game->player_id_2) {
            return;
        }

        $player1 = $this->game->user_id_1;
        $player2 = auth()->id();

        $gameData = $this->game->data;
        $gameData['decks'][$player2] = $this->fields['deck_id'];
        $gameData['current_player'] = rand(0, 1) ? $player2 : $player1;

        foreach ($gameData['decks'] as $player => $deck) {
            $user = User::find($player);

            $gameData["player_{$player}"] = [
                'id' => $user->id,
                'name' => $user->username,
                'avatar' => $user->avatar_url,
                'uuid' => (string) Str::uuid(),
                'board' => [],
                'hand' => [],
                'graveyard' => [],
                'energy' => 0,
                'power' => 2000,
                'originalPower' => 2000,
                'deck' => Deck::find($deck)
                    ->idList()
                    ->shuffle()
                    ->map(fn (int $id) => Card::find($id)->toJavaScript($user))
                    ->map(function (array $card) use ($player) {
                        $card['owner'] = $player;

                        return $card;
                    })
                    ->toArray(),
            ];
        }

        $this->game->update([
            'data' => $gameData,
            'user_id_1' => $player1,
            'user_id_2' => $player2,
        ]);

        return redirect()->to($this->game->route());
    }
}
