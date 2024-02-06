<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;

enum Keyword: string implements HasLabel
{
    use HasList;

    case PROTECT = 'protect';
    case RUSH = 'rush';
    case GHOSTLY = 'ghostly';
    case SCENERY = 'scenery';
    case TIRELESS = 'tireless';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Speedy',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::TIRELESS => 'Tireless',
        };
    }

    public function toText(array $parameters = []): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Speedy',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::TIRELESS => 'Tireless',
        };
    }

    public function description(): ?string
    {
        return match ($this) {
            self::PROTECT => 'This dude must be attacked first, if able',
            self::RUSH => 'Can attack in the same turn this dude was played',
            self::GHOSTLY => 'This dude cannot be directly targeted by abilities',
            self::SCENERY => 'This dude cannot attack or deal damage',
            self::TIRELESS => 'This dude does not die when its power reaches 0',
        };
    }
}
