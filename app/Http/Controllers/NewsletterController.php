<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:191'],
        ], [], ['email' => 'adresse e-mail']);

        $alreadySubscribed = NewsletterSubscriber::where('email', $data['email'])->exists();

        if (! $alreadySubscribed) {
            NewsletterSubscriber::create($data);
        }

        $message = $alreadySubscribed
            ? 'Vous êtes déjà inscrit à la newsletter.'
            : 'Merci ! Vous êtes inscrit à la newsletter.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
