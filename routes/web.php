<?php

use App\Livewire\Storefront\CartRecovery;
use App\Livewire\Storefront\AboutPage;
use App\Livewire\Storefront\AccountPage;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\ContactPage;
use App\Livewire\Storefront\Homepage;
use App\Livewire\Storefront\LoginPage;
use App\Livewire\Storefront\OrderDetail;
use App\Livewire\Storefront\OrderHistory;
use App\Livewire\Storefront\ProductCatalog;
use App\Livewire\Storefront\ProductDetail;
use App\Livewire\Storefront\RegisterPage;
use App\Livewire\Storefront\ThankYou;
use App\Livewire\Storefront\WishlistPage;
use App\Livewire\Storefront\OrderTracking;
use App\Livewire\Storefront\CmsPageShow;
use App\Livewire\Storefront\AddressBook;
use App\Livewire\Storefront\CollectionDetail;
use App\Livewire\Storefront\CollectionPage;
use App\Livewire\Storefront\CreateReturnRequest;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────
//  Storefront — full-page Livewire components
// ─────────────────────────────────────────────────────────────────

Route::get('/', Homepage::class)->name('home');
Route::get('/catalog', ProductCatalog::class)->name('catalog');
Route::get('/products/{slug}', ProductDetail::class)->name('products.show');

// Search re-uses the catalog component (ProductCatalog has #[Url(as:'q')] searchQuery)
Route::get('/search', ProductCatalog::class)->name('search');

// Cart
Route::get('/cart', CartPage::class)->name('cart.index');

// Checkout (Livewire component handles placeOrder internally)
Route::get('/checkout', Checkout::class)->name('checkout.show');
Route::get('/checkout/thank-you/{order}', ThankYou::class)->name('checkout.thankyou');

// Static pages
Route::get('/page/{slug}', CmsPageShow::class)->name('page.show');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/contact', ContactPage::class)->name('contact');

// Order tracking
Route::get('/track-order', OrderTracking::class)->name('track.order');

// Collections
Route::get('/collections', CollectionPage::class)->name('collections.index');
Route::get('/collections/{slug}', CollectionDetail::class)->name('collections.show');

// ─────────────────────────────────────────────────────────────────
//  Authentication — Livewire components + OTP AJAX endpoints
// ─────────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/register/verify', [App\Http\Controllers\Auth\OtpController::class, 'showRegistrationVerify'])->name('register.verify');

    // OTP AJAX endpoints (not Livewire — external SMS/verify flows)
    Route::post('/otp/login/send', [App\Http\Controllers\Auth\OtpController::class, 'sendLoginOtp'])->name('otp.login.send');
    Route::post('/otp/login/verify', [App\Http\Controllers\Auth\OtpController::class, 'verifyLoginOtp'])->name('otp.login.verify');
    Route::post('/register/otp/send', [App\Http\Controllers\Auth\OtpController::class, 'sendRegistrationOtp'])->name('register.otp.send');
    Route::post('/register/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verifyRegistrationOtp'])->name('register.otp.verify');
});

// ─────────────────────────────────────────────────────────────────
//  Customer account — Livewire + controller endpoints
// ─────────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    // Account profile (Livewire)
    Route::get('/account', AccountPage::class)->name('account');

    // Order history & detail (Livewire)
    Route::get('/orders', OrderHistory::class)->name('customer.orders.index');
    Route::get('/orders/{id}', OrderDetail::class)->name('customer.orders.show');

    // Addresses — Livewire component handles all CRUD via Livewire methods
    Route::get('/account/addresses', AddressBook::class)->name('account.addresses');

    // Wishlist (Livewire page)
    Route::get('/wishlist', WishlistPage::class)->name('wishlist');

    // Returns
    Route::get('/orders/{orderId}/return', CreateReturnRequest::class)->name('customer.orders.return');

    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

// ─────────────────────────────────────────────────────────────────
//  Merchant (SaaS) — onboarding + dashboard
// ─────────────────────────────────────────────────────────────────

Route::prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/onboard', \App\Livewire\Merchant\Onboarding::class)->middleware('auth')->name('onboard');
    Route::get('/dashboard', \App\Livewire\Merchant\Dashboard::class)->middleware('auth')->name('dashboard');
    Route::get('/upgrade', function () {
        return view('merchant.upgrade');
    })->middleware('auth')->name('upgrade');
});

// ─────────────────────────────────────────────────────────────────
//  Abandoned cart recovery (public — no auth needed)
// ─────────────────────────────────────────────────────────────────

Route::get('/cart/recover/{token}', CartRecovery::class)->name('cart.recover');

// ─────────────────────────────────────────────────────────────────
//  Admin (Blade — unchanged)
// ─────────────────────────────────────────────────────────────────

require __DIR__ . '/admin.php';
