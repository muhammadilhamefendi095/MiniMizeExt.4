<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\AuditLog;
use App\Models\Bid;
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

        $bid = $artwork->bids()->create([
            'buyer_id' => $request->user()->id,
            'amount' => $request->amount,
        ]);

        $artwork->update(['current_price' => $request->amount]);

        AuditLog::record('bid.placed', $bid, [
            'artwork' => $artwork->title,
            'amount' => (string) $bid->amount,
        ]);

        return back()->with('status', 'Tawaran berhasil dipasang!');
    }

    /**
     * Batalkan tawaran milik sendiri, selama pameran/lelang masih berlangsung.
     * Harga karya otomatis turun ke tawaran tertinggi berikutnya yang masih aktif,
     * atau kembali ke harga awal kalau tidak ada tawaran lain.
     */
    public function destroy(Request $request, Bid $bid)
    {
        abort_unless($bid->buyer_id === $request->user()->id, 403);

        $artwork = $bid->artwork;

        abort_unless($artwork->isAuctionOpen(), 422, 'Tawaran tidak bisa dibatalkan karena lelang/pameran sudah berakhir.');

        $bid->update(['is_cancelled' => true]);

        $nextHighest = $artwork->bids()->first();

        $artwork->update([
            'current_price' => $nextHighest ? $nextHighest->amount : $artwork->starting_price,
        ]);

        AuditLog::record('bid.cancelled', $bid, [
            'artwork' => $artwork->title,
            'amount' => (string) $bid->amount,
        ]);

        return back()->with('status', 'Tawaranmu berhasil dibatalkan.');
    }
}
