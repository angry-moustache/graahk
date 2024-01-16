<?php

namespace App\Livewire;

use App\Models\Set;
use Livewire\Component;

class Dashboard extends Component
{
    public null | Set $currentSet = null;

    public function setSet(Set $set)
    {
        $this->currentSet = $set;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'sets' => Set::latest()->get(),
        ]);
    }
}
