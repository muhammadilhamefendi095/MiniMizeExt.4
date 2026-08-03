<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Order;
use Illuminate\Console\Command;

class ReassignExpiredBidWinners extends Command
{
    protected $signature = 'bids:reassign';

    protected $description = 'Cek order pemenang lelang yang sudah lewat batas waktu klaim (3 jam), tawarkan ke penawar tertinggi berikutnya';

    public function handle(): void
    {
        $expiredOrders = Order::query()
            ->whereNotNull('artwork_id')
            ->whereNotNull('claim_deadline')
            ->where('payment_status', 'pending')
            ->where('claim_deadline', '<', now())
            ->with(['artwork.bids', 'buyer'])
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Tidak ada order yang lewat batas waktu klaim saat ini.');

            return;
        }

        foreach ($expiredOrders as $order) {
            $artwork = $order->artwork;

            $order->update(['payment_status' => 'expired']);

            AuditLog::record('auction.claim_expired', $order, [
                'artwork' => $artwork->title,
                'expired_buyer' => $order->buyer->name,
            ]);

            $this->warn("Batas klaim '{$artwork->title}' oleh {$order->buyer->name} sudah lewat, dibatalkan.");

            // Buyer yang sudah pernah ditawari (order apa pun, termasuk yang baru di-expired-kan)
            // untuk karya ini tidak akan ditawari ulang.
            $alreadyOfferedBuyerIds = Order::where('artwork_id', $artwork->id)->pluck('buyer_id');

            $nextBid = $artwork->bids()
                ->whereNotIn('buyer_id', $alreadyOfferedBuyerIds)
                ->first(); // relasi bids() sudah otomatis exclude yang is_cancelled & urut tertinggi

            if (! $nextBid) {
                $this->warn("Tidak ada penawar lain untuk '{$artwork->title}'. Karya tidak terjual.");

                AuditLog::record('auction.no_more_bidders', $artwork, [
                    'artwork' => $artwork->title,
                ]);

                continue;
            }

            $closeCommand = new CloseExpiredAuctions();
            $newOrder = $closeCommand->createOrderForBidder($artwork, $nextBid);

            $this->info("'{$artwork->title}' ditawarkan ke penawar berikutnya: {$newOrder->buyer->name} (Rp ".number_format($nextBid->amount, 0, ',', '.').').');
        }
    }
}
