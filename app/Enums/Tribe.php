<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Tribe: string implements HasLabel
{
    case HUMAN = 'human';
    case SPOIDER = 'spoider';
    case CTHULHIAN = 'cthulhian';
    case GOD = 'god';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HUMAN => 'Human',
            self::SPOIDER => 'Spoider',
            self::CTHULHIAN => 'Cthulhian',
            self::GOD => 'God',
        };
    }

    public function getText(): ?string
    {
        return match ($this) {
            self::HUMAN => 'Human',
            self::SPOIDER => 'Spoider',
            self::CTHULHIAN => 'Cthulhian',
            self::GOD => 'God',
        };
    }
}
