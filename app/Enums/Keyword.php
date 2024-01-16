<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Keyword: string implements HasLabel
{
    case PROTECT = 'protect';
    case RUSH = 'rush';
    case GHOSTLY = 'ghostly';
    case SCENERY = 'scenery';
    case DRAINED = 'drained';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Rush',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::DRAINED => 'Drained',
        };
    }

    public function toText(array $parameters = []): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect.',
            self::RUSH => 'Rush.',
            self::GHOSTLY => 'Ghostly.',
            self::SCENERY => 'Scenery.',
            self::DRAINED => 'Drained.',
        };
    }
}
