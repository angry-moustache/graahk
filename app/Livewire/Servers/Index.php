<?php

namespace App\Livewire\Servers;

use App\Models\Game;
use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        app('site')->title('Server Browser');
    }

    public function render()
    {
        $canCreate = Game::ongoing()->where(function ($query) {
            $query->where('user_id_1', auth()->id())->orWhere('user_id_2', auth()->id());
        })->count() === 0;

        return view('livewire.servers.index', [
            'games' => Game::latest()->ongoing()->get(),
            'canCreate' => $canCreate,
        ]);
    }
}
