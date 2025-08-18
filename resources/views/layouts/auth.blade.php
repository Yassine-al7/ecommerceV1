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
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="auth-container py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            @yield('content')
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

    @if(session('status'))
        <div id="toast" class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-info-circle mr-2"></i>{{ session('status') }}
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
