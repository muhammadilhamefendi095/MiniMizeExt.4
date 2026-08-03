<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Kelola Pameran</h2>
            <p>Atur kapan pameran dibuka — artis hanya bisa submit karya selama pameran aktif</p>
        </div>

        <div class="auth-box" style="margin:0 0 60px 0; max-width:100%;">
            <h1 style="font-size:1.2rem;">Buat Pameran Baru</h1>
            <form method="POST" action="{{ route('admin.exhibitions.store') }}" style="margin-top:20px;">
                @csrf
                <div class="form-group">
                    <label>Nama Pameran</label>
                    <input type="text" name="title" required placeholder="MINI MIZE EXT.5">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="datetime-local" name="start_at" required>
                        @error('start_at') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Berakhir</label>
                        <input type="datetime-local" name="end_at" required>
                        @error('end_at') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button class="btn-submit">Buat Pameran</button>
            </form>
        </div>

        <div class="section-header scroll-reveal">
            <h2>Daftar Pameran</h2>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse ($exhibitions as $ex)
                <div style="padding:20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px;">
                        <div>
                            <p style="font-weight:600; font-size:1.05rem;">{{ $ex->title }}</p>
                            <p style="font-size:0.8rem; color:#777; margin-top:4px;">{{ $ex->artworks_count }} karya terdaftar</p>
                        </div>
                        <span style="font-size:0.75rem; padding:4px 12px; background: {{ $ex->isOpen() ? 'rgba(85,255,85,0.1)' : 'rgba(255,85,85,0.1)' }}; color: {{ $ex->isOpen() ? '#55FF55' : '#FF5555' }};">
                            {{ $ex->isOpen() ? 'SEDANG BUKA' : 'TUTUP' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.exhibitions.update', $ex) }}" style="display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:10px; align-items:end;">
                        @csrf @method('PATCH')
                        <input type="hidden" name="description" value="{{ $ex->description }}">
                        <div>
                            <label style="display:block; font-size:0.7rem; color:#999; margin-bottom:5px;">Nama</label>
                            <input type="text" name="title" value="{{ $ex->title }}" required style="width:100%; padding:10px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.7rem; color:#999; margin-bottom:5px;">Mulai</label>
                            <input type="datetime-local" name="start_at" value="{{ $ex->start_at?->format('Y-m-d\TH:i') }}" required style="width:100%; padding:10px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                        </div>
                        <div>
                            <label style="display:block; font-size:0.7rem; color:#999; margin-bottom:5px;">Berakhir</label>
                            <input type="datetime-local" name="end_at" value="{{ $ex->end_at?->format('Y-m-d\TH:i') }}" required style="width:100%; padding:10px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                        </div>
                        <button class="nav-btn" style="padding:10px 16px; font-size:0.7rem;">Simpan</button>
                    </form>

                    @if ($ex->artworks_count === 0)
                        <form method="POST" action="{{ route('admin.exhibitions.destroy', $ex) }}" onsubmit="return confirm('Hapus pameran ini?')" style="margin-top:10px;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#FF5555;cursor:pointer;font-size:0.75rem;">Hapus pameran</button>
                        </form>
                    @endif
                </div>
            @empty
                <p style="color:#666;">Belum ada pameran dibuat.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
