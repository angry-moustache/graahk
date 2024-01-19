<?php

namespace App\Http\Controllers\Api;

use App\Enums\Trigger;
use App\Events\EmitEvent;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __invoke(Game $game, Request $request)
    {
        $event = Trigger::from($request->event);

        EmitEvent::dispatch($game, $event, $request->data ?? []);
    }
}
