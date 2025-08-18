<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function verify($encodedEmail)
    {
        try {
            $email = base64_decode($encodedEmail);

            $user = User::where('email', $email)->first();

            if (!$user) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Invalid verification link.'
                ]);
            }

            if ($user->email_verified_at) {
                return redirect()->route('login')->with('status',
                    'Your email is already verified. You can now login.'
                );
            }

            // Marquer l'email comme vérifié
            $user->email_verified_at = now();
            $user->save();

            // Connecter l'utilisateur
            Auth::login($user);

            return redirect()->route('seller.dashboard')->with('success',
                'Your email has been verified successfully! Welcome to your dashboard.'
            );

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Invalid verification link.'
            ]);
        }
    }
}
