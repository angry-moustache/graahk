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
            self::RUSH => 'Rush',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::TIRELESS => 'Tireless',
        };
    }

    public function toText(array $parameters = []): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Rush',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::TIRELESS => 'Tireless',
        };
    }
}
