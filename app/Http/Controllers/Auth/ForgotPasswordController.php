<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Valider l'email
        $request->validate([
            'email' => 'required|email'
        ]);

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find a user with that email address.'
            ]);
        }

        try {
            Log::info('Attempting to send password reset email to: ' . $request->email);

            // Envoyer le lien de réinitialisation à l'email fourni
            $status = Password::sendResetLink(['email' => $request->email]);

            Log::info('Password reset status: ' . $status);

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', 'We have emailed your password reset link to ' . $request->email);
            }

            Log::error('Password reset failed with status: ' . $status);
            return back()->withErrors(['email' => 'Unable to send reset link. Please try again later.']);

        } catch (\Exception $e) {
            Log::error('Password reset exception: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors([
                'email' => 'Unable to send reset link. Error: ' . $e->getMessage()
            ]);
        }
    }
}
