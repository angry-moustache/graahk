<?php

namespace App\Livewire\Decks;

use App\CardCache;
use App\Enums;
use App\Enums\CardType;
use App\Enums\Effect;
use App\Enums\Format;
use App\Enums\Keyword;
use App\Enums\Tribe;
use App\Livewire\Traits\CanToast;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Set;
use App\Models\WeeklyPack;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    use CanToast;

    public bool $loading = true;

    public Deck $deck;

    public Collection $deckList;

    public string $name;

    public null | int $mainCardId = null;

    public Collection $filters;
    public Collection $filterOptions;

    public function mount()
    {
        app('site')->title($this->deck->name);

        abort_if($this->deck->user_id !== auth()->id(), 403);

        $this->deckList = $this->deck->list();
        $this->name = $this->deck->name;
        $this->mainCardId = $this->deck->main_card_id;

        $this->filters = collect([
            'sort' => 'cost-asc',
        ]);

        $this->filterOptions = collect([
            'sets' => [],
            'effects' => [],
            'keywords' => [],
            'targets' => [],
            'tribes' => [],
            'triggers' => [],
            'cardTypes' => [],
        ]);
    }

    public function doneLoading()
    {
        $this->loading = false;
    }

    public function render()
    {
        if ($this->loading) {
            return view('livewire.loading');
        }

        $this->filters = $this->filters->reject(fn ($i) => in_array($i, [null, '']));

        // Get the cards that you're allowed to use
        $cards = CardCache::get();
        $format = $this->deck->format;

        if ($format === Format::WEEKLY) {
            $cards = $cards->filter(fn (array $card) => in_array($card['id'], WeeklyPack::current()->cards));
        }

        // Set the allowed filters
        $this->filterOptions = collect([
            'sets' => Set::pluck('name', 'id')->sort(),
            'keywords' => $cards->pluck('keywords')->flatten()->unique()->sort()->mapWithKeys(fn ($item) => [$item => Keyword::from($item)->getLabel()]),
            'tribes' => $cards->pluck('tribes')->flatten()->unique()->sort()->mapWithKeys(fn ($item) => [$item => Tribe::from($item)->getLabel()]),
            'cardTypes' => $cards->pluck('type')->unique()->sort()->mapWithKeys(fn ($item) => [$item => CardType::from($item)->getLabel()]),
        ]);

        $cards = $cards
            ->reject(fn (array $card) => $card['type'] === CardType::TOKEN->value)
            ->sortBy([
                [...explode('-', $this->filters->get('sort'))],
                ['name', 'asc'],
            ])
            ->when($this->filters->get('set'), fn ($collection, $set) => $collection->filter(fn ($card) => in_array($set, $card['sets'])))
            ->when($this->filters->get('keyword'), fn ($collection, $keyword) => $collection->filter(fn ($card) => in_array($keyword, $card['keywords'])))
            ->when($this->filters->get('tribe'), fn ($collection, $tribe) => $collection->filter(fn ($card) => in_array($tribe, $card['tribes'])))
            ->when($this->filters->get('type'), fn ($collection, $type) => $collection->where('type', $type))
            ->when(! is_null($this->filters->get('cost')), fn ($collection) => $collection->where('cost', $this->filters->get('cost')))
            ->when(! is_null($this->filters->get('power')), fn ($collection) => $collection->where('power', $this->filters->get('power')))
            ->when($this->filters->get('search'), function ($collection, $search) {
                return $collection->filter(fn($card) => Str::contains(
                    implode(' ', [$card['name'], $card['text'], $card['tribesText']]),
                    $search,
                    true
                ));
            });

        return view('livewire.decks.edit', [
            'cards' => Card::find($cards->pluck('id')),
            'cardList' => CardCache::get(),
            'cardTypes' => $this->filterOptions->get('cardTypes'),
            'sets' => $this->filterOptions->get('sets'),
            'keywords' => $this->filterOptions->get('keywords'),
            'tribes' => $this->filterOptions->get('tribes'),
            'sorting' => [
                'cost-asc' => 'Cost (low to high)',
                'cost-desc' => 'Cost (high to low)',
                'power-asc' => 'Power (low to high)',
                'power-desc' => 'Power (high to low)',
            ],
        ]);
    }

    public function saveDeck()
    {
        $this->validate([
            'name' => 'required',
        ]);

        $this->deck->update([
            'name' => $this->name,
            'main_card_id' => $this->mainCardId,
            'cards' => $this->deckList->pluck('amount', 'card.id'),
        ]);

        $this->deck->touch();

        $this->toast('Deck has been saved!');
    }

    public function resetFilters()
    {
        $this->filters = collect([
            'sort' => 'cost-asc',
        ]);
    }
}
