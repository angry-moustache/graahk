<?php

namespace App\Filament\Resources;

use App\Enums\Animation;
use App\Enums\CardType;
use App\Enums\Effect;
use App\Enums\Keyword;
use App\Enums\Tribe;
use App\Enums\Trigger;
use App\Filament\Components\Columns\AttachmentColumn;
use App\Filament\Components\Forms\AttachmentInput;
use App\Filament\Resources\CardResource\Pages;
use App\Models\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Card')->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),

                AttachmentInput::make('attachment_id')
                    ->label('Image')
                    ->required(),

                Grid::make()->schema([
                    Select::make('type')
                        ->options(CardType::class)
                        ->default('dude')
                        ->required(),

                    Select::make('tribes')
                        ->options(Tribe::class)
                        ->multiple(),

                    TextInput::make('cost')
                        ->type('number')
                        ->default(1)
                        ->required(),

                    TextInput::make('power')
                        ->type('number')
                        ->default(100)
                        ->required(),
                ]),

                Select::make('sets')
                    ->relationship('sets', 'name')
                    ->multiple()
                    ->preload(),

                TextInput::make('enter_speed')
                    ->type('number')
                    ->default(500)
                    ->label('Enter speed')
                    ->helperText('The speed at which the card enters the screen, in milliseconds.')
                    ->required(),
            ]),

            Section::make('Effects')->schema([
                Textarea::make('masked_text')
                    ->label('Masked text')
                    ->helperText(fn (null | Card $record) => new HtmlString(collect([
                            'This text will be displayed on the card instead of the auto-generated text.',
                            $record?->toText(),
                        ])->filter()->join('<br><br>')
                    )),

                Select::make('keywords')
                    ->options(Keyword::class)
                    ->multiple(),

                Repeater::make('effects')->schema([
                    Grid::make()->schema([
                        Select::make('trigger')
                            ->options(Trigger::class)
                            ->required(),

                        Select::make('effect')
                            ->options(Effect::class)
                            ->required()
                            ->reactive(),
                    ]),

                    Grid::make()->schema(fn (Get $get) =>[
                        ...Effect::tryFrom($get('effect'))?->schema() ?? [],

                        Select::make('animation')
                            ->options(Animation::class)
                            ->nullable()
                            ->reactive(),

                        Grid::make()->schema([
                            ...Animation::tryFrom($get('animation'))?->schema() ?? [],
                        ]),
                    ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cost')
                    ->sortable(),

                TextColumn::make('power')
                    ->sortable(),

                AttachmentColumn::make('attachment_id')
                    ->label('Image'),

                TextColumn::make('sets')
                    ->getStateUsing(fn (Card $record) => $record->sets->pluck('name')->join(', ')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->filters([
                SelectFilter::make('sets')
                    ->relationship('sets', 'name')
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
