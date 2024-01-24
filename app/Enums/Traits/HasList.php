<?php

namespace App\Enums\Traits;

trait HasList
{
    public static function list()
    {
        return collect(static::cases())
            ->mapWithKeys(fn ($enum) => [$enum->value => $enum->getLabel()])
            ->sort();
    }
}
