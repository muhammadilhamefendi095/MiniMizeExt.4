<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingArtworks = Artwork::with('artist')->where('status', 'pending')->latest()->get();
        $recentOrders = Order::with(['buyer', 'artwork', 'merchandise'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('pendingArtworks', 'recentOrders'));
    }

    public function approveArtwork(Artwork $artwork)
    {
        $artwork->update(['status' => 'approved']);

        return back()->with('status', 'Karya "'.$artwork->title.'" disetujui dan tayang di katalog.');
    }

    public function rejectArtwork(Artwork $artwork)
    {
        $artwork->update(['status' => 'rejected']);

        return back()->with('status', 'Karya "'.$artwork->title.'" ditolak.');
    }
}
