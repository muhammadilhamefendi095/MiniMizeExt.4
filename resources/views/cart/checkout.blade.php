<x-app-layout>
    <section class="section-bordered" style="text-align:center;">
        <div class="auth-box" style="text-align:center;">
            <h1>Selesaikan Pembayaran</h1>
            <p class="subtitle">Order: {{ $order->order_code }}</p>
            <p class="price-val" style="font-size:2rem; margin:20px 0;">
                Rp {{ number_format($order->final_price, 0, ',', '.') }}
            </p>

            <button id="pay-button" class="btn-submit">Bayar Sekarang</button>
        </div>
    </section>

    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    {{-- Kalau sudah production, ganti src di atas menjadi: https://app.midtrans.com/snap/snap.js --}}

    <script type="text/javascript">
        document.getElementById('pay-button').addEventListener('click', function () {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function () {
                    window.location.href = "{{ route('orders.success', $order) }}";
                },
                onPending: function () {
                    window.location.href = "{{ route('orders.success', $order) }}";
                },
                onError: function () {
                    alert('Pembayaran gagal, silakan coba lagi.');
                },
            });
        });
    </script>
</x-app-layout>
