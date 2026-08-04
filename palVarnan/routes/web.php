<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;

Route::get('/select-country', [CountryController::class, 'index']);
Route::post('/select-country', [CountryController::class, 'store']);

Route::view('/login', 'pages.login');
Route::view('/register', 'pages.register');
Route::view('/cart', 'pages.cart');
Route::view('/checkout', 'pages.checkout');
Route::view('/products', 'pages.products');
Route::view('/product', 'pages.product-detail');
Route::view('/wishlist', 'pages.wishlist');
Route::view('/myaccount', 'pages.myaccount');


Route::get('/', [HomeController::class, 'index']);

Route::get('/search', [SearchController::class, 'search']);
Route::get('/products', [ProductController::class, 'index']);

Route::post('/register-store', [AuthController::class, 'registerStore']) ->name('register.store');
Route::post('/login-store', [AuthController::class, 'login'])->name('login.store');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password-direct', [AuthController::class, 'resetPassword'])->name('password.reset.direct');

Route::put('/profile-update', [ProfileController::class, 'update'])
    ->name('profile.update');

Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');

Route::get('/cart', [CartController::class, 'index']);

Route::post('/cart-update', [CartController::class, 'update'])
    ->name('cart.update');

Route::get('/cart-remove/{id}', [CartController::class, 'remove'])
    ->name('cart.remove');


Route::post('/cart/store', [CartController::class, 'store'])
    ->name('cart.store');

Route::post('/cart/ajax-update', [CartController::class, 'ajaxUpdate'])->name('cart.ajaxUpdate');
Route::post('/cart/ajax-remove', [CartController::class, 'ajaxRemove'])->name('cart.ajaxRemove');

Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/{id}/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::get('/wishlist/check', [WishlistController::class, 'check'])->middleware('auth');
Route::get('/wishlist/check-product', [WishlistController::class, 'checkProduct'])->middleware('auth');
Route::get('/product/{slug}', [ProductController::class, 'show']);
Route::get('/cart/subtotal', [CartController::class, 'subtotal']);

Route::get('/myaccount', [ProfileController::class, 'index'])->name('account.index');


Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');

    Route::post('/checkout/address', [CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::put('/checkout/address/{address}', [CheckoutController::class, 'updateAddress'])->name('address.update');
    Route::delete('/checkout/address/{address}', [CheckoutController::class, 'destroyAddress'])->name('address.destroy');
});