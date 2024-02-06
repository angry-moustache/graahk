<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeckHelperResource\Pages;
use App\Models\Card;
use App\Models\DeckHelper;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeckHelperResource extends Resource
{
    protected static ?string $model = DeckHelper::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->required(),

                Textarea::make('description')
                    ->rows(5)
                    ->required(),

                Select::make('main_card_id')
                    ->label('Main card')
                    ->options(fn () => Card::pluck('name', 'id')->sort())
                    ->searchable()
                    ->required(),

                Select::make('cards')
                    ->relationship('cards', 'name')
                    ->options(fn () => Card::pluck('name', 'id')->sort())
                    ->multiple()
                    ->searchable()
                    ->rules('min:3'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('main_card_id')
                    ->label('Main card')
                    ->formatStateUsing(fn ($record) => $record->mainCard?->name)
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeckHelpers::route('/'),
            'create' => Pages\CreateDeckHelper::route('/create'),
            'edit' => Pages\EditDeckHelper::route('/{record}/edit'),
        ];
    }
}
