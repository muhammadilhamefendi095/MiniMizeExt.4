<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'bio', 'avatar_path',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isArtist(): bool
    {
        return $this->role === 'artist';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class, 'artist_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class, 'buyer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }
}
