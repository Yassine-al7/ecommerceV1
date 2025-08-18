<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('seller.dashboard')->with('status', 'Your email is already verified.');
        }

        try {
            $verificationUrl = url('/email/verify/' . base64_encode($user->email));

            Mail::raw(
                "Hello {$user->name},\n\n" .
                "Please click the link below to verify your email address:\n" .
                "{$verificationUrl}\n\n" .
                "If you did not create an account, please ignore this email.\n\n" .
                "Best regards,\n" .
                config('app.name'),
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Verify Your Email Address')
                            ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            return back()->with('status', 'Verification link sent!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Unable to send verification email.']);
        }
    }

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
