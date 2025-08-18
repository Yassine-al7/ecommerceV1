<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PendingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\VerificationCodeMail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'numero_telephone' => 'required|string|max:20',
            'store_name' => 'required|string|max:255',
            'rib' => 'required|string|max:255',
        ]);

        // Vérifier si l'email existe déjà
        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'Cette adresse email est déjà utilisée.'])->withInput();
        }

        // Nettoyer d'abord les anciens enregistrements en attente pour cet email
        PendingRegistration::where('email', $request->email)->delete();

        // Générer un code de vérification à 6 chiffres
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Créer un enregistrement temporaire
        PendingRegistration::create([
            'email' => $request->email,
            'verification_code' => $verificationCode,
            'expires_at' => now()->addMinutes(15), // Code valide 15 minutes
        ]);

        // Stocker les données utilisateur en session de manière plus robuste
        session()->put('pending_user', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'numero_telephone' => $request->numero_telephone,
            'store_name' => $request->store_name,
            'rib' => $request->rib,
            'role' => 'seller',
        ]);

        // Forcer la sauvegarde de la session
        session()->save();

        // Envoyer le code par email
        Mail::to($request->email)->send(new VerificationCodeMail($verificationCode));

        return redirect()->route('register.verify')->with('success', 'Un code de vérification a été envoyé à votre adresse email.');
    }

    public function showVerifyCodeForm()
    {
        // Debug: vérifier l'état de la session
        \Log::info('showVerifyCodeForm - État de la session:', [
            'has_pending_user' => session()->has('pending_user'),
            'pending_user_data' => session('pending_user'),
            'session_id' => session()->getId(),
        ]);

        // Régénérer l'ID de session pour plus de sécurité
        if (session()->has('pending_user')) {
            session()->regenerate();
            \Log::info('Session régénérée pour pending_user');
        }

        // Si l'utilisateur est connecté mais non vérifié, gérer le code
        if (Auth::check() && !Auth::user()->email_verified_at) {
            \Log::info('Utilisateur connecté mais non vérifié');
            $user = Auth::user();

            // Nettoyer d'abord les codes expirés
            PendingRegistration::where('email', $user->email)
                ->where('expires_at', '<', now())
                ->delete();

            // Vérifier s'il y a déjà un code valide en attente
            $pendingRegistration = PendingRegistration::where('email', $user->email)
                ->where('expires_at', '>', now())
                ->first();

            if (!$pendingRegistration) {
                // Générer un nouveau code
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Créer un nouveau code
                PendingRegistration::create([
                    'email' => $user->email,
                    'verification_code' => $verificationCode,
                    'expires_at' => now()->addMinutes(15),
                ]);

                // Envoyer le nouveau code
                Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

                return view('auth.verify-code')->with('success', 'Un nouveau code de vérification a été envoyé à votre adresse email.');
            }

            return view('auth.verify-code')->with('info', 'Un code de vérification est déjà en attente. Vérifiez votre email.');
        }

        // Si c'est une nouvelle inscription
        if (!session('pending_user')) {
            return redirect()->route('register')->with('error', 'Veuillez d\'abord remplir le formulaire d\'inscription.');
        }

        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        try {
            $request->validate([
                'verification_code' => 'required|string|size:6',
            ]);

            $verificationCode = $request->verification_code;

            // Debug: vérifier l'état de la session
            \Log::info('Vérification du code - État de la session:', [
                'has_pending_user' => session()->has('pending_user'),
                'pending_user_data' => session('pending_user'),
                'auth_check' => Auth::check(),
                'verification_code' => $verificationCode
            ]);

            // Si l'utilisateur est connecté mais non vérifié
            if (Auth::check() && !Auth::user()->email_verified_at) {
                \Log::info('Utilisateur connecté mais non vérifié');
                $user = Auth::user();

                // Chercher le code dans pending_registrations
                $pendingRegistration = PendingRegistration::where('email', $user->email)
                    ->where('verification_code', $verificationCode)
                    ->where('expires_at', '>', now())
                    ->first();

                if (!$pendingRegistration) {
                    \Log::warning('Code invalide pour utilisateur connecté');
                    return back()->withErrors(['verification_code' => 'Code invalide ou expiré.'])->withInput();
                }

                // Marquer l'email comme vérifié
                $user->update(['email_verified_at' => now()]);

                // Supprimer le code temporaire
                $pendingRegistration->delete();

                return redirect()->route('seller.dashboard')->with('success', 'Votre email a été vérifié avec succès !');
            }

            // Si c'est une nouvelle inscription (session pending_user)
            if (session('pending_user')) {
                \Log::info('Nouvelle inscription - session pending_user trouvée');
                $pendingUser = session('pending_user');

                // Vérifier le code
                $pendingRegistration = PendingRegistration::where('email', $pendingUser['email'])
                    ->where('verification_code', $verificationCode)
                    ->where('expires_at', '>', now())
                    ->first();

                if (!$pendingRegistration) {
                    \Log::warning('Code invalide pour nouvelle inscription');
                    return back()->withErrors(['verification_code' => 'Code invalide ou expiré.'])->withInput();
                }

                \Log::info('Code validé, création de l\'utilisateur');

                // Créer l'utilisateur
                $userData = [
                    'name' => $pendingUser['name'],
                    'email' => $pendingUser['email'],
                    'password' => Hash::make($pendingUser['password']),
                    'role' => $pendingUser['role'],
                    'numero_telephone' => $pendingUser['numero_telephone'],
                    'store_name' => $pendingUser['store_name'],
                    'rib' => $pendingUser['rib'],
                    'email_verified_at' => now(), // Email vérifié via le code
                ];

                // Debug: afficher les données avant création
                \Log::info('Création utilisateur avec données:', $userData);

                $user = User::create($userData);

                // Vérifier que l'utilisateur a bien été créé avec email_verified_at
                if (!$user->email_verified_at) {
                    \Log::error('Utilisateur créé sans email_verified_at');
                    // Forcer la mise à jour
                    $user->update(['email_verified_at' => now()]);
                }

                \Log::info('Utilisateur créé avec succès:', ['id' => $user->id, 'email_verified_at' => $user->email_verified_at]);

                // Supprimer l'enregistrement temporaire
                $pendingRegistration->delete();

                // Nettoyer la session
                session()->forget('pending_user');

                \Log::info('Redirection vers login après création réussie');

                // Rediriger vers la page de connexion avec un message de succès
                return redirect()->route('login')
                    ->with('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter avec vos identifiants.');
            }

            // Si ni connecté ni session, rediriger vers inscription
            \Log::warning('Aucune session pending_user trouvée, redirection vers register');
            return redirect()->route('register')->with('error', 'Veuillez d\'abord vous inscrire ou vous connecter.');

        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // Gérer l'expiration du token CSRF
            return redirect()->route('register.verify')
                ->with('error', 'La session a expiré. Veuillez saisir à nouveau le code de vérification.')
                ->withInput();
        } catch (\Exception $e) {
            // Gérer les autres erreurs
            return back()->withErrors(['verification_code' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }

    public function resendCode()
    {
        if (!Auth::check() || Auth::user()->email_verified_at) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Nettoyer tous les anciens codes
        PendingRegistration::where('email', $user->email)->delete();

        // Générer un nouveau code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Créer un nouveau code
        PendingRegistration::create([
            'email' => $user->email,
            'verification_code' => $verificationCode,
            'expires_at' => now()->addMinutes(15),
        ]);

        // Envoyer le nouveau code
        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

        return back()->with('success', 'Un nouveau code de vérification a été envoyé à votre adresse email.');
    }
}
