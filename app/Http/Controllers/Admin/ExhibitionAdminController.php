<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Exhibition;
use Illuminate\Http\Request;

class ExhibitionAdminController extends Controller
{
    public function index()
    {
        $exhibitions = Exhibition::withCount('artworks')->latest()->get();

        return view('admin.exhibitions.index', compact('exhibitions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ]);

        $exhibition = Exhibition::create($data);

        AuditLog::record('exhibition.created', $exhibition, ['title' => $exhibition->title]);

        return back()->with('status', 'Pameran baru berhasil dibuat.');
    }

    public function update(Request $request, Exhibition $exhibition)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ]);

        $exhibition->update($data);

        AuditLog::record('exhibition.updated', $exhibition, ['title' => $exhibition->title]);

        return back()->with('status', 'Jadwal pameran diperbarui.');
    }

    public function destroy(Exhibition $exhibition)
    {
        abort_if($exhibition->artworks()->exists(), 422, 'Tidak bisa hapus pameran yang sudah punya karya terdaftar.');

        AuditLog::record('exhibition.deleted', null, ['title' => $exhibition->title]);

        $exhibition->delete();

        return back()->with('status', 'Pameran dihapus.');
    }
}
