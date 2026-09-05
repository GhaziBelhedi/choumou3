<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/livres', [BookController::class, 'index'])->name('books.index');
Route::get('/livres/{book}', [BookController::class, 'show'])->name('books.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier', [CartController::class, 'store'])->name('cart.store');
Route::patch('/panier/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/panier/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/panier/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

Route::prefix('commande')->name('checkout.')->group(function () {
    Route::get('/adresse', [CheckoutController::class, 'showAddress'])->name('address');
    Route::post('/adresse', [CheckoutController::class, 'storeAddress'])->name('address.store');
    Route::get('/recapitulatif', [CheckoutController::class, 'showReview'])->name('review');
    Route::post('/confirmer', [CheckoutController::class, 'placeOrder'])->name('place');
    Route::get('/confirmation/{orderNumber}', [CheckoutController::class, 'showConfirmation'])->name('confirmation');
});

Route::get('/suivi-commande', [OrderTrackingController::class, 'showForm'])->name('orders.track');
Route::post('/suivi-commande', [OrderTrackingController::class, 'track'])->name('orders.track.submit');

Route::middleware('auth')->group(function () {
    Route::get('/mon-compte/commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mon-compte/commandes/{order}', [OrderController::class, 'show'])->name('orders.show');
});
