<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function showForm(): View
    {
        return view('orders.track', ['order' => null]);
    }

    public function track(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'order_number' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ], [], [
            'order_number' => 'numéro de commande',
            'phone' => 'téléphone',
        ]);

        $order = Order::where('order_number', $data['order_number'])
            ->where('shipping_phone', $data['phone'])
            ->with(['items', 'statusHistory'])
            ->first();

        if (! $order) {
            return back()
                ->withErrors(['order_number' => 'Aucune commande ne correspond à ces informations.'])
                ->withInput();
        }

        return view('orders.track', compact('order'));
    }
}
