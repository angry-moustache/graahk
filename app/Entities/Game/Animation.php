<?php

namespace App\Entities\Game;

use Illuminate\Support\Str;

class Animation
{
    public string $uuid;

    public function __construct(
        public string $name,
        public int $duration = 1000,
    ) {
        $this->uuid = Str::uuid();
    }
}
