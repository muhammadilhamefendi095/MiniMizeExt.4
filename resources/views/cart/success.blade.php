<x-app-layout>
    <section class="section-bordered" style="text-align:center;">
        <div class="auth-box" style="text-align:center;">
            <h1>Terima Kasih!</h1>
            <p class="subtitle">
                Order <strong style="color:#FFF;">{{ $order->order_code }}</strong> sedang diproses.
                Status pembayaran saat ini: <strong style="color:#FFF200;">{{ $order->payment_status }}</strong>.
            </p>
            <p style="color:#666; font-size:0.85rem; margin-bottom:30px;">
                Status akan otomatis berubah menjadi "paid" setelah pembayaran dikonfirmasi Midtrans.
            </p>
            <a href="{{ route('catalog.index') }}" class="btn-submit" style="display:inline-block; text-decoration:none;">
                Kembali ke Katalog
            </a>
        </div>
    </section>
</x-app-layout>
