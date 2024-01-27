<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Contracts\HasLabel;

enum Animation: string implements HasLabel
{
    use HasList;

    case CIRCLE_EXPLOSION = 'circle_explosion';
    case ENERGY_PULSE = 'energy_pulse';
    case CTHULHULHULHU = 'cthulhulhulhu';
    case PROJECTILE = 'projectile';
    case UNNAMED_ONE = 'unnamed_one';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CIRCLE_EXPLOSION => 'Circle explosion',
            self::CTHULHULHULHU => 'Cthulhulhulhu',
            self::ENERGY_PULSE => 'Energy pulse',
            self::PROJECTILE => 'Projectile',
            self::UNNAMED_ONE => 'Blessed arrival (unnamed one effect)',
        };
    }

    public function schema(): array
    {
        return match ($this) {
            self::CIRCLE_EXPLOSION => [
                Select::make('color')->required()->options([
                    'purple' => 'Purple',
                    'red' => 'Red',
                    'yellow' => 'Yellow',
                ]),
            ],
            self::ENERGY_PULSE => [
                Select::make('color')->required()->options([
                    'yellow' => 'Yellow',
                    'red' => 'Red',
                ]),
            ],
            self::PROJECTILE => [
                Select::make('type')->required()->options([
                    'broken_bottle' => 'Broken bottle',
                    'food' => 'Food',
                    'red_laser' => 'Red laser',
                    'ice' => 'Ice block',
                    'web' => 'Webbing',
                ]),

                TextInput::make('size')
                    ->helperText('A normal size is around 200')
                    ->required(),

                Toggle::make('thrown'),
            ],
            default => [],
        };
    }
}
