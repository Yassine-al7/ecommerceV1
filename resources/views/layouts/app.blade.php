<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/sidebar-mobile.css') }}" rel="stylesheet">
</head>
<body class="gradient-bg min-h-screen">
    <div class="min-h-screen">
        <div class="flex flex-col min-h-screen">
            <!-- Bouton toggle sidebar mobile -->
            <button id="sidebarToggle" class="hamburger-button fixed top-4 left-4 z-50 md:hidden bg-blue-600 text-white p-3 rounded-xl shadow-lg hover:bg-blue-700 hover:scale-105 transition-all duration-200 group">
                <div class="flex flex-col items-center justify-center w-6 h-6">
                    <span class="hamburger-line w-6 h-0.5 bg-white rounded-full transition-all duration-200 group-hover:bg-blue-100"></span>
                    <span class="hamburger-line w-6 h-0.5 bg-white rounded-full transition-all duration-200 group-hover:bg-blue-100 mt-1"></span>
                    <span class="hamburger-line w-6 h-0.5 bg-white rounded-full transition-all duration-200 group-hover:bg-blue-100 mt-1"></span>
                </div>
            </button>

            <!-- Sidebar Overlay for Mobile -->
            <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[9998] md:hidden hidden"></div>

            <!-- Bandeau d'Alerte pour Messages Admin -->
            <div id="adminMessagesContainer" class="fixed top-0 left-0 right-0 z-50 transform -translate-y-full transition-transform duration-500">
                <!-- Les messages s'afficheront ici dynamiquement -->
            </div>

            <!-- Sidebar Mobile (en haut) - UNIQUEMENT sur mobile -->
            <aside id="sidebar" class="md:hidden fixed top-0 left-0 right-0 z-[9999] bg-blue-800 text-white p-4 space-y-4 transform -translate-y-full transition-transform duration-300 ease-in-out shadow-2xl max-h-screen overflow-y-auto">
                @auth
                    @if(auth()->user()->isAdmin())
                        <!-- Navigation Admin Mobile -->
                        <div class="sidebar-header">
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center space-x-3 flex-1">
                                    <i class="fas fa-user-shield text-2xl text-blue-200"></i>
                                    <div class="text-xl font-bold tracking-wide text-white">Admin Panel</div>
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
                                <div class="flex items-center space-x-3 flex-1">
                                    <i class="fas fa-store text-2xl text-green-200"></i>
                                    <div class="text-xl font-bold tracking-wide text-white">Seller Panel</div>
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
                                <i class="fas fa-sign-out-alt"></i><span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </aside>

            <!-- Sidebar Desktop (à gauche) - UNIQUEMENT sur desktop -->
            <aside id="sidebarDesktop" class="hidden md:block fixed inset-y-0 left-0 z-40 w-72 text-white p-6 space-y-6 glass-effect overflow-y-auto">
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="text-xl md:text-2xl font-bold tracking-wide mb-3">Admin Panel</div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-gauge text-lg"></i><span class="text-sm md:text-base">Dashboard</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-box text-lg"></i><span class="text-sm md:text-base">Produits</span>
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-tags text-lg"></i><span class="text-sm md:text-base">Catégories</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-list-check text-lg"></i><span class="text-sm md:text-base">Commandes</span>
                            </a>

                            <a href="{{ route('admin.statistics.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-chart-bar text-lg"></i><span class="text-sm md:text-base">Statistiques</span>
                            </a>
                            <a href="{{ route('admin.stock.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-boxes text-lg"></i><span class="text-sm md:text-base">Stock</span>
                            </a>
                            <a href="{{ route('admin.invoices.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-file-invoice text-lg"></i><span class="text-sm md:text-base">Facturation</span>
                            </a>
                            <a href="{{ route('admin.messages.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-bullhorn text-lg"></i><span class="text-sm md:text-base">Messages</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-store text-lg"></i><span class="text-sm md:text-base">Vendeurs</span>
                            </a>
                            <a href="{{ route('admin.admins.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-user-shield text-lg"></i><span class="text-sm md:text-base">Administrateurs</span>
                            </a>
                        </nav>

                        <!-- Déconnexion séparé -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 w-full text-left p-3 rounded-lg hover:bg-white/10 transition-colors text-gray-300 hover:text-white">
                                    <i class="fas fa-sign-out-alt text-lg"></i><span class="text-sm md:text-base font-medium">Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-xl md:text-2xl font-bold tracking-wide mb-3">Seller Panel</div>
                        <nav class="space-y-1">
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-gauge text-lg"></i><span class="text-sm md:text-base">Dashboard</span>
                            </a>

                            <a href="{{ route('seller.products.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-box text-lg"></i><span class="text-sm md:text-base">Mes Produits</span>
                            </a>

                            <!-- Section Commandes -->
                            <div class="pt-2">
                                <h4 class="text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">Commandes</h4>
                                <div class="space-y-1 ml-2">
                                    <a href="{{ route('seller.orders.create') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-plus text-blue-400 text-sm"></i><span class="text-xs md:text-sm">Nouvelle commande</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-list text-gray-400 text-sm"></i><span class="text-xs md:text-sm">Toutes les commandes</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'en attente']) }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-clock text-yellow-400 text-sm"></i><span class="text-xs md:text-sm">En attente</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'confirme']) }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-check text-blue-400 text-sm"></i><span class="text-xs md:text-sm">Confirmé</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'livré']) }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-check-circle text-green-400 text-sm"></i><span class="text-xs md:text-sm">Livré</span>
                                    </a>
                                    <a href="{{ route('seller.orders.index', ['status' => 'annulé']) }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                        <i class="fas fa-times-circle text-red-400 text-sm"></i><span class="text-xs md:text-sm">Annulé</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Facturation -->
                            <a href="{{ route('seller.invoices.index') }}" class="flex items-center space-x-2 md:space-x-3 p-3 rounded-lg hover:bg-white/10 transition-colors">
                                <i class="fas fa-file-invoice text-purple-400 text-lg"></i><span class="text-sm md:text-base">Facturation</span>
                            </a>
                        </nav>

                        <!-- Déconnexion séparé -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 w-full text-left p-3 rounded-lg hover:bg-white/10 transition-colors text-gray-300 hover:text-white">
                                    <i class="fas fa-sign-out-alt text-lg"></i><span class="font-medium">Déconnexion</span>
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
    <script src="{{ asset('js/app.js') }}"></script>

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
                const response = await fetch('/admin/messages/active');
                this.messages = await response.json();
                console.log('Messages chargés:', this.messages);
            } catch (error) {
                console.error('Erreur lors du chargement des messages:', error);
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
