<?php

namespace App;

use App\Models\Card;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CardCache
{
    const KEY = 'cards';

    public static function build(): Collection
    {
        $cards = Card::all()->map->toJavaScript();

        Cache::forget(self::KEY);
        Cache::rememberForever(self::KEY, fn () => $cards);

        return $cards;
    }

    public static function get(): Collection
    {
        return Cache::get(self::KEY, self::build());
    }

    public static function flush(): void
    {
        Cache::forget(self::KEY);
    }
}
