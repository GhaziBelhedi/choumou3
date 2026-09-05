<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Ce fichier est monté sous le préfixe /admin avec les middlewares web+auth+admin
// (voir bootstrap/app.php). Toutes les routes ci-dessous sont donc déjà protégées.

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('produits', ProductController::class)->parameters(['produits' => 'product']);
Route::delete('/produits-images/{image}', [ProductController::class, 'destroyImage'])->name('produits.images.destroy');
Route::resource('categories', CategoryController::class)->parameters(['categories' => 'category']);
Route::resource('editeurs', PublisherController::class)->parameters(['editeurs' => 'publisher']);
Route::resource('coupons', CouponController::class);

Route::get('/commandes', [OrderController::class, 'index'])->name('orders.index');
Route::get('/commandes/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/commandes/{order}/statut', [OrderController::class, 'updateStatus'])->name('orders.status');

Route::get('/avis', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/avis/{review}/approuver', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::post('/avis/{review}/rejeter', [ReviewController::class, 'reject'])->name('reviews.reject');
Route::delete('/avis/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
Route::get('/utilisateurs/{user}', [UserController::class, 'show'])->name('users.show');
Route::post('/utilisateurs/{user}/role', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');

Route::get('/gouvernorats', [GovernorateController::class, 'index'])->name('governorates.index');
Route::patch('/gouvernorats/{governorate}', [GovernorateController::class, 'update'])->name('governorates.update');

Route::get('/parametres', [SettingController::class, 'edit'])->name('settings.edit');
Route::patch('/parametres', [SettingController::class, 'update'])->name('settings.update');
