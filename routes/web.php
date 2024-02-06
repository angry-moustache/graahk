<?php

use App\Http\Middleware\Authenticate;
use App\Livewire;
use App\Models\Draft;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('login', Livewire\Auth\Login::class)
    ->name('login.index');

Route::get('/', Livewire\Dashboard::class)
    ->name('dashboard.index');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('logout', function () {
        auth()->logout();

        return redirect()->to('login');
    })->name('logout.index');

    Route::get('profiles', Livewire\Profiles\Index::class)
        ->name('profile.index');

    Route::get('profile/edit', Livewire\Profiles\Edit::class)
        ->name('profile.edit');

    Route::get('decks', Livewire\Decks\Index::class)
        ->name('deck.index');

    Route::get('decks/edit/{deck:id}', Livewire\Decks\Edit::class)
        ->name('deck.edit');

    Route::get('deck-helper', Livewire\DeckHelper\Index::class)
        ->name('deck-helper.index');

    Route::get('draft', function () {
        $draft = Draft::firstOrCreate(
            ['user_id' => auth()->id()],
            ['cards' => []],
        );

        return redirect()->to(route('draft.create', $draft));
    })->name('draft.index');

    Route::get('draft/{uuid}', Livewire\Drafts\Create::class)
        ->name('draft.create');

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
