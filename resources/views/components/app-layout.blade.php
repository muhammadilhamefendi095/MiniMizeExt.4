<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MINI MIZE EXT.4 — Art Exhibition & Auction' }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <nav class="navbar entry-fade">
        <div class="logo-container">
            <a href="{{ route('home') }}" style="color:#FFF200; font-weight:900; letter-spacing:1px; text-decoration:none; font-size:1.1rem;">
                MINI MIZE EXT.4
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('catalog.index') }}" class="nav-link-item">Katalog</a></li>
            <li><a href="{{ route('home') }}#merchandise" class="nav-link-item">Merchandise</a></li>
            <li><a href="{{ route('buyers.index') }}" class="nav-link-item">Kolektor</a></li>

            @guest
                <li><a href="{{ route('register.buyer') }}" class="nav-link-item">Daftar Pembeli</a></li>
                <li><a href="{{ route('register.artist') }}" class="nav-btn">Daftar Artis</a></li>
                <li><a href="{{ route('login') }}" class="cart-trigger-btn">Masuk</a></li>
            @endguest

            @auth
                @if (auth()->user()->role === 'artist')
                    <li><a href="{{ route('dashboard.artist') }}" class="nav-link-item">Dashboard Artis</a></li>
                @endif
                @if (auth()->user()->role === 'admin')
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link-item">Admin</a></li>
                    <li><a href="{{ route('admin.merchandise.index') }}" class="nav-link-item">Kelola Merch</a></li>
                @endif
                <li><a href="#" id="cart-btn" class="cart-trigger-btn">Keranjang ({{ count(session('cart', [])) }})</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-link-item" style="background:none;border:none;cursor:pointer;">Keluar</button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>

    @if (session('status'))
        <div style="max-width:1400px;margin:20px auto 0;padding:0 50px;">
            <div class="status-flash">{{ session('status') }}</div>
        </div>
    @endif

    {{ $slot }}

    <div id="cart-sidebar" class="cart-sidebar">
        <div class="cart-sidebar-header">
            <h3>Keranjang Kamu</h3>
            <button id="close-cart" class="close-btn">&times;</button>
        </div>
        <div id="cart-items-container" class="cart-items-container">
            @php
                $cartArtworks = \App\Models\Artwork::whereIn('id', session('cart', []))->get();
            @endphp

            @forelse ($cartArtworks as $item)
                <div style="padding:15px; background-color:#05070B; border:1px solid rgba(255,255,255,0.05); margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h4 style="font-size:0.95rem; font-weight:600;">{{ $item->title }}</h4>
                        <p style="color:#FFF200; font-size:0.85rem; font-weight:bold; margin-top:5px;">
                            Rp {{ number_format($item->starting_price, 0, ',', '.') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('cart.remove', $item) }}">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#FF5555;cursor:pointer;font-size:0.8rem;">Hapus</button>
                    </form>
                </div>
            @empty
                <p class="empty-text">Keranjangmu masih kosong.</p>
            @endforelse
        </div>
        @auth
            @if (count(session('cart', [])) > 0)
                <form method="POST" action="{{ route('checkout.cart') }}">
                    @csrf
                    <button type="submit" class="btn-checkout">Checkout Sekarang</button>
                </form>
            @endif
        @else
            @if (count(session('cart', [])) > 0)
                <a href="{{ route('login') }}" class="btn-checkout">Masuk untuk Checkout</a>
            @endif
        @endauth
    </div>

    <script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
