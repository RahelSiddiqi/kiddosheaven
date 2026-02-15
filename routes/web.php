<?php

use App\Http\Controllers\Shop\OrderTrackingController;
use App\Http\Controllers\Shop\SearchController;
use Illuminate\Support\Facades\Route;

// Livewire Storefront Components
use App\Livewire\Storefront\Homepage;
use App\Livewire\Storefront\ProductCatalog;
use App\Livewire\Storefront\ProductDetail;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\ThankYou;
use App\Livewire\Storefront\AboutPage;
use App\Livewire\Storefront\ContactPage;
use App\Livewire\Storefront\LoginPage;
use App\Livewire\Storefront\RegisterPage;
use App\Livewire\Storefront\AccountPage;
use App\Livewire\Storefront\OrderHistory;
use App\Livewire\Storefront\OrderDetail;

// Public routes (Livewire)
Route::get('/', Homepage::class)->name('home');
Route::get('/catalog', ProductCatalog::class)->name('catalog');
Route::get('/products/{slug}', ProductDetail::class)->name('products.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/track-order', [OrderTrackingController::class, 'show'])->name('track.order');

// Cart & Checkout
Route::get('/cart', CartPage::class)->name('cart.index');
Route::get('/checkout', Checkout::class)->name('checkout.show');
Route::get('/checkout/thank-you/{order}', ThankYou::class)->name('checkout.thankyou');

// Static pages (Livewire)
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');
Route::get('/page/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Customer Authentication (Livewire)
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
});

// Customer Account (Livewire)
Route::middleware('auth')->group(function () {
    Route::get('/account', AccountPage::class)->name('account');
    Route::get('/orders', OrderHistory::class)->name('customer.orders.index');
    Route::get('/orders/{id}', OrderDetail::class)->name('customer.orders.show');
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

// Admin (already defined in routes/admin.php)
require __DIR__ . '/admin.php';
