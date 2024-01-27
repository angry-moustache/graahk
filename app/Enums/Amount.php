<?php

namespace App\Enums;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Contracts\HasLabel;

enum Amount: string implements HasLabel
{
    case FOR_EACH_DUDE_PLAYER = 'for_each_dude_player';
    case FOR_EACH_DUDE_OPPONENT = 'for_each_dude_opponent';
    case FOR_EACH_DUDE = 'for_each_dude';

    case FOR_EACH_ENERGY_PLAYER = 'for_each_energy_player';
    case FOR_EACH_ENERGY_OPPONENT = 'for_each_energy_opponent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FOR_EACH_DUDE_PLAYER => 'For each dude you control',
            self::FOR_EACH_DUDE_OPPONENT => 'For each dude your opponent controls',
            self::FOR_EACH_DUDE => 'For each dude on the field',

            self::FOR_EACH_ENERGY_PLAYER => 'For each energy you control',
            self::FOR_EACH_ENERGY_OPPONENT => 'For each energy your opponent controls',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::FOR_EACH_DUDE_PLAYER => 'for each dude you control',
            self::FOR_EACH_DUDE_OPPONENT => 'for each dude your opponent controls',
            self::FOR_EACH_DUDE => 'for each dude on the field',

            self::FOR_EACH_ENERGY_PLAYER => 'for each energy you control',
            self::FOR_EACH_ENERGY_OPPONENT => 'for each energy your opponent controls',
        };
    }

    public static function fields(): array
    {
        return [
            Grid::make(3)->schema([
                TextInput::make('amount')
                    ->helperText('Put "X" to make the amount reactive')
                    ->reactive()
                    ->required(),

                Select::make('amount_special')
                    ->hidden(fn (Get $get) => $get('amount') !== 'X')
                    ->options(static::class)
                    ->required(),

                TextInput::make('amount_multiplier')
                    ->hidden(fn (Get $get) => $get('amount') !== 'X')
                    ->reactive()
                    ->required(),
            ]),
        ];
    }
}
