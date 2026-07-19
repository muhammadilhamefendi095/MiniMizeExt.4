<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function store(Request $request, Artwork $artwork)
    {
        abort_unless($artwork->isAuctionOpen(), 422, 'Lelang untuk karya ini sudah ditutup.');

        $minBid = $artwork->current_price + 1;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:'.$minBid],
        ], [
            'amount.min' => 'Tawaran harus lebih tinggi dari harga saat ini (minimal Rp '.number_format($minBid, 0, ',', '.').').',
        ]);

        $artwork->bids()->create([
            'buyer_id' => $request->user()->id,
            'amount' => $request->amount,
        ]);

        $artwork->update(['current_price' => $request->amount]);

        return back()->with('status', 'Tawaran berhasil dipasang!');
    }
}
