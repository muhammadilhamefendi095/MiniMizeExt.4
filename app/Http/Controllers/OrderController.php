<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\AuditLog;
use App\Models\Merchandise;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkoutMerchandise(Request $request, Merchandise $merchandise, MidtransService $midtrans)
    {
        abort_unless($merchandise->is_active && $merchandise->stock > 0, 422, 'Merchandise ini tidak tersedia.');

        $order = Order::create([
            'order_code' => 'ORD-'.strtoupper(Str::random(10)),
            'merchandise_id' => $merchandise->id,
            'quantity' => 1,
            'buyer_id' => $request->user()->id,
            'final_price' => $merchandise->price,
        ]);

        $snapToken = $midtrans->createSnapToken($order);

        return view('cart.checkout', compact('order', 'snapToken'));
    }

    /**
     * Checkout langsung untuk satu karya (dipakai baik dari tombol "Beli" harga tetap
     * maupun dari pemenang lelang).
     */
    public function checkout(Request $request, Artwork $artwork, MidtransService $midtrans)
    {
        abort_unless($artwork->status === 'approved', 422, 'Karya ini tidak tersedia.');

        if ($artwork->is_auction) {
            $order = Order::where('artwork_id', $artwork->id)
                ->where('buyer_id', $request->user()->id)
                ->where('payment_status', 'pending')
                ->firstOrFail();
        } else {
            $order = Order::firstOrCreate(
                ['artwork_id' => $artwork->id, 'buyer_id' => $request->user()->id, 'payment_status' => 'pending'],
                [
                    'order_code' => 'ORD-'.strtoupper(Str::random(10)),
                    'final_price' => $artwork->starting_price,
                ]
            );
        }

        // Selalu pakai order_code BARU tiap kali minta Snap token, supaya Midtrans
        // tidak menolak karena order_id lama masih "pending".
        $order->order_code = 'ORD-'.strtoupper(Str::random(10));
        $order->save();

        $snapToken = $midtrans->createSnapToken($order);

        return view('cart.checkout', compact('order', 'snapToken'));
    }

    public function checkoutCart(Request $request, MidtransService $midtrans)
    {
        $ids = session('cart', []);
        $artworks = Artwork::whereIn('id', $ids)->approved()->where('is_auction', false)->get();

        abort_if($artworks->isEmpty(), 422, 'Keranjang kosong.');

        $orderCode = 'ORD-'.strtoupper(Str::random(10));
        $total = $artworks->sum('starting_price');

        $mainOrder = Order::create([
            'order_code' => $orderCode,
            'artwork_id' => $artworks->first()->id,
            'buyer_id' => $request->user()->id,
            'final_price' => $total,
        ]);

        $snapToken = $midtrans->createSnapToken($mainOrder);

        session()->forget('cart');

        return view('cart.checkout', ['order' => $mainOrder, 'snapToken' => $snapToken, 'artworks' => $artworks]);
    }

    /**
     * Webhook notifikasi dari Midtrans setelah pembayaran selesai/gagal.
     * Daftarkan URL ini di Midtrans Dashboard > Settings > Payment > Payment Notification URL
     */
    public function notification(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        $notif = new \Midtrans\Notification();

        // ===== VERIFIKASI SIGNATURE KEY =====
        $expectedSignature = hash('sha512',
            $notif->order_id.
            $notif->status_code.
            $notif->gross_amount.
            config('midtrans.server_key')
        );

        if (! hash_equals($expectedSignature, (string) $notif->signature_key)) {
            \Illuminate\Support\Facades\Log::warning('Midtrans webhook signature tidak valid', [
                'order_id' => $notif->order_id,
                'ip' => $request->ip(),
            ]);

            AuditLog::record('payment.invalid_signature', null, [
                'order_id' => $notif->order_id,
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }
        // ===== AKHIR VERIFIKASI =====

        $order = Order::where('order_code', $notif->order_id)->firstOrFail();

        $status = match ($notif->transaction_status) {
            'capture', 'settlement' => 'paid',
            'pending' => 'pending',
            'deny', 'cancel' => 'cancelled',
            'expire' => 'expired',
            default => $order->payment_status,
        };

        $order->update([
            'payment_status' => $status,
            'payment_method' => $notif->payment_type,
            'midtrans_transaction_id' => $notif->transaction_id,
        ]);

        AuditLog::record('order.'.$status, $order, [
            'order_code' => $order->order_code,
            'amount' => (string) $order->final_price,
            'payment_type' => $notif->payment_type,
        ]);

        if ($status === 'paid') {
            if ($order->artwork_id) {
                $order->artwork()->update(['status' => 'sold']);
            }

            if ($order->merchandise_id) {
                $order->merchandise()->decrement('stock', $order->quantity);
            }
        }

        return response()->json(['message' => 'OK']);
    }

    public function success(Order $order)
    {
        return view('cart.success', compact('order'));
    }
}
