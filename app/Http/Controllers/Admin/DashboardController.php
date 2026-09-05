<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $revenueThisMonth = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $pendingOrders = Order::where('status', 'pending')->count();
        $lowStockBooks = Book::active()->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count();
        $outOfStockBooks = Book::active()->where('stock_quantity', 0)->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        $recentOrders = Order::latest()->limit(6)->get();

        $topBooks = Book::orderByDesc('sales_count')->limit(5)->get(['id', 'title', 'author', 'sales_count', 'stock_quantity']);

        return view('admin.dashboard', compact(
            'revenueThisMonth', 'ordersThisMonth', 'pendingOrders',
            'lowStockBooks', 'outOfStockBooks', 'pendingReviews',
            'recentOrders', 'topBooks'
        ));
    }
}
