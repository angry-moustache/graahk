<?php

namespace App\Livewire\Modal;

use App\Enums\Format;
use App\Models\Deck;

class CreateDeck extends Modal
{
    public function render()
    {
        return view('livewire.modal.create-deck', [
            'formats' => collect([Format::STANDARD, Format::WEEKLY, Format::CHAOS]),
        ]);
    }

    public function createDeck(string $format)
    {
        $format = Format::from($format);

        $deck = Deck::create([
            'name' => "New {$format->name()} Deck",
            'format' => $format->value,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('deck.edit', $deck);
    }
}
