<?php

namespace App\Livewire;

use App\Models\Card;
use App\Models\Game;
use App\Models\Set;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public null | Set $currentSet = null;

    public null | Card $highlighted = null;

    public int $level = 1;

    public function setSet(Set $set)
    {
        $this->currentSet = $set;
        // Game::where('user_id_1', 2)->where('user_id_2', 4)->delete();
        // Game::where('user_id_1', 4)->where('user_id_2', 2)->delete();
    }

    public function setHighlighted(null | int $card)
    {
        $this->highlighted = Card::find($card);
    }

    public function render()
    {
        $sets = Set::latest()->get();
        $this->currentSet ??= $sets->last();

        return view('livewire.dashboard', [
            'sets' => $sets,
            'users' => User::latest()->get()->sortByDesc->gamesPlayed(),
        ]);
    }
}
