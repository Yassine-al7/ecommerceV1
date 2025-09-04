<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'منصة التجارة الإلكترونية')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #6366f1;
            --brand-secondary: #8b5cf6;
            --brand-accent: #06b6d4;
            --brand-dark: #1e293b;
            --brand-light: #f8fafc;
        }

        body {
            font-family: 'Cairo', 'Inter', sans-serif;
        }

        /* Dark mode variables */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
        }

        [data-theme="dark"] {
            --bg-primary: #1f2937;
            --bg-secondary: #111827;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #374151;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        /* Dark mode specific styles */
        .dark-mode-bg {
            background-color: var(--bg-primary);
        }

        .dark-mode-text {
            color: var(--text-primary);
        }

        .dark-mode-text-secondary {
            color: var(--text-secondary);
        }

        .dark-mode-border {
            border-color: var(--border-color);
        }

        .dark-mode-card {
            background-color: var(--bg-primary);
            border-color: var(--border-color);
        }

                    /* Theme toggle button */
        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .theme-toggle i {
            color: white;
            font-size: 16px;
        }

        /* Affilook Logo Styles */
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

        /* Mobile Menu Animation */
        .mobile-menu-enter {
            animation: slideDown 0.3s ease-out;
        }

        .mobile-menu-exit {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .affilook-logo {
                font-size: 1.5rem;
            }

            .hero-pattern {
                padding-top: 80px;
            }
        }
    </style>
</head>
<body class="dark-mode-bg scroll-smooth">
        <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="affilook-logo text-2xl font-bold text-white">Affilook</div>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#features" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">المميزات</a>
                        <a href="#about" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">حولنا</a>
                        <a href="#contact" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">اتصل بنا</a>
                    </div>
                </div>

                <!-- Desktop Actions -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Theme Toggle Button -->
                    <div class="theme-toggle" onclick="toggleTheme()">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </div>
                    <a href="{{ route('login') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary text-white px-6 py-2 rounded-lg text-sm font-medium">
                        إنشاء حساب
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-2">
                    <!-- Theme Toggle Button -->
                    <div class="theme-toggle" onclick="toggleTheme()">
                        <i id="theme-icon-mobile" class="fas fa-moon"></i>
                    </div>
                    <button id="mobile-menu-button" class="text-white hover:text-gray-200 p-2 rounded-md transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden bg-black/20 backdrop-blur-lg rounded-lg mt-2 p-4">
                <div class="flex flex-col space-y-3">
                    <a href="#features" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">المميزات</a>
                    <a href="#about" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">حولنا</a>
                    <a href="#contact" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">اتصل بنا</a>
                    <div class="border-t border-white/20 pt-3 mt-3">
                        <a href="{{ route('login') }}" class="block text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors mb-2">
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('register') }}" class="block btn-primary text-white px-6 py-2 rounded-lg text-sm font-medium text-center">
                            إنشاء حساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4">منصة التجارة الإلكترونية</h3>
                    <p class="text-gray-400 mb-4">المنصة التي تتيح لك بدء عملك التجاري عبر الإنترنت بدون أي استثمار أولي.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">المميزات</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">حولنا</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">اتصل بنا</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">الدعم</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">مركز المساعدة</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">الوثائق</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">اتصل بالدعم</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 منصة التجارة الإلكترونية. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script>
        // Theme toggle functionality
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            const themeIconMobile = document.getElementById('theme-icon-mobile');
            const currentTheme = body.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                body.removeAttribute('data-theme');
                themeIcon.className = 'fas fa-moon';
                themeIconMobile.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                themeIconMobile.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = document.getElementById('mobile-menu-button');
            const icon = menuButton.querySelector('i');

            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('mobile-menu-enter');
                icon.className = 'fas fa-times text-xl';
            } else {
                mobileMenu.classList.add('mobile-menu-exit');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('mobile-menu-enter', 'mobile-menu-exit');
                }, 300);
                icon.className = 'fas fa-bars text-xl';
            }
        }

        // Load saved theme (dark mode by default)
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const themeIcon = document.getElementById('theme-icon');
            const themeIconMobile = document.getElementById('theme-icon-mobile');

            // If no theme is saved, default to dark mode
            if (savedTheme === 'light') {
                document.body.removeAttribute('data-theme');
                themeIcon.className = 'fas fa-moon';
                themeIconMobile.className = 'fas fa-moon';
            } else {
                // Default to dark mode
                document.body.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                themeIconMobile.className = 'fas fa-sun';
                // Save dark theme as default if no theme was saved
                if (!savedTheme) {
                    localStorage.setItem('theme', 'dark');
                }
            }

            // Add event listener to mobile menu button
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', toggleMobileMenu);
            }

            // Close mobile menu when clicking on a link
            const mobileMenuLinks = document.querySelectorAll('#mobile-menu a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    const mobileMenu = document.getElementById('mobile-menu');
                    const menuButton = document.getElementById('mobile-menu-button');
                    const icon = menuButton.querySelector('i');

                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('mobile-menu-enter', 'mobile-menu-exit');
                    icon.className = 'fas fa-bars text-xl';
                });
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-opacity-95');
            } else {
                nav.classList.remove('bg-opacity-95');
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = document.getElementById('mobile-menu-button');

            if (!mobileMenu.classList.contains('hidden') &&
                !mobileMenu.contains(event.target) &&
                !menuButton.contains(event.target)) {
                toggleMobileMenu();
            }
        });
    </script>
</body>
</html>
