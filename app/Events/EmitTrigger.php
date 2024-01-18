<?php

namespace App\Events;

use App\Enums\Trigger;
use App\Models\Game;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmitTrigger implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private Game $game,
        public Trigger $trigger,
        public array $data = [],
    ) {
        //
    }

    public function broadcastOn()
    {
        return [$this->game->id];
    }

    public function broadcastAs()
    {
        return 'trigger';
    }
}
