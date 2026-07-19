<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Admin Panel</h2>
        </div>

        <h3 style="margin-bottom:20px; color:#999; text-transform:uppercase; font-size:0.85rem; letter-spacing:1px;">Karya Menunggu Verifikasi</h3>
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:60px;">
            @forelse ($pendingArtworks as $artwork)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <div style="width:60px;height:60px;background:#111;overflow:hidden;">
                            @if ($artwork->image_path)
                                <img src="{{ Storage::url($artwork->image_path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @endif
                        </div>
                        <div>
                            <p style="font-weight:600;">{{ $artwork->title }}</p>
                            <p style="font-size:0.85rem; color:#777;">oleh {{ $artwork->artist->name }} — Rp {{ number_format($artwork->starting_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <form method="POST" action="{{ route('admin.artworks.approve', $artwork) }}">
                            @csrf
                            <button class="nav-btn" style="border-color:#55FF55; color:#55FF55; padding:8px 16px; font-size:0.75rem;">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.artworks.reject', $artwork) }}">
                            @csrf
                            <button class="nav-btn" style="border-color:#FF5555; color:#FF5555; padding:8px 16px; font-size:0.75rem;">Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="color:#666;">Tidak ada karya yang menunggu verifikasi.</p>
            @endforelse
        </div>

        <h3 style="margin-bottom:20px; color:#999; text-transform:uppercase; font-size:0.85rem; letter-spacing:1px;">Transaksi Terbaru</h3>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse ($recentOrders as $order)
                <div style="display:flex; justify-content:space-between; padding:16px 20px; background:rgba(13,16,23,0.85); border:1px solid rgba(255,255,255,0.05); font-size:0.85rem;">
                    <div>
                        <p style="font-weight:600;">
                            {{ $order->order_code }} —
                            {{ $order->artwork_id ? $order->artwork->title : $order->merchandise->name }}
                        </p>
                        <p style="color:#777;">{{ $order->buyer->name }}</p>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-weight:600; color:#FFF200;">Rp {{ number_format($order->final_price, 0, ',', '.') }}</p>
                        <p style="color:#777;">{{ $order->payment_status }}</p>
                    </div>
                </div>
            @empty
                <p style="color:#666;">Belum ada transaksi.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
