<?php

namespace App\Events;

use App\Enums\Trigger;
use App\Models\Game;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class EmitEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $uuid;

    public function __construct(
        private Game $game,
        public string $event,
        public array $data = [],

    ) {
        $this->uuid = Str::uuid();
    }

    public function broadcastOn()
    {
        return [$this->game->id];
    }

    public function broadcastAs()
    {
        return 'event';
    }
}
