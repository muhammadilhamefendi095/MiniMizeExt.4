<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan setiap jam untuk menutup lelang yang sudah lewat waktu
// dan otomatis membuat order pending untuk pemenang tawaran tertinggi.
Schedule::command('auctions:close')->hourly();
