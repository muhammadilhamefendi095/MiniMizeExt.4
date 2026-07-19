<?php

namespace App\Http\Controllers;

use App\Models\Order;

class BuyerListController extends Controller
{
    public function index()
    {
        // Hanya tampilkan order yang sudah lunas, dengan data secukupnya (privasi terjaga)
        $orders = Order::with(['buyer', 'artwork', 'merchandise'])
            ->where('payment_status', 'paid')
            ->latest()
            ->paginate(20);

        return view('buyers.index', compact('orders'));
    }
}
