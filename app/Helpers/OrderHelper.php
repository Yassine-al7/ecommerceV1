<?php

namespace App\Helpers;

class OrderHelper
{
    /**
     * Obtenir le label d'affichage pour un statut
     */
    public static function getStatusLabel($status)
    {
        $labels = [
            'en attente' => 'En attente',
            'confirmé' => 'Confirmé',
            'pas de réponse' => 'Pas de réponse',
            'expédition' => 'Expédition',
            'livré' => 'Livré',
            'annulé' => 'Annulé',
            'reporté' => 'Reporté',
            'retourné' => 'Retourné',
            'problematique' => 'Problématique'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Obtenir la catégorie de statut pour le filtrage
     */
    public static function getStatusCategory($status)
    {
        // Traiter les statuts problématiques
        if (in_array($status, ['annulé', 'retourné', 'reporté', 'pas de réponse'])) {
            return 'problematique';
        }

        // Normaliser les anciens statuts vers les nouveaux
        $statusMap = [
            'confirme' => 'confirmé',
            'en livraison' => 'confirmé',
            'livre' => 'livré',
            'annule' => 'annulé',
            'retourne' => 'retourné',
            'pas de reponse' => 'pas de réponse'
        ];

        if (isset($statusMap[$status])) {
            return $statusMap[$status];
        }

        // Retourner le statut tel quel s'il est valide
        $validStatuses = ['en attente', 'confirmé', 'expédition', 'livré'];
        if (in_array($status, $validStatuses)) {
            return $status;
        }

        // Par défaut, traiter comme problématique
        return 'problematique';
    }

    /**
     * Obtenir la couleur CSS pour un statut
     */
    public static function getStatusColor($status)
    {
        $colors = [
            'en attente' => 'bg-yellow-100 text-yellow-800',
            'confirmé' => 'bg-blue-100 text-blue-800',
            'pas de réponse' => 'bg-gray-100 text-gray-800',
            'expédition' => 'bg-purple-100 text-purple-800',
            'livré' => 'bg-green-100 text-green-800',
            'annulé' => 'bg-red-100 text-red-800',
            'reporté' => 'bg-orange-100 text-orange-800',
            'retourné' => 'bg-gray-100 text-gray-800',
            'problematique' => 'bg-red-100 text-red-800'
        ];

        return $colors[$status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Obtenir l'icône FontAwesome pour un statut
     */
    public static function getStatusIcon($status)
    {
        $icons = [
            'en attente' => 'fas fa-clock',
            'confirmé' => 'fas fa-check',
            'pas de réponse' => 'fas fa-question-circle',
            'expédition' => 'fas fa-shipping-fast',
            'livré' => 'fas fa-check-circle',
            'annulé' => 'fas fa-times-circle',
            'reporté' => 'fas fa-exclamation-triangle',
            'retourné' => 'fas fa-undo',
            'problematique' => 'fas fa-exclamation-triangle'
        ];

        return $icons[$status] ?? 'fas fa-question-circle';
    }

    /**
     * Calculer les statistiques des commandes
     */
    public static function calculateOrderStats($orders)
    {
        $stats = [
            'en attente' => 0,
            'confirmé' => 0,
            'expédition' => 0,
            'livré' => 0,
            'pas de réponse' => 0,
            'annulé' => 0,
            'reporté' => 0,
            'retourné' => 0,
            'problematique' => 0,
            'total' => 0
        ];

        foreach ($orders as $order) {
            $stats['total']++;

            // Compter chaque statut individuellement
            if (isset($stats[$order->status])) {
                $stats[$order->status]++;
            }

            // Compter aussi dans la catégorie problématique si applicable
            $category = self::getStatusCategory($order->status);
            if ($category === 'problematique') {
                $stats['problematique']++;
            }
        }

        return $stats;
    }
}
