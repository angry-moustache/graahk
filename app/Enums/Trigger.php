<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Trigger: string implements HasLabel
{
    case ENTER_FIELD = 'enter_field';
    case LEAVE_FIELD = 'leave_field';

    case START_TURN = 'start_turn';
    case END_TURN = 'end_turn';
    case GAIN_ENERGY = 'gain_energy';

    case PLAY_DUDE = 'play_dude';
    case PLAYER_PLAY_DUDE = 'player_play_dude';
    case OPPONENT_PLAY_DUDE = 'opponent_play_dude';

    case DEALING_DAMAGE = 'dealing_damage';
    case AFTER_DAMAGE = 'after_damage';
    case ATTACK = 'attack';

    case PLAYER_DUDE_DIES = 'dude_dies';

    case DRAW_CARD = 'draw_card';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENTER_FIELD => 'Enters the field',
            self::LEAVE_FIELD => 'Leaves the field',
            self::START_TURN => 'Start of turn',
            self::END_TURN => 'End of turn',
            self::GAIN_ENERGY => 'Gain energy',
            self::PLAY_DUDE => 'Anyone plays dude',
            self::PLAYER_PLAY_DUDE => 'You play dude',
            self::OPPONENT_PLAY_DUDE => 'Opponent plays dude',
            self::DEALING_DAMAGE => 'Dealing damage',
            self::AFTER_DAMAGE => 'After taking damage',
            self::ATTACK => 'Attacks',
            self::PLAYER_DUDE_DIES => 'Dude you control dies',
            self::DRAW_CARD => 'After drawing a card',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::ENTER_FIELD => 'When this dude enters the field,',
            self::LEAVE_FIELD => 'When this dude dies,',
            self::START_TURN => 'At the start of your turn,',
            self::END_TURN => 'At the end of your turn,',
            self::GAIN_ENERGY => 'When you gain energy,',
            self::PLAY_DUDE => 'Whenever anyone plays a dude,',
            self::PLAYER_PLAY_DUDE => 'When you play a dude,',
            self::OPPONENT_PLAY_DUDE => 'When your opponent plays a dude,',
            self::DEALING_DAMAGE => 'When this dude deals damage,',
            self::AFTER_DAMAGE => 'After this dude survives damage,',
            self::ATTACK => 'When this dude attacks,',
            self::PLAYER_DUDE_DIES => 'When a dude you control dies,',
            self::DRAW_CARD => 'After you draw a card,',
        };
    }
}
