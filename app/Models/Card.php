<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\Enums\CardType;
use App\Enums\Effect;
use App\Enums\Keyword;
use App\Enums\Tribe;
use App\Enums\Trigger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;

class Card extends Model
{
    protected $fillable = [
        'name',
        'attachment_id',
        'type',
        'cost',
        'power',
        'tribes',
        'effects',
        'keywords',
        'masked_text',
    ];

    protected $casts = [
        'type' => CardType::class,
        'tribes' => 'array',
        'effects' => 'array',
        'keywords' => 'array',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function sets()
    {
        return $this->belongsToMany(Set::class);
    }

    public function scopeDudes($query)
    {
        return $query->where('type', CardType::DUDE);
    }

    public function getTribes(): Collection
    {
        return Collection::wrap($this->tribes)->map(function (string $tribe) {
            return Tribe::from($tribe)->toText();
        });
    }

    public function toText(): string
    {
        $keywords = Collection::wrap($this->keywords)->map(function (string $keyword) {
            return '<strong>' . Keyword::from($keyword)->toText() . '</strong>';
        });

        if ($this->masked_text) {
            return ($keywords->count() ? $keywords->join(' ') . ' ' : '')
                . $this->masked_text;
        }

        $effects = Collection::wrap($this->effects);
        $text = '';

        for ($i = 0; $i < $effects->count(); $i++) {
            $trigger = null;
            $effect = $effects[$i];
            if ($effect['trigger'] === ($effects[$i - 1]['trigger'] ?? false)) {
                $trigger = ', then ';
            }

            $text .= implode(' ', [
                $trigger ?? Trigger::tryFrom($effect['trigger'])?->toText(),
                Effect::tryFrom($effect['effect'])?->toText($effect),
            ]);
        }

        return collect([strlen($text) > 0 ? $text . '.' : ''])
            ->prepend($keywords)
            ->flatten()
            ->join('. ');
    }

    public function toJavaScript(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->attachment->path(),
            'cost' => $this->cost,
            'originalCost' => $this->cost,
            'power' => $this->power,
            'originalPower' => $this->power,
            'tribes' => $this->getTribes()->join(', '),
            'text' => $this->toText(),
            'effects' => $this->effects,
            'ready' => Collection::wrap($this->keywords)->contains(Keyword::RUSH->value),
        ];
    }

    public static function booted()
    {
        static::addGlobalScope('sorted', function ($query) {
            $query->orderBy('cost')->orderBy('name');
        });

        static::addGlobalScope('hasAttachment', function ($query) {
            $query->has('attachment');
        });
    }
}
