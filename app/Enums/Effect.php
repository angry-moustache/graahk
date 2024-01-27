<?php

namespace App\Enums;

use App\Enums\Amount;
use App\Enums\Traits\HasList;
use App\Models\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    // Dude specific effects
    case UNNAMED_ONE = 'unnamed_one';

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
            self::SILENCE => 'Stifle',
            self::READY_DUDE => 'Ready dude',
            self::SHUFFLE_INTO_DECK => 'Shuffle into deck',
            self::SHUFFLE_INTO_OPPONENT_DECK => 'Shuffle into opponent\'s deck',
            self::UNNAMED_ONE => 'Unnamed one effect',
        };
    }

    public function schema(): array
    {
        return match ($this) {
            self::SPAWN_TOKEN => [
                ...Amount::fields(),
                Select::make('target')->options(Target::class)->required(),
                Select::make('token')
                    ->options(fn () => Card::where('type', CardType::TOKEN)->orderBy('name')->pluck('name', 'id'))
                    ->required(),
            ],
            self::SPAWN_DUDE => [
                ...Amount::fields(),
                Select::make('target')->options(Target::class)->required(),
                Select::make('dude')
                    ->options(fn () => Card::where('type', CardType::DUDE)->orderBy('name')->pluck('name', 'id'))
                    ->required(),
            ],
            self::STUN => [
                Select::make('target')->options(Target::class)->required(),
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
                Select::make('target')->options(Target::class)->required(),
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
            default => [
                ...Amount::fields(),
                Select::make('target')->options(Target::class)->required(),
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

        return collect(match ($this) {
            self::DRAW_CARDS => [
                Target::from($parameters['target'])->toText(),
                "draw {$parameters['amount']}",
                Str::plural('card', $parameters['amount']),
                $amountExtra,
            ],
            self::DEAL_DAMAGE => [
                "deal {$parameters['amount']} damage",
                $amountExtra,
                'to',
                Target::from($parameters['target'])->toText(),
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
                Target::from($parameters['target'])->toText(),
                "gain {$parameters['amount']} power",
                $amountExtra,
            ],
            self::HEAL => [
                'heal',
                Target::from($parameters['target'])->toText(),
                "for {$parameters['amount']}",
                $amountExtra,
            ],
            self::GAIN_ENERGY => [
                Target::from($parameters['target'])->toText(),
                Str::plural('gain', $parameters['amount']),
                "{$parameters['amount']} energy",
                $amountExtra,
            ],
            self::DUPLICATE => [
                'duplicate',
                Target::from($parameters['target'])->toText(),
            ],
            self::KILL => [
                'this kills',
                Target::from($parameters['target'])->toText(),
            ],
            self::RESET_HEALTH => [
                'reset',
                Target::from($parameters['target'])->toText(),
                'to their original power',
            ],
            self::STUN => [
                'stun',
                Target::from($parameters['target'])->toText(),
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
                'stifle',
                Target::from($parameters['target'])->toText(),
            ],
            self::UNNAMED_ONE =>[
                'it gains 50 power for each friendly dude or token that died this game',
            ],
            self::BOUNCE => [
                'return',
                Target::from($parameters['target'])->toText(),
                'to its owner\'s hand',
            ],
            self::READY_DUDE => [
                'ready',
                Target::from($parameters['target'])->toText(),
            ],
            self::SHUFFLE_INTO_DECK => [
                'shuffle',
                Target::from($parameters['target'])->toText(),
                'into your deck',
            ],
            self::SHUFFLE_INTO_OPPONENT_DECK => [
                'shuffle',
                Target::from($parameters['target'])->toText(),
                'into your opponent\'s deck',
            ],
        })
            ->filter()
            ->join(' ');
    }
}
