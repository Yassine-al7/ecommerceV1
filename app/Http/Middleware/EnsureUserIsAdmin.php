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
        \Illuminate\Support\Facades\Log::info('Admin Middleware Check:', [
            'url' => $request->fullUrl(),
            'user_id' => $user ? $user->id : 'Guest',
            'is_admin' => $user ? ($user->isAdmin() ? 'Yes' : 'No') : 'N/A'
        ]);

        if (!$user || !$user->isAdmin()) {
            \Illuminate\Support\Facades\Log::warning('Admin Access Denied');
            if (!$user) {
                return redirect()->route('login');
            }
            abort(403, 'Accès réservé aux administrateurs.');
        }
        return $next($request);
    }

}


