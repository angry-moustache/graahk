<?php

namespace App\Enums;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Effect: string implements HasLabel
{
    case DRAW_CARDS = 'draw_cards';
    case DEAL_DAMAGE = 'deal_damage';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAW_CARDS => 'Draw cards',
            self::DEAL_DAMAGE => 'Deal damage',
        };
    }

    public function schema(): array
    {
        return match ($this) {
            self::DRAW_CARDS, self::DEAL_DAMAGE => [
                TextInput::make('amount')->required(),
                Select::make('target')->options(Target::class)->required()
            ],
        };
    }

    public function toText(array $parameters): ?string
    {
        return implode(' ', match ($this) {
            self::DRAW_CARDS => [
                Target::from($parameters['target'])->toText(),
                "draw {$parameters['amount']}",
                Str::plural('card', $parameters['amount']),
            ],
            self::DEAL_DAMAGE => [
                "deal {$parameters['amount']}",
                'damage',
                'to',
                Target::from($parameters['target'])->toText(),
            ],
        });
    }
}
