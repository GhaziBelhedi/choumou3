<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items', 'statusHistory.changedBy']);

        return view('orders.show', compact('order'));
    }
}
