<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produits/consultes-recemment', [ProductController::class, 'recentlyViewed'])->name('products.recently-viewed');
Route::get('/livres', [ProductController::class, 'books'])->name('products.books');
Route::get('/fournitures-scolaires', [ProductController::class, 'supplies'])->name('products.supplies');
Route::get('/produits/{product}', [ProductController::class, 'show'])->name('products.show');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

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
    Route::get('/mon-compte', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/mon-compte', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/mon-compte/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/mon-compte/adresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.address.destroy');

    Route::get('/mon-compte/commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mon-compte/commandes/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/liste-de-souhaits', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/liste-de-souhaits/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::post('/produits/{product}/avis', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/tout-marquer-lu', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
