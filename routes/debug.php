<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-admin', function () {
    $user = auth()->user();
    
    if (!$user) {
        return response()->json([
            'status' => 'Not authenticated',
            'message' => 'No user is currently logged in'
        ]);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'isAdmin_method' => $user->isAdmin(),
        'hasRole_admin' => $user->hasRole('admin'),
        'role_comparison' => $user->role === 'admin' ? 'true' : 'false',
        'all_attributes' => $user->getAttributes()
    ]);
})->middleware('web');
