<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Stellar Traffic – Plataforma líder en gestión inteligente del tránsito, seguridad vial y respuesta a emergencias en tiempo real.">
    <title>Stellar Traffic – Gestión Inteligente del Tránsito</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════
           DESIGN SYSTEM — Gray & White Premium Theme
           ═══════════════════════════════════════════ */
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

            --accent: #404040;
            --accent-hover: #262626;
            --accent-light: rgba(64, 64, 64, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Outfit', sans-serif;
            background-color: var(--white);
            color: var(--gray-800);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── Typography ── */
        .font-display { font-family: 'Outfit', 'Inter', sans-serif; }
        .text-gradient-dark {
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Glass Panels ── */
        .glass-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(1.8);
            -webkit-backdrop-filter: blur(20px) saturate(1.8);
            border: 1px solid rgba(0, 0, 0, 0.06);
        }
        .glass-dark {
            background: rgba(23, 23, 23, 0.85);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 0.7; }
        }
        @keyframes grain {
            0%, 100% { transform: translate(0, 0); }
            10% { transform: translate(-5%, -10%); }
            30% { transform: translate(3%, -15%); }
            50% { transform: translate(12%, 9%); }
            70% { transform: translate(9%, 4%); }
            90% { transform: translate(-1%, 7%); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }

        .anim-fade-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-fade-in { animation: fadeIn 0.6s ease forwards; }
        .anim-slide-left { animation: slideLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-slide-right { animation: slideRight 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .anim-float { animation: float 6s ease-in-out infinite; }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }

        /* Scroll-triggered reveal */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Noise Texture Overlay ── */
        .noise::before {
            content: '';
            position: absolute;
            inset: -200%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            opacity: 0.025;
            pointer-events: none;
            animation: grain 8s steps(10) infinite;
            z-index: 0;
        }

        /* ══════════════════════════════
           HEADER
           ══════════════════════════════ */
        .st-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0 2rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .st-header.scrolled {
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
        .st-logo-text {
            line-height: 1.1;
        }
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
        .st-nav a:hover {
            color: var(--gray-900);
        }
        .st-nav a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gray-800);
            border-radius: 1px;
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .st-nav a:hover::after {
            width: 100%;
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

        /* Mobile menu toggle */
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

        /* ══════════════════════════════
           HERO
           ══════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 8rem 2rem 4rem;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(165deg,
                var(--gray-50) 0%,
                var(--white) 30%,
                var(--gray-100) 70%,
                var(--gray-150) 100%);
            z-index: -2;
        }
        /* Abstract geometric shapes */
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
        }
        .hero-shape-1 {
            width: 600px;
            height: 600px;
            top: -15%;
            right: -10%;
            background: radial-gradient(circle, rgba(212, 212, 212, 0.3) 0%, transparent 70%);
            animation: pulse-soft 8s ease-in-out infinite;
        }
        .hero-shape-2 {
            width: 400px;
            height: 400px;
            bottom: -5%;
            left: -5%;
            background: radial-gradient(circle, rgba(163, 163, 163, 0.15) 0%, transparent 70%);
            animation: pulse-soft 10s ease-in-out infinite 2s;
        }
        .hero-shape-3 {
            width: 200px;
            height: 200px;
            top: 30%;
            left: 20%;
            background: radial-gradient(circle, rgba(212, 212, 212, 0.2) 0%, transparent 70%);
            animation: pulse-soft 6s ease-in-out infinite 4s;
        }
        /* Grid pattern */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,0,0,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.025) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: -1;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 30%, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 80% 80% at 50% 30%, black 30%, transparent 80%);
        }

        .hero-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            width: 100%;
        }
        .hero-content { position: relative; z-index: 2; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 100px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            opacity: 0;
        }
        .hero-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
        }
        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.03em;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            opacity: 0;
        }
        .hero-title .accent {
            color: var(--gray-400);
        }
        .hero-desc {
            font-size: 1.1rem;
            line-height: 1.75;
            color: var(--gray-500);
            max-width: 520px;
            margin-bottom: 2.5rem;
            opacity: 0;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            opacity: 0;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gray-900);
            color: var(--white);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.85rem 2.25rem;
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--gray-700);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.16);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.85rem 2.25rem;
            border-radius: 14px;
            border: 1.5px solid var(--gray-300);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            transform: translateY(-2px);
        }

        .hero-visual {
            position: relative;
            z-index: 2;
            opacity: 0;
        }
        .hero-map-card {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-map-card:hover {
            transform: translateY(-8px) scale(1.01);
        }
        .hero-map-card img {
            width: 100%;
            height: 460px;
            object-fit: cover;
            display: block;
        }
        .hero-map-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            background: linear-gradient(to top, rgba(0,0,0,0.65), transparent);
        }
        .hero-map-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .hero-map-info-text h4 {
            color: var(--white);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.15rem;
        }
        .hero-map-info-text p {
            color: rgba(255,255,255,0.65);
            font-size: 0.8rem;
        }
        .hero-map-arrow {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        .hero-map-card:hover .hero-map-arrow {
            background: rgba(255,255,255,0.25);
            transform: translateX(3px);
        }

        /* Floating Stats on Hero */
        .hero-floating-stat {
            position: absolute;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            z-index: 3;
        }
        .hero-floating-stat.stat-1 {
            top: -10px;
            left: -30px;
            animation: float 5s ease-in-out infinite;
        }
        .hero-floating-stat.stat-2 {
            bottom: 60px;
            right: -20px;
            animation: float 7s ease-in-out infinite 1.5s;
        }
        .hero-floating-stat .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }
        .hero-floating-stat .stat-value {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--gray-900);
        }
        .hero-floating-stat .stat-label {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* ══════════════════════════════
           MARQUEE — Instituciones
           ══════════════════════════════ */
        .marquee-section {
            padding: 3rem 0;
            border-top: 1px solid var(--gray-150);
            border-bottom: 1px solid var(--gray-150);
            background: var(--gray-50);
            overflow: hidden;
        }
        .marquee-label {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--gray-400);
            margin-bottom: 1.5rem;
        }
        .marquee-track {
            display: flex;
            animation: marquee 30s linear infinite;
            width: fit-content;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }
        .marquee-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 3rem;
            white-space: nowrap;
        }
        .marquee-item .m-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gray-700);
        }
        .marquee-item .m-desc {
            font-size: 0.8rem;
            color: var(--gray-400);
        }
        .marquee-item .m-divider {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--gray-300);
        }

        /* ══════════════════════════════
           FEATURES
           ══════════════════════════════ */
        .features-section {
            padding: 7rem 2rem;
            position: relative;
            background: var(--white);
        }
        .features-inner {
            max-width: 1280px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--gray-500);
            margin-bottom: 1rem;
        }
        .section-tag .tag-line {
            width: 24px;
            height: 2px;
            background: var(--gray-300);
            border-radius: 1px;
        }
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--gray-900);
            margin-bottom: 1rem;
            line-height: 1.15;
        }
        .section-subtitle {
            font-size: 1.05rem;
            color: var(--gray-500);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        .feature-card {
            position: relative;
            padding: 2rem;
            border-radius: 20px;
            background: var(--gray-50);
            border: 1px solid var(--gray-150);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            cursor: default;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gray-300), var(--gray-500));
            opacity: 0;
            transition: opacity 0.4s;
            border-radius: 20px 20px 0 0;
        }
        .feature-card:hover {
            background: var(--white);
            border-color: var(--gray-200);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
        }
        .feature-card:hover::before {
            opacity: 1;
        }
        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(-3deg);
        }
        .feature-icon.icon-nav { background: linear-gradient(135deg, #e5e5e5, #d4d4d4); }
        .feature-icon.icon-alert { background: linear-gradient(135deg, #fecaca, #fca5a5); }
        .feature-icon.icon-coord { background: linear-gradient(135deg, #e0e7ff, #c7d2fe); }
        .feature-icon.icon-safe { background: linear-gradient(135deg, #d1fae5, #a7f3d0); }

        .feature-card h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            font-size: 0.9rem;
            line-height: 1.6;
            color: var(--gray-500);
        }

        /* ══════════════════════════════
           SHOWCASE — Mapa Interactivo
           ══════════════════════════════ */
        .showcase-section {
            padding: 7rem 2rem;
            position: relative;
            background: var(--gray-50);
            overflow: hidden;
        }
        .showcase-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }
        .showcase-img-wrapper {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.04);
        }
        .showcase-img-wrapper img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: block;
        }
        .showcase-img-wrapper .overlay-badge {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            padding: 0.5rem 1rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .overlay-badge .live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px rgba(34,197,94,0.6);
        }
        .showcase-content {
            padding: 1rem 0;
        }
        .showcase-content .section-tag { text-align: left; }
        .showcase-content .section-title { text-align: left; }
        .showcase-feature-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .showcase-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            font-size: 0.95rem;
            color: var(--gray-600);
            line-height: 1.6;
        }
        .showcase-feature-list .check-icon {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            background: var(--gray-900);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .showcase-feature-list .check-icon svg {
            width: 14px;
            height: 14px;
        }

        /* ══════════════════════════════
           STATS
           ══════════════════════════════ */
        .stats-section {
            padding: 5rem 2rem;
            background: var(--gray-900);
            position: relative;
            overflow: hidden;
        }
        .stats-section .noise::before { opacity: 0.04; }
        .stats-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            position: relative;
            z-index: 1;
        }
        .stat-card {
            text-align: center;
            padding: 2rem 1rem;
            position: relative;
        }
        .stat-card:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background: rgba(255,255,255,0.1);
        }
        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 0.5rem;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.5;
        }

        /* ══════════════════════════════
           HOW IT WORKS
           ══════════════════════════════ */
        .how-section {
            padding: 7rem 2rem;
            background: var(--white);
        }
        .how-inner {
            max-width: 1280px;
            margin: 0 auto;
        }
        .how-steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 4rem;
        }
        .how-step {
            position: relative;
            padding: 2.5rem 2rem;
            border-radius: 20px;
            background: var(--gray-50);
            border: 1px solid var(--gray-150);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .how-step:hover {
            background: var(--white);
            border-color: var(--gray-200);
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        }
        .step-number {
            font-family: 'Outfit', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            color: var(--gray-100);
            line-height: 1;
            margin-bottom: 1rem;
            transition: color 0.3s;
        }
        .how-step:hover .step-number {
            color: var(--gray-200);
        }
        .how-step h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }
        .how-step p {
            font-size: 0.9rem;
            color: var(--gray-500);
            line-height: 1.7;
        }

        /* ══════════════════════════════
           TESTIMONIALS
           ══════════════════════════════ */
        .testimonials-section {
            padding: 7rem 2rem;
            background: var(--gray-50);
        }
        .testimonials-inner {
            max-width: 1280px;
            margin: 0 auto;
        }
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 4rem;
        }
        .testimonial-card {
            padding: 2rem;
            border-radius: 20px;
            background: var(--white);
            border: 1px solid var(--gray-150);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            border-color: var(--gray-200);
        }
        .testimonial-stars {
            display: flex;
            gap: 2px;
            margin-bottom: 1rem;
        }
        .testimonial-stars svg { width: 18px; height: 18px; fill: #fbbf24; }
        .testimonial-text {
            font-size: 0.95rem;
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-style: italic;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .testimonial-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--gray-600);
        }
        .testimonial-author-info h5 {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--gray-800);
        }
        .testimonial-author-info p {
            font-size: 0.78rem;
            color: var(--gray-400);
        }

        /* ══════════════════════════════
           CTA FINAL
           ══════════════════════════════ */
        .cta-section {
            padding: 7rem 2rem;
            background: var(--white);
            position: relative;
        }
        .cta-inner {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .cta-inner .section-title {
            margin-bottom: 1.25rem;
        }
        .cta-desc {
            font-size: 1.1rem;
            color: var(--gray-500);
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }
        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* ══════════════════════════════
           FOOTER
           ══════════════════════════════ */
        .st-footer {
            background: var(--gray-950);
            padding: 5rem 2rem 2rem;
            color: rgba(255,255,255,0.6);
            position: relative;
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
        .footer-col ul {
            list-style: none;
            padding: 0;
        }
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
        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }
        .footer-bottom-links a {
            text-decoration: none;
            color: var(--gray-600);
            transition: color 0.25s;
        }
        .footer-bottom-links a:hover { color: var(--gray-400); }

        /* ══════════════════════════════
           RESPONSIVE
           ══════════════════════════════ */
        @media (max-width: 1024px) {
            .hero-inner { grid-template-columns: 1fr; gap: 3rem; }
            .hero-visual { order: -1; }
            .hero-content { text-align: center; }
            .hero-desc { margin: 0 auto 2.5rem; }
            .hero-actions { justify-content: center; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .showcase-inner { grid-template-columns: 1fr; gap: 3rem; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); }
            .stat-card:nth-child(2)::after { display: none; }
            .how-steps { grid-template-columns: 1fr; }
            .testimonials-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .hero-floating-stat { display: none; }
        }
        @media (max-width: 768px) {
            .st-nav { display: none; }
            .st-menu-toggle { display: block; }
            .hero { padding: 7rem 1.25rem 3rem; min-height: auto; }
            .hero-map-card img { height: 300px; }
            .features-grid { grid-template-columns: 1fr; }
            .stats-inner { grid-template-columns: 1fr; }
            .stat-card::after { display: none !important; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ══════════════════════════════ HEADER ══════════════════════════════ -->
    <header class="st-header" id="mainHeader">
        <div class="st-header-inner">
            <a href="{{ route('home') }}" class="st-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Stellar Traffic Logo">
                <div class="st-logo-text">
                    <span>STELLAR</span>
                    <span>TRAFFIC</span>
                </div>
            </a>

            <nav class="st-nav" id="mainNav">
                <a href="{{ route('home') }}">Inicio</a>
                <a href="{{ route('cobertura') }}">Cobertura</a>
                <a href="{{ route('emergencias') }}">Emergencias</a>
                <a href="{{ route('acerca-de') }}">Acerca de</a>
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

    <!-- ══════════════════════════════ HERO ══════════════════════════════ -->
    <section class="hero noise" id="hero">
        <div class="hero-bg"></div>
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>
        <div class="hero-grid"></div>

        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge anim-fade-up" style="opacity:0;">
                    <span class="dot"></span>
                    Plataforma activa 24/7
                </div>
                <h1 class="hero-title anim-fade-up delay-100" style="opacity:0;">
                    Control inteligente<br>
                    del <span class="accent">tránsito vial</span>
                </h1>
                <p class="hero-desc anim-fade-up delay-200" style="opacity:0;">
                    Stellar Traffic centraliza la gestión del tránsito, visualiza mapas en tiempo real y coordina respuestas de emergencia con una interfaz moderna e intuitiva.
                </p>
                <div class="hero-actions anim-fade-up delay-300" style="opacity:0;">
                    <a href="{{ route('visitor.map') }}" class="btn-primary">
                        Explorar mapa
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>
                    <a href="#features" class="btn-secondary">
                        Conoce más
                    </a>
                </div>
            </div>

            <div class="hero-visual anim-slide-left delay-300" style="opacity:0;">
                <!-- Floating stat 1 -->
                <div class="hero-floating-stat stat-1">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#e5e5e5,#d4d4d4);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#525252" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Monitoreo activo</div>
                </div>
                <!-- Floating stat 2 -->
                <div class="hero-floating-stat stat-2">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div class="stat-value">38%</div>
                    <div class="stat-label">Más eficiente</div>
                </div>

                <a href="{{ route('visitor.map') }}" class="hero-map-card" aria-label="Abrir mapa interactivo">
                    <img src="{{ asset('images/map_mockup.png') }}" alt="Mapa interactivo de Stellar Traffic">
                    <div class="hero-map-overlay">
                        <div class="hero-map-info">
                            <div class="hero-map-info-text">
                                <h4>Mapa en tiempo real</h4>
                                <p>Rutas, incidentes y tiempos estimados</p>
                            </div>
                            <div class="hero-map-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ MARQUEE — INSTITUCIONES ══════════════════════════════ -->
    <section class="marquee-section">
        <p class="marquee-label">Instituciones integradas en la plataforma</p>
        <div style="overflow:hidden;">
            <div class="marquee-track">
                <!-- Set 1 -->
                <div class="marquee-item"><span class="m-name">PNC</span><span class="m-desc">Policía Nacional Civil</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">VMT</span><span class="m-desc">Viceministerio de Transporte</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">CONASEVI</span><span class="m-desc">Consejo Nacional de Seguridad Vial</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">Bomberos</span><span class="m-desc">Cuerpos de Socorro</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">Conductores</span><span class="m-desc">Comunidad Vial</span><span class="m-divider"></span></div>
                <!-- Set 2 (duplicate for seamless loop) -->
                <div class="marquee-item"><span class="m-name">PNC</span><span class="m-desc">Policía Nacional Civil</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">VMT</span><span class="m-desc">Viceministerio de Transporte</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">CONASEVI</span><span class="m-desc">Consejo Nacional de Seguridad Vial</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">Bomberos</span><span class="m-desc">Cuerpos de Socorro</span><span class="m-divider"></span></div>
                <div class="marquee-item"><span class="m-name">Conductores</span><span class="m-desc">Comunidad Vial</span><span class="m-divider"></span></div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ FEATURES ══════════════════════════════ -->
    <section class="features-section" id="features">
        <div class="features-inner">
            <div class="section-header reveal">
                <div class="section-tag">
                    <span class="tag-line"></span>
                    Funcionalidades
                    <span class="tag-line"></span>
                </div>
                <h2 class="section-title">Todo lo que necesitas,<br>en un solo lugar</h2>
                <p class="section-subtitle">Herramientas poderosas diseñadas para optimizar la gestión del tránsito y la respuesta ante emergencias.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon icon-nav">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#525252" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3>Navegación en tiempo real</h3>
                    <p>Visualiza rutas, congestión y puntos de interés con datos actualizados al instante.</p>
                </div>
                <div class="feature-card reveal" style="transition-delay:0.1s;">
                    <div class="feature-icon icon-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3>Alertas al instante</h3>
                    <p>Recibe notificaciones de incidentes, accidentes y emergencias en tus rutas habituales.</p>
                </div>
                <div class="feature-card reveal" style="transition-delay:0.2s;">
                    <div class="feature-icon icon-coord">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#4f46e5" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3>Coordinación integrada</h3>
                    <p>Conecta policía, bomberos, socorristas y autoridades en un solo canal de respuesta.</p>
                </div>
                <div class="feature-card reveal" style="transition-delay:0.3s;">
                    <div class="feature-icon icon-safe">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3>Seguridad avanzada</h3>
                    <p>Tecnología y datos para prevenir siniestros y proteger vidas en cada trayecto.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ SHOWCASE ══════════════════════════════ -->
    <section class="showcase-section" id="showcase">
        <div class="showcase-inner">
            <div class="showcase-img-wrapper reveal">
                <img src="{{ asset('images/map_hero.png') }}" alt="Mapa de Stellar Traffic en acción">
                <div class="overlay-badge">
                    <span class="live-dot"></span>
                    Mapa en vivo
                </div>
            </div>
            <div class="showcase-content">
                <div class="section-tag reveal">
                    <span class="tag-line"></span>
                    Mapa interactivo
                </div>
                <h2 class="section-title reveal" style="transition-delay:0.1s;">Visibilidad total<br>de cada ruta</h2>
                <ul class="showcase-feature-list reveal" style="transition-delay:0.2s;">
                    <li>
                        <span class="check-icon"><svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                        Visualización de congestión vehicular con colores de intensidad.
                    </li>
                    <li>
                        <span class="check-icon"><svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                        Ubicación de incidentes y despliegue de unidades de emergencia.
                    </li>
                    <li>
                        <span class="check-icon"><svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                        Estimación de tiempos de arribo y rutas alternas sugeridas.
                    </li>
                    <li>
                        <span class="check-icon"><svg fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                        Puntos de cobertura de cámaras y sensores integrados al sistema.
                    </li>
                </ul>
                <a href="{{ route('visitor.map') }}" class="btn-primary reveal" style="transition-delay:0.3s;">
                    Abrir mapa interactivo
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ STATS ══════════════════════════════ -->
    <section class="stats-section noise" id="stats">
        <div class="stats-inner">
            <div class="stat-card reveal">
                <div class="stat-number" data-count="24">24/7</div>
                <div class="stat-label">Monitoreo continuo<br>sin interrupciones</div>
            </div>
            <div class="stat-card reveal" style="transition-delay:0.1s;">
                <div class="stat-number" data-count="1200">+1,200</div>
                <div class="stat-label">Incidentes atendidos<br>diariamente</div>
            </div>
            <div class="stat-card reveal" style="transition-delay:0.2s;">
                <div class="stat-number" data-count="38">38%</div>
                <div class="stat-label">Reducción en tiempos<br>de respuesta</div>
            </div>
            <div class="stat-card reveal" style="transition-delay:0.3s;">
                <div class="stat-number" data-count="5">5+</div>
                <div class="stat-label">Instituciones conectadas<br>a la plataforma</div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ HOW IT WORKS ══════════════════════════════ -->
    <section class="how-section" id="how">
        <div class="how-inner">
            <div class="section-header reveal">
                <div class="section-tag">
                    <span class="tag-line"></span>
                    ¿Cómo funciona?
                    <span class="tag-line"></span>
                </div>
                <h2 class="section-title">Tres pasos para<br>una vía más segura</h2>
                <p class="section-subtitle">Un flujo simple y eficiente que conecta a todas las partes involucradas en la gestión del tránsito.</p>
            </div>

            <div class="how-steps">
                <div class="how-step reveal">
                    <div class="step-number">01</div>
                    <h3>Monitoreo y detección</h3>
                    <p>El sistema captura datos en tiempo real de cámaras, sensores y reportes ciudadanos para detectar incidentes y congestiones.</p>
                </div>
                <div class="how-step reveal" style="transition-delay:0.15s;">
                    <div class="step-number">02</div>
                    <h3>Coordinación inmediata</h3>
                    <p>Las instituciones relevantes (PNC, bomberos, VMT) son notificadas automáticamente y coordinan la respuesta desde un panel unificado.</p>
                </div>
                <div class="how-step reveal" style="transition-delay:0.3s;">
                    <div class="step-number">03</div>
                    <h3>Resolución y reporte</h3>
                    <p>El incidente se resuelve, se documentan los detalles y los datos alimentan el análisis para mejorar la prevención futura.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ TESTIMONIALS ══════════════════════════════ -->
    <section class="testimonials-section" id="testimonials">
        <div class="testimonials-inner">
            <div class="section-header reveal">
                <div class="section-tag">
                    <span class="tag-line"></span>
                    Testimonios
                    <span class="tag-line"></span>
                </div>
                <h2 class="section-title">Lo que dicen quienes<br>ya usan la plataforma</h2>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card reveal">
                    <div class="testimonial-stars">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <p class="testimonial-text">"El tiempo de respuesta ante accidentes se redujo significativamente. La coordinación entre unidades es mucho más fluida desde que usamos Stellar Traffic."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">MR</div>
                        <div class="testimonial-author-info">
                            <h5>Marcos Reyes</h5>
                            <p>Coordinador PNC — Zona Central</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal" style="transition-delay:0.1s;">
                    <div class="testimonial-stars">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <p class="testimonial-text">"Poder visualizar el mapa con incidentes en tiempo real nos permite despachar unidades de forma más estratégica. Es una herramienta indispensable."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">LC</div>
                        <div class="testimonial-author-info">
                            <h5>Laura Contreras</h5>
                            <p>Supervisora de Despacho — Bomberos</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card reveal" style="transition-delay:0.2s;">
                    <div class="testimonial-stars">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <p class="testimonial-text">"Como conductor, las alertas anticipadas me han ayudado a evitar zonas de alto riesgo. La interfaz es limpia y muy fácil de usar."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">JP</div>
                        <div class="testimonial-author-info">
                            <h5>José Padilla</h5>
                            <p>Conductor — Usuario Regular</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ CTA ══════════════════════════════ -->
    <section class="cta-section" id="cta">
        <div class="cta-inner reveal">
            <div class="section-tag">
                <span class="tag-line"></span>
                Comienza ahora
                <span class="tag-line"></span>
            </div>
            <h2 class="section-title">¿Listo para transformar la<br>gestión del tránsito?</h2>
            <p class="cta-desc">Únete a las instituciones y ciudadanos que ya confían en Stellar Traffic para hacer las vías más seguras y eficientes.</p>
            <div class="cta-actions">
                <a href="{{ route('register') }}" class="btn-primary">
                    Crear cuenta gratuita
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('visitor.map') }}" class="btn-secondary">
                    Ver demostración
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ FOOTER ══════════════════════════════ -->
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
                    <p style="font-size:0.875rem;color:var(--gray-500);margin-bottom:1rem;">Mantente informado sobre novedades y actualizaciones de la plataforma.</p>
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

    <!-- ══════════════════════════════ SCRIPTS ══════════════════════════════ -->
    <script>
        // Header scroll effect
        const header = document.getElementById('mainHeader');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 60) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Scroll reveal
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        // Mobile menu (simple toggle)
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

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
