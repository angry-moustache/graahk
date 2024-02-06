<?php

namespace App\Enums;

enum Format: string
{
    case WEEKLY = 'weekly';
    case STANDARD = 'standard';
    case CHAOS = 'chaos';

    public function name(): ?string
    {
        return match ($this) {
            self::WEEKLY => 'Weekly',
            self::STANDARD => 'Standard',
            self::CHAOS => 'Chaos',
        };
    }

    public function description(): ?string
    {
        return match ($this) {
            self::WEEKLY => 'A set of randomized cards that changes every week on monday morning',
            self::STANDARD => 'Standard format, you get all cards in the Core Set and one pack of your choice [WORK IN PROGRESS]',
            self::CHAOS => 'You\'ll have access to all cards in the game, go nuts!',
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::WEEKLY => 'rpg-clockwork',
            self::STANDARD => 'rpg-sea-serpent',
            self::CHAOS => 'rpg-crown-of-thorns',
        };
    }

    public function style(): ?string
    {
        return match ($this) {
            self::WEEKLY => 'background-color: #FFD700; color: #181818;',
            self::STANDARD => 'background-color: #911BDE; color: #FFF;',
            self::CHAOS => 'background-color: #AC1616; color: #FFF;',
        };
    }
}
