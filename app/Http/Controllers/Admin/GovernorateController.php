<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GovernorateController extends Controller
{
    public function index(): View
    {
        $governorates = Governorate::orderBy('name')->get();

        return view('admin.governorates.index', compact('governorates'));
    }

    public function update(Request $request, Governorate $governorate): RedirectResponse
    {
        $governorate->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "{$governorate->name} mis à jour.");
    }
}
