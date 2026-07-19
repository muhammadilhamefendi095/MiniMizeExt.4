<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Merchandise;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Checkout langsung untuk satu merchandise event (bukan karya seni).
     * Selalu buat order baru setiap kali, dan langsung kurangi stok.
     */
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
            // Untuk karya lelang: order hanya dibuat otomatis oleh command `auctions:close`
            // untuk pemenang tawaran tertinggi. Di sini kita cari order yang sudah ada itu.
            $order = Order::where('artwork_id', $artwork->id)
                ->where('buyer_id', $request->user()->id)
                ->where('payment_status', 'pending')
                ->firstOrFail();
        } else {
            // Untuk karya harga tetap: buat order baru kalau belum ada
            $order = Order::firstOrCreate(
                ['artwork_id' => $artwork->id, 'buyer_id' => $request->user()->id, 'payment_status' => 'pending'],
                [
                    'order_code' => 'ORD-'.strtoupper(Str::random(10)),
                    'final_price' => $artwork->starting_price,
                ]
            );
        }

        $snapToken = $midtrans->createSnapToken($order);

        return view('cart.checkout', compact('order', 'snapToken'));
    }

    /**
     * Checkout semua karya harga tetap di dalam keranjang sekaligus.
     * Untuk kesederhanaan, tiap karya tetap dibuatkan Order terpisah,
     * lalu ditotal jadi satu transaksi Midtrans.
     */
    public function checkoutCart(Request $request, MidtransService $midtrans)
    {
        $ids = session('cart', []);
        $artworks = Artwork::whereIn('id', $ids)->approved()->where('is_auction', false)->get();

        abort_if($artworks->isEmpty(), 422, 'Keranjang kosong.');

        $orderCode = 'ORD-'.strtoupper(Str::random(10));
        $total = $artworks->sum('starting_price');

        // Simpan satu order "ringkasan" — item detail tetap dicatat per karya di tabel orders
        $mainOrder = Order::create([
            'order_code' => $orderCode,
            'artwork_id' => $artworks->first()->id, // referensi utama
            'buyer_id' => $request->user()->id,
            'final_price' => $total,
        ]);

        $snapToken = $midtrans->createSnapToken($mainOrder);

        // Kosongkan keranjang sekarang karena karya-karyanya sudah "dikunci" ke dalam order ini
        session()->forget('cart');

        return view('cart.checkout', ['order' => $mainOrder, 'snapToken' => $snapToken, 'artworks' => $artworks]);
    }

    /**
     * Webhook notifikasi dari Midtrans setelah pembayaran selesai/gagal.
     * Daftarkan URL ini di Midtrans Dashboard > Settings > Configuration > Payment Notification URL
     */
    public function notification(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        $notif = new \Midtrans\Notification();

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
