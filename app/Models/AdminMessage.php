<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdminMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'priority',
        'is_active',
        'expires_at',
        'target_roles',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'target_roles' => 'array',
    ];

    /**
     * Scope pour les messages actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope pour les messages par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour les messages par priorité
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope pour les messages ciblant un rôle spécifique
     */
    public function scopeForRole($query, $role)
    {
        return $query->whereJsonContains('target_roles', $role)
                    ->orWhereNull('target_roles');
    }

    /**
     * Vérifier si le message est expiré
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifier si le message est urgent
     */
    public function isUrgent()
    {
        return $this->priority === 'urgent';
    }

    /**
     * Obtenir la classe CSS pour le type de message
     */
    public function getTypeClass()
    {
        return match($this->type) {
            'info' => 'bg-blue-50 border-blue-200 text-blue-800',
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'error' => 'bg-red-50 border-red-200 text-red-800',
            'celebration' => 'bg-purple-50 border-purple-200 text-purple-800',
            default => 'bg-gray-50 border-gray-200 text-gray-800',
        };
    }

    /**
     * Obtenir la classe CSS pour la priorité
     */
    public function getPriorityClass()
    {
        return match($this->priority) {
            'low' => 'border-l-4 border-l-gray-400',
            'medium' => 'border-l-4 border-l-blue-400',
            'high' => 'border-l-4 border-l-orange-400',
            'urgent' => 'border-l-4 border-l-red-400',
            default => 'border-l-4 border-l-gray-400',
        };
    }

    /**
     * Obtenir l'icône pour le type de message
     */
    public function getIcon()
    {
        return match($this->type) {
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
            'celebration' => 'fas fa-trophy',
            default => 'fas fa-bell',
        };
    }
}
