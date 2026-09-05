<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $revenueThisMonth = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $revenueLastMonth = Order::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : ($revenueThisMonth > 0 ? 100 : 0);

        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $pendingOrders = Order::where('status', 'pending')->count();
        $lowStockProducts = Product::active()->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count();
        $outOfStockProducts = Product::active()->where('stock_quantity', 0)->count();
        $pendingReviews = Review::where('is_approved', false)->count();

        $recentOrders = Order::latest()->limit(6)->get();

        $topProducts = Product::orderByDesc('sales_count')->limit(5)->get(['id', 'title', 'author', 'type', 'sales_count', 'stock_quantity']);

        // ---- Données pour les graphiques (Chart.js) ----

        // Chiffre d'affaires des 14 derniers jours (courbe)
        $revenueByDay = collect(range(13, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'value' => (float) Order::where('status', '!=', 'cancelled')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total'),
            ];
        });

        // Répartition des commandes par statut (donut)
        $ordersByStatus = collect(Order::STATUSES)->map(function ($label, $status) {
            return [
                'label' => $label,
                'value' => Order::where('status', $status)->count(),
            ];
        })->filter(fn ($row) => $row['value'] > 0)->values();

        // Chiffre d'affaires par type de produit (livres vs fournitures)
        $revenueByType = OrderItem::query()
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.type', DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('products.type')
            ->pluck('total', 'type');

        return view('admin.dashboard', compact(
            'revenueThisMonth', 'revenueTrend', 'ordersThisMonth',
            'pendingOrders', 'lowStockProducts', 'outOfStockProducts', 'pendingReviews',
            'recentOrders', 'topProducts', 'revenueByDay', 'ordersByStatus', 'revenueByType'
        ));
    }
}
