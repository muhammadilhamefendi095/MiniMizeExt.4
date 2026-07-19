<x-app-layout>
    <main class="catalog">
        <div class="section-header scroll-reveal">
            <h2>Katalog Pameran</h2>
            <p>Semua karya yang sedang tayang</p>
        </div>

        <form method="GET" style="display:flex; gap:15px; margin-bottom:50px; flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul karya..."
                   style="flex:1; min-width:200px; padding:14px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
            <select name="exhibition_id" style="padding:14px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                <option value="">Semua pameran</option>
                @foreach ($exhibitions as $ex)
                    <option value="{{ $ex->id }}" @selected(request('exhibition_id') == $ex->id)>{{ $ex->title }}</option>
                @endforeach
            </select>
            <button class="nav-btn" style="padding:14px 30px;">Filter</button>
        </form>

        <div class="grid-container">
            @forelse ($artworks as $art)
                <div class="card scroll-reveal">
                    <div class="card-img-container">
                        <span class="art-badge">LOT {{ str_pad($art->id, 2, '0', STR_PAD_LEFT) }}</span>
                        @if ($art->image_path)
                            <img src="{{ Storage::url($art->image_path) }}" alt="{{ $art->title }}" class="card-img">
                        @endif
                    </div>
                    <div class="card-content">
                        <h3>{{ $art->title }}</h3>
                        <p class="artist-name">Oleh: {{ $art->artist->name }}</p>

                        @if ($art->is_auction && $art->auction_ends_at)
                            <p style="font-size:0.8rem; color:#888; margin-bottom:5px;">
                                Sisa Waktu:
                                <span data-countdown="{{ $art->auction_ends_at->getTimestampMs() }}" style="color:#FFF200; font-weight:bold; font-family:monospace;"></span>
                            </p>
                        @endif

                        <div class="price-box">
                            <span class="price-label">Current Bid:</span>
                            <span class="price-val">Rp {{ number_format($art->current_price, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('catalog.show', $art) }}" class="btn-bid">
                            {{ $art->is_auction ? 'Tempatkan Bid' : 'Lihat & Beli' }}
                        </a>
                    </div>
                </div>
            @empty
                <p style="color:#666;">Belum ada karya yang tayang.</p>
            @endforelse
        </div>

        <div style="margin-top:60px;">{{ $artworks->links() }}</div>
    </main>
</x-app-layout>
