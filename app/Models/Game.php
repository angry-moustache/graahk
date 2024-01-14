<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'name',
        'user_id_1',
        'user_id_2',
        'data',
        'finished_at',
    ];

    protected $casts = [
        'data' => 'array',
        'finished_at' => 'datetime',
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    public function scopeOngoing($query)
    {
        return $query->whereNull('finished_at');
    }
}
