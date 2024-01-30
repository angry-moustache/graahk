<?php

namespace App\Livewire\Decks;

use App\Enums;
use App\Livewire\Traits\CanToast;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Set;
use Illuminate\Support\Collection;
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

        $query = Card::query()
            ->where(fn ($q) => $q->artifacts())
            ->orWhere(fn ($q) => $q->dudes());

        $cards = $query
            ->when($this->filters->get('set'), fn ($query, $set) => $query->whereHas('sets', fn ($query) => $query->where('id', $set)))
            ->when($this->filters->get('keyword'), fn ($query, $keyword) => $query->where('keywords', 'LIKE', "%\"{$keyword}\"%"))
            ->when($this->filters->get('tribe'), fn ($query, $tribe) => $query->where('tribes', 'LIKE', "%\"{$tribe}\"%"))
            ->when($this->filters->get('effect'), fn ($query, $effect) => $query->where('effects', 'LIKE', "%\"{$effect}\"%"))
            ->when($this->filters->get('target'), fn ($query, $target) => $query->where('effects', 'LIKE', "%\"{$target}\"%"))
            ->when($this->filters->get('trigger'), fn ($query, $trigger) => $query->where('effects', 'LIKE', "%\"{$trigger}\"%"))
            ->when(! is_null($this->filters->get('cost')), fn ($query) => $query->where('cost', $this->filters->get('cost')))
            ->when(! is_null($this->filters->get('power')), fn ($query) => $query->where('power', $this->filters->get('power')))
            ->when($this->filters->get('search'), function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy(...explode('-', $this->filters->get('sort')))
            ->get();

        return view('livewire.decks.edit', [
            'cards' => $cards,
            'cardList' => $query->get()->mapWithKeys(fn (Card $card) => [$card->id => $card->toJavaScript()]),
            'sets' => Set::latest()->pluck('name', 'id'),
            'effects' => Enums\Effect::list(),
            'keywords' => Enums\Keyword::list(),
            'targets' => Enums\Target::list(),
            'tribes' => Enums\Tribe::list(),
            'triggers' => Enums\Trigger::list(),
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
}
