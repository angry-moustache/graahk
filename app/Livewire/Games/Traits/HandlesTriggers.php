<?php

namespace App\Livewire\Games\Traits;

use App\Enums\Effect;
use App\Enums\GameStatus;
use App\Enums\Trigger;

trait HandlesTriggers
{
    public function trigger(Trigger $trigger, ...$args)
    {
        match ($trigger) {
            Trigger::START_TURN => $this->triggerStartTurn(),
            default => null,
        };
    }

    public function checkTriggers(Trigger $trigger)
    {
        // Check triggers on the board
    }

    private function triggerStartTurn()
    {
        // Gain energy
        $this->player->energy += 3;
        $this->queue(Effect::GAIN_ENERGY->animation());
        $this->checkTriggers(Trigger::GAIN_ENERGY);

        // Draw a card
        $this->player->drawCard();
        $this->queue(Effect::DRAW_CARDS->animation());
        $this->checkTriggers(Trigger::DRAW_CARD);

        $this->checkTriggers(Trigger::START_TURN);
    }
}
