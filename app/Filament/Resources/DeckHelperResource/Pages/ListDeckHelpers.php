<?php

namespace App\Filament\Resources\DeckHelperResource\Pages;

use App\Filament\Resources\DeckHelperResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeckHelpers extends ListRecords
{
    protected static string $resource = DeckHelperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
