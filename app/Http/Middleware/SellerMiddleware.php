<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur est un vendeur (pas admin)
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'Accès réservé aux vendeurs.');
        }

        return $next($request);
    }
}
