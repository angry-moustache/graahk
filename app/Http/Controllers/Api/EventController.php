<?php

namespace App\Http\Controllers\Api;

use App\Events\EmitEvent;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __invoke(Game $game, Request $request)
    {
        EmitEvent::dispatch(
            $game,
            $request->event,
            $request->data ?? [],
        );
    }
}
