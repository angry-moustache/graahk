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
use Illuminate\Support\Str;

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
        'enter_speed',
    ];

    protected $casts = [
        'type' => CardType::class,
        'tribes' => 'array',
        'effects' => 'array',
        'keywords' => 'array',
        'enter_speed' => 'integer',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function sets()
    {
        return $this->belongsToMany(Set::class);
    }

    public function experience()
    {
        return $this->belongsToMany(User::class, 'experience')
            ->withPivot('experience');
    }

    public function scopeDudes($query)
    {
        return $query->where('type', CardType::DUDE);
    }

    public function scopeArtifacts($query)
    {
        return $query->where('type', CardType::ARTIFACT);
    }

    public function scopeTokens($query)
    {
        return $query->where('type', CardType::TOKEN);
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
            return '<strong>' . Keyword::from($keyword)->toText() . '.</strong>';
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
                $trigger = ', then';
            }

            $text .= trim(implode(' ', [
                $trigger ?? Trigger::tryFrom($effect['trigger'])?->toText($effect),
                Effect::tryFrom($effect['effect'])?->toText($effect),
            ]));

            if ($effect['trigger'] !== ($effects[$i + 1]['trigger'] ?? false)) {
                $text .= '. ';
            }
        }

        return trim(
            collect([$text])
                ->prepend($keywords)
                ->flatten()
                ->join(' ')
        );
    }

    public function toJavaScript(null | User $user = null): array
    {
        $user ??= auth()->user();

        return [
            'id' => $this->id,
            'uuid' => (string) Str::uuid(),
            'name' => $this->name,
            'image' => $this->attachment->path(),
            'cost' => $this->cost,
            'originalCost' => $this->cost,
            'power' => $this->power,
            'originalPower' => $this->power,
            'tribes' => $this->tribes,
            'tribesText' => $this->getTribes()->join(', '),
            'text' => $this->toText(),
            'keywords' => $this->keywords,
            'effects' => $this->effects,
            'type' => $this->type,
            'ready' => false,
            'enterSpeed' => $this->enter_speed,
            'level' => $this->getLevel($user),
        ];
    }

    public function getLevel(null | User $user = null): int
    {
        $user = $user?->id ?? auth()->id();

        $experience = $this->experience->where('id', $user)->first()
            ?->pivot->experience;

        if ($experience >= 3000) return 4;
        elseif ($experience >= 2000) return 3;
        elseif ($experience >= 1000) return 2;
        else return 1;
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
