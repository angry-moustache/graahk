<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CardType: string implements HasLabel
{
    case DUDE = 'dude';
    case TOKEN = 'token';
    case ARTIFACT = 'artifact';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DUDE => 'Dude',
            self::TOKEN => 'Token',
            self::ARTIFACT => 'Artifact',
        };
    }
}
