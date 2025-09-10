<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of sellers only.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'seller');

        // Filtre par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('store_name', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistiques des vendeurs seulement
        $stats = [
            'total' => User::where('role', 'seller')->count(),
            'active' => User::where('role', 'seller')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'seller')->where('is_active', false)->count(),
            'recent' => User::where('role', 'seller')->whereDate('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created seller.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'store_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'rib' => ['nullable', 'string', 'max:50'],
        ]);

        $seller = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'seller', // Toujours créer des vendeurs
            'store_name' => $request->store_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'rib' => $request->rib,
            'email_verified_at' => now(), // Auto-vérifier les comptes créés par l'admin
        ]);

        // Assigner par défaut tous les produits existants à ce vendeur
        $this->assignAllProductsToSeller($seller);

        return redirect()->route('admin.users.index')
            ->with('success', 'Vendeur créé avec succès !');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Charger les relations pour afficher plus d'informations
        $user->loadCount(['assignedProducts', 'orders']);

        // Statistiques du vendeur si c'est un vendeur
        $sellerStats = null;
        if ($user->role === 'seller') {
            $sellerStats = [
                'total_orders' => $user->orders()->count(),
                'pending_orders' => $user->orders()->where('status', 'en attente')->count(),
                'delivered_orders' => $user->orders()->where('status', 'livré')->count(),
                'cancelled_orders' => $user->orders()->where('status', 'annulé')->count(),
                'total_revenue' => $user->orders()->where('status', 'livré')->sum('prix_commande'),
                'total_profit' => $user->orders()->where('status', 'livré')->sum('marge_benefice'),
                'assigned_products' => $user->assignedProducts()->count(),
            ];
        }

        return view('admin.users.show', compact('user', 'sellerStats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified seller.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'store_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'rib' => ['nullable', 'string', 'max:50'],
        ];

        // Validation du mot de passe seulement si fourni
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        $oldRole = $user->role;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'store_name' => $request->store_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'rib' => $request->rib,
        ];

        // Mettre à jour le mot de passe seulement si fourni
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Si l'utilisateur devient vendeur, lui assigner tous les produits par défaut
        if ($oldRole !== 'seller' && $user->role === 'seller') {
            $this->assignAllProductsToSeller($user);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Vendeur mis à jour avec succès !');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Vérifier s'il y a des commandes associées
        $ordersCount = $user->orders()->count();
        if ($ordersCount > 0) {
            return redirect()->back()
                ->with('error', "Impossible de supprimer cet utilisateur car il a {$ordersCount} commande(s) associée(s).");
        }

        // Détacher tous les produits assignés avant la suppression
        $user->assignedProducts()->detach();

        // Forcer la déconnexion de l'utilisateur si il est connecté
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Le vendeur {$userName} a été supprimé avec succès.");
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        // Empêcher la désactivation de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $oldStatus = $user->is_active;
        $user->update([
            'is_active' => !($user->is_active ?? true)
        ]);

        $status = $user->is_active ? 'activé' : 'désactivé';

        // Si le compte vient d'être désactivé, forcer la déconnexion
        if (!$user->is_active && $oldStatus) {
            // Invalider toutes les sessions de cet utilisateur
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->back()
            ->with('success', "Le vendeur {$user->name} a été {$status} avec succès.");
    }

    /**
     * Assigner tous les produits existants au vendeur donné (par défaut).
     */
    private function assignAllProductsToSeller(User $seller): void
    {
        if ($seller->role !== 'seller') {
            return;
        }

        $productIdsAlready = $seller->assignedProducts()->pluck('produits.id')->toArray();
        $products = \App\Models\Product::select('id', 'prix_admin_moyen', 'prix_vente')->get();

        $attach = [];
        foreach ($products as $product) {
            if (!in_array($product->id, $productIdsAlready, true)) {
                $attach[$product->id] = [
                    'prix_admin' => $product->prix_admin_moyen,
                    'prix_vente' => $product->prix_vente,
                    'visible' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($attach)) {
            $seller->assignedProducts()->attach($attach);
        }
    }
}
