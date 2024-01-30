<?php

namespace App\Enums;

use App\Enums\Amount;
use App\Enums\Traits\HasList;
use App\Models\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum Effect: string implements HasLabel
{
    use HasList;

    case DRAW_CARDS = 'draw_cards';
    case DEAL_DAMAGE = 'deal_damage';
    case SPAWN_TOKEN = 'spawn_token';
    case SPAWN_DUDE = 'spawn_dude';
    case GAIN_ENERGY = 'gain_energy';
    case BUFF_DUDE = 'buff_dude';
    case HEAL = 'heal';
    case DUPLICATE = 'duplicate';
    case KILL = 'kill';
    case RESET_HEALTH = 'reset_health';
    case STUN = 'stun';
    case DRAW_SPECIFIC_COST = 'draw_specific_cost';
    case DRAW_SPECIFIC_TRIBE = 'draw_specific_tribe';
    case DRAW_SPECIFIC_DUDE = 'draw_specific_dude';
    case BOUNCE = 'bounce';
    case SILENCE = 'silence';
    case READY_DUDE = 'ready_dudes';
    case SHUFFLE_INTO_DECK = 'shuffle_into_deck';
    case SHUFFLE_INTO_OPPONENT_DECK = 'shuffle_into_opponents_deck';
    case UNNAMED_ONE = 'unnamed_one';
    case GIVE_KEYWORD = 'give_keyword';

    // Artifact effects
    case GAIN_CHARGE = 'gain_charge';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAW_CARDS => 'Draw cards',
            self::DEAL_DAMAGE => 'Deal damage',
            self::SPAWN_TOKEN => 'Spawn token',
            self::SPAWN_DUDE => 'Spawn dude',
            self::BUFF_DUDE => 'Buff dude',
            self::HEAL => 'Heal',
            self::GAIN_ENERGY => 'Gain energy',
            self::DUPLICATE => 'Duplicate',
            self::KILL => 'Kill',
            self::RESET_HEALTH => 'Reset health',
            self::STUN => 'Stun',
            self::DRAW_SPECIFIC_COST => 'Draw specific card (cost)',
            self::DRAW_SPECIFIC_TRIBE => 'Draw specific card (tribe)',
            self::DRAW_SPECIFIC_DUDE => 'Draw specific card (dude)',
            self::BOUNCE => 'Bounce',
            self::SILENCE => 'Silence',
            self::READY_DUDE => 'Ready dude',
            self::SHUFFLE_INTO_DECK => 'Shuffle into deck',
            self::SHUFFLE_INTO_OPPONENT_DECK => 'Shuffle into opponent\'s deck',
            self::UNNAMED_ONE => 'Unnamed one effect',
            self::GIVE_KEYWORD => 'Give keyword',

            // Artifact effects
            self::GAIN_CHARGE => 'Gain charge',
        };
    }

    public function schema(): array
    {
        $targetField = Grid::make()->schema(fn (Get $get) => [
            Select::make('target')
                ->options(Target::class)
                ->reactive()
                ->required(),

            ...Target::tryFrom($get('target'))?->schema() ?? [],
        ]);

        return match ($this) {
            self::SPAWN_TOKEN => [
                ...Amount::fields(),
                $targetField,
                Select::make('token')
                    ->options(fn () => Card::where('type', CardType::TOKEN)->orderBy('name')->pluck('name', 'id'))
                    ->required(),
            ],
            self::SPAWN_DUDE => [
                ...Amount::fields(),
                $targetField,
                Select::make('dude')
                    ->options(fn () => Card::where('type', CardType::DUDE)->orderBy('name')->pluck('name', 'id'))
                    ->required(),
            ],
            self::STUN => [
                $targetField,
                Select::make('stun_type')->options([
                    'ice' => 'Ice',
                    'web' => 'Web',
                ])->required(),
            ],
            self::DUPLICATE,
            self::KILL,
            self::RESET_HEALTH,
            self::BOUNCE,
            self::SILENCE,
            self::UNNAMED_ONE,
            self::READY_DUDE,
            self::SHUFFLE_INTO_DECK,
            self::SHUFFLE_INTO_OPPONENT_DECK => [
                $targetField,
            ],
            self::DRAW_SPECIFIC_COST => [
                ...Amount::fields(),
                Select::make('operator')
                    ->required()
                    ->options(fn () => [
                        'greater than equal' => 'Greater than or equal',
                        'less than equal' => 'Less than or equal',
                        'equal to' => 'Equal to',
                    ]),
                TextInput::make('cost')->required(),
            ],
            self::DRAW_SPECIFIC_TRIBE => [
                ...Amount::fields(),
                Select::make('tribe')->options(Tribe::class)->required(),
            ],
            self::DRAW_SPECIFIC_DUDE => [
                ...Amount::fields(),
                Select::make('dude')
                    ->options(fn () => Card::where('type', CardType::DUDE)->orderBy('name')->pluck('name', 'id'))
                    ->required(),
            ],
            self::GAIN_CHARGE => [
                ...Amount::fields(),
            ],
            self::GIVE_KEYWORD => [
                $targetField,
                Select::make('keyword')->options(Keyword::class)->required(),
            ],
            default => [
                ...Amount::fields(),
                $targetField,
            ],
        };
    }

    public function toText(array $parameters): ?string
    {
        $amountExtra = null;
        if (isset($parameters['amount'])) {
            if ($parameters['amount'] === 'X') {
                $parameters['amount'] = $parameters['amount_multiplier'];
                $amountExtra = Amount::tryFrom($parameters['amount_special'])?->toText();
            } else {
                $parameters['amount'] = $parameters['amount'];
            }
        }

        $target = Target::tryFrom($parameters['target'] ?? null)?->toText($parameters);

        return collect(match ($this) {
            self::DRAW_CARDS => [
                $target,
                "draw {$parameters['amount']}",
                Str::plural('card', $parameters['amount']),
                $amountExtra,
            ],
            self::DEAL_DAMAGE => [
                "deal {$parameters['amount']} damage",
                'to',
                $target,
                $amountExtra,
            ],
            self::SPAWN_TOKEN => [
                'spawn',
                $parameters['amount'],
                Str::plural(Card::find($parameters['token'])->name, $parameters['amount']),
                $amountExtra,
                Target::from($parameters['target']) === Target::OPPONENT ? 'for your opponent' : '',
            ],
            self::SPAWN_DUDE => [
                'spawn',
                $parameters['amount'],
                Str::plural(Card::find($parameters['dude'])->name, $parameters['amount']),
                $amountExtra,
                Target::from($parameters['target']) === Target::OPPONENT ? 'for your opponent' : '',
            ],
            self::BUFF_DUDE => [
                $target,
                "gain {$parameters['amount']} power",
                $amountExtra,
            ],
            self::HEAL => [
                'heal',
                $target,
                "for {$parameters['amount']}",
                $amountExtra,
            ],
            self::GAIN_ENERGY => [
                $target,
                Str::plural('gain', $parameters['amount']),
                "{$parameters['amount']} energy",
                $amountExtra,
            ],
            self::DUPLICATE => [
                'duplicate',
                $target,
            ],
            self::KILL => [
                'this kills',
                $target,
            ],
            self::RESET_HEALTH => [
                'reset',
                $target,
                'to their original power',
            ],
            self::STUN => [
                'stun',
                $target,
            ],
            self::DRAW_SPECIFIC_COST => [
                "draw {$parameters['amount']}",
                Str::plural('card', $parameters['amount']),
                $amountExtra,
                'that cost',
                match ($parameters['operator']) {
                    'greater than equal' => 'greater than or equal to',
                    'less than equal' => 'less than or equal to',
                    'equal to' => '',
                },
                $parameters['cost'],
            ],
            self::DRAW_SPECIFIC_TRIBE => [
                "draw {$parameters['amount']}",
                Str::plural('card', $parameters['amount']),
                $amountExtra,
                'of the <i>',
                Tribe::from($parameters['tribe'])->toText(),
                '</i> tribe'
            ],
            self::DRAW_SPECIFIC_DUDE => [
                "draw {$parameters['amount']}",
                Str::plural('card', $parameters['amount']),
                $amountExtra,
                'named',
                Card::find($parameters['dude'])->name,
            ],
            self::SILENCE => [
                'silence',
                $target,
            ],
            self::UNNAMED_ONE =>[
                'it gains 50 power for each friendly dude or token that died this game',
            ],
            self::BOUNCE => [
                'return',
                $target,
                'to its owner\'s hand',
            ],
            self::READY_DUDE => [
                'ready',
                $target,
            ],
            self::SHUFFLE_INTO_DECK => [
                'shuffle',
                $target,
                'into your deck',
            ],
            self::SHUFFLE_INTO_OPPONENT_DECK => [
                'shuffle',
                $target,
                'into your opponent\'s deck',
            ],
            self::GAIN_CHARGE => [
                'gain',
                $parameters['amount'],
                Str::plural('charge', $parameters['amount']),
                $amountExtra,
            ],
        })
            ->filter()
            ->join(' ');
    }
}
