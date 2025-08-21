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
            'non confirmé' => 'Non confirmé', // Ajout pour cohérence avec le formulaire
            'confirme' => 'Confirmé',
            'en livraison' => 'En livraison',
            'livre' => 'Livré',
            'livré' => 'Livré', // Ajout de la version avec accent
            'pas de réponse' => 'Pas de réponse',
            'annulé' => 'Annulé',
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
        // Traiter 'pas de réponse' comme une catégorie séparée (vendeur doit appeler)
        if ($status === 'pas de réponse') {
            return 'pas de réponse';
        }

        // Traiter les autres statuts problématiques
        if (in_array($status, ['annulé', 'retourné'])) {
            return 'problematique';
        }

        // Normaliser les statuts avec/sans accents
        $normalizedStatus = str_replace(['é', 'è', 'à'], ['e', 'e', 'a'], $status);

        // Traiter 'non confirmé' comme 'en attente'
        if ($normalizedStatus === 'non confirme') {
            return 'en attente';
        }

        // Traiter 'en livraison' comme 'confirme' (car si en livraison = confirmé)
        if ($normalizedStatus === 'en livraison') {
            return 'confirme';
        }

        return $normalizedStatus;
    }

    /**
     * Obtenir la couleur CSS pour un statut
     */
    public static function getStatusColor($status)
    {
        $colors = [
            'en attente' => 'bg-yellow-100 text-yellow-800',
            'non confirmé' => 'bg-yellow-100 text-yellow-800', // Même couleur que 'en attente'
            'confirme' => 'bg-blue-100 text-blue-800',
            'en livraison' => 'bg-blue-100 text-blue-800',
            'livre' => 'bg-green-100 text-green-800',
            'livré' => 'bg-green-100 text-green-800', // Ajout de la version avec accent
            'problematique' => 'bg-red-100 text-red-800',
            'pas de réponse' => 'bg-red-100 text-red-800',
            'annulé' => 'bg-red-100 text-red-800',
            'retourné' => 'bg-red-100 text-red-800'
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
            'non confirmé' => 'fas fa-clock', // Même icône que 'en attente'
            'confirme' => 'fas fa-check-circle',
            'en livraison' => 'fas fa-truck',
            'livre' => 'fas fa-check-double',
            'livré' => 'fas fa-check-double', // Ajout de la version avec accent
            'problematique' => 'fas fa-exclamation-triangle',
            'pas de réponse' => 'fas fa-exclamation-triangle',
            'annulé' => 'fas fa-times-circle',
            'retourné' => 'fas fa-undo'
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
            'confirme' => 0,
            'en livraison' => 0,
            'livre' => 0,
            'pas de réponse' => 0, // Nouvelle catégorie séparée
            'problematique' => 0,
            'total' => 0
        ];

        foreach ($orders as $order) {
            $stats['total']++;

            $category = self::getStatusCategory($order->status);

            if ($category === 'problematique') {
                $stats['problematique']++;
            } elseif ($category === 'pas de réponse') {
                $stats['pas de réponse']++;
            } else {
                $stats[$category]++;
            }
        }

        return $stats;
    }
}
