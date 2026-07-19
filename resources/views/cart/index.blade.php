<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Keranjang</h2>
        </div>

        @if ($artworks->isEmpty())
            <p style="color:#777;">Keranjangmu masih kosong. <a href="{{ route('catalog.index') }}" style="color:#FFF200;">Lihat katalog</a>.</p>
        @else
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach ($artworks as $artwork)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05);">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <div style="width:70px;height:70px;background:#111;overflow:hidden;">
                                @if ($artwork->image_path)
                                    <img src="{{ Storage::url($artwork->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                                @endif
                            </div>
                            <div>
                                <p style="font-weight:600;">{{ $artwork->title }}</p>
                                <p style="font-size:0.85rem; color:#777;">{{ $artwork->artist->name }}</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:20px;">
                            <p style="font-weight:600; color:#FFF200;">Rp {{ number_format($artwork->starting_price, 0, ',', '.') }}</p>
                            <form method="POST" action="{{ route('cart.remove', $artwork) }}">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:#FF5555;cursor:pointer;font-size:0.85rem;">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:40px; padding-top:25px; border-top:1px solid rgba(255,255,255,0.1);">
                <p style="font-weight:600; font-size:1.1rem;">Total: Rp {{ number_format($artworks->sum('starting_price'), 0, ',', '.') }}</p>
                @auth
                    <form method="POST" action="{{ route('checkout.cart') }}">
                        @csrf
                        <button class="btn-submit" style="width:auto; padding:16px 40px;">Checkout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-submit" style="width:auto; padding:16px 40px; display:inline-block; text-align:center; text-decoration:none;">Masuk untuk Checkout</a>
                @endauth
            </div>
        @endif
    </section>
</x-app-layout>
