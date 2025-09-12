<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Affilook')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        :root {
            --brand-primary: {{ config('branding.primary') }};
            --brand-secondary: {{ config('branding.secondary') }};
            --brand-on-primary: {{ config('branding.on_primary') }};
            --brand-gradient-start: {{ config('branding.gradient')[0] }};
            --brand-gradient-mid: {{ config('branding.gradient')[1] }};
            --brand-gradient-end: {{ config('branding.gradient')[2] }};
            --sidebar-link: {{ data_get(config('branding'), 'sidebar.link_color') }};
            --sidebar-link-hover: {{ data_get(config('branding'), 'sidebar.link_hover') }};
        }
        .gradient-bg {
            background: linear-gradient(135deg, var(--brand-gradient-start) 0%, var(--brand-secondary) 25%, var(--brand-gradient-mid) 50%, var(--brand-primary) 75%, var(--brand-gradient-end) 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .card-gradient {
            background: linear-gradient(180deg, #0f172a, #1e293b);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .card-frame { position: relative; }
        .card-frame::after {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: 1rem; /* match rounded-2xl */
            padding: 1px;
            background: linear-gradient(135deg, var(--brand-gradient-start), var(--brand-gradient-end));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            pointer-events: none;
        }

        /* Floating labels with dark inputs */
        .form-field { position: relative; }
        .input-dark {
            width: 100%;
            padding: 1.1rem 1rem 0.9rem 1rem;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            border-radius: 0.75rem; /* rounded-lg */
            transition: box-shadow .2s, border-color .2s, background .2s;
        }
        .input-dark::placeholder { color: transparent; }
        .input-dark:focus {
            outline: none;
            border-color: var(--sidebar-link);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--sidebar-link) 30%, transparent);
            background: rgba(255, 255, 255, 0.09);
        }
        .floating-label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.8);
            font-size: .95rem;
            transition: all .18s ease;
            pointer-events: none;
        }
        .input-dark:focus + .floating-label,
        .input-dark:not(:placeholder-shown) + .floating-label {
            top: 0.35rem;
            transform: none;
            font-size: .75rem;
            color: var(--sidebar-link);
        }
        .error-text { margin-top: .35rem; font-size: .85rem; color: #fecaca; }
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Subtle vignette overlay */
        .vignette-bg::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(120% 120% at 50% 40%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.4) 100%);
            z-index: 0;
        }
        /* Gradient mesh blobs (brand blue + deep navy) */
        .mesh-bg::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(600px 420px at 22% 28%, color-mix(in srgb, var(--sidebar-link) 28%, transparent) 0%, transparent 60%),
                radial-gradient(700px 480px at 78% 72%, rgba(15,23,42,0.35) 0%, transparent 60%);
            filter: blur(12px);
            z-index: 0;
        }
        /* Gentle center dim to blend card with background (auth pages only) */
        .auth-container::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(600px 420px at 50% 42%, rgba(0,0,0,0.12) 0%, rgba(0,0,0,0) 60%);
            z-index: 0;
        }
        .auth-container > * { position: relative; z-index: 1; }
        /* Subtle grain on auth pages */
        .auth-container::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 3px 3px;
            opacity: .6;
            z-index: 0;
        }
        .link-brand { color: var(--sidebar-link); }
        .link-brand:hover { color: var(--sidebar-link-hover); }
        .btn-brand {
            color: var(--sidebar-link);
            border-color: transparent;
        }
        .btn-brand:focus { outline: 2px solid var(--sidebar-link); outline-offset: 2px; }
        .btn-brand:hover { color: var(--sidebar-link-hover); }

        /* Animated background lines */
        .space-lines {
            position: fixed;
            inset: 0;
            z-index: 5; /* clearly above background overlays, below content (z-10) */
            pointer-events: none;
            overflow: hidden;
        }
        .space-lines .line {
            position: absolute;
            left: -30%;
            width: 160%;
            height: 5px;
            opacity: 0.9;
            mix-blend-mode: screen;
            filter: none;
            will-change: transform;
        }
        .space-lines .line span {
            display: block;
            width: 100%;
            height: 100%;
            --glow: rgba(59,130,246,0.7);
            filter: drop-shadow(0 0 18px var(--glow)) drop-shadow(0 0 36px var(--glow));
            animation: lineDrift 14s linear infinite, glowPulse 2.8s ease-in-out infinite;
        }
        .space-lines .line-1 { top: 22%; transform: rotate(8deg); }
        .space-lines .line-1 span {
            background: linear-gradient(90deg, rgba(59,130,246,0) 0%, rgba(59,130,246,0.85) 50%, rgba(59,130,246,0) 100%);
            animation-duration: 16s;
            opacity: .7;
            --glow: rgba(59,130,246,0.8);
        }
        .space-lines .line-2 { bottom: 26%; transform: rotate(-12deg); }
        .space-lines .line-2 span {
            background: linear-gradient(90deg, rgba(96,165,250,0) 0%, rgba(96,165,250,0.8) 50%, rgba(96,165,250,0) 100%);
            animation-duration: 20s;
            opacity: .65;
            --glow: rgba(96,165,250,0.8);
        }
        .space-lines .line-3 { top: 50%; transform: rotate(2deg); }
        .space-lines .line-3 span {
            background: linear-gradient(90deg, rgba(37,99,235,0) 0%, rgba(37,99,235,0.75) 50%, rgba(37,99,235,0) 100%);
            animation-duration: 24s;
            opacity: .6;
            --glow: rgba(37,99,235,0.85);
        }
        .space-lines .line-4 { top: 12%; transform: rotate(-4deg); }
        .space-lines .line-4 span {
            background: linear-gradient(90deg, rgba(147,197,253,0) 0%, rgba(147,197,253,0.8) 50%, rgba(147,197,253,0) 100%);
            animation-duration: 18s;
            animation-delay: .6s;
            opacity: .55;
            --glow: rgba(147,197,253,0.9);
        }
        .space-lines .line-5 { top: 68%; transform: rotate(-6deg); }
        .space-lines .line-5 span {
            background: linear-gradient(90deg, rgba(29,78,216,0) 0%, rgba(29,78,216,0.8) 50%, rgba(29,78,216,0) 100%);
            animation-duration: 22s;
            animation-delay: 1.2s;
            opacity: .6;
            --glow: rgba(29,78,216,0.9);
        }
        .space-lines .line-6 { bottom: 14%; transform: rotate(10deg); }
        .space-lines .line-6 span {
            background: linear-gradient(90deg, rgba(125,211,252,0) 0%, rgba(125,211,252,0.8) 50%, rgba(125,211,252,0) 100%);
            animation-duration: 26s;
            animation-delay: .9s;
            opacity: .5;
            --glow: rgba(125,211,252,0.85);
        }
        .space-lines .line-7 { top: 85%; transform: rotate(-1deg); }
        .space-lines .line-7 span {
            background: linear-gradient(90deg, rgba(37,99,235,0) 0%, rgba(59,130,246,0.75) 50%, rgba(37,99,235,0) 100%);
            animation-duration: 28s;
            animation-delay: 1.8s;
            opacity: .5;
            --glow: rgba(59,130,246,0.85);
        }
        @keyframes lineDrift {
            0% { transform: translateX(-35%); }
            100% { transform: translateX(35%); }
        }
        @keyframes glowPulse {
            0%, 100% { filter: drop-shadow(0 0 16px var(--glow)) drop-shadow(0 0 32px var(--glow)); }
            50% { filter: drop-shadow(0 0 34px var(--glow)) drop-shadow(0 0 64px var(--glow)); }
        }
        /* Additional lines with varied hues and timings */
        .space-lines .line-8 { top: 35%; transform: rotate(14deg); }
        .space-lines .line-8 span {
            background: linear-gradient(90deg, rgba(56,189,248,0) 0%, rgba(56,189,248,0.85) 50%, rgba(56,189,248,0) 100%);
            animation-duration: 18s; animation-delay: .4s; opacity: .7; --glow: rgba(56,189,248,0.9);
        }
        .space-lines .line-9 { bottom: 40%; transform: rotate(-8deg); }
        .space-lines .line-9 span {
            background: linear-gradient(90deg, rgba(14,165,233,0) 0%, rgba(14,165,233,0.85) 50%, rgba(14,165,233,0) 100%);
            animation-duration: 21s; animation-delay: 1.1s; opacity: .7; --glow: rgba(14,165,233,0.9);
        }
        .space-lines .line-10 { top: 5%; transform: rotate(3deg); }
        .space-lines .line-10 span {
            background: linear-gradient(90deg, rgba(147,197,253,0) 0%, rgba(147,197,253,0.85) 50%, rgba(147,197,253,0) 100%);
            animation-duration: 25s; animation-delay: .9s; opacity: .65; --glow: rgba(147,197,253,0.9);
        }
        .space-lines .line-11 { bottom: 8%; transform: rotate(6deg); }
        .space-lines .line-11 span {
            background: linear-gradient(90deg, rgba(99,102,241,0) 0%, rgba(99,102,241,0.8) 50%, rgba(99,102,241,0) 100%);
            animation-duration: 27s; animation-delay: 1.6s; opacity: .65; --glow: rgba(99,102,241,0.85);
        }
        .space-lines .line-12 { top: 75%; transform: rotate(-14deg); }
        .space-lines .line-12 span {
            background: linear-gradient(90deg, rgba(2,132,199,0) 0%, rgba(2,132,199,0.85) 50%, rgba(2,132,199,0) 100%);
            animation-duration: 30s; animation-delay: 2.2s; opacity: .65; --glow: rgba(2,132,199,0.9);
        }
        .space-lines .line-13 { top: 42%; transform: rotate(-3deg); }
        .space-lines .line-13 span {
            background: linear-gradient(90deg, rgba(59,130,246,0) 0%, rgba(99,102,241,0.9) 50%, rgba(59,130,246,0) 100%);
            animation-duration: 19s; animation-delay: .7s; opacity: .75; --glow: rgba(99,102,241,0.95);
        }
        .space-lines .line-14 { bottom: 52%; transform: rotate(7deg); }
        .space-lines .line-14 span {
            background: linear-gradient(90deg, rgba(96,165,250,0) 0%, rgba(96,165,250,0.9) 50%, rgba(96,165,250,0) 100%);
            animation-duration: 23s; animation-delay: 1.3s; opacity: .75; --glow: rgba(96,165,250,0.95);
        }
        /* Lines 15-20 removed to slightly reduce density */
    </style>
</head>
<body class="dark-mode-bg has-bg-image vignette-bg mesh-bg">
    <div class="fixed inset-0 z-0 pointer-events-none" style="background-image:url('{{ asset('images/background.png') }}'); background-size:cover; background-position:center; background-color:#0b1220; background-blend-mode:multiply; filter:blur(16px) saturate(0) brightness(0.6); transform:scale(1.06); opacity:.32"></div>
    <div class="space-lines">
        <div class="line line-1"><span></span></div>
        <div class="line line-2"><span></span></div>
        <div class="line line-3"><span></span></div>
        <div class="line line-4"><span></span></div>
        <div class="line line-5"><span></span></div>
        <div class="line line-6"><span></span></div>
        <div class="line line-7"><span></span></div>
        <div class="line line-8"><span></span></div>
        <div class="line line-9"><span></span></div>
        <div class="line line-10"><span></span></div>
        <div class="line line-11"><span></span></div>
        <div class="line line-12"><span></span></div>
        <div class="line line-13"><span></span></div>
        <div class="line line-14"><span></span></div>

    </div>
    <div class="auth-container py-12 px-4 sm:px-6 lg:px-8 relative z-10">
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
