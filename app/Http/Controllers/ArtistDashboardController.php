<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtistDashboardController extends Controller
{
    public function index(Request $request)
    {
        $artworks = $request->user()->artworks()->with('exhibition')->latest()->get();

        $openExhibitions = Exhibition::open()->get();

        return view('dashboard.artist', compact('artworks', 'openExhibitions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'medium' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:100'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'is_auction' => ['nullable', 'boolean'],
            'auction_ends_at' => ['nullable', 'date', 'after:now'],
            'exhibition_id' => ['required', 'exists:exhibitions,id'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $exhibition = Exhibition::findOrFail($data['exhibition_id']);
        abort_unless($exhibition->isOpen(), 422, 'Pameran ini sedang tidak menerima pendaftaran karya baru.');

        // Simpan gambar ke disk default (MinIO kalau sudah dikonfigurasi, atau 'public' kalau belum)
        $path = $request->file('image')->store('artworks', config('filesystems.default'));

        $artwork = $request->user()->artworks()->create([
            ...$data,
            'is_auction' => $request->boolean('is_auction'),
            'image_path' => $path,
            'current_price' => $data['starting_price'],
            'status' => 'pending',
        ]);

        AuditLog::record('artwork.submitted', $artwork, [
            'title' => $artwork->title,
            'exhibition' => $exhibition->title,
        ]);

        return back()->with('status', 'Karya berhasil diunggah dan menunggu verifikasi admin.');
    }

    public function destroy(\App\Models\Artwork $artwork)
    {
        abort_unless($artwork->artist_id === auth()->id(), 403); // ini diganti sama yang bawah mungkin? tapi valid ji sih
        $this->authorize('delete', $artwork);

        if ($artwork->image_path) {
            Storage::disk(config('filesystems.default'))->delete($artwork->image_path);
        }

        AuditLog::record('artwork.deleted_by_artist', null, [
            'title' => $artwork->title,
            'artwork_id' => $artwork->id,
        ]);

        $artwork->delete();

        return back()->with('status', 'Karya dihapus.');
    }
}
