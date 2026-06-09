<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Stellar Traffic – Plataforma de gestión inteligente del tránsito y seguridad vial.">
    <title>@yield('title', 'Stellar Traffic – Gestión Inteligente del Tránsito')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --white: #ffffff;
            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-150: #eeeeee;
            --gray-200: #e5e5e5;
            --gray-300: #d4d4d4;
            --gray-400: #a3a3a3;
            --gray-500: #737373;
            --gray-600: #525252;
            --gray-700: #404040;
            --gray-800: #262626;
            --gray-900: #171717;
            --gray-950: #0a0a0a;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Outfit', sans-serif;
            background-color: var(--white);
            color: var(--gray-800);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── Header ── */
        .st-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0 2rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(1.8);
            -webkit-backdrop-filter: blur(20px) saturate(1.8);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 30px rgba(0,0,0,0.04);
        }
        .st-header-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 0;
        }
        .st-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .st-logo img {
            height: 42px;
            width: 42px;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .st-logo-text { line-height: 1.1; }
        .st-logo-text span:first-child {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 0.15em;
            color: var(--gray-900);
        }
        .st-logo-text span:last-child {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-weight: 300;
            font-size: 1.15rem;
            letter-spacing: 0.15em;
            color: var(--gray-500);
        }
        .st-nav {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .st-nav a {
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--gray-500);
            transition: color 0.25s;
            position: relative;
        }
        .st-nav a:hover, .st-nav a.active {
            color: var(--gray-900);
        }
        .st-nav a.active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gray-800);
            border-radius: 1px;
        }
        .st-nav a:hover::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gray-800);
            border-radius: 1px;
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .st-cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gray-900);
            color: var(--white);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.65rem 1.75rem;
            border-radius: 100px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .st-cta-btn:hover {
            background: var(--gray-700);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.18);
        }

        .st-menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
        }
        .st-menu-toggle span {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--gray-700);
            margin: 5px 0;
            border-radius: 2px;
            transition: 0.3s;
        }

        /* ── Footer ── */
        .st-footer {
            background: var(--gray-950);
            padding: 5rem 2rem 2rem;
            color: rgba(255,255,255,0.6);
        }
        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        .footer-brand .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .footer-brand .footer-logo img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: contain;
        }
        .footer-brand .footer-logo-text span:first-child {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.12em;
            color: var(--white);
        }
        .footer-brand .footer-logo-text span:last-child {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-weight: 300;
            font-size: 1.05rem;
            letter-spacing: 0.12em;
            color: var(--gray-500);
        }
        .footer-brand p {
            font-size: 0.875rem;
            line-height: 1.7;
            color: var(--gray-500);
            max-width: 300px;
        }
        .footer-col h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.25rem;
        }
        .footer-col ul { list-style: none; padding: 0; }
        .footer-col ul li { margin-bottom: 0.75rem; }
        .footer-col ul li a {
            text-decoration: none;
            font-size: 0.875rem;
            color: var(--gray-500);
            transition: color 0.25s;
        }
        .footer-col ul li a:hover { color: var(--white); }
        .footer-social {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            text-decoration: none;
        }
        .footer-social a:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }
        .footer-social a svg { width: 18px; height: 18px; fill: var(--gray-400); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--gray-600);
        }
        .footer-bottom-links { display: flex; gap: 1.5rem; }
        .footer-bottom-links a {
            text-decoration: none;
            color: var(--gray-600);
            transition: color 0.25s;
        }
        .footer-bottom-links a:hover { color: var(--gray-400); }

        /* ── Main content area ── */
        .page-content {
            padding-top: 5rem;
            min-height: 60vh;
        }

        @media (max-width: 768px) {
            .st-nav { display: none; }
            .st-menu-toggle { display: block; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app" style="min-height:100vh;display:flex;flex-direction:column;justify-content:space-between;">

        <!-- Header -->
        <header class="st-header">
            <div class="st-header-inner">
                <a href="{{ route('home') }}" class="st-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Stellar Traffic Logo">
                    <div class="st-logo-text">
                        <span>STELLAR</span>
                        <span>TRAFFIC</span>
                    </div>
                </a>

                <nav class="st-nav" id="mainNav">
                    <a href="{{ route('home') }}" class="{{ Route::currentRouteName() == 'home' ? 'active' : '' }}">Inicio</a>
                    <a href="{{ route('cobertura') }}" class="{{ Route::currentRouteName() == 'cobertura' ? 'active' : '' }}">Cobertura</a>
                    <a href="{{ route('emergencias') }}" class="{{ Route::currentRouteName() == 'emergencias' ? 'active' : '' }}">Emergencias</a>
                    <a href="{{ route('acerca-de') }}" class="{{ Route::currentRouteName() == 'acerca-de' ? 'active' : '' }}">Acerca de</a>
                </nav>

                <div style="display:flex;align-items:center;gap:1rem;">
                    <a href="{{ route('login') }}" class="st-cta-btn">
                        Iniciar sesión
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <button class="st-menu-toggle" id="menuToggle" aria-label="Abrir menú">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="page-content" style="flex-grow:1;">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="st-footer">
            <div class="footer-inner">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <img src="{{ asset('images/logo.png') }}" alt="Stellar Traffic">
                            <div class="footer-logo-text">
                                <span>STELLAR</span>
                                <span>TRAFFIC</span>
                            </div>
                        </div>
                        <p>Sistema integral para la gestión de movilidad, seguridad vial y coordinación de emergencias en tiempo real.</p>
                    </div>

                    <div class="footer-col">
                        <h4>Plataforma</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">Inicio</a></li>
                            <li><a href="{{ route('visitor.map') }}">Mapa interactivo</a></li>
                            <li><a href="{{ route('cobertura') }}">Cobertura</a></li>
                            <li><a href="{{ route('emergencias') }}">Emergencias</a></li>
                        </ul>
                    </div>

                    <div class="footer-col">
                        <h4>Compañía</h4>
                        <ul>
                            <li><a href="{{ route('acerca-de') }}">Acerca de</a></li>
                            <li><a href="#">Contacto</a></li>
                            <li><a href="#">Privacidad</a></li>
                            <li><a href="#">Términos</a></li>
                        </ul>
                    </div>

                    <div class="footer-col">
                        <h4>Síguenos</h4>
                        <p style="font-size:0.875rem;color:var(--gray-500);margin-bottom:1rem;">Mantente informado sobre novedades de la plataforma.</p>
                        <div class="footer-social">
                            <a href="#" aria-label="Twitter / X">
                                <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <a href="#" aria-label="Instagram">
                                <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.203 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                            <a href="#" aria-label="Facebook">
                                <svg viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <span>&copy; {{ date('Y') }} Stellar Traffic. Todos los derechos reservados.</span>
                    <div class="footer-bottom-links">
                        <a href="#">Política de privacidad</a>
                        <a href="#">Términos de servicio</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const mainNav = document.getElementById('mainNav');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                const isOpen = mainNav.style.display === 'flex';
                mainNav.style.display = isOpen ? 'none' : 'flex';
                mainNav.style.flexDirection = 'column';
                mainNav.style.position = 'absolute';
                mainNav.style.top = '100%';
                mainNav.style.left = '0';
                mainNav.style.right = '0';
                mainNav.style.background = 'rgba(255,255,255,0.97)';
                mainNav.style.backdropFilter = 'blur(20px)';
                mainNav.style.padding = '1.5rem 2rem';
                mainNav.style.borderBottom = '1px solid rgba(0,0,0,0.06)';
                mainNav.style.gap = '1rem';
                mainNav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.06)';
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
