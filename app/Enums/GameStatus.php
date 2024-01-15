<?php

namespace App\Enums;

enum GameStatus: string
{
    case SETUP = 'setup';
    case TURN_START = 'turn_started';
    case PLAYING = 'playing';
    case FINISHED = 'finished';
}
