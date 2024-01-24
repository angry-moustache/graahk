<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
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

    // Area of effect
    case ALL_PLAYER_DUDES = 'all_player_dudes';
    case ALL_OTHER_PLAYER_DUDES = 'all_other_player_dudes';
    case ALL_OPPONENT_DUDES = 'all_opponent_dudes';

    case ALL_DUDES = 'all_dudes';
    case ALL_OTHER_DUDES = 'all_other_dudes';
    case EVERYTHING = 'everything';

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
            self::ALL_PLAYERS => 'All players',
            self::ALL_PLAYER_DUDES => 'All player dudes',
            self::ALL_OTHER_PLAYER_DUDES => 'All player dudes except itself',
            self::ALL_OPPONENT_DUDES => 'All opponent dudes',
            self::ALL_DUDES => 'All dudes',
            self::ALL_OTHER_DUDES => 'All other dudes',
            self::EVERYTHING => 'Everything',
            self::ITSELF => 'Itself',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::PLAYER => 'you',
            self::OPPONENT => 'your opponent',
            self::DUDE => 'target dude',
            self::DUDE_PLAYER => 'a dude you control',
            self::DUDE_OPPONENT => 'a dude your opponent controls',
            self::ALL_PLAYERS => 'all players',
            self::ALL_PLAYER_DUDES => 'all dudes you control',
            self::ALL_OTHER_PLAYER_DUDES => 'all other dudes you control',
            self::ALL_OPPONENT_DUDES => 'all dudes your opponent controls',
            self::ALL_DUDES => 'all dudes',
            self::ALL_OTHER_DUDES => 'all other dudes',
            self::EVERYTHING => 'everything',
            self::ITSELF => 'this',
        };
    }
}
