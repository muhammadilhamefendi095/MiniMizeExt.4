<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tutup lelang yang sudah lewat waktu / pamerannya berakhir, buat order untuk pemenang
Schedule::command('auctions:close')->hourly();

// Cek order yang lewat batas klaim 3 jam, pindahkan ke penawar berikutnya.
// Jalan lebih sering karena batas waktunya cuma 3 jam, biar tidak terlalu lama menggantung.
Schedule::command('bids:reassign')->everyFifteenMinutes();
