<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Shop\OrderTrackingController;
use App\Http\Controllers\Shop\SearchController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/catalog', [ShopController::class, 'catalog'])->name('catalog');
Route::get('/products/{slug}', [ShopController::class, 'showProduct'])->name('products.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/track-order', [OrderTrackingController::class, 'show'])->name('track.order');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{slug}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/checkout/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');

// Static pages
Route::get('/page/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');
Route::view('/about', 'about')->name('about');
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// Customer Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // OTP Login (AJAX)
    Route::post('/otp/login/send', [App\Http\Controllers\Auth\OtpController::class, 'sendLoginOtp'])->name('otp.login.send');
    Route::post('/otp/login/verify', [App\Http\Controllers\Auth\OtpController::class, 'verifyLoginOtp'])->name('otp.login.verify');

    // OTP Registration Verification
    Route::get('/register/verify', [App\Http\Controllers\Auth\OtpController::class, 'showRegistrationVerify'])->name('register.verify');
    Route::post('/register/otp/send', [App\Http\Controllers\Auth\OtpController::class, 'sendRegistrationOtp'])->name('register.otp.send');
    Route::post('/register/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verifyRegistrationOtp'])->name('register.otp.verify');
});

// Customer Account
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');

    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    // Addresses
    Route::get('/account/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'index'])->name('account.addresses');
    Route::post('/account/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'store'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::post('/account/addresses/{address}/default', [\App\Http\Controllers\Customer\AddressController::class, 'setDefault'])->name('account.addresses.default');

    // Wishlist
    Route::get('/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/add/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/move-to-cart/{product}', [\App\Http\Controllers\Customer\WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
    Route::delete('/wishlist/clear', [\App\Http\Controllers\Customer\WishlistController::class, 'clear'])->name('wishlist.clear');

    // Reviews
    Route::post('/products/{slug}/reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [\App\Http\Controllers\Customer\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Customer\ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

// Admin (already defined in routes/admin.php)
require __DIR__ . '/admin.php';
