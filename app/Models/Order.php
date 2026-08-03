<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code', 'artwork_id', 'merchandise_id', 'quantity', 'buyer_id', 'final_price',
        'payment_status', 'payment_method', 'midtrans_transaction_id', 'payment_proof_path',
        'claim_deadline',
    ];

    protected function casts(): array
    {
        return [
            'final_price' => 'decimal:2',
            'claim_deadline' => 'datetime',
        ];
    }

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
