<x-app-layout>
    <section class="section-dark">
        <div style="display:flex; align-items:center; gap:30px; margin-bottom:60px;" class="scroll-reveal">
            <div class="avatar-container" style="width:140px; height:140px; margin:0;">
                @if ($artist->avatar_path)
                    <img src="{{ Storage::url($artist->avatar_path) }}" alt="{{ $artist->name }}" class="avatar-img">
                @else
                    <div style="width:100%;height:100%;background:#111;"></div>
                @endif
            </div>
            <div>
                <h1 style="font-size:2rem; font-weight:900; text-transform:uppercase;">{{ $artist->name }}</h1>
                @if ($artist->bio)
                    <p style="color:#999; margin-top:10px; max-width:600px; line-height:1.6;">{{ $artist->bio }}</p>
                @endif
            </div>
        </div>

        <div class="section-header scroll-reveal">
            <h2>Karya</h2>
        </div>

        <div class="grid-container">
            @forelse ($artworks as $art)
                <a href="{{ route('catalog.show', $art) }}" class="card scroll-reveal" style="text-decoration:none; color:inherit; display:block;">
                    <div class="card-img-container">
                        @if ($art->image_path)
                            <img src="{{ Storage::url($art->image_path) }}" alt="{{ $art->title }}" class="card-img">
                        @endif
                    </div>
                    <div class="card-content">
                        <h3>{{ $art->title }}</h3>
                        <p class="price-val" style="margin-top:10px;">Rp {{ number_format($art->current_price, 0, ',', '.') }}</p>
                    </div>
                </a>
            @empty
                <p style="color:#666;">Belum ada karya yang tayang.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
