<?php

namespace App\Http\Controllers\Api;

use App\Enums\Trigger;
use App\Events\EmitTrigger;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class TriggerController extends Controller
{
    public function __invoke(Game $game, Request $request)
    {
        $trigger = Trigger::from($request->trigger);

        EmitTrigger::dispatch($game, $trigger, $request->data ?? []);
    }
}
