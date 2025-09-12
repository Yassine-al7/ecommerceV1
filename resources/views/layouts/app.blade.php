<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Affilook')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Professional font stack: Inter (latin) + Cairo (arabic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Legacy display fonts (kept for compatibility with existing styles) -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .affilook-logo {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            background: linear-gradient(45deg, #000000, #1e40af, #3b82f6);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .affilook-logo:hover {
            text-shadow: 0 0 30px rgba(59, 130, 246, 0.8);
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/sidebar-mobile.css') }}" rel="stylesheet">

    <!-- Fallback CSS inline -->
    <style>
        /* Reset and base styles */
        * {
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --text-primary: rgb(20, 41, 62);
            --text-secondary: rgb(77, 137, 198);
            --border-color: #374151;
        }

        body {
            font-family: 'Inter', 'Cairo', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            margin: 0;
            padding: 0;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Prefer Cairo for RTL (Arabic) contexts */
        [dir="rtl"] body,
        html[dir="rtl"] body {
            font-family: 'Cairo', 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', 'Cairo', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
            color: var(--text-primary);
            margin: 0 0 1rem 0;
        }

        /* Product cards */
        .product-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            background-color: #1f2937;
        }

        .product-title {
            font-size: 1.2em;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-category {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .product-stock {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .color-swatch {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        /* Admin interface */
        .admin-header {
            background: #111827;
            color: var(--text-primary);
            padding: 20px;
            margin-bottom: 30px;
        }

        .admin-title {
            font-size: 2em;
            margin: 0;
        }

        .admin-subtitle {
            font-size: 1.1em;
            opacity: 0.8;
            margin: 5px 0 0 0;
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background-color: var(--sidebar-link);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: var(--sidebar-link-hover);
        }

        /* Login page styles */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #111827 0%, #0b1220 100%);
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-title {
            text-align: center;
            color: #1a202c;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .login-links {
            text-align: center;
            margin-top: 20px;
        }

        .login-links a {
            color: #3b82f6;
            text-decoration: none;
        }

        .login-links a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                gap: 15px;
            }

            .product-card {
                margin-bottom: 15px;
            }

            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
    <style>
        /* Global blurred background image layer */
        .site-bg {
            position: fixed;
            inset: 0;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-color: #0b1220; /* tint to dark */
            background-blend-mode: multiply; /* blend image with dark tint */
            filter: blur(10px) saturate(0) brightness(0.85);
            transform: scale(1.05);
            opacity: .22;
            pointer-events: none;
            z-index: 0;
        }
        .app-root { position: relative; z-index: 1; }
        :root {
            --brand-primary: {{ config('branding.primary') }};
            --brand-secondary: {{ config('branding.secondary') }};
            --brand-on-primary: {{ config('branding.on_primary') }};
            --brand-gradient-start: {{ config('branding.gradient')[0] }};
            --brand-gradient-mid: {{ config('branding.gradient')[1] }};
            --brand-gradient-end: {{ config('branding.gradient')[2] }};
            --sidebar-link: {{ data_get(config('branding'), 'sidebar.link_color') }};
            --sidebar-link-hover: {{ data_get(config('branding'), 'sidebar.link_hover') }};
            --sidebar-text: {{ data_get(config('branding'), 'sidebar.text_color') }};
            --sidebar-theme: {{ data_get(config('branding'), 'sidebar.theme') === 'light' ? 'light' : 'dark' }};
        }
        .gradient-bg {
            background: linear-gradient(135deg, var(--brand-gradient-start) 0%, var(--brand-secondary) 25%, var(--brand-gradient-mid) 50%, var(--brand-primary) 75%, var(--brand-gradient-end) 100%);
        }
        .brand-text { color: var(--brand-on-primary); }
        .brand-bg { background-color: var(--brand-primary); }
        .brand-border { border-color: var(--brand-primary); }

        /* Force dark background for app pages, overriding external CSS */
        html { background-color: var(--bg-primary) !important; }
        body.dark-mode-bg {
            background-color: var(--bg-primary) !important;
            color: var(--text-primary) !important;
        }

        /* Force dark background for app pages, overriding external CSS */
        body.dark-mode-bg {
            background-color: var(--bg-primary) !important;
            color: var(--text-primary) !important;
        }

        /* Subtle vignette overlay */
        .vignette-bg::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(120% 120% at 50% 40%, rgba(0,0,0,0) 55%, rgba(0,0,0,0.35) 100%);
            z-index: 0;
        }

        /* Professional mesh glow overlay */
        .mesh-bg::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(60% 40% at 50% 110%, rgba(34, 211, 238, 0.25) 0%, rgba(34, 211, 238, 0.1) 35%, transparent 70%),
                radial-gradient(45% 35% at 88% 15%, rgba(59, 130, 246, 0.14) 0%, transparent 60%),
                radial-gradient(40% 30% at 12% 18%, rgba(29, 78, 216, 0.12) 0%, transparent 60%);
            filter: blur(16px);
            z-index: 0;
        }

        /* Modern Product Card Styles */
        .modern-product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-product-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="dark-mode-bg has-bg-image vignette-bg mesh-bg min-h-screen">
    <div class="site-bg" style="background-image: url('{{ asset('images/background.png') }}')"></div>
    <div class="min-h-screen app-root">
        <div class="flex flex-col min-h-screen">
                                                <!-- Header Mobile avec Menu à gauche et Logo centré -->
            <header class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white/10 backdrop-blur-lg border-b border-white/20">
                                <div class="flex items-center justify-between px-4 py-3 h-16">
                    <!-- Bouton toggle sidebar mobile - À gauche, aligné en haut -->
                    <div class="flex items-start pt-1">
                        <button id="sidebarToggle" class="hamburger-button bg-black/40 text-white p-2.5 rounded-lg shadow-lg hover:bg-black/60 transition-all duration-200">
                            <div class="flex flex-col items-center justify-center w-5 h-5">
                                <span class="hamburger-line w-5 h-0.5 bg-white rounded-full transition-all duration-200"></span>
                                <span class="hamburger-line w-5 h-0.5 bg-white rounded-full transition-all duration-200 mt-1"></span>
                                <span class="hamburger-line w-5 h-0.5 bg-white rounded-full transition-all duration-200 mt-1"></span>
                            </div>
                        </button>
                    </div>

                    <!-- Logo Mobile - Centré -->
                    <div class="flex items-center justify-center flex-1">
                        <div class="affilook-logo text-xl text-white">Affilook</div>
                    </div>

                    <!-- Espace vide pour équilibrer le layout -->
                    <div class="w-12"></div>
                </div>
            </header>

            <!-- Menu mobile (style landing) -->
            <div id="mobileAppMenu" class="lg:hidden hidden bg-black/20 backdrop-blur-lg rounded-lg mt-2 mx-4 p-4 z-[9999]">
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('admin.dashboard') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                            <a href="{{ route('admin.products.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Produits</a>
                            <a href="{{ route('admin.categories.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Catégories</a>
                            <a href="{{ route('admin.orders.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Commandes</a>
                            <a href="{{ route('admin.statistics.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Statistiques</a>
                            <a href="{{ route('admin.stock.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Stock</a>
                            <a href="{{ route('admin.invoices.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Facturation</a>
                            <a href="{{ route('admin.users.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Vendeurs</a>
                            <a href="{{ route('admin.admins.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Administrateurs</a>
                            <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-white/20">
                                @csrf
                                <button type="submit" class="w-full text-left text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ __('nav.admin.logout') }}</button>
                            </form>
                        </div>
                    @else
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('seller.dashboard') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                            <a href="{{ route('seller.products.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Mes Produits</a>
                            <div class="border-t border-white/20 my-2"></div>
                            <a href="{{ route('seller.orders.create') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Nouvelle commande</a>
                            <a href="{{ route('seller.orders.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Toutes les commandes</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'en attente']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">En attente</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'confirmé']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Confirmé</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'livré']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Livré</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'expédition']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Expédition</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'annulé']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Annulé</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'reporté']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Reporté</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'retourné']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Retourné</a>
                            <a href="{{ route('seller.orders.index', ['status' => 'pas de réponse']) }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Pas de réponse</a>
                            <div class="border-t border-white/20 my-2"></div>
                            <a href="{{ route('seller.invoices.index') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Facturation</a>
                            <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-white/20">
                                @csrf
                                <button type="submit" class="w-full text-left text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ __('nav.seller.logout') }}</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Sidebar Desktop (à gauche) - UNIQUEMENT sur desktop -->
            <aside id="sidebarDesktop" class="hidden lg:block fixed inset-y-0 left-0 z-40 w-72 p-6 space-y-6 glass-effect overflow-y-auto" style="
                background: linear-gradient(180deg, #0f172a, #1e293b);
                color: {{ config('branding.sidebar.theme') === 'light' ? '#0a0a0a' : '#ffffff' }};
            ">
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="flex items-center justify-center mb-4">
                            <div class="affilook-logo text-4xl">Affilook</div>
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-gauge text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.dashboard') }}</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-box text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.products') }}</span>
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-tags text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.categories') }}</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-list-check text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.orders') }}</span>
                            </a>

                            <a href="{{ route('admin.statistics.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-chart-bar text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.statistics') }}</span>
                            </a>
                            <a href="{{ route('admin.stock.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-boxes text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.stock') }}</span>
                            </a>
                            <a href="{{ route('admin.invoices.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-file-invoice text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.invoices') }}</span>
                            </a>
                            <a href="{{ route('admin.messages.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-bullhorn text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.messages') }}</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-store text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.sellers') }}</span>
                            </a>
                            <a href="{{ route('admin.admins.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-user-shield text-lg"></i><span class="text-sm md:text-base">{{ __('nav.admin.admins') }}</span>
                            </a>
                        </nav>

                        <!-- Déconnexion séparé -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 w-full text-left p-3 rounded-lg hover:bg-white/10 transition-colors text-gray-300 hover:text-white">
                                    <i class="fas fa-sign-out-alt text-lg"></i><span class="text-sm md:text-base font-medium">{{ __('nav.admin.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center justify-center mb-4">
                            <div class="affilook-logo text-4xl">Affilook</div>
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('seller.dashboard') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-gauge text-lg"></i><span class="text-sm md:text-base">{{ __('nav.seller.dashboard') }}</span>
                            </a>

                            <a href="{{ route('seller.products.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('seller.products.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-box text-lg"></i><span class="text-sm md:text-base">{{ __('nav.seller.my_products') }}</span>
                            </a>

                            <!-- Section Commandes -->
                            <div class="pt-2">
                                <h4 class="text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">{{ __('nav.seller.orders') }}</h4>
                                <div class="space-y-1 ml-2">
                                    <a href="{{ route('seller.orders.create') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('seller.orders.create') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-plus text-blue-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.new_order') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('seller.orders.index') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-list text-gray-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.all_orders') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'en attente']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=en%20attente') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-clock text-yellow-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.pending') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'confirmé']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=confirmé') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-check text-blue-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.confirmed') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'livré']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=livré') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-check-circle text-green-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.delivered') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'expédition']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=expédition') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-shipping-fast text-purple-400 text-sm"></i><span class="text-xs md:text-sm">Expédition</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'annulé']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=annulé') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-times-circle text-red-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.cancelled') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'reporté']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=reporté') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-exclamation-triangle text-orange-400 text-sm"></i><span class="text-xs md:text-sm">Reporté</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'retourné']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=retourné') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-undo text-gray-400 text-sm"></i><span class="text-xs md:text-sm">Retourné</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'pas de réponse']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=pas%20de%20réponse') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-question-circle text-gray-500 text-sm"></i><span class="text-xs md:text-sm">Pas de réponse</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Facturation -->
                            <a href="{{ route('seller.invoices.index') }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('seller.invoices.*') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                <i class="fas fa-file-invoice text-purple-400 text-lg"></i><span class="text-sm md:text-base">{{ __('nav.seller.invoices') }}</span>
                            </a>
                        </nav>

                        <!-- Déconnexion séparé -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 w-full text-left p-3 rounded-lg hover:bg-white/10 transition-colors text-gray-300 hover:text-white">
                                    <i class="fas fa-sign-out-alt text-lg"></i><span class="font-medium">{{ __('nav.seller.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="text-xl font-bold tracking-wide">Bienvenue</div>
                    <nav class="space-y-2">
                        <a href="{{ route('login') }}" class="flex items-center space-x-3 hover:underline">
                            <i class="fas fa-right-to-bracket"></i><span>Login</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center space-x-3 hover:underline">
                            <i class="fas fa-user-plus"></i><span>Register</span>
                        </a>
                    </nav>
                @endauth
            </aside>
            <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8 transition-all duration-300 md:ml-72 pt-20 md:pt-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notifications -->
    @if(session('success'))
        <div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('build/assets/app.js') }}"></script>
    <script src="{{ asset('js/admin-messages.js') }}"></script>

    <!-- Gestion des Messages Admin -->
    <script>
    // Gestion des messages admin
    class AdminMessageManager {
        constructor() {
            this.container = document.getElementById('adminMessagesContainer');
            this.messages = [];
            this.currentMessageIndex = 0;
            this.isShowing = false;
            this.init();
        }

        async init() {
            await this.loadMessages();
            this.startMessageRotation();
        }

        async loadMessages() {
            try {
                // Vérifier si on est sur une page admin avant de charger les messages
                if (window.location.pathname.includes('/admin/')) {
                    const response = await fetch('/admin/messages/active');
                    if (response.ok) {
                        this.messages = await response.json();
                        console.log('Messages chargés:', this.messages);
                    } else {
                        console.log('Route messages non disponible, messages désactivés');
                        this.messages = [];
                    }
                } else {
                    // Sur les pages non-admin, pas de messages
                    console.log('Page non-admin, messages désactivés');
                    this.messages = [];
                }
            } catch (error) {
                console.log('Erreur lors du chargement des messages (ignorée):', error.message);
                this.messages = [];
            }
        }

        startMessageRotation() {
            if (this.messages.length === 0) return;

            // Afficher le premier message
            this.showNextMessage();

            // Rotation automatique toutes les 8 secondes
            setInterval(() => {
                this.showNextMessage();
            }, 8000);
        }

        showNextMessage() {
            if (this.messages.length === 0) return;

            const message = this.messages[this.currentMessageIndex];
            this.displayMessage(message);

            // Passer au message suivant
            this.currentMessageIndex = (this.currentMessageIndex + 1) % this.messages.length;
        }

        displayMessage(message) {
            if (this.isShowing) {
                this.hideCurrentMessage();
                setTimeout(() => this.displayMessage(message), 500);
                return;
            }

            this.isShowing = true;
            this.container.innerHTML = this.createMessageHTML(message);

            // Afficher le message avec animation
            requestAnimationFrame(() => {
                this.container.classList.remove('-translate-y-full');
                this.container.classList.add('translate-y-0');
            });

            // Masquer automatiquement après 7 secondes
            setTimeout(() => {
                this.hideCurrentMessage();
            }, 7000);
        }

        hideCurrentMessage() {
            this.isShowing = false;
            this.container.classList.remove('translate-y-0');
            this.container.classList.add('-translate-y-full');
        }

        createMessageHTML(message) {
            const typeClasses = {
                'info': 'bg-blue-50 border-blue-200 text-blue-800',
                'success': 'bg-green-50 border-green-200 text-green-800',
                'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800',
                'error': 'bg-red-50 border-red-200 text-red-800',
                'celebration': 'bg-purple-50 border-purple-200 text-purple-800'
            };

            const priorityClasses = {
                'low': 'border-l-4 border-l-gray-400',
                'medium': 'border-l-4 border-l-blue-400',
                'high': 'border-l-4 border-l-orange-400',
                'urgent': 'border-l-4 border-l-red-400'
            };

            const typeClass = typeClasses[message.type] || 'bg-gray-50 border-gray-200 text-gray-800';
            const priorityClass = priorityClasses[message.priority] || 'border-l-4 border-l-gray-400';
            const icon = this.getIcon(message.type);

            return `
                <div class="border-b ${typeClass} ${priorityClass} shadow-lg">
                    <div class="max-w-7xl mx-auto px-4 py-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="${icon} text-lg"></i>
                                <div>
                                    <h4 class="font-semibold text-sm">${message.title}</h4>
                                    <p class="text-xs opacity-90">${message.message}</p>
                                </div>
                            </div>
                            <button onclick="adminMessageManager.hideCurrentMessage()"
                                    class="text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        getIcon(type) {
            const icons = {
                'info': 'fas fa-info-circle',
                'success': 'fas fa-check-circle',
                'warning': 'fas fa-exclamation-triangle',
                'error': 'fas fa-times-circle',
                'celebration': 'fas fa-trophy'
            };
            return icons[type] || 'fas fa-bell';
        }
    }

    // Initialiser le gestionnaire de messages
    let adminMessageManager;
    document.addEventListener('DOMContentLoaded', function() {
        adminMessageManager = new AdminMessageManager();
    });
    </script>

    <!-- Scripts existants -->
    <script>
        // Auto-hide toast notifications
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);

        // Mobile dropdown menu (inspired by landing page)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileAppMenu = document.getElementById('mobileAppMenu');

            if (!sidebarToggle || !mobileAppMenu) return;

            function toggleMobileMenu() {
                if (mobileAppMenu.classList.contains('hidden')) {
                    mobileAppMenu.classList.remove('hidden');
                    sidebarToggle.classList.add('sidebar-open');
                } else {
                    mobileAppMenu.classList.add('hidden');
                    sidebarToggle.classList.remove('sidebar-open');
                }
            }

            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMobileMenu();
            });

            // Close on outside click
            document.addEventListener('click', function(event) {
                const clickedInside = mobileAppMenu.contains(event.target) || sidebarToggle.contains(event.target);
                if (!clickedInside && !mobileAppMenu.classList.contains('hidden')) {
                    mobileAppMenu.classList.add('hidden');
                    sidebarToggle.classList.remove('sidebar-open');
                }
            });

            // Close on link click
            mobileAppMenu.querySelectorAll('a, button[type="submit"]').forEach(el => {
                el.addEventListener('click', function() {
                    mobileAppMenu.classList.add('hidden');
                    sidebarToggle.classList.remove('sidebar-open');
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
