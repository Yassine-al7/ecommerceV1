<?php

if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        switch ($status) {
            case 'en attente':
                return 'bg-yellow-100 text-yellow-800';
            case 'confirmé':
                return 'bg-blue-100 text-blue-800';
            case 'pas de réponse':
                return 'bg-gray-100 text-gray-800';
            case 'expédition':
                return 'bg-purple-100 text-purple-800';
            case 'livré':
                return 'bg-green-100 text-green-800';
            case 'annulé':
                return 'bg-red-100 text-red-800';
            case 'reporté':
                return 'bg-orange-100 text-orange-800';
            case 'retourné':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        switch ($status) {
            case 'en attente':
                return 'En attente';
            case 'confirmé':
                return 'Confirmé';
            case 'pas de réponse':
                return 'Pas de réponse';
            case 'expédition':
                return 'Expédition';
            case 'livré':
                return 'Livré';
            case 'annulé':
                return 'Annulé';
            case 'reporté':
                return 'Reporté';
            case 'retourné':
                return 'Retourné';
            default:
                return ucfirst($status);
        }
    }
}
