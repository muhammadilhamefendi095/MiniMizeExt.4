<?php

return [
    // Ambil di Midtrans Dashboard > Settings > Access Keys
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    // true = pakai akun sandbox (testing gratis), false = akun produksi (uang asli)
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    'is_sanitized' => true,
    'is_3ds' => true,
];
