<!doctype html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#05070B; color:#fff; padding:30px;">
    <div style="max-width:500px; margin:0 auto; background:#0D1017; padding:30px; border:1px solid #222;">
        <h1 style="color:#FFF200; font-size:20px;">Selamat, {{ $order->buyer->name }}!</h1>

        <p>Kamu memenangkan lelang untuk karya:</p>
        <h2 style="font-size:18px;">"{{ $order->artwork->title }}"</h2>
        <p>oleh {{ $order->artwork->artist->name }}</p>

        <p style="font-size:20px; color:#FFF200; font-weight:bold; margin:20px 0;">
            Rp {{ number_format($order->final_price, 0, ',', '.') }}
        </p>

        @if ($order->claim_deadline)
            <div style="background:rgba(255,85,85,0.1); border:1px solid rgba(255,85,85,0.3); padding:15px 20px; margin:20px 0;">
                <p style="margin:0; color:#FF8888; font-size:14px;">
                    <strong>Penting:</strong> selesaikan pembayaran sebelum
                    <strong>{{ $order->claim_deadline->format('d M Y, H:i') }} WIB</strong>
                    ({{ $order->claim_deadline->diffForHumans() }}).
                    Kalau lewat batas waktu ini, karya akan otomatis ditawarkan ke penawar tertinggi berikutnya.
                </p>
            </div>
        @endif

        <p>Kode pesanan kamu:</p>
        <p style="font-family:monospace; background:#05070B; padding:10px; display:inline-block;">{{ $order->order_code }}</p>

        <p style="margin-top:30px;">
            <a href="{{ route('checkout.show', $order->artwork) }}" style="background:#FFF200; color:#000; padding:14px 30px; text-decoration:none; font-weight:bold; display:inline-block;">
                Bayar Sekarang
            </a>
        </p>

        <p style="margin-top:30px; font-size:12px; color:#666;">
            Email ini dikirim otomatis dari MINI MIZE EXT.4. Jangan balas email ini.
        </p>
    </div>
</body>
</html>
