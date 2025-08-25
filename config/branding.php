<?php

return [
    // Public path to the logo image; place your file in public/images/logo.png by default
    'logo_path' => env('BRAND_LOGO_PATH', 'images/logo.png'),

    // Primary brand color used for accents and buttons
    'primary' => env('BRAND_PRIMARY', '#2563eb'),

    // Secondary brand color (e.g., for seller)
    'secondary' => env('BRAND_SECONDARY', '#1e40af'),

    // Text contrast color on primary backgrounds
    'on_primary' => env('BRAND_ON_PRIMARY', '#ffffff'),

    // Optional gradient for backgrounds
    'gradient' => [
        env('BRAND_GRADIENT_START', '#1e3a8a'),
        env('BRAND_GRADIENT_MID', '#2563eb'),
        env('BRAND_GRADIENT_END', '#3b82f6'),
    ],

    // Sidebar specific options
    'sidebar' => [
        // Optional background image in public/ (e.g., images/eyes.png)
        'background_image' => env('BRAND_SIDEBAR_BG', ''),
        // Link colors matching logo palette
        'link_color' => env('BRAND_SIDEBAR_LINK', '#0a7cff'),
        'link_hover' => env('BRAND_SIDEBAR_LINK_HOVER', '#003b8f'),
        'text_color' => env('BRAND_SIDEBAR_TEXT', '#0a0a0a'), // for light sidebars, black-like
        // Choose theme: dark or light
        'theme' => env('BRAND_SIDEBAR_THEME', 'dark'),
    ],
];


