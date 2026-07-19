<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id', 'exhibition_id', 'title', 'description', 'medium', 'size',
        'starting_price', 'current_price', 'image_path', 'status',
        'is_auction', 'auction_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_auction' => 'boolean',
            'auction_ends_at' => 'datetime',
            'starting_price' => 'decimal:2',
            'current_price' => 'decimal:2',
        ];
    }

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class)->orderByDesc('amount');
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function isAuctionOpen(): bool
    {
        if (! $this->is_auction) {
            return false;
        }

        return $this->status === 'approved'
            && (is_null($this->auction_ends_at) || $this->auction_ends_at->isFuture());
    }
}
