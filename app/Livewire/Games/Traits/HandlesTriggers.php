<?php

namespace App\Livewire\Games\Traits;

use App\Enums\GameStatus;
use App\Enums\Trigger;

trait HandlesTriggers
{
    public function checkTrigger(Trigger $trigger)
    {
        // dd($trigger);
        $this->updateGameData();
    }

    public function triggerTurnStart()
    {
        $turn = $this->gameData['turn'] ?? 0;
        $this->gameData['turn'] = $turn + 1;

        $this->effectGainEnergy($this->currentPlayerId, 3); // Gain energy

        // Draw card (if not first turn)
        if ($this->gameData['turn'] > 1) {
            $this->effectDrawRandomCard($this->currentPlayerId, 1);
        }

        $this->status = GameStatus::PLAYING;

        $this->checkTrigger(Trigger::START_TURN);
    }

    public function triggerEndTurn()
    {
        // Are you allowed?
        if (
            // $this->status !== GameStatus::PLAYING ||
            $this->currentPlayerId !== $this->playerId
        ) {
            return;
        }

        $this->gameData['current_player'] = $this->opponentId;
        $this->status = GameStatus::TURN_START;

        $this->checkTrigger(Trigger::END_TURN);
    }
}
