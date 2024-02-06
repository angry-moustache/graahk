<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class WeeklyPack extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'attachment_id',
        'cards',
    ];

    protected $casts = [
        'cards' => 'array',
    ];

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    public function list(): Collection
    {
        return Collection::wrap($this->cards)
            ->map(fn (int $cardId) => Card::find($cardId))
            ->filter()
            ->values();
    }

    public static function current(): self
    {
        return self::orderBy('created_at', 'desc')->first();
    }
}
