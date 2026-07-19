<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;

class MerchandiseController extends Controller
{
    public function show(Merchandise $merchandise)
    {
        abort_unless($merchandise->is_active, 404);

        return view('merchandise.show', compact('merchandise'));
    }
}
