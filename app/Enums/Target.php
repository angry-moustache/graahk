<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Target: string implements HasLabel
{
    case PLAYER = 'player';
    case OPPONENT = 'opponent';
    case DUDE = 'dude';
    case ALL_PLAYERS = 'all_players';
    case ALL_PLAYER_DUDES = 'all_player_dudes';
    case ALL_OPPONENT_DUDES = 'all_opponent_dudes';
    case ALL_DUDES = 'all_dudes';
    case ALL_OTHER_DUDES = 'all_other_dudes';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLAYER => 'Player',
            self::OPPONENT => 'Opponent',
            self::DUDE => 'Dude',
            self::ALL_PLAYERS => 'All players',
            self::ALL_PLAYER_DUDES => 'All player dudes',
            self::ALL_OPPONENT_DUDES => 'All opponent dudes',
            self::ALL_DUDES => 'All dudes',
            self::ALL_OTHER_DUDES => 'All other dudes',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::PLAYER => 'you',
            self::OPPONENT => 'your opponent',
            self::DUDE => 'dude',
            self::ALL_PLAYERS => 'all players',
            self::ALL_PLAYER_DUDES => 'all dudes you control',
            self::ALL_OPPONENT_DUDES => 'all dudes your opponent controls',
            self::ALL_DUDES => 'all dudes',
            self::ALL_OTHER_DUDES => 'all other dudes',
        };
    }
}
