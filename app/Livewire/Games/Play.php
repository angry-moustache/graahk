<?php

namespace App\Livewire\Games;

use App\Entities\Game\Player;
use App\Entities\Game\Queue;
use App\Enums\GameStatus;
use App\Enums\Trigger;
use App\Livewire\Games\Traits;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Play extends Component
{
    use Traits\HandlesSetup;
    use Traits\HandlesTriggers;
    use Traits\HandlesEffects;

    public GameStatus $status;

    public Game $game;

    public bool $yourTurn = false;

    private Player $playerStart;
    private Player $opponentStart;

    private int $playerId;
    private int $opponentId;

    private Player $player;
    private Player $opponent;

    public Collection $gameData;

    private Queue $queue;

    protected $listeners = [
        'update-board' => 'updateBoard',
    ];

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->status = GameStatus::tryFrom($game->data['status']) ?? GameStatus::SETUP;
    }

    public function render()
    {
        $this->queue = new Queue;

        $this->setPlayerData();
        $this->handleGameStatus();

        $this->queue->send();

        if ($this->status !== GameStatus::ANIMATIONS) {
            $this->saveGameData();
        }

        return optional(view('livewire.games.play', [
            'player' => $this->playerStart,
            'opponent' => $this->opponentStart,
            'queue' => $this->queue,
        ]))->layout('components.layouts.game');
    }

    public function updateBoard($board)
    {
        $this->status = GameStatus::ANIMATIONS;

        $this->playerId = auth()->user()->id;
        $this->opponentId = $this->game->opponentId($this->playerId);

        $opponent = $board["player_{$this->opponentId}"];
        $player = $board["player_{$this->playerId}"];

        $this->opponent = Player::make()
            ->power($opponent['power'])
            ->energy($opponent['energy'])
            ->user(User::find($opponent['user']['id']))
            ->deck($opponent['deck'])
            ->hand($opponent['hand'])
            ->board($opponent['board']);

        $this->player = Player::make()
            ->power($player['power'])
            ->energy($player['energy'])
            ->user(User::find($player['user']['id']))
            ->deck($player['deck'])
            ->hand($player['hand'])
            ->board($player['board']);
    }

    private function queue(mixed $job): void
    {
        $this->queue->push($job, [
            'player_' . $this->player->user->id => clone $this->player,
            'player_' . $this->opponent->user->id => clone $this->opponent,
        ]);
    }

    private function handleGameStatus(): void
    {
        if (! $this->yourTurn) {
            return;
        }

        match ($this->status) {
            GameStatus::TURN_START => $this->trigger(Trigger::START_TURN),
            default => null,
        };

        $this->status = GameStatus::PLAYING;
    }

    private function setPlayerData(): void
    {
        $this->gameData = collect($this->game->data);

        $this->yourTurn = $this->gameData['current_player'] == auth()->user()->id;

        $this->playerId = auth()->user()->id;
        $this->opponentId = $this->game->opponentId($this->playerId);

        $playerData = $this->game->data["player_{$this->playerId}"];
        $opponentData = $this->game->data["player_{$this->opponentId}"];

        $this->opponent ??= Player::make()
            ->power($opponentData['power'])
            ->energy($opponentData['energy'])
            ->user(User::find($this->opponentId))
            ->deck($opponentData['deck'])
            ->hand($opponentData['hand'])
            ->board($opponentData['board']);

        $this->player ??= Player::make()
            ->power($playerData['power'])
            ->energy($playerData['energy'])
            ->user(auth()->user())
            ->deck($playerData['deck'])
            ->hand($playerData['hand'])
            ->board($playerData['board']);

        $this->playerStart = clone $this->player;
        $this->opponentStart = clone $this->opponent;
    }

    private function saveGameData(): void
    {
        if (! $this->yourTurn) {
            return;
        }

        $this->gameData["player_{$this->playerId}"] = $this->player->toArray();
        $this->gameData["player_{$this->opponentId}"] = $this->opponent->toArray();
        $this->gameData['status'] = $this->status->value;

        $this->game->update([
            'data' => $this->gameData->toArray(),
        ]);
    }
}
