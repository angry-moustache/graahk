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

        return Collection::wrap($this->effects)->map(function (array $effect) {
            return implode(' ', [
                Trigger::from($effect['trigger'])->toText(),
                Effect::from($effect['effect'])->toText($effect),
            ]) . '. ';
        })
            ->prepend($keywords)
            ->flatten()
            ->join(' ');
    }

    public function toJavascript(): array
    {
        return [
            'id' => $this->id,
            'cost' => $this->cost,
            'power' => $this->power,
            'name' => $this->name,
            'image' => $this->attachment->path(),
        ];
    }

    public static function booted()
    {
        static::addGlobalScope('sorted', function ($query) {
            $query->orderBy('cost')->orderBy('name');
        });

        // static::addGlobalScope('hasAttachment', function ($query) {
        //     $query->has('attachment');
        // });
    }
}
