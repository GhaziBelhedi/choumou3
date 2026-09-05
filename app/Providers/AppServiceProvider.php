<?php

namespace App\Providers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-admin', fn (User $user) => $user->isAdmin());

        View::composer('layouts.app', function ($view) {
            $cart = app(CartService::class)->peekCart();
            $view->with('cartCount', $cart ? $cart->itemsCount() : 0);
        });
    }
}
