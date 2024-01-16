<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateQueue implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $queue;

  public function __construct($queue)
  {
      $this->queue = $queue;
  }

  public function broadcastOn()
  {
      return ['my-channel'];
  }

  public function broadcastAs()
  {
      return 'update-queue';
  }
}
