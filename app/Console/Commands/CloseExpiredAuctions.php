<?php

namespace App\Console\Commands;

use App\Mail\AuctionWonMail;
use App\Models\Artwork;
use App\Models\AuditLog;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CloseExpiredAuctions extends Command
{
    protected $signature = 'auctions:close';

    protected $description = 'Tutup lelang yang sudah lewat waktu (atau pamerannya berakhir), buat order untuk pemenang dengan batas klaim 3 jam, dan kirim email notifikasi';

    /**
     * Batas waktu pemenang harus menyelesaikan pembayaran sebelum ditawarkan
     * ke penawar tertinggi berikutnya. Diubah di sini kalau perlu.
     */
    public const CLAIM_HOURS = 3;

    public function handle(): void
    {
        $artworks = Artwork::query()
            ->where('is_auction', true)
            ->where('status', 'approved')
            ->whereDoesntHave('order')
            ->with(['bids', 'exhibition'])
            ->get()
            ->filter(function ($artwork) {
                $auctionTimeUp = $artwork->auction_ends_at && $artwork->auction_ends_at->isPast();
                $exhibitionClosed = $artwork->exhibition && ! $artwork->exhibition->isOpen()
                    && $artwork->exhibition->end_at && $artwork->exhibition->end_at->isPast();

                return $auctionTimeUp || $exhibitionClosed;
            });

        foreach ($artworks as $artwork) {
            $winningBid = $artwork->bids()->first(); // exclude cancelled, urut tertinggi

            if (! $winningBid) {
                $this->info("Lelang '{$artwork->title}' ditutup, tidak ada penawar.");

                continue;
            }

            $this->createOrderForBidder($artwork, $winningBid);
        }

        if ($artworks->isEmpty()) {
            $this->info('Tidak ada lelang yang perlu ditutup saat ini.');
        }
    }

    public function createOrderForBidder(Artwork $artwork, $bid): Order
    {
        $order = Order::create([
            'order_code' => 'ORD-'.strtoupper(Str::random(10)),
            'artwork_id' => $artwork->id,
            'buyer_id' => $bid->buyer_id,
            'final_price' => $bid->amount,
            'payment_status' => 'pending',
            'claim_deadline' => now()->addHours(self::CLAIM_HOURS),
        ]);

        $order->load(['artwork.artist', 'buyer']);

        AuditLog::record('auction.winner_assigned', $order, [
            'artwork' => $artwork->title,
            'buyer' => $order->buyer->name,
            'amount' => (string) $bid->amount,
            'claim_deadline' => $order->claim_deadline->toDateTimeString(),
        ]);

        try {
            Mail::to($order->buyer->email)->send(new AuctionWonMail($order));
            $this->info("Lelang '{$artwork->title}' ditutup. Pemenang: {$order->buyer->name}, batas bayar ".self::CLAIM_HOURS." jam, email terkirim.");
        } catch (\Throwable $e) {
            $this->error("Order dibuat, tapi gagal kirim email ke {$order->buyer->email}: {$e->getMessage()}");
        }

        return $order;
    }
}
