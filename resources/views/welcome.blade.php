<x-app-layout>
    <header class="hero">
        <div class="hero-cover-container">
            <div class="hero-slideshow">
                <div class="slide active" style="background-image: linear-gradient(to top, rgba(5, 7, 11, 0.8) 0%, rgba(5, 7, 11, 0.2) 100%), url('{{ asset('images/hero1.jpg') }}');">
                    <div class="slide-content-overlay">
                        <span class="slide-tag">EXCLUSIVELY IN EXT.4</span>
                    </div>
                </div>
                <div class="slide" style="background-image: linear-gradient(to top, rgba(5, 7, 11, 0.8) 0%, rgba(5, 7, 11, 0.2) 100%), url('{{ asset('images/hero22.jpg') }}');">
                    <div class="slide-content-overlay">
                        <span class="slide-tag">FEATURED LOT</span>
                    </div>
                </div>
                <div class="slide" style="background-image: linear-gradient(to top, rgba(5, 7, 11, 0.8) 0%, rgba(5, 7, 11, 0.2) 100%), url('{{ asset('images/hero3.jpg') }}');">
                    <div class="slide-content-overlay">
                        <span class="slide-tag">LIMITED AUCTION</span>
                    </div>
                </div>
            </div>

            <div class="slider-indicators">
                <span class="indicator-line active" onclick="goToSlide(0)"></span>
                <span class="indicator-line" onclick="goToSlide(1)"></span>
                <span class="indicator-line" onclick="goToSlide(2)"></span>
            </div>
        </div>

        <div class="hero-bottom-actions">
            <a href="#katalog" class="btn-gallery-enter">Masuk Ke Ruang Pameran &rarr;</a>
        </div>
    </header>

    {{-- MERCHANDISE EVENT (dari panitia, bukan artis) --}}
    @if ($merchandises->isNotEmpty())
        <section class="catalog" id="merchandise" style="padding-bottom:60px;">
            <div class="section-header scroll-reveal">
                <h2>Official Merchandise</h2>
                <p>Produk resmi dari panitia MINI MIZE EXT.4 — beli langsung, tanpa lelang</p>
            </div>

            <div class="grid-container">
                @foreach ($merchandises as $item)
                    <div class="card scroll-reveal">
                        <div class="card-img-container">
                            <span class="art-badge" style="background-color:#FFF;">MERCH</span>
                            @if ($item->image_path)
                                <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" class="card-img">
                            @endif
                        </div>
                        <div class="card-content">
                            <h3>{{ $item->name }}</h3>
                            <p class="artist-name">Stok: {{ $item->stock }}</p>

                            <div class="price-box">
                                <span class="price-label">Harga</span>
                                <span class="price-val">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>

                            @auth
                                @if (auth()->user()->role === 'buyer')
                                    <a href="{{ route('merchandise.checkout', $item) }}" class="btn-bid">Beli Sekarang</a>
                                @else
                                    <div style="font-size:0.75rem; color:#666; text-align:center; padding:10px; border:1px dashed #333;">Hanya akun pembeli yang bisa checkout</div>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn-bid">Masuk untuk Beli</a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- KATALOG --}}
    <main class="catalog" id="katalog">
        <div class="section-header scroll-reveal">
            <h2>Katalog Lelang Aktif</h2>
            <p>Karya terbaru yang tayang di pameran ini</p>
        </div>

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
                <p style="color:#666;">Belum ada karya yang tayang saat ini.</p>
            @endforelse
        </div>

        @if ($artworks->isNotEmpty())
            <div style="text-align:center; margin-top:60px;">
                <a href="{{ route('catalog.index') }}" class="nav-btn" style="padding:16px 40px;">Lihat Semua Katalog</a>
            </div>
        @endif
    </main>

    {{-- ARTIS --}}
    <section class="section-dark" id="artis">
        <div class="section-header scroll-reveal">
            <h2>Featured Submitter</h2>
            <p>Seniman yang memamerkan karyanya di EXT.4 ini</p>
        </div>
        <div class="artist-grid">
            @forelse ($artists as $artist)
                <a href="{{ route('artists.show', $artist) }}" class="artist-profile-card scroll-reveal" style="text-decoration:none; color:inherit; display:block;">
                    <div class="avatar-container">
                        @if ($artist->avatar_path)
                            <img src="{{ Storage::url($artist->avatar_path) }}" alt="{{ $artist->name }}" class="avatar-img">
                        @else
                            <div style="width:100%;height:100%;background:#111;"></div>
                        @endif
                    </div>
                    <h4>{{ $artist->name }}</h4>
                    <p>{{ \Illuminate\Support\Str::limit($artist->bio, 90) ?: 'Seniman pameran EXT.4' }}</p>
                </a>
            @empty
                <p style="color:#666;">Belum ada artis yang tayang.</p>
            @endforelse
        </div>
    </section>

    {{-- KOLEKTOR / PEMBELI --}}
    <section class="section-bordered" id="pembeli">
        <div class="section-header scroll-reveal">
            <h2>Daftar Kolektor Terverifikasi</h2>
            <p>Apresiasi untuk penawar tertinggi yang memenangkan lot pameran</p>
        </div>
        <div class="table-wrapper scroll-reveal">
            <table class="minimal-table">
                <thead>
                    <tr>
                        <th>Nama Kolektor</th>
                        <th>Karya Seni</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($winners as $order)
                        <tr>
                            <td>{{ $order->buyer->name }}</td>
                            <td>
                                @if ($order->artwork_id)
                                    "{{ $order->artwork->title }}"
                                @else
                                    "{{ $order->merchandise->name }}" <span style="color:#666; font-size:0.75rem;">(Merch)</span>
                                @endif
                            </td>
                            <td><span class="status-badge">Selesai/Lunas</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="color:#666;">Belum ada transaksi yang selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
