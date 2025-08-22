<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminMessageController extends Controller
{
    /**
     * Afficher la liste des messages
     */
    public function index()
    {
        $messages = AdminMessage::latest()->paginate(15);
        $stats = [
            'total' => AdminMessage::count(),
            'active' => AdminMessage::active()->count(),
            'urgent' => AdminMessage::active()->byPriority('urgent')->count(),
            'celebration' => AdminMessage::active()->byType('celebration')->count(),
        ];

        return view('admin.messages.index', compact('messages', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.messages.create');
    }

    /**
     * Stocker un nouveau message
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error,celebration',
            'priority' => 'required|in:low,medium,high,urgent',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'in:seller,admin,user',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Si aucune date d'expiration, le message n'expire jamais
        if (empty($data['expires_at'])) {
            $data['expires_at'] = null;
        }

        // Si aucun rôle cible, le message s'affiche pour tous
        if (empty($data['target_roles'])) {
            $data['target_roles'] = null;
        }

        AdminMessage::create($data);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message créé avec succès !');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(AdminMessage $message)
    {
        return view('admin.messages.edit', compact('message'));
    }

    /**
     * Mettre à jour un message
     */
    public function update(Request $request, AdminMessage $message)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error,celebration',
            'priority' => 'required|in:low,medium,high,urgent',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'in:seller,admin,user',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Si aucune date d'expiration, le message n'expire jamais
        if (empty($data['expires_at'])) {
            $data['expires_at'] = null;
        }

        // Si aucun rôle cible, le message s'affiche pour tous
        if (empty($data['target_roles'])) {
            $data['target_roles'] = null;
        }

        $message->update($data);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message mis à jour avec succès !');
    }

    /**
     * Supprimer un message
     */
    public function destroy(AdminMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message supprimé avec succès !');
    }

    /**
     * Activer/Désactiver un message
     */
    public function toggleStatus(AdminMessage $message)
    {
        $message->update(['is_active' => !$message->is_active]);

        $status = $message->is_active ? 'activé' : 'désactivé';
        return redirect()->route('admin.messages.index')
            ->with('success', "Message {$status} avec succès !");
    }


}
