<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // TEMPORAIREMENT DÉSACTIVÉ - Permettre l'accès sans vérification d'email
        /*
        if (Auth::check() && !Auth::user()->email_verified_at) {
            // Rediriger vers la page de vérification au lieu de déconnecter
            return redirect()->route('register.verify')->with('warning', 'Votre compte n\'est pas encore vérifié. Veuillez saisir le code reçu par email.');
        }
        */

        return $next($request);
    }
}
