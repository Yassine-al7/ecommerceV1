<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            \Illuminate\Support\Facades\Log::warning('Admin access denied', [
                'url' => $request->fullUrl(),
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : null
            ]);
            
            if (!$user) {
                return redirect()->route('login');
            }
            abort(403, 'Accès réservé aux administrateurs.');
        }
        
        return $next($request);
    }

}


