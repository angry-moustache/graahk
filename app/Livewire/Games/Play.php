<?php

namespace App\Livewire\Games;

use App\Enums\GameStatus;
use App\Enums\Trigger;
use App\Livewire\Games\Traits;
use App\Models\Card;
use App\Models\Game;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Play extends Component
{
    use Traits\HandlesSetup;
    use Traits\HandlesTriggers;
    use Traits\HandlesEffects;

    public Game $game;

    public GameStatus $status;

    public Collection $gameData;

    public string $playerId;
    public string $opponentId;
    public string $currentPlayerId;

    public function render()
    {
        // Get the new board state
        $this->game->refresh();
        $this->gameData = collect($this->game->data);
        $this->status = GameStatus::tryFrom($this->gameData->get('status', GameStatus::SETUP->value));

        $this->playerId = (string) auth()->user()->id;
        $this->opponentId = (string) $this->game->opponentId($this->playerId);
        $this->currentPlayerId = (string) $this->gameData->get('current_player');

        // Check the game state
        if ($this->playerId === $this->currentPlayerId) {
            match ($this->status) {
                GameStatus::SETUP => $this->setup(),
                GameStatus::TURN_START => $this->triggerTurnStart(),
                default => null,
            };
        }

        if ($this->status === GameStatus::SETUP) {
            return optional(view('livewire.games.loading'))
                ->layout('components.layouts.game');
        }

        // Stuff to do before rendering
        $playerData = collect($this->gameData->get("player_{$this->playerId}"));
        $playerData['hand'] = collect($playerData['hand'])->map(fn (int $card) => Card::find($card));
        $opponentData = collect($this->gameData->get("player_{$this->opponentId}"));

        $this->updateGameData();

        return optional(view('livewire.games.play', [
            'playerData' => $playerData,
            'opponentData' => $opponentData,
        ]))->layout('components.layouts.game');
    }

    public function playCard(int $key)
    {
        // Is it the player's turn?
        if (
            $this->status !== GameStatus::PLAYING ||
            $this->playerId !== $this->currentPlayerId
        ) {
            return;
        }

        $card = Card::find(data_get($this->gameData, "player_{$this->playerId}.hand.{$key}"));

        // Is the card playable?
        if ($card->cost > data_get($this->gameData, "player_{$this->playerId}.energy")) {
            return;
        }

        // Play the card
        $playerData = $this->gameData["player_{$this->playerId}"];
        $playerData['energy'] -= $card->cost;
        $playerData['hand'] = collect($playerData['hand'])->forget($key)->values()->toArray();

        // Put it on the board
        $playerData['board'][] = [
            'id' => (string) Str::uuid(),
            'name' => $card->name,
            'image' => $card->attachment->path(),
            'text' => $card->toText(),
            'type' => $card->type,
            'cost' => $card->cost,
            'power' => $card->power,
            'originalPower' => $card->power,
            'tribes' => $card->tribes,
            'keywords' => $card->keywords,
            'ready' => false,
        ];

        $this->gameData->put("player_{$this->playerId}", $playerData);

        $this->checkTrigger(Trigger::PLAY_DUDE);
        $this->checkTrigger(Trigger::OPPONENT_PLAY_DUDE);
    }

    public function updateGameData()
    {
        $this->game->update(['data' => [
            'status' => $this->status->value,
        ] + $this->gameData->toArray()]);
    }
}
