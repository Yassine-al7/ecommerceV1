<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 25%, #1d4ed8 50%, #2563eb 75%, #3b82f6 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <div class="min-h-screen">
        <div class="flex min-h-screen">
            <aside class="w-72 text-white p-6 space-y-6 glass-effect">
                @auth
                    @if(auth()->user()->isAdmin())
                        <div class="text-2xl font-bold tracking-wide">Admin Panel</div>
                        <nav class="space-y-2">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-gauge"></i><span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-box"></i><span>Produits</span>
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-list-check"></i><span>Commandes</span>
                            </a>
                            <a href="{{ route('admin.sellers.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-users"></i><span>Vendeurs</span>
                            </a>
                            <a href="{{ route('admin.statistics.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-chart-bar"></i><span>Statistiques</span>
                            </a>
                            <a href="{{ route('admin.stock.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-boxes"></i><span>Stock</span>
                            </a>
                            <a href="{{ route('admin.invoices.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-file-invoice"></i><span>Facturation</span>
                            </a>
                            <a href="{{ route('admin.admins.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-user-shield"></i><span>Administrateurs</span>
                            </a>
                        </nav>

                        <!-- Déconnexion séparé -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex items-center space-x-3 hover:underline w-full text-left text-gray-300 hover:text-white">
                                    <i class="fas fa-sign-out-alt"></i><span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-2xl font-bold tracking-wide">Seller Panel</div>
                        <nav class="space-y-2">
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-gauge"></i><span>Dashboard</span>
                            </a>

                                                        <a href="{{ route('seller.products.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-box"></i><span>Mes Produits</span>
                            </a>

                            <!-- Menu déroulant des commandes -->
                            <div class="relative group">
                                <button class="flex items-center space-x-3 hover:underline w-full text-left">
                                    <i class="fas fa-list-check"></i>
                                    <span>Commandes</span>
                                    <i class="fas fa-chevron-down ml-auto text-sm transition-transform group-hover:rotate-180"></i>
                                </button>

                                <!-- Sous-menu déroulant -->
                                <div class="absolute left-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-2">
                                        <a href="{{ route('seller.orders.create') }}" class="block px-4 py-2 hover:bg-blue-50 rounded-lg">
                                            <i class="fas fa-plus mr-2 text-blue-600"></i>Nouvelle commande
                                        </a>
                                        <a href="{{ route('seller.orders.index') }}" class="block px-4 py-2 hover:bg-blue-50 rounded-lg">
                                            <i class="fas fa-list mr-2 text-gray-600"></i>Toutes les commandes
                                        </a>
                                        <a href="{{ route('seller.orders.index', ['status' => 'en attente']) }}" class="block px-4 py-2 hover:bg-blue-100 rounded-lg">
                                            <i class="fas fa-clock mr-2 text-yellow-600"></i>En attente
                                        </a>
                                        <a href="{{ route('seller.orders.index', ['status' => 'livre']) }}" class="block px-4 py-2 hover:bg-blue-100 rounded-lg">
                                            <i class="fas fa-check-circle mr-2 text-green-600"></i>Livré
                                        </a>
                                        <a href="{{ route('seller.orders.index', ['status' => 'annule']) }}" class="block px-4 py-2 hover:bg-blue-100 rounded-lg">
                                            <i class="fas fa-times-circle mr-2 text-red-600"></i>Annulé
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </nav>
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
            <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
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

    <script>
        // Auto-hide toast notifications
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    </script>
</body>
</html>
