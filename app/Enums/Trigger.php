<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Trigger: string implements HasLabel
{
    case ENTER_FIELD = 'enter_field';
    case LEAVE_FIELD = 'leave_field';
    case START_TURN = 'start_turn';
    case END_TURN = 'end_turn';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENTER_FIELD => 'Enters the field',
            self::LEAVE_FIELD => 'Leaves the field',
            self::START_TURN => 'Start of turn',
            self::END_TURN => 'End of turn',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::ENTER_FIELD => 'When this card enters the field,',
            self::LEAVE_FIELD => 'When this card leaves the field,',
            self::START_TURN => 'At the start of your turn,',
            self::END_TURN => 'At the end of your turn,',
        };
    }
}
