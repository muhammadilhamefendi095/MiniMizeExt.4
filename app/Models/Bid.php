<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = ['artwork_id', 'buyer_id', 'amount', 'is_cancelled'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_cancelled' => 'boolean',
        ];
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
    }
}
