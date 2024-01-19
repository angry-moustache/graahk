<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::put('games/{game:id}', [Api\GameController::class, 'update']);
Route::post('games/{game:id}/trigger', Api\TriggerController::class);

Route::get('cards/{card:id}', [Api\CardController::class, 'show']);
