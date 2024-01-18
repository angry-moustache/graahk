<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function update(Game $game, Request $request)
    {
        $game->update(['data' => $request->get('gameState')]);
    }
}
