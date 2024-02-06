<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Forms\Components\Select;
use Filament\Support\Contracts\HasLabel;

enum Target: string implements HasLabel
{
    use HasList;

    // Players
    case PLAYER = 'player';
    case OPPONENT = 'opponent';
    case ALL_PLAYERS = 'all_players';

    // Targeted
    case DUDE = 'dude';
    case DUDE_PLAYER = 'dude_player';
    case DUDE_OPPONENT = 'dude_opponent';
    case TARGET_ANYTHING = 'target_anything';

    // Area of effect
    case ALL_PLAYER_DUDES = 'all_player_dudes';
    case ALL_OTHER_PLAYER_DUDES = 'all_other_player_dudes';
    case ALL_OPPONENT_DUDES = 'all_opponent_dudes';

    case ALL_DUDES = 'all_dudes';
    case ALL_OTHER_DUDES = 'all_other_dudes';
    case EVERYTHING = 'everything';

    // Tribe only
    case ALL_TRIBE = 'all_tribe';
    case ALL_TRIBE_BUT_SELF = 'all_tribe_but_self';
    case ALL_PLAYER_TRIBE = 'all_player_tribe';
    case ALL_PLAYER_TRIBE_BUT_SELF = 'all_player_tribe_but_self';
    case ALL_OPPONENT_TRIBE = 'all_opponent_tribe';

    // Hand
    case HAND_ALL = 'hand_all';
    case HAND_TARGET = 'hand_target';

    // Others
    case SOURCE = 'source';

    // Self
    case ITSELF = 'itself';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLAYER => 'Player',
            self::OPPONENT => 'Opponent',
            self::DUDE => 'Target dude',
            self::DUDE_PLAYER => 'Target dude (player)',
            self::DUDE_OPPONENT => 'Target dude (opponent)',
            self::TARGET_ANYTHING => 'Target (player or dude)',
            self::ALL_PLAYERS => 'All players',
            self::ALL_PLAYER_DUDES => 'All player dudes',
            self::ALL_OTHER_PLAYER_DUDES => 'All player dudes except itself',
            self::ALL_OPPONENT_DUDES => 'All opponent dudes',
            self::ALL_DUDES => 'All dudes',
            self::ALL_OTHER_DUDES => 'All other dudes',
            self::EVERYTHING => 'Everything',
            self::ITSELF => 'Itself',
            self::ALL_TRIBE => 'All tribe',
            self::ALL_TRIBE_BUT_SELF => 'All tribe but self',
            self::ALL_PLAYER_TRIBE => 'All player tribe',
            self::ALL_PLAYER_TRIBE_BUT_SELF => 'All player tribe but self',
            self::ALL_OPPONENT_TRIBE => 'All opponent tribe',
            self::HAND_ALL => 'Hand (all)',
            self::HAND_TARGET => 'Hand (target)',
            self::SOURCE => 'Source (the card that was targeted)',
        };
    }

    public function toText(array $parameters): ?string
    {
        $tribe = Tribe::tryFrom($parameters['target_tribe'] ?? null)?->toText();

        return match ($this) {
            self::PLAYER => 'you',
            self::OPPONENT => 'your opponent',
            self::DUDE => 'target dude',
            self::DUDE_PLAYER => 'target dude you control',
            self::DUDE_OPPONENT => 'target dude your opponent controls',
            self::TARGET_ANYTHING => 'target dude or player',
            self::ALL_PLAYERS => 'all players',
            self::ALL_PLAYER_DUDES => 'all dudes you control',
            self::ALL_OTHER_PLAYER_DUDES => 'all other dudes you control',
            self::ALL_OPPONENT_DUDES => 'all dudes your opponent controls',
            self::ALL_DUDES => 'all dudes',
            self::ALL_OTHER_DUDES => 'all other dudes',
            self::EVERYTHING => 'everything',
            self::ITSELF => 'this',
            self::ALL_TRIBE => "all <i>{$tribe}</i> dudes",
            self::ALL_TRIBE_BUT_SELF => "all <i>{$tribe}</i> dudes except this",
            self::ALL_PLAYER_TRIBE => "all <i>{$tribe}</i> dudes you control",
            self::ALL_PLAYER_TRIBE_BUT_SELF => "all <i>{$tribe}</i> dudes you control except this",
            self::ALL_OPPONENT_TRIBE => "all <i>{$tribe}</i> dudes your opponent controls",
            self::HAND_ALL => 'all cards in your hand',
            self::HAND_TARGET => 'target card in your hand',
            self::SOURCE => 'that dude',
        };
    }

    public function schema(): array
    {
        return match ($this) {
            self::ALL_TRIBE,
            self::ALL_TRIBE_BUT_SELF,
            self::ALL_PLAYER_TRIBE,
            self::ALL_PLAYER_TRIBE_BUT_SELF,
            self::ALL_OPPONENT_TRIBE => [
                Select::make('target_tribe')
                    ->options(Tribe::class)
                    ->required(),
            ],
            default => [],
        };
    }
}
