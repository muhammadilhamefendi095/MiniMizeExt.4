<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $ids = session('cart', []);
        $artworks = Artwork::whereIn('id', $ids)->approved()->get();

        return view('cart.index', compact('artworks'));
    }

    public function add(Request $request, Artwork $artwork)
    {
        abort_unless($artwork->status === 'approved' && ! $artwork->is_auction, 422,
            'Karya ini hanya bisa didapatkan lewat lelang, bukan keranjang.');

        $cart = session('cart', []);
        if (! in_array($artwork->id, $cart)) {
            $cart[] = $artwork->id;
        }
        session(['cart' => $cart]);

        return back()->with('status', 'Karya ditambahkan ke keranjang.');
    }

    public function remove(Artwork $artwork)
    {
        $cart = collect(session('cart', []))->reject(fn ($id) => $id == $artwork->id)->values()->all();
        session(['cart' => $cart]);

        return back()->with('status', 'Karya dihapus dari keranjang.');
    }
}
