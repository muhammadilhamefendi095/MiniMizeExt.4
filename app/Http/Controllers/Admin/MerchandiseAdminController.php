<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Merchandise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MerchandiseAdminController extends Controller
{
    public function index()
    {
        $merchandises = Merchandise::latest()->get();

        return view('admin.merchandise.index', compact('merchandises'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('image')->store('merchandise', config('filesystems.default'));

        $merchandise = Merchandise::create([
            ...$data,
            'image_path' => $path,
            'is_active' => true,
        ]);

        AuditLog::record('merchandise.created', $merchandise, ['name' => $merchandise->name]);

        return back()->with('status', 'Merchandise berhasil ditambahkan.');
    }

    public function update(Request $request, Merchandise $merchandise)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $merchandise->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::record('merchandise.updated', $merchandise, ['name' => $merchandise->name]);

        return back()->with('status', 'Merchandise berhasil diperbarui.');
    }

    public function destroy(Merchandise $merchandise)
    {
        if ($merchandise->image_path) {
            Storage::disk(config('filesystems.default'))->delete($merchandise->image_path);
        }

        AuditLog::record('merchandise.deleted', null, ['name' => $merchandise->name]);

        $merchandise->delete();

        return back()->with('status', 'Merchandise dihapus.');
    }
}
