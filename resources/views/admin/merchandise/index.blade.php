<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Kelola Merchandise Event</h2>
            <p>Produk resmi panitia — tidak melalui proses submit artis</p>
        </div>

        <div class="auth-box" style="margin:0 0 60px 0; max-width:100%;">
            <h1 style="font-size:1.2rem;">Tambah Merchandise Baru</h1>
            <form method="POST" action="{{ route('admin.merchandise.store') }}" enctype="multipart/form-data" style="margin-top:20px;">
                @csrf
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="price" required min="0">
                        @error('price') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stock" required min="0">
                        @error('stock') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Foto Produk</label>
                    <input type="file" name="image" accept="image/*" required style="padding:10px;">
                    @error('image') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <button class="btn-submit">Tambah Produk</button>
            </form>
        </div>

        <div class="section-header scroll-reveal">
            <h2>Daftar Merchandise</h2>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse ($merchandises as $item)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <div style="width:60px;height:60px;background:#111;overflow:hidden;">
                            @if ($item->image_path)
                                <img src="{{ Storage::url($item->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @endif
                        </div>
                        <div>
                            <p style="font-weight:600;">{{ $item->name }}</p>
                            <p style="font-size:0.85rem; color:#777;">
                                Rp {{ number_format($item->price, 0, ',', '.') }} — Stok: {{ $item->stock }}
                                — <span style="color: {{ $item->is_active ? '#55FF55' : '#FF5555' }};">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </p>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <form method="POST" action="{{ route('admin.merchandise.update', $item) }}" style="display:flex; gap:8px; align-items:center;">
                            @csrf @method('PATCH')
                            <input type="hidden" name="name" value="{{ $item->name }}">
                            <input type="hidden" name="description" value="{{ $item->description }}">
                            <input type="number" name="stock" value="{{ $item->stock }}" style="width:70px; padding:8px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                            <input type="number" name="price" value="{{ $item->price }}" style="width:110px; padding:8px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                            <label style="display:flex; align-items:center; gap:5px; font-size:0.75rem; text-transform:none;">
                                <input type="checkbox" name="is_active" value="1" @checked($item->is_active) style="width:auto;"> Aktif
                            </label>
                            <button class="nav-btn" style="padding:8px 14px; font-size:0.7rem;">Simpan</button>
                        </form>
                        <form method="POST" action="{{ route('admin.merchandise.destroy', $item) }}" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#FF5555;cursor:pointer;font-size:0.8rem;">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="color:#666;">Belum ada merchandise.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
