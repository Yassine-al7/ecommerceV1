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
use App\Models\PendingRegistration;

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

        // Create a pending registration and send code
        $code = (string) random_int(100000, 999999);
        PendingRegistration::updateOrCreate(
            ['email' => $validatedData['email']],
            [
                'name' => $validatedData['name'],
                'password_hash' => Hash::make($validatedData['password']),
                'code' => $code,
                'expires_at' => now()->addMinutes(15),
            ]
        );

        $this->sendVerificationCode($validatedData['email'], $code, $validatedData['name']);

        return redirect()->route('register.verify.form', ['email' => $validatedData['email']])
            ->with('status', 'A verification code has been sent to your email. Please enter it to complete your registration.');
    }

    /**
     * Envoyer un code de vérification par e-mail
     */
    private function sendVerificationCode(string $email, string $code, string $name): void
    {
        try {
            Mail::raw(
                "Hello {$name},\n\n" .
                "Your verification code is: {$code}\n\n" .
                "This code expires in 15 minutes. If you did not request this, please ignore this email.\n\n" .
                "Regards,\n" .
                config('app.name'),
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('Your Verification Code')
                            ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );
            Log::info('Verification code sent to: ' . $email);

        } catch (\Exception $e) {
            Log::error('Failed to send verification code to ' . $email . ': ' . $e->getMessage());
        }
    }

    public function showVerifyCodeForm(Request $request)
    {
        return view('auth.verify-code', ['email' => $request->get('email')]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $pending = PendingRegistration::where('email', $request->email)->first();

        if (!$pending || $pending->code !== $request->code || $pending->expires_at->isPast()) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.'])->withInput();
        }

        try {
            $user = User::create([
                'name' => $pending->name,
                'email' => $pending->email,
                'password' => $pending->password_hash,
                'role' => 'seller',
                'email_verified_at' => now(),
            ]);

            // Cleanup and login
            $pending->delete();
            Auth::login($user);

            return redirect()->route('seller.dashboard')->with('success', 'Account created and email verified. Welcome!');
        } catch (\Exception $e) {
            Log::error('Failed to complete registration for ' . $request->email . ': ' . $e->getMessage());
            return back()->withErrors(['email' => 'Unable to complete registration.'])->withInput();
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
