<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'cards',
    ];

    protected $casts = [
        'cards' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
