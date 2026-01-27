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
        
        // Enhanced logging for debugging
        \Illuminate\Support\Facades\Log::info('=== Admin Middleware Check ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_exists' => $user ? 'Yes' : 'No',
            'user_id' => $user ? $user->id : 'N/A',
            'user_email' => $user ? $user->email : 'N/A',
            'user_role' => $user ? $user->role : 'N/A',
            'role_type' => $user ? gettype($user->role) : 'N/A',
            'role_length' => $user && $user->role ? strlen($user->role) : 'N/A',
            'role_trimmed' => $user && $user->role ? trim($user->role) : 'N/A',
            'isAdmin_result' => $user ? ($user->isAdmin() ? 'TRUE' : 'FALSE') : 'N/A',
            'hasRole_admin' => $user ? ($user->hasRole('admin') ? 'TRUE' : 'FALSE') : 'N/A',
            'role_comparison' => $user ? ($user->role === 'admin' ? 'MATCH' : 'NO MATCH') : 'N/A'
        ]);

        if (!$user || !$user->isAdmin()) {
            \Illuminate\Support\Facades\Log::warning('=== Admin Access DENIED ===', [
                'reason' => !$user ? 'No user authenticated' : 'User is not admin',
                'user_role' => $user ? $user->role : 'N/A'
            ]);
            
            if (!$user) {
                return redirect()->route('login');
            }
            abort(403, 'Accès réservé aux administrateurs.');
        }
        
        \Illuminate\Support\Facades\Log::info('=== Admin Access GRANTED ===');
        return $next($request);
    }

}


