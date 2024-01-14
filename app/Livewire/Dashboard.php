<?php

namespace App\Livewire;

use App\Models\Card;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public Collection $cards;

    public function mount()
    {
        $this->cards = Card::latest()->get();
    }
}
