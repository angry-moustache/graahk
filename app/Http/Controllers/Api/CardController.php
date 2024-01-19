<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;

class CardController extends Controller
{
    public function show(Card $card)
    {
        return response()->json($card->toJavaScript());
    }
}
