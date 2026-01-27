<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::info('Checking Admin Middleware', [
            'user' => $request->user() ? $request->user()->id : 'Guest',
            'role' => $request->user() ? $request->user()->role : 'N/A'
        ]);

        if (!$request->user() || !$request->user()->isAdmin()) {
            \Illuminate\Support\Facades\Log::warning('Admin Middleware Rejected Access');
            return redirect()->route('login');
        }
        return $next($request);
    }
}


