<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount('orders');

        if ($q = $request->string('q')->toString()) {
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['orders' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'Vous ne pouvez pas modifier votre propre rôle.');

        // 'role' est volontairement absent du Fillable de User (pas éditable via un formulaire
        // classique, pour éviter qu'un champ caché ne s'auto-promeuve admin) — forceFill() ici
        // car c'est une action admin explicite et contrôlée (pas un mass-assignment de requête).
        $user->forceFill(['role' => $user->isAdmin() ? 'customer' : 'admin'])->save();

        return back()->with('success', $user->isAdmin() ? "{$user->name} est maintenant administrateur." : "{$user->name} n'est plus administrateur.");
    }
}
