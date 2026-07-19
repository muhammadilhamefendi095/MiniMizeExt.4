<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Exhibition;
use Illuminate\Http\Request;

class ArtworkCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Artwork::approved()->with(['artist', 'exhibition'])->latest();

        if ($request->filled('exhibition_id')) {
            $query->where('exhibition_id', $request->exhibition_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        $artworks = $query->paginate(12)->withQueryString();
        $exhibitions = Exhibition::orderByDesc('start_date')->get();

        return view('catalog.index', compact('artworks', 'exhibitions'));
    }

    public function show(Artwork $artwork)
    {
        abort_unless(
            $artwork->status === 'approved' || $artwork->status === 'sold',
            404
        );

        $artwork->load(['artist', 'bids.buyer', 'order']);

        return view('catalog.show', compact('artwork'));
    }
}
