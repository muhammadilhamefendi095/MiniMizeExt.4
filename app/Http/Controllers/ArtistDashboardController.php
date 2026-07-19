<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtistDashboardController extends Controller
{
    public function index(Request $request)
    {
        $artworks = $request->user()->artworks()->with('exhibition')->latest()->get();
        $exhibitions = Exhibition::orderByDesc('start_date')->get();

        return view('dashboard.artist', compact('artworks', 'exhibitions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'medium' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:100'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'is_auction' => ['required', 'boolean'],
            'auction_ends_at' => ['nullable', 'date', 'after:now'],
            'exhibition_id' => ['nullable', 'exists:exhibitions,id'],
            'image' => ['required', 'image', 'max:4096'], // maks 4MB
        ]);

        $path = $request->file('image')->store('artworks', 'public');

        $request->user()->artworks()->create([
            ...$data,
            'image_path' => $path,
            'current_price' => $data['starting_price'],
            'status' => 'pending', // menunggu verifikasi admin
        ]);

        return back()->with('status', 'Karya berhasil diunggah dan menunggu verifikasi admin.');
    }

    public function destroy(Artwork $artwork)
    {
        abort_unless($artwork->artist_id === auth()->id(), 403);

        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
        }

        $artwork->delete();

        return back()->with('status', 'Karya dihapus.');
    }
}
