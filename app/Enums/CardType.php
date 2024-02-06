<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;

enum CardType: string implements HasLabel
{
    use HasList;

    case DUDE = 'dude';
    case TOKEN = 'token';
    case RUSE = 'ruse';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DUDE => 'Dude',
            self::TOKEN => 'Token',
            self::RUSE => 'Ruse',
        };
    }
}
