<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Forms\Components\Select;
use Filament\Support\Contracts\HasLabel;

enum TargetCondition: string implements HasLabel
{
    use HasList;

    case OWNER = 'owner';
    case NOT_SELF = 'not_self';
    case TRIBE = 'tribe';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OWNER => 'Owner is',
            self::NOT_SELF => 'Except itself',
            self::TRIBE => 'Has tribe',
        };
    }

    public function toText(array $parameters): ?string
    {
        $tribe = Tribe::tryFrom($parameters['tribe'] ?? null)?->toText();

        return match ($this) {
            self::NOT_SELF => 'except this',
            self::TRIBE => "with tribe <i>{$tribe}</i>",
            self::OWNER => match ($parameters['owner']) {
                Target::PLAYER => 'you control',
                Target::OPPONENT => 'your opponent controls',
            },
        };
    }

    public function schema(): array
    {
        return match ($this) {
            self::OWNER => [
                Select::make('owner')
                    ->options([
                        Target::PLAYER => 'Player',
                        Target::OPPONENT => 'Opponent',
                    ])
                    ->required(),
            ],
            self::TRIBE => [
                Select::make('tribe')
                    ->options(Tribe::class)
                    ->required(),
            ],
            default => [],
        };
    }
}
