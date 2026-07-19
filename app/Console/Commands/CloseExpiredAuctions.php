<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CloseExpiredAuctions extends Command
{
    protected $signature = 'auctions:close';

    protected $description = 'Tutup lelang yang sudah lewat waktu, lalu buat order pending untuk penawar tertinggi';

    public function handle(): void
    {
        $artworks = Artwork::query()
            ->where('is_auction', true)
            ->where('status', 'approved')
            ->whereNotNull('auction_ends_at')
            ->where('auction_ends_at', '<=', now())
            ->whereDoesntHave('order') // belum pernah dibuatkan order
            ->with('bids')
            ->get();

        foreach ($artworks as $artwork) {
            $winningBid = $artwork->bids()->orderByDesc('amount')->first();

            if (! $winningBid) {
                // Tidak ada yang menawar, biarkan saja statusnya approved (dianggap tidak laku)
                continue;
            }

            Order::create([
                'order_code' => 'ORD-'.strtoupper(Str::random(10)),
                'artwork_id' => $artwork->id,
                'buyer_id' => $winningBid->buyer_id,
                'final_price' => $winningBid->amount,
                'payment_status' => 'pending',
            ]);

            $this->info("Lelang '{$artwork->title}' ditutup. Pemenang: {$winningBid->buyer->name}, order dibuat untuk dibayar.");

            // TODO: kirim notifikasi email/WhatsApp ke pemenang di sini
        }

        if ($artworks->isEmpty()) {
            $this->info('Tidak ada lelang yang perlu ditutup saat ini.');
        }
    }
}
