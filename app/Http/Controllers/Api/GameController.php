<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function update(Game $game, Request $request)
    {
        $data = $game->data;

        info($request->get('gameState'));
        foreach ($request->get('gameState') as $key => $value) {
            $data[$key] = $value;
        }

        $game->update(['data' => $data]);
    }

    public function finish(Game $game, Request $request)
    {
        if ($game->finished_at) {
            return;
        }

        $game->update(['finished_at' => now()]);

        // TODO: grant experience
    }
}
