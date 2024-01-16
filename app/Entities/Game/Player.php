<?php

namespace App\Entities\Game;

use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Collection;

class Player
{
    public int $power = 2000;

    public int $energy = 0;

    public User $user;

    public array $deck;

    public array $hand;

    public array $board;

    public static function make()
    {
        return new static();
    }

    public function toArray(): array
    {
        return [
            'power' => $this->power,
            'energy' => $this->energy,
            'user' => $this->user->toArray(),
            'deck' => $this->deck,
            'hand' => $this->hand,
            'board' => $this->board,
        ];
    }

    public function power(int $amount)
    {
        $this->power = $amount;

        return $this;
    }

    public function energy(int $amount)
    {
        $this->energy = $amount;

        return $this;
    }

    public function user(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function deck(array $deck)
    {
        $this->deck = $deck;

        return $this;
    }

    public function hand(array $hand)
    {
        $this->hand = collect($hand)->map(function (array | int | Card $card) {
            if (is_int($card)) {
                $card = Card::find($card);
            }

            if (is_array($card)) {
                return $card;
            }

            return $card->toJavaScript();
        })->toArray();

        return $this;
    }

    public function board(array $board)
    {
        $this->board = $board;

        return $this;
    }

    public function drawCards(int $amount = 1)
    {
        for ($i = 0; $i < $amount; $i++) {
            $this->drawCard();
        }
    }

    public function drawCard()
    {
        $card = array_shift($this->deck);

        if ($card) {
            $this->hand[] = Card::find($card)->toJavaScript();
        }
    }
}
