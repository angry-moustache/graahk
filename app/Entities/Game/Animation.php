<?php

namespace App\Entities\Game;

class Animation
{
    public function __construct(
        public string $name,
        public int $duration = 1000,
    ) {
        //
    }
}
