# Website Pameran & Lelang Karya — Laravel

Proyek ini berisi file custom (migration, model, controller, route, view) untuk
website pameran dengan pendaftaran pembeli/artis terpisah, katalog, lelang/bid,
keranjang, checkout dengan Midtrans, halaman daftar pembeli, dan admin panel.

Karena Laravel butuh Composer untuk membuat kerangka dasarnya (folder vendor,
file konfigurasi bawaan, Breeze untuk auth), ikuti urutan di bawah ini persis:
kamu generate skeleton dulu lewat command, baru tempel file-file custom ini di atasnya.

### Table of Contents
- [Persiapan tools](#0-persiapan-tools)
- [Setup project](#1-buat-project-laravel-baru)
- [Konfigurasi env](#5-konfigurasi-environment-env)
- [Generate dan instalasi](#6-generate-key-migrasi-storage-link)
- [MINIO](#10-setup-minio)
---

## 0. Persiapan tools

Install dulu di komputer (kalau belum ada):
- **PHP 8.2+** — cek dengan `php -v`
- **Composer** — https://getcomposer.org/download/
- **Node.js 18+** dan npm — untuk compile CSS/JS (Tailwind via Vite)
- **MySQL** (atau bisa pakai SQLite untuk development, lebih simpel)
- **VS Code** dengan extension: PHP Intelephense, Laravel Blade Snippets

---

## 1. Buat project Laravel baru

Buka terminal di folder tempat kamu ingin menyimpan project, lalu:

```bash
composer create-project laravel/laravel exhibition-app
cd exhibition-app
```

## 2. Install Laravel Breeze (autentikasi + Tailwind bawaan)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

Saat ditanya "Would you like dark mode support?" pilih `No` (opsional, bebas).

## 3. Install package Midtrans untuk pembayaran

```bash
composer require midtrans/midtrans-php
```

## 4. Salin semua file custom dari proyek ini

Salin (copy-paste / drag) folder dan file berikut dari proyek yang saya buatkan
ke dalam folder `exhibition-app` hasil instalasi Laravel tadi — **timpa file yang sudah ada bila diminta**:

```
database/migrations/2024_01_01_000001_add_role_columns_to_users_table.php
database/migrations/2024_01_01_000002_create_exhibitions_table.php
database/migrations/2024_01_01_000003_create_artworks_table.php
database/migrations/2024_01_01_000004_create_bids_table.php
database/migrations/2024_01_01_000005_create_orders_table.php
database/seeders/AdminSeeder.php

app/Models/User.php            (timpa)
app/Models/Exhibition.php
app/Models/Artwork.php
app/Models/Bid.php
app/Models/Order.php

app/Http/Middleware/CheckRole.php
app/Http/Controllers/Auth/BuyerRegisterController.php
app/Http/Controllers/Auth/ArtistRegisterController.php
app/Http/Controllers/ArtworkCatalogController.php
app/Http/Controllers/ArtistProfileController.php
app/Http/Controllers/ArtistDashboardController.php
app/Http/Controllers/BidController.php
app/Http/Controllers/CartController.php
app/Http/Controllers/OrderController.php
app/Http/Controllers/BuyerListController.php
app/Http/Controllers/Admin/AdminController.php

app/Services/MidtransService.php

config/midtrans.php

routes/web.php                 (timpa)

resources/views/components/app-layout.blade.php   (timpa file layout Breeze)
resources/views/welcome.blade.php                 (timpa)
resources/views/auth/register-buyer.blade.php
resources/views/auth/register-artist.blade.php
resources/views/catalog/index.blade.php
resources/views/catalog/show.blade.php
resources/views/artists/show.blade.php
resources/views/cart/index.blade.php
resources/views/cart/checkout.blade.php
resources/views/cart/success.blade.php
resources/views/dashboard/artist.blade.php
resources/views/admin/dashboard.blade.php
resources/views/buyers/index.blade.php
```

Lalu buka `bootstrap/app.php` (sudah ada otomatis dari instalasi Laravel) dan tambahkan
bagian `withMiddleware` seperti dicontohkan di file `bootstrap/app.php.EXAMPLE` yang saya
sertakan — supaya middleware `role:artist`, `role:buyer`, `role:admin` di routes/web.php bisa jalan.

**Hapus route registrasi bawaan Breeze** yang tidak dipakai: buka `routes/auth.php`,
hapus baris yang mengarah ke `RegisteredUserController` (registrasi umum), karena kita
sudah punya registrasi terpisah untuk buyer & artist. Login tetap pakai punya Breeze.

## 5. Konfigurasi environment (.env)

Copy `.env.example` menjadi `.env` (Laravel sudah generate ini). Lalu sesuaikan:

```env
APP_NAME="Pameran & Lelang"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exhibition_app
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

# Daftar gratis di https://dashboard.midtrans.com (mode Sandbox, gratis untuk testing)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

Kalau belum mau setup MySQL, bisa pakai SQLite lebih cepat untuk development:
```env
DB_CONNECTION=sqlite
```
lalu buat file kosong: `touch database/database.sqlite` (hapus baris DB_HOST dkk).

## 6. Generate key, migrasi, storage link

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan storage:link
```

Akun admin default: `admin@pameran.test` / `password123` — **ganti password ini setelah deploy**.

## 7. Install dependency & jalankan

```bash
npm install
npm run build
php artisan serve
```

Buka `http://localhost:8000` — situs sudah bisa dites: daftar sebagai artis, unggah karya,
login sebagai admin untuk approve karya, daftar sebagai pembeli untuk bid/beli.

Untuk masuk sebagai admin, login manual pakai akun seeder di atas lewat halaman `/login`.

---

## 8. Setup Midtrans (sistem pembayaran)

1. Daftar gratis di https://dashboard.midtrans.com
2. Setelah masuk dashboard, pastikan mode **Sandbox** aktif (pojok kanan atas) — ini gratis untuk simulasi pembayaran tanpa uang asli
3. Buka **Settings > Access Keys**, salin `Server Key` dan `Client Key` ke `.env`
4. Buka **Settings > Configuration**, isi **Payment Notification URL** dengan:
   `https://domainmu.com/payment/notification`
   (saat masih development lokal, gunakan tool seperti `ngrok` agar Midtrans bisa mengakses localhost-mu)
5. Untuk kartu simulasi test pembayaran sandbox, cek dokumentasi resmi:
   https://docs.midtrans.com/docs/testing-payment-on-sandbox
6. Kalau sudah siap live (uang asli), daftar akun production di Midtrans (butuh verifikasi bisnis/KTP),
   lalu ganti `MIDTRANS_IS_PRODUCTION=true` dan pakai Server/Client Key versi production, serta ganti
   `src` script Snap di `resources/views/cart/checkout.blade.php` dari `app.sandbox.midtrans.com` ke `app.midtrans.com`.

---

## 9. Deploy ke hosting

### A. Opsi gratis (untuk uji coba / demo)

**Railway.app** atau **Render.com** (tier gratis terbatas, tapi mendukung PHP+MySQL dengan baik):
1. Push project ke GitHub
2. Connect repo di Railway/Render, pilih PHP/Laravel template
3. Tambahkan MySQL database dari addon mereka
4. Set environment variables (isi `.env` kamu) di dashboard mereka
5. Set build command: `composer install && npm install && npm run build`
6. Set start command: `php artisan migrate --force && php artisan serve --host 0.0.0.0 --port $PORT`

Catatan: hosting gratis PHP klasik seperti InfinityFree/000webhost **tidak disarankan**
untuk Laravel karena butuh akses Composer/artisan CLI yang biasanya tidak tersedia di sana.

### B. Opsi berbayar (disarankan untuk yang serius/transaksi nyata)

- **Hostinger (Paket Bisnis, ada khusus Laravel hosting)** — ~Rp50-80rb/bulan, mudah untuk pemula, support panel cPanel
- **DigitalOcean / Vultr VPS** — ~$6/bulan, lebih fleksibel tapi butuh setup server manual (Nginx, PHP-FPM, MySQL)
- **Laravel Forge + DigitalOcean** — ~$12/bulan untuk Forge + $6 VPS, memudahkan deploy otomatis dari GitHub

Langkah umum deploy ke VPS/cPanel:
```bash
git clone <repo-kamu> 
composer install --optimize-autoloader --no-dev
npm install && npm run build
cp .env.example .env   # lalu isi kredensial production
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
```
Arahkan document root domain ke folder `public/` (bukan root project) — ini penting untuk keamanan Laravel.

### Domain
- Beli domain .id/.com (~Rp150-250rb/tahun) di Niagahoster/Rumahweb/Namecheap
- Pasang SSL gratis (Let's Encrypt, biasanya otomatis tersedia di Hostinger/cPanel)

## 10. Setup Minio
- Download dan setup minio dulu. Pastikan access bucket publik agar bisa read & write file. Detailnya bisa ditanyakan langsung.
- Controller yang sudah ada tidak perlu diubah, sudah sesuai standar fleksibel upload.
- Cuma perlu penyesuaian .env. Contohnya sudah ada di .env.example. Perhatikan bucket yang dibuat di lokal harus disesuaikan namanya.

```
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=<nama-bucket-yang-dibuat>
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_ENDPOINT=http://localhost:9000
AWS_URL=http://localhost:9000/<nama-bucket-yang-dibuat>
```
---

## Ringkasan alur bisnis yang sudah dibuatkan

- **Registrasi** — form terpisah `/register/buyer` dan `/register/artist`, role tersimpan di kolom `role`
- **Katalog** — `/catalog`, hanya menampilkan karya `status = approved`
- **Halaman artis** — `/artists/{id}`, publik
- **Submit karya** — artis login ke `/dashboard/artist`, upload karya berstatus `pending`
- **Verifikasi admin** — `/admin`, approve/reject karya
- **Lelang (bid)** — kalau `is_auction = true`, buyer login bisa pasang tawaran di halaman detail karya
- **Keranjang & checkout harga tetap** — kalau `is_auction = false`, buyer bisa masukkan ke keranjang lalu checkout via Midtrans
- **Pembayaran** — Midtrans Snap popup, status otomatis ter-update lewat webhook `/payment/notification`
- **Daftar pembeli** — `/buyers`, menampilkan nama pembeli + karya yang dimenangkan (hanya order berstatus `paid`)

## Yang perlu kamu kembangkan sendiri (belum termasuk)
- Checkout untuk pemenang lelang (saat ini route `checkout.show` sudah ada, tinggal ditambah tombol
  "Bayar sekarang" khusus pemenang di halaman detail karya setelah lelang berakhir)
- Notifikasi email/WhatsApp otomatis (bisa pakai Laravel Notification + Fonnte/WA Business API)
- Cron job untuk otomatis menutup lelang yang sudah lewat `auction_ends_at` (pakai Laravel Scheduler)
- Halaman edit profil, ganti password, upload avatar
