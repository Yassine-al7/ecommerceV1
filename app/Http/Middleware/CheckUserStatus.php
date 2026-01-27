<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (Auth::check()) {
            $user = Auth::user();

            // Vérifier si le compte est désactivé
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Votre compte a été désactivé par l\'administrateur. Contactez le support pour plus d\'informations.');
            }

            // Vérifier si le compte existe encore (pas supprimé)
            if (!$user->exists) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Votre compte n\'existe plus. Contactez le support pour plus d\'informations.');
            }
        }

        return $next($request);
    }
}
