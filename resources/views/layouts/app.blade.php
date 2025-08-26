<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/sidebar-mobile.css') }}" rel="stylesheet">
    <style>
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

        /* Subtle vignette overlay */
        .vignette-bg::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(120% 120% at 50% 40%, rgba(0,0,0,0) 55%, rgba(0,0,0,0.35) 100%);
            z-index: 0;
        }
    </style>
</head>
<body class="gradient-bg vignette-bg min-h-screen">
    <div class="min-h-screen">
        <div class="flex flex-col min-h-screen">
            <!-- Bouton toggle sidebar mobile -->
            <button id="sidebarToggle" class="hamburger-button fixed top-4 left-4 z-50 md:hidden brand-bg text-white p-3 rounded-xl shadow-lg hover:scale-105 transition-all duration-200 group">
                <div class="flex flex-col items-center justify-center w-6 h-6">
                    <span class="hamburger-line w-6 h-0.5 bg-white rounded-full transition-all duration-200 group-hover:bg-blue-100"></span>
                    <span class="hamburger-line w-6 h-0.5 bg-white rounded-full transition-all duration-200 group-hover:bg-blue-100 mt-1"></span>
                    <span class="hamburger-line w-6 h-0.5 bg-white rounded-full transition-all duration-200 group-hover:bg-blue-100 mt-1"></span>
                </div>
            </button>

            <!-- Sidebar Overlay for Mobile -->
            <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[9998] md:hidden hidden"></div>



            <!-- Sidebar Mobile (en haut) - UNIQUEMENT sur mobile -->
            <aside id="sidebar" class="md:hidden fixed top-0 left-0 right-0 z-[9999] text-white p-4 space-y-4 transform -translate-y-full transition-transform duration-300 ease-in-out shadow-2xl max-h-screen overflow-y-auto" style="background: linear-gradient(180deg, #0f172a, #1e293b);">
                @auth
                    @if(auth()->user()->isAdmin())
                        <!-- Navigation Admin Mobile -->
                        <div class="sidebar-header">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center justify-start flex-1">
                                    <img src="{{ asset(config('branding.logo_path')) }}" alt="Logo" class="h-14 w-auto rounded-md bg-white/10 p-1">
                                </div>
                                <button id="closeSidebarAdmin" class="text-white hover:text-gray-300 p-2 rounded-lg hover:bg-white/10 transition-colors flex-shrink-0">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-gauge text-lg"></i><span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-box text-lg"></i><span>Produits</span>
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-tags text-lg"></i><span>Catégories</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-list-check text-lg"></i><span>Commandes</span>
                            </a>

                            <a href="{{ route('admin.statistics.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-chart-bar text-lg"></i><span>Statistiques</span>
                            </a>
                            <a href="{{ route('admin.stock.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-boxes text-lg"></i><span>Stock</span>
                            </a>
                            <a href="{{ route('admin.invoices.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-file-invoice text-lg"></i><span>Facturation</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-store text-lg"></i><span>Vendeurs</span>
                            </a>
                            <a href="{{ route('admin.admins.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-user-shield text-lg"></i><span>Administrateurs</span>
                            </a>
                        </nav>
                    @else
                        <!-- Navigation Vendeur Mobile -->
                        <div class="sidebar-header">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center justify-start flex-1">
                                    <img src="{{ asset(config('branding.logo_path')) }}" alt="Logo" class="h-14 w-auto rounded-md bg-white/10 p-1">
                                </div>
                                <button id="closeSidebarSeller" class="text-white hover:text-gray-300 p-2 rounded-lg hover:bg-white/10 transition-colors flex-shrink-0">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-gauge text-lg"></i><span>Dashboard</span>
                            </a>
                            <a href="{{ route('seller.products.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-box text-lg"></i><span>Mes Produits</span>
                            </a>
                            <div class="pt-2">
                                <h4 class="text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">Commandes</h4>
                                <div class="space-y-1 ml-2">
                                    <a href="{{ route('seller.orders.create') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-plus text-blue-400"></i><span>Nouvelle commande</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-list text-gray-400"></i><span>Toutes les commandes</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'en attente']) }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-clock text-yellow-400"></i><span>En attente</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'confirme']) }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-check text-blue-400"></i><span>Confirmé</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'livré']) }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-check-circle text-green-400"></i><span>Livré</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'annulé']) }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-times-circle text-red-400"></i><span>Annulé</span>
                                    </a>
                                </div>
                            </div>
                            <a href="{{ route('seller.invoices.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-file-invoice text-purple-400"></i><span>Facturation</span>
                            </a>
                        </nav>
                    @endif

                    <!-- Déconnexion séparé -->
                    <div class="pt-4 border-t border-gray-700">
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 hover:underline w-full text-left text-gray-300 hover:text-white p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-sign-out-alt"></i><span>{{ auth()->check() && auth()->user()->isAdmin() ? __('nav.admin.logout') : __('nav.seller.logout') }}</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </aside>

            <!-- Sidebar Desktop (à gauche) - UNIQUEMENT sur desktop -->
            <aside id="sidebarDesktop" class="hidden md:block fixed inset-y-0 left-0 z-40 w-72 p-6 space-y-6 glass-effect overflow-y-auto" style="
                background: linear-gradient(180deg, #0f172a, #1e293b);
                color: {{ config('branding.sidebar.theme') === 'light' ? '#0a0a0a' : '#ffffff' }};
            ">
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="flex items-center justify-center mb-4">
                            <img src="{{ asset(config('branding.logo_path')) }}" alt="Logo" class="h-16 w-auto rounded-md bg-white/10 p-1">
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
                            <img src="{{ asset(config('branding.logo_path')) }}" alt="Logo" class="h-16 w-auto rounded-md bg-white/10 p-1">
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
                                    <a href="{{ route('seller.orders.index', ['status' => 'confirme']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=confirme') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-check text-blue-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.confirmed') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'livré']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=livré') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-check-circle text-green-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.delivered') }}</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'annulé']) }}" class="nav-link flex items-center space-x-2 md:space-x-3 p-3 rounded-lg transition-colors {{ request()->fullUrlIs('*status=annulé') ? 'active' : '' }}" style="color: var(--sidebar-link)">
                                        <i class="fas fa-times-circle text-red-400 text-sm"></i><span class="text-xs md:text-sm">{{ __('nav.seller.cancelled') }}</span>
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
            <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8 transition-all duration-300 md:ml-72">
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
    {{-- Script app.js supprimé car non nécessaire --}}

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

        // Sidebar responsive functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarDesktop = document.getElementById('sidebarDesktop');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const closeSidebarAdmin = document.getElementById('closeSidebarAdmin');
            const closeSidebarSeller = document.getElementById('closeSidebarSeller');

            // Fonction pour fermer le sidebar
            function closeSidebarFunction() {
                sidebar.classList.add('-translate-y-full');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                sidebarToggle.classList.remove('sidebar-open');
                // Permettre de cliquer sur le fond
                document.body.style.pointerEvents = 'auto';
                sidebarOverlay.style.pointerEvents = 'none';
            }

            // Fonction pour ouvrir le sidebar
            function openSidebarFunction() {
                sidebar.classList.remove('-translate-y-full');
                sidebarOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                sidebarToggle.classList.add('sidebar-open');
                // Permettre de cliquer sur le sidebar mais pas sur le fond
                document.body.style.pointerEvents = 'auto';
                sidebarOverlay.style.pointerEvents = 'auto';
            }

            // Toggle sidebar mobile (slide down from top)
            sidebarToggle.addEventListener('click', function() {
                const isOpen = !sidebar.classList.contains('-translate-y-full');

                if (isOpen) {
                    // Fermer le sidebar
                    closeSidebarFunction();
                } else {
                    // Ouvrir le sidebar
                    openSidebarFunction();
                }
            });

            // Close sidebar with close button
            if (closeSidebarAdmin) {
                closeSidebarAdmin.addEventListener('click', function() {
                    closeSidebarFunction();
                });
            }
            if (closeSidebarSeller) {
                closeSidebarSeller.addEventListener('click', function() {
                    closeSidebarFunction();
                });
            }

            // Close sidebar when clicking overlay
            sidebarOverlay.addEventListener('click', function(e) {
                // Fermer seulement si on clique sur l'overlay lui-même, pas sur le sidebar
                if (e.target === sidebarOverlay) {
                    closeSidebarFunction();
                }
            });

            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebarFunction();
                }
            });

            // Close sidebar on window resize (if going from mobile to desktop)
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) { // md breakpoint
                    closeSidebarFunction();
                } else {
                    sidebar.classList.add('-translate-y-full');
                }
            });

            // Auto-close sidebar when clicking on a link (mobile)
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        // Fermer le sidebar après un court délai pour permettre la navigation
                        setTimeout(() => {
                            closeSidebarFunction();
                        }, 100);
                    }
                });
            });

            // Permettre de cliquer sur le fond quand le sidebar est fermé
            function enableBackgroundClicks() {
                if (sidebar.classList.contains('-translate-y-full')) {
                    document.body.style.pointerEvents = 'auto';
                    sidebarOverlay.style.pointerEvents = 'none';
                } else {
                    document.body.style.pointerEvents = 'auto';
                    sidebarOverlay.style.pointerEvents = 'auto';
                }
            }

            // Vérifier l'état initial
            enableBackgroundClicks();

            // Observer les changements de classe pour le sidebar
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        enableBackgroundClicks();
                    }
                });
            });

            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
