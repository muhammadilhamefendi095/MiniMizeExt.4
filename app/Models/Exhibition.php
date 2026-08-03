<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'start_at', 'end_at', 'cover_image_path',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * Pameran dianggap "buka" kalau waktu sekarang ada di antara start_at dan end_at.
     * Kalau start_at/end_at kosong, dianggap belum diatur (tidak buka).
     */
    public function isOpen(): bool
    {
        if (! $this->start_at || ! $this->end_at) {
            return false;
        }

        return now()->between($this->start_at, $this->end_at);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotNull('start_at')
            ->whereNotNull('end_at')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }
}
