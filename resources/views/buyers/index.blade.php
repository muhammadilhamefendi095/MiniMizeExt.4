<x-app-layout>
    <section class="section-bordered">
        <div class="section-header scroll-reveal">
            <h2>Daftar Kolektor Terverifikasi</h2>
            <p>Semua pembeli yang telah menyelesaikan pembayaran</p>
        </div>

        <div class="table-wrapper scroll-reveal">
            <table class="minimal-table">
                <thead>
                    <tr>
                        <th>Nama Kolektor</th>
                        <th>Karya Seni</th>
                        <th>Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->buyer->name }}</td>
                            <td>
                                @if ($order->artwork_id)
                                    <a href="{{ route('catalog.show', $order->artwork) }}" style="color:#FFF200; text-decoration:none;">
                                        "{{ $order->artwork->title }}"
                                    </a>
                                @else
                                    "{{ $order->merchandise->name }}" <span style="color:#666; font-size:0.75rem;">(Merch)</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($order->final_price, 0, ',', '.') }}</td>
                            <td><span class="status-badge">Selesai/Lunas</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="color:#666;">Belum ada transaksi yang selesai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:40px;">{{ $orders->links() }}</div>
    </section>
</x-app-layout>
