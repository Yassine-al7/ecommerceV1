<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des prix de livraison
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration des prix de livraison
    | utilisés dans le calcul des marges de bénéfice.
    |
    */

    'default_price' => env('DELIVERY_DEFAULT_PRICE', 0),

    'prices' => [
        'standard' => env('DELIVERY_STANDARD_PRICE', 0),
        'express' => env('DELIVERY_EXPRESS_PRICE', 0),
        'free_threshold' => env('DELIVERY_FREE_THRESHOLD', 0), // Seuil pour la livraison gratuite
    ],

    'zones' => [
        'local' => env('DELIVERY_LOCAL_PRICE', 0),
        'regional' => env('DELIVERY_REGIONAL_PRICE', 0),
        'national' => env('DELIVERY_NATIONAL_PRICE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Prix de livraison par ville
    |--------------------------------------------------------------------------
    |
    | Prix de livraison automatiques selon la ville sélectionnée
    |
    */
    'cities' => [
        'Casablanca' => [
            'price' => 15.00,
            'zone' => 'local',
            'delivery_time' => '1-2 jours'
        ],
        'Rabat' => [
            'price' => 20.00,
            'zone' => 'local',
            'delivery_time' => '1-2 jours'
        ],
        'Fès' => [
            'price' => 25.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Marrakech' => [
            'price' => 25.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Agadir' => [
            'price' => 30.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Tanger' => [
            'price' => 30.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Meknès' => [
            'price' => 25.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Oujda' => [
            'price' => 35.00,
            'zone' => 'regional',
            'delivery_time' => '3-4 jours'
        ],
        'Tétouan' => [
            'price' => 30.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'El Jadida' => [
            'price' => 20.00,
            'zone' => 'local',
            'delivery_time' => '1-2 jours'
        ],
        'Safi' => [
            'price' => 25.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Béni Mellal' => [
            'price' => 25.00,
            'zone' => 'regional',
            'delivery_time' => '2-3 jours'
        ],
        'Kénitra' => [
            'price' => 20.00,
            'zone' => 'local',
            'delivery_time' => '1-2 jours'
        ],
        'Témara' => [
            'price' => 18.00,
            'zone' => 'local',
            'delivery_time' => '1-2 jours'
        ],
        'Mohammedia' => [
            'price' => 18.00,
            'zone' => 'local',
            'delivery_time' => '1-2 jours'
        ],
        'Autre' => [
            'price' => 40.00,
            'zone' => 'national',
            'delivery_time' => '3-5 jours'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Règles de livraison
    |--------------------------------------------------------------------------
    |
    | Règles spéciales pour certaines conditions
    |
    */
    'rules' => [
        'free_shipping_threshold' => 500.00, // Livraison gratuite au-dessus de 500 DH
        'express_multiplier' => 1.5, // Multiplicateur pour livraison express
        'remote_area_surcharge' => 15.00, // Surcharge pour zones éloignées
    ]
];
