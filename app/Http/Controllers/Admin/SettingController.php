<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'flatShippingPrice' => Setting::get('flat_shipping_price', 0),
            'freeShippingThreshold' => Setting::get('free_shipping_threshold', 0),
            'sitePhone' => Setting::get('site_phone', ''),
            'siteEmail' => Setting::get('site_email', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'flat_shipping_price' => ['required', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['required', 'numeric', 'min:0'],
            'site_phone' => ['nullable', 'string', 'max:30'],
            'site_email' => ['nullable', 'email', 'max:191'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Paramètres mis à jour.');
    }
}
