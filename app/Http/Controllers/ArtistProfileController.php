<?php

namespace App\Http\Controllers;

use App\Models\User;

class ArtistProfileController extends Controller
{
    public function show(User $artist)
    {
        abort_unless($artist->role === 'artist', 404);

        $artworks = $artist->artworks()->approved()->latest()->get();

        return view('artists.show', compact('artist', 'artworks'));
    }
}
