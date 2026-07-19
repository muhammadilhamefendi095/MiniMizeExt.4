<x-app-layout>
    <main class="catalog">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:60px;">
            <div class="card-img-container" style="height:520px;">
                @if ($artwork->image_path)
                    <img src="{{ Storage::url($artwork->image_path) }}" alt="{{ $artwork->title }}" class="card-img">
                @endif
            </div>

            <div>
                <h1 style="font-size:2.2rem; font-weight:900; text-transform:uppercase; margin-bottom:10px;">{{ $artwork->title }}</h1>
                <a href="{{ route('artists.show', $artwork->artist) }}" style="color:#FFF200; text-decoration:none; font-size:0.9rem;">
                    Oleh: {{ $artwork->artist->name }}
                </a>

                <p style="color:#AAA; margin:25px 0; line-height:1.7;">{{ $artwork->description }}</p>

                <div style="display:flex; gap:30px; color:#777; font-size:0.85rem; margin-bottom:25px;">
                    @if ($artwork->medium)<span><strong style="color:#FFF;">Medium:</strong> {{ $artwork->medium }}</span>@endif
                    @if ($artwork->size)<span><strong style="color:#FFF;">Ukuran:</strong> {{ $artwork->size }}</span>@endif
                </div>

                @if ($artwork->is_auction && $artwork->auction_ends_at && $artwork->isAuctionOpen())
                    <p style="font-size:0.85rem; color:#888; margin-bottom:15px;">
                        Sisa Waktu:
                        <span data-countdown="{{ $artwork->auction_ends_at->getTimestampMs() }}" style="color:#FFF200; font-weight:bold; font-family:monospace; font-size:1.1rem;"></span>
                    </p>
                @endif

                <div class="price-box" style="margin-bottom:30px;">
                    <span class="price-label">{{ $artwork->is_auction ? 'Current Bid' : 'Harga' }}</span>
                    <span class="price-val" style="font-size:1.4rem;">Rp {{ number_format($artwork->current_price, 0, ',', '.') }}</span>
                </div>

                @if ($artwork->status === 'sold')
                    <div class="status-flash">Karya ini sudah terjual.</div>

                @elseif ($artwork->is_auction)
                    @if ($artwork->isAuctionOpen())
                        @auth
                            @if (auth()->user()->role === 'buyer')
                                <form method="POST" action="{{ route('bids.store', $artwork) }}" style="display:flex; gap:10px;">
                                    @csrf
                                    <input type="number" name="amount" min="{{ $artwork->current_price + 1 }}"
                                           placeholder="Masukkan tawaran (Rp)"
                                           style="flex:1; padding:16px; background:rgba(5,7,11,0.9); border:1px solid rgba(255,255,255,0.1); color:#FFF;">
                                    <button class="btn-bid" style="width:auto; padding:16px 30px;">Tempatkan Bid</button>
                                </form>
                                @error('amount') <p class="form-error">{{ $message }}</p> @enderror
                            @else
                                <p style="color:#777; font-size:0.85rem;">Hanya akun pembeli yang bisa ikut lelang.</p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-bid">Masuk untuk ikut lelang</a>
                        @endauth
                    @else
                        <div class="status-flash">Lelang untuk karya ini sudah ditutup.</div>

                        @auth
                            @if ($artwork->order && $artwork->order->buyer_id === auth()->id() && $artwork->order->payment_status === 'pending')
                                <div style="margin-top:15px;">
                                    <p style="color:#FFF200; font-weight:bold; margin-bottom:12px;">Selamat, kamu memenangkan lelang ini!</p>
                                    <a href="{{ route('checkout.show', $artwork) }}" class="btn-checkout" style="display:inline-block; padding:16px 30px;">
                                        Bayar Sekarang
                                    </a>
                                </div>
                            @endif
                        @endauth
                    @endif

                    @if ($artwork->bids->isNotEmpty())
                        <div style="margin-top:35px;">
                            <p style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; color:#555; margin-bottom:12px;">Riwayat Tawaran</p>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                @foreach ($artwork->bids->take(5) as $bid)
                                    <div style="display:flex; justify-content:space-between; padding:10px 15px; background:rgba(5,7,11,0.6); font-size:0.85rem;">
                                        <span>{{ $bid->buyer->name }}</span>
                                        <span style="color:#FFF200; font-weight:bold;">Rp {{ number_format($bid->amount, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @else
                    @auth
                        <form method="POST" action="{{ route('cart.add', $artwork) }}">
                            @csrf
                            <button class="btn-bid">Tambah ke Keranjang</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-bid">Masuk untuk membeli</a>
                    @endauth
                @endif
            </div>
        </div>
    </main>
</x-app-layout>
