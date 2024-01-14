<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Keyword: string implements HasLabel
{
    case PROTECT = 'protect';
    case RUSH = 'rush';
    case PHANTOM = 'phantom';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Rush',
            self::PHANTOM => 'Phantom',
        };
    }

    public function toText(array $parameters = []): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect.',
            self::RUSH => 'Rush.',
            self::PHANTOM => 'Phantom.',
        };
    }
}
