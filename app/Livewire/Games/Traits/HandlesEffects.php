<?php

namespace App\Livewire\Games\Traits;

use App\Enums\Trigger;

trait HandlesEffects
{
    public function effectShuffle(string $playerId)
    {
        $player = $this->gameData["player_$playerId"];
        $player['deck'] = collect($player['deck'])->shuffle()->toArray();
        $this->gameData->put("player_$playerId", $player);
    }

    public function effectDrawRandomCard(string $playerId, int $amount)
    {
        $player = $this->gameData["player_$playerId"];
        $hand = $player['hand'];
        $deck = $player['deck'];

        for ($i = 0; $i < $amount; $i++) {
            $hand[] = array_pop($deck);
        }

        $player['hand'] = $hand;
        $player['deck'] = $deck;

        $this->gameData->put("player_$playerId", $player);

        $this->checkTrigger(Trigger::DRAW_CARD);
    }

    public function effectGainEnergy(string $playerId, int $amount)
    {
        $player = $this->gameData["player_$playerId"];
        $player['energy'] += $amount;
        $this->gameData->put("player_$playerId", $player);

        $this->checkTrigger(Trigger::GAIN_ENERGY);
    }
}
