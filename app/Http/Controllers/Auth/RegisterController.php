<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidEmailAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    protected $redirectTo = '/seller/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        Log::info('Registration attempt for email: ' . $request->email);

        // Check if user already exists before validation
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            Log::warning('Registration failed: User already exists - ' . $request->email);
            return back()->withErrors([
                'email' => 'An account with this email address already exists. Please try logging in instead.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Validate the request with email verification
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new ValidEmailAddress],
                'password' => 'required|string|min:8|confirmed',
                'terms' => 'required|accepted',
            ], [
                'name.required' => 'Please enter your full name.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already registered. Please try logging in.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
                'terms.required' => 'You must accept the terms and conditions.',
                'terms.accepted' => 'You must accept the terms and conditions to continue.',
            ]);
        } catch (ValidationException $e) {
            Log::error('Registration validation failed: ' . json_encode($e->errors()));
            throw $e;
        }

        // Test d'envoi d'email de vérification
        if (!$this->canSendEmailTo($validatedData['email'])) {
            return back()->withErrors([
                'email' => 'We cannot send emails to this address. Please use a different email address.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Create the user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'seller',
                'email_verified_at' => null, // L'email n'est pas encore vérifié
            ]);

            Log::info('User created successfully: ' . $user->email);

            // Envoyer un email de vérification
            $this->sendVerificationEmail($user);

            // Ne pas connecter l'utilisateur automatiquement
            return redirect()->route('login')->with('status',
                'Your account has been created! Please check your email (' . $user->email . ') and click the verification link to activate your account.'
            );

        } catch (\Exception $e) {
            Log::error('Registration failed with exception: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors([
                'email' => 'There was an error creating your account. Please try again. Error: ' . $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Tester si on peut envoyer un email à cette adresse
     */
    private function canSendEmailTo(string $email): bool
    {
        try {
            // Test simple d'envoi d'email
            Mail::raw('Email verification test', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Email Verification Test')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Email verification test successful for: ' . $email);
            return true;

        } catch (\Exception $e) {
            Log::error('Email verification test failed for ' . $email . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer un email de vérification
     */
    private function sendVerificationEmail(User $user): void
    {
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

            Log::info('Verification email sent to: ' . $user->email);

        } catch (\Exception $e) {
            Log::error('Failed to send verification email to ' . $user->email . ': ' . $e->getMessage());
        }
    }
    public function showSellerRegistrationForm()
    {
        return view('auth.register-seller');
    }

    public function registerSeller(Request $request)
    {
        return $this->register($request); // Réutilise ta logique actuelle
    }

}
