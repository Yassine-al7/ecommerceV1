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
                                <i class="fas fa-store"></i><span>Vendeurs</span>
                            </a>
                            <a href="{{ route('admin.statistics.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-chart-line"></i><span>Statistiques</span>
                            </a>
                            <a href="{{ route('admin.invoices.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-file-invoice-dollar"></i><span>Facturation</span>
                            </a>
                        </nav>
                    @else
                        <div class="text-2xl font-bold tracking-wide">Seller Panel</div>
                        <nav class="space-y-2">
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-gauge"></i><span>Dashboard</span>
                            </a>
                            <a href="{{ route('seller.orders.index') }}" class="flex items-center space-x-3 hover:underline">
                                <i class="fas fa-list-check"></i><span>Mes Commandes</span>
                            </a>
                        </nav>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="pt-4">
                        @csrf
                        <button class="flex items-center space-x-3 hover:underline">
                            <i class="fas fa-right-from-bracket"></i><span>Logout</span>
                        </button>
                    </form>
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
