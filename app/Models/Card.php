<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\Enums\Effect;
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
        'masked_text',
    ];

    protected $casts = [
        'tribes' => 'array',
        'effects' => 'array',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function getTribes(): Collection
    {
        return Collection::wrap($this->tribes)->map(function (string $tribe) {
            return Tribe::from($tribe)->getText();
        });
    }

    public function getText(): string
    {
        if ($this->masked_text) {
            return $this->masked_text;
        }

        return Collection::wrap($this->effects)->map(function (array $effect) {
            return implode(' ', [
                Trigger::from($effect['trigger'])->toText(),
                Effect::from($effect['effect'])->toText($effect),
            ]) . '. ';
        })->join(' ');
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
}
