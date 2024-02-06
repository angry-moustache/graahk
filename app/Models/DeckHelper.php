<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeckHelper extends Model
{
    protected $fillable = [
        'name',
        'description',
        'main_card_id',
    ];

    public function mainCard()
    {
        return $this->belongsTo(Card::class, 'main_card_id');
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class);
    }
}
