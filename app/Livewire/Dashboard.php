<?php

namespace App\Livewire;

use App\Models\Set;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public null | Set $currentSet = null;

    public int $level = 1;

    public function setSet(Set $set)
    {
        $this->currentSet = $set;
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
