<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Buat Snap token untuk satu order, dipakai untuk memunculkan popup pembayaran.
     */
    public function createSnapToken(Order $order): string
    {
        $itemName = $order->merchandise_id
            ? $order->merchandise->name
            : $order->artwork->title;

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_code,
                'gross_amount' => (int) $order->final_price,
            ],
            'customer_details' => [
                'first_name' => $order->buyer->name,
                'email' => $order->buyer->email,
                'phone' => $order->buyer->phone,
            ],
            'item_details' => [[
                'id' => $order->merchandise_id ?: $order->artwork_id,
                'price' => (int) $order->final_price,
                'quantity' => 1,
                'name' => substr($itemName, 0, 50),
            ]],
        ];

        return Snap::getSnapToken($params);
    }
}
