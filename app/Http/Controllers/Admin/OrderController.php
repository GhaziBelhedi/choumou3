<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->withCount('items');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($q = $request->string('q')->toString()) {
            $query->where(function ($q2) use ($q) {
                $q2->where('order_number', 'like', "%{$q}%")
                    ->orWhere('shipping_full_name', 'like', "%{$q}%")
                    ->orWhere('shipping_phone', 'like', "%{$q}%");
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'statusHistory.changedBy', 'user', 'coupon']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUSES))],
            'note' => ['nullable', 'string', 'max:255'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $order->status = $data['status'];

        if ($data['status'] === 'cancelled') {
            $order->cancellation_reason = $data['cancellation_reason'] ?? null;
        }

        $order->save();

        $order->statusHistory()->create([
            'status' => $data['status'],
            'note' => $data['note'] ?? null,
            'changed_by' => $request->user()->id,
        ]);

        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order));
        }

        return back()->with('success', "Statut mis à jour : {$order->statusLabel()}.");
    }
}
