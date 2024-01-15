<?php

namespace App\Livewire\Games\Traits;

use App\Enums\GameStatus;
use App\Models\Deck;

trait HandlesSetup
{
    public function setup()
    {
        // Setup game
        $this->status = GameStatus::TURN_START;
        $this->gameData->put('status', GameStatus::TURN_START->value);
        $this->gameData->put('current_player', $this->game->user_id_1);

        // Setup hands
        for ($i = 1; $i <= 2; $i++) {
            $playerId = $this->game->{"user_id_{$i}"};

            $data = collect([
                'power' => 2000,
                'energy' => 0,
                'hand' => [],
                'deck' => Deck::find($this->game->data['decks'][$playerId])
                    ->idList()
                    ->toArray(),
            ]);

            $this->gameData->put("player_{$playerId}", $data);

            $this->effectShuffle($playerId); // Shuffle decks
            $this->effectDrawRandomCard($playerId, 5); // Draw opening hand

            $this->updateGameData();
        }
    }
}
