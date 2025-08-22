<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Mapper les noms de couleurs prédéfinies vers des codes hex
     */
    public static function getPredefinedColors()
    {
        return [
            'rouge' => '#ff0000', 'vert' => '#00ff00', 'bleu' => '#0000ff',
            'jaune' => '#ffff00', 'noir' => '#000000', 'blanc' => '#ffffff',
            'orange' => '#ffa500', 'violet' => '#800080', 'rose' => '#ffc0cb',
            'marron' => '#a52a2a', 'gris' => '#808080', 'beige' => '#f5f5dc',
            'turquoise' => '#40e0d0', 'or' => '#ffd700', 'argent' => '#c0c0c0',
            'bordeaux' => '#800020'
        ];
    }

    /**
     * Générer une couleur hex basée sur le nom
     */
    public static function generateColorFromName($name)
    {
        // Utiliser le hash du nom pour générer une couleur cohérente
        $hash = crc32(strtolower($name));
        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = $hash & 0x0000FF;

        // Assurer une luminosité minimale pour la lisibilité
        if ($r + $g + $b < 200) {
            $r = min(255, $r + 50);
            $g = min(255, $g + 50);
            $b = min(255, $b + 50);
        }

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Obtenir la couleur de fond pour une couleur donnée
     */
    public static function getBackgroundColor($couleur)
    {
        $colorMap = self::getPredefinedColors();

        // S'assurer que $couleur est une chaîne avant d'appliquer trim()
        if (is_array($couleur)) {
            $couleur = is_array($couleur) && isset($couleur[0]) ? $couleur[0] : '';
        }
        $couleurLower = strtolower(trim((string)$couleur));

        // Déterminer la couleur de fond
        if (isset($colorMap[$couleurLower])) {
            // Couleur prédéfinie
            return $colorMap[$couleurLower];
        } else {
            // Couleur personnalisée - générer une couleur basée sur le nom
            return self::generateColorFromName($couleur);
        }
    }

    /**
     * Décoder les couleurs depuis le champ couleur (JSON ou string)
     */
    public static function decodeColors($couleur)
    {
        $couleurs = [];

        if (is_string($couleur)) {
            // Essayer de décoder le JSON
            $decoded = json_decode($couleur, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $couleurs = $decoded;
            } else {
                // Si ce n'est pas du JSON, traiter comme une couleur simple
                $couleurs = [$couleur];
            }
        } elseif (is_array($couleur)) {
            $couleurs = $couleur;
        }

        return $couleurs;
    }
}
