<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'avatar_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function avatar()
    {
        return $this->belongsTo(Attachment::class, 'avatar_id');
    }

    public function decks()
    {
        return $this->hasMany(Deck::class);
    }

    public function experience()
    {
        return $this->belongsToMany(Card::class, 'experience')
            ->withPivot('experience');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar?->path() ?? asset('/images/icon.jpg');
    }

    public function gamesPlayed(): int
    {
        return Game::where('user_id_1', $this->id)
            ->orWhere('user_id_2', $this->id)
            ->count();
    }
}
