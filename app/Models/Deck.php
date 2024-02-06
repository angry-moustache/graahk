<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\Enums\Format;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Deck extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'format',
        'main_card_id',
        'cards',
    ];

    protected $casts = [
        'format' => Format::class,
        'cards' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mainCard()
    {
        return $this->belongsTo(Card::class);
    }

    public function route(): string
    {
        return route('deck.edit', $this);
    }

    public function isLegal(): bool
    {
        return Collection::wrap($this->cards)->sum() === 30;
    }

    public function image(): null | Attachment
    {
        return $this->mainCard?->attachment
            ?? Card::find(array_key_first($this->cards ?? []))?->attachment
            ?? null;
    }

    public function list(): Collection
    {
        return Collection::wrap($this->cards)
            ->filter(fn (int $amount, int $cardId) => Card::find($cardId))
            ->map(fn (int $amount, int $cardId) => [
                'amount' => $amount,
                'card' => Card::find($cardId)->toJavaScript(),
            ])
            ->values();
    }

    public function idList(): Collection
    {
        return Collection::wrap($this->cards)
            ->map(fn (int $amount, int $cardId) => collect()->pad($amount, $cardId))
            ->flatten()
            ->values();
    }
}
