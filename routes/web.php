<?php

use App\Http\Middleware\Authenticate;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('login', Livewire\Auth\Login::class)
    ->name('login.index');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('logout', function () {
        auth()->logout();

        return redirect()->to('login');
    })->name('logout.index');

    Route::get('/', Livewire\Dashboard::class)
        ->name('dashboard.index');

    Route::get('decks', Livewire\Decks\Index::class)
        ->name('deck.index');

    Route::get('decks/edit/{deck:id}', Livewire\Decks\Edit::class)
        ->name('deck.edit');

    Route::get('server', Livewire\Servers\Index::class)
        ->name('server.index');

    Route::get('play/{game:id}', Livewire\Games\Play::class)
        ->name('game.play');
});

Route::get('test', function () {
    \App\Models\Card::where('name', 'Free Food')->get()->each(function ($card) {
        $card->toText();
    });
});
