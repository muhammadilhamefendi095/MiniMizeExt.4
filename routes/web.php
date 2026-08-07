<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuditLogAdminController;
use App\Http\Controllers\Admin\ExhibitionAdminController;
use App\Http\Controllers\Admin\MerchandiseAdminController;
use App\Http\Controllers\ArtistDashboardController;
use App\Http\Controllers\ArtistProfileController;
use App\Http\Controllers\ArtworkCatalogController;
use App\Http\Controllers\Auth\ArtistRegisterController;
use App\Http\Controllers\Auth\BuyerRegisterController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\BuyerListController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ============ HALAMAN PUBLIK ============

Route::get('/', function () {
    $artworks = \App\Models\Artwork::approved()->with('artist')->latest()->take(6)->get();
    $artists = \App\Models\User::where('role', 'artist')->whereHas('artworks', fn ($q) => $q->approved())->take(4)->get();
    $winners = \App\Models\Order::with(['buyer', 'artwork', 'merchandise'])->where('payment_status', 'paid')->latest()->take(5)->get();
    $merchandises = \App\Models\Merchandise::active()->latest()->get();

    return view('welcome', compact('artworks', 'artists', 'winners', 'merchandises'));
})->name('home');

Route::get('/catalog', [ArtworkCatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{artwork}', [ArtworkCatalogController::class, 'show'])->name('catalog.show');

Route::get('/artists/{artist}', [ArtistProfileController::class, 'show'])->name('artists.show');

Route::get('/buyers', [BuyerListController::class, 'index'])->name('buyers.index');

// ============ REGISTRASI TERPISAH ============

Route::get('/register/buyer', [BuyerRegisterController::class, 'create'])->name('register.buyer');
Route::post('/register/buyer', [BuyerRegisterController::class, 'store']);

Route::get('/register/artist', [ArtistRegisterController::class, 'create'])->name('register.artist');
Route::post('/register/artist', [ArtistRegisterController::class, 'store']);

// ============ KERANJANG ============

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{artwork}', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/{artwork}', [CartController::class, 'remove'])->name('cart.remove');

// ============ HALAMAN YANG WAJIB LOGIN ============

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/artworks/{artwork}/bid', [BidController::class, 'store'])
        ->middleware('role:buyer')->name('bids.store');
    Route::delete('/bids/{bid}', [BidController::class, 'destroy'])
        ->middleware('role:buyer')->name('bids.destroy');

    Route::get('/checkout/{artwork}', [OrderController::class, 'checkout'])
        ->middleware('role:buyer')->name('checkout.show');
    Route::post('/checkout/cart', [OrderController::class, 'checkoutCart'])
        ->middleware('role:buyer')->name('checkout.cart');
    Route::get('/merchandise/{merchandise}/checkout', [OrderController::class, 'checkoutMerchandise'])
        ->middleware('role:buyer')->name('merchandise.checkout');
    Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');

    Route::get('/dashboard/artist', [ArtistDashboardController::class, 'index'])
        ->middleware('role:artist')->name('dashboard.artist');
    Route::post('/dashboard/artist/artworks', [ArtistDashboardController::class, 'store'])
        ->middleware('role:artist')->name('dashboard.artist.store');
    Route::delete('/dashboard/artist/artworks/{artwork}', [ArtistDashboardController::class, 'destroy'])
        ->middleware('role:artist')->name('dashboard.artist.destroy'); // Palingan di sini tambah policy 

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/artworks/{artwork}/approve', [AdminController::class, 'approveArtwork'])->name('admin.artworks.approve');
        Route::post('/artworks/{artwork}/reject', [AdminController::class, 'rejectArtwork'])->name('admin.artworks.reject');

        Route::get('/merchandise', [MerchandiseAdminController::class, 'index'])->name('admin.merchandise.index');
        Route::post('/merchandise', [MerchandiseAdminController::class, 'store'])->name('admin.merchandise.store');
        Route::patch('/merchandise/{merchandise}', [MerchandiseAdminController::class, 'update'])->name('admin.merchandise.update');
        Route::delete('/merchandise/{merchandise}', [MerchandiseAdminController::class, 'destroy'])->name('admin.merchandise.destroy');

        Route::get('/exhibitions', [ExhibitionAdminController::class, 'index'])->name('admin.exhibitions.index');
        Route::post('/exhibitions', [ExhibitionAdminController::class, 'store'])->name('admin.exhibitions.store');
        Route::patch('/exhibitions/{exhibition}', [ExhibitionAdminController::class, 'update'])->name('admin.exhibitions.update');
        Route::delete('/exhibitions/{exhibition}', [ExhibitionAdminController::class, 'destroy'])->name('admin.exhibitions.destroy');

        Route::get('/audit-logs', [AuditLogAdminController::class, 'index'])->name('admin.audit-logs.index');
    });
});

// ============ WEBHOOK PEMBAYARAN ============

Route::post('/payment/notification', [OrderController::class, 'notification'])->name('payment.notification');

require __DIR__.'/auth.php';
