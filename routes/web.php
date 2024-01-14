<?php

use App\Http\Middleware\Authenticate;
use App\Livewire\Auth;
use Illuminate\Support\Facades\Route;

Route::get('login', Auth\Login::class)
    ->name('login.index');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('logout', function () {
        auth()->logout();

        return redirect()->to('login');
    })->name('logout.index');

    Route::get('/', fn () => dd(auth()->user()));
});
