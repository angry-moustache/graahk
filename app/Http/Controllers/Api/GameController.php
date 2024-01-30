<?php

namespace App\Http\Controllers\Api;

use App\Events\EmitEvent;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function update(Game $game, Request $request)
    {
        $data = $game->data;

        foreach ($request->get('gameState') as $key => $value) {
            $data[$key] = $value;
        }

        $game->update(['data' => $data]);
    }

    public function finish(Game $game)
    {
        if ($game->finished_at) {
            return;
        }

        $game->update(['finished_at' => now()]);

        // TODO: grant experience based on victory
        $gains = collect($game->data['decks'])->mapWithKeys(function (int $deck, int $owner) {
            $deck = Deck::find($deck);
            $card = Card::find($deck->idList()->shuffle()->first());

            $experience = DB::table('experience')
                ->where('user_id', $owner)
                ->where('card_id', $card->id)
                ->first();

            if ($experience) {
                DB::table('experience')
                    ->where('user_id', $owner)
                    ->where('card_id', $card->id)
                    ->update([
                        'experience' => $experience->experience + 1000,
                    ]);
            } else {
                DB::table('experience')
                    ->insert([
                        'user_id' => $owner,
                        'card_id' => $card->id,
                        'experience' => 1000,
                    ]);
            }

            return [
                $owner => $card->toJavaScript(User::find($owner))
            ];
        });

        EmitEvent::dispatch($game, 'exp_gain', $gains->toArray());
    }
}
