<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Dashboard Artis</h2>
        </div>

        <div class="auth-box" style="margin:0 0 60px 0; max-width:100%;">
            <h1 style="font-size:1.2rem;">Unggah Karya Baru</h1>
            <form method="POST" action="{{ route('dashboard.artist.store') }}" enctype="multipart/form-data" style="margin-top:20px;">
                @csrf
                <div class="form-group">
                    <label>Judul Karya</label>
                    <input type="text" name="title" required>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Medium</label>
                        <input type="text" name="medium" placeholder="Cat minyak di atas kanvas">
                    </div>
                    <div class="form-group">
                        <label>Ukuran</label>
                        <input type="text" name="size" placeholder="60x80 cm">
                    </div>
                </div>
                <div class="form-group">
                    <label>Harga Awal (Rp)</label>
                    <input type="number" name="starting_price" required min="0">
                    @error('starting_price') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Pameran</label>
                    <select name="exhibition_id">
                        <option value="">— Tidak terikat pameran —</option>
                        @foreach ($exhibitions as $ex)
                            <option value="{{ $ex->id }}">{{ $ex->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:10px; text-transform:none;">
                        <input type="checkbox" name="is_auction" value="1" checked style="width:auto;">
                        Jual dengan sistem lelang (kalau tidak dicentang = harga tetap)
                    </label>
                </div>
                <div class="form-group">
                    <label>Lelang Berakhir Pada (opsional)</label>
                    <input type="datetime-local" name="auction_ends_at">
                </div>
                <div class="form-group">
                    <label>Foto Karya</label>
                    <input type="file" name="image" accept="image/*" required style="padding:10px;">
                    @error('image') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <button class="btn-submit">Unggah Karya</button>
            </form>
        </div>

        <div class="section-header scroll-reveal">
            <h2>Karya-Karyamu</h2>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse ($artworks as $artwork)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05);">
                    <div>
                        <p style="font-weight:600;">{{ $artwork->title }}</p>
                        <p style="font-size:0.85rem; color:#777; margin-top:4px;">
                            Status:
                            <span style="color: {{ match($artwork->status) { 'pending' => '#FFF200', 'approved' => '#55FF55', 'rejected' => '#FF5555', default => '#777' } }};">
                                {{ $artwork->status }}
                            </span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('dashboard.artist.destroy', $artwork) }}" onsubmit="return confirm('Hapus karya ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#FF5555;cursor:pointer;font-size:0.85rem;">Hapus</button>
                    </form>
                </div>
            @empty
                <p style="color:#666;">Belum ada karya yang diunggah.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
