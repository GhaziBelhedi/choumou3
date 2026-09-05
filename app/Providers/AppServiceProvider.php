<?php

namespace App\Providers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
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

            if (Auth::check()) {
                $view->with('unreadNotificationsCount', Auth::user()->unreadNotifications()->count());
                $view->with('latestNotifications', Auth::user()->notifications()->latest()->limit(5)->get());
            }
        });

        View::composer('admin.layouts.admin', function ($view) {
            $view->with('pendingOrdersCount', \App\Models\Order::where('status', 'pending')->count());
            $view->with('pendingReviewsCount', \App\Models\Review::where('is_approved', false)->count());
        });
    }
}
