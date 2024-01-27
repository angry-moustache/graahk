<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\User;

class CardController extends Controller
{
    public function show(Card $card, null | User $user = null)
    {
        return response()->json(
            $card->toJavaScript($user)
        );
    }
}
