<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des villes et frais de livraison
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration des villes disponibles
    | et leurs frais de livraison associés.
    |
    */

    'cities' => [
        'casablanca' => [
            'name' => 'Casablanca',
            'price' => 30.00,
            'delivery_time' => '1-2 jours',
        ],
        'rabat' => [
            'name' => 'Rabat',
            'price' => 35.00,
            'delivery_time' => '1-2 jours',
        ],
        'fes' => [
            'name' => 'Fès',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'marrakech' => [
            'name' => 'Marrakech',
            'price' => 45.00,
            'delivery_time' => '2-3 jours',
        ],
        'agadir' => [
            'name' => 'Agadir',
            'price' => 50.00,
            'delivery_time' => '3-4 jours',
        ],
        'tanger' => [
            'name' => 'Tanger',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'meknes' => [
            'name' => 'Meknès',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'oujda' => [
            'name' => 'Oujda',
            'price' => 45.00,
            'delivery_time' => '3-4 jours',
        ],
        'kenitra' => [
            'name' => 'Kénitra',
            'price' => 35.00,
            'delivery_time' => '1-2 jours',
        ],
        'tetouan' => [
            'name' => 'Tétouan',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'safi' => [
            'name' => 'Safi',
            'price' => 45.00,
            'delivery_time' => '2-3 jours',
        ],
        'el_jadida' => [
            'name' => 'El Jadida',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'beni_mellal' => [
            'name' => 'Béni Mellal',
            'price' => 45.00,
            'delivery_time' => '2-3 jours',
        ],
        'taza' => [
            'name' => 'Taza',
            'price' => 45.00,
            'delivery_time' => '3-4 jours',
        ],
        'nador' => [
            'name' => 'Nador',
            'price' => 50.00,
            'delivery_time' => '3-4 jours',
        ],
        'larache' => [
            'name' => 'Larache',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'khemisset' => [
            'name' => 'Khémisset',
            'price' => 35.00,
            'delivery_time' => '1-2 jours',
        ],
        'guelmim' => [
            'name' => 'Guelmim',
            'price' => 55.00,
            'delivery_time' => '4-5 jours',
        ],
        'berkane' => [
            'name' => 'Berkane',
            'price' => 50.00,
            'delivery_time' => '3-4 jours',
        ],
        'taourirt' => [
            'name' => 'Taourirt',
            'price' => 50.00,
            'delivery_time' => '3-4 jours',
        ],
        'boulemane' => [
            'name' => 'Boulemane',
            'price' => 45.00,
            'delivery_time' => '2-3 jours',
        ],
        'ifrane' => [
            'name' => 'Ifrane',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'moulay_yacoub' => [
            'name' => 'Moulay Yacoub',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'sefra' => [
            'name' => 'Sefra',
            'price' => 55.00,
            'delivery_time' => '4-5 jours',
        ],
        'errachidia' => [
            'name' => 'Errachidia',
            'price' => 55.00,
            'delivery_time' => '4-5 jours',
        ],
        'midelt' => [
            'name' => 'Midelt',
            'price' => 50.00,
            'delivery_time' => '3-4 jours',
        ],
        'azrou' => [
            'name' => 'Azrou',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'khouribga' => [
            'name' => 'Khouribga',
            'price' => 40.00,
            'delivery_time' => '2-3 jours',
        ],
        'youssoufia' => [
            'name' => 'Youssoufia',
            'price' => 45.00,
            'delivery_time' => '2-3 jours',
        ],
        'settat' => [
            'name' => 'Settat',
            'price' => 35.00,
            'delivery_time' => '1-2 jours',
        ],
        'benslimane' => [
            'name' => 'Benslimane',
            'price' => 35.00,
            'delivery_time' => '1-2 jours',
        ],
        'nouaceur' => [
            'name' => 'Nouaceur',
            'price' => 30.00,
            'delivery_time' => '1-2 jours',
        ],
        'mohammedia' => [
            'name' => 'Mohammedia',
            'price' => 30.00,
            'delivery_time' => '1-2 jours',
        ],
        'temara' => [
            'name' => 'Témara',
            'price' => 30.00,
            'delivery_time' => '1-2 jours',
        ],
        'skhirat' => [
            'name' => 'Skhirat',
            'price' => 30.00,
            'delivery_time' => '1-2 jours',
        ],
        'autre' => [
            'name' => 'Autre ville',
            'price' => 60.00,
            'delivery_time' => '5-7 jours',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des délais de livraison
    |--------------------------------------------------------------------------
    |
    | Délais de livraison par défaut selon la zone
    |
    */

    'delivery_zones' => [
        'zone_1' => [
            'name' => 'Zone 1 - Grandes villes',
            'cities' => ['casablanca', 'rabat', 'kenitra', 'khemisset', 'settat', 'benslimane', 'nouaceur', 'mohammedia', 'temara', 'skhirat'],
            'delivery_time' => '1-2 jours',
            'price_range' => [30.00, 35.00],
        ],
        'zone_2' => [
            'name' => 'Zone 2 - Villes moyennes',
            'cities' => ['fes', 'marrakech', 'tanger', 'meknes', 'oujda', 'tetouan', 'safi', 'el_jadida', 'beni_mellal', 'taza', 'larache', 'azrou', 'khouribga', 'youssoufia'],
            'delivery_time' => '2-3 jours',
            'price_range' => [35.00, 45.00],
        ],
        'zone_3' => [
            'name' => 'Zone 3 - Villes éloignées',
            'cities' => ['agadir', 'nador', 'taourirt', 'boulemane', 'ifrane', 'moulay_yacoub', 'sefra', 'errachidia', 'midelt'],
            'delivery_time' => '3-4 jours',
            'price_range' => [45.00, 55.00],
        ],
        'zone_4' => [
            'name' => 'Zone 4 - Zones très éloignées',
            'cities' => ['guelmim', 'autre'],
            'delivery_time' => '4-7 jours',
            'price_range' => [55.00, 60.00],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des options de livraison
    |--------------------------------------------------------------------------
    |
    | Options de livraison disponibles
    |
    */

    'delivery_options' => [
        'standard' => [
            'name' => 'Livraison standard',
            'description' => 'Livraison à domicile en 1-7 jours selon la ville',
            'price_multiplier' => 1.0,
        ],
        'express' => [
            'name' => 'Livraison express',
            'description' => 'Livraison prioritaire en 24-48h',
            'price_multiplier' => 1.5,
        ],
        'premium' => [
            'name' => 'Livraison premium',
            'description' => 'Livraison garantie le jour même ou le lendemain',
            'price_multiplier' => 2.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des jours de livraison
    |--------------------------------------------------------------------------
    |
    | Jours et heures de livraison disponibles
    |
    */

    'delivery_days' => [
        'monday' => [
            'name' => 'Lundi',
            'available' => true,
            'hours' => ['09:00-12:00', '14:00-18:00'],
        ],
        'tuesday' => [
            'name' => 'Mardi',
            'available' => true,
            'hours' => ['09:00-12:00', '14:00-18:00'],
        ],
        'wednesday' => [
            'name' => 'Mercredi',
            'available' => true,
            'hours' => ['09:00-12:00', '14:00-18:00'],
        ],
        'thursday' => [
            'name' => 'Jeudi',
            'available' => true,
            'hours' => ['09:00-12:00', '14:00-18:00'],
        ],
        'friday' => [
            'name' => 'Vendredi',
            'available' => true,
            'hours' => ['09:00-12:00', '14:00-18:00'],
        ],
        'saturday' => [
            'name' => 'Samedi',
            'available' => true,
            'hours' => ['09:00-12:00'],
        ],
        'sunday' => [
            'name' => 'Dimanche',
            'available' => false,
            'hours' => [],
        ],
    ],
];
