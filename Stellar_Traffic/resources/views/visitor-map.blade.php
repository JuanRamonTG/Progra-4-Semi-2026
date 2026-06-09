<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa visitante - Stellar Traffic</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        :root {
            --bg: #0b1121;
            --panel: rgba(15, 23, 42, 0.78);
            --panel-solid: #111827;
            --line: rgba(255, 255, 255, 0.1);
            --text: #ffffff;
            --muted: #9ca3af;
            --blue: #2563eb;
            --blue-soft: #3b82f6;
            --green: #22c55e;
            --amber: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        [hidden] {
            display: none !important;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        .glass-panel {
            background: var(--panel);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--line);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.28);
        }

        .visitor-shell {
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .page-glow {
            position: fixed;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(120px);
            opacity: 0.28;
            z-index: 0;
        }

        .page-glow.one {
            width: 360px;
            height: 360px;
            top: -120px;
            left: -80px;
            background: #2563eb;
        }

        .page-glow.two {
            width: 320px;
            height: 320px;
            right: -110px;
            bottom: 40px;
            background: #4f46e5;
        }

        .visitor-header {
            position: relative;
            z-index: 30;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            border-bottom: 1px solid var(--line);
        }

        .brand-link,
        .header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-link {
            color: var(--text);
            text-decoration: none;
        }

        .brand-link img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.22);
        }

        .brand-text span {
            display: block;
            line-height: 1.02;
            letter-spacing: 0.18em;
        }

        .brand-text .strong {
            font-size: 18px;
            font-weight: 800;
        }

        .brand-text .light {
            font-size: 17px;
            font-weight: 300;
            color: #d1d5db;
        }

        .header-copy {
            color: var(--muted);
            font-size: 13px;
            display: none;
        }

        .header-button {
            min-height: 40px;
            border-radius: 999px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.04);
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .header-button:hover {
            border-color: rgba(59, 130, 246, 0.55);
            background: rgba(59, 130, 246, 0.14);
        }

        .map-stage {
            position: relative;
            z-index: 1;
            flex: 1;
            min-height: 0;
        }

        #visitor-map {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: #0f172a;
        }

        .control-panel {
            position: absolute;
            z-index: 20;
            top: 22px;
            left: 22px;
            width: min(450px, calc(100vw - 44px));
            max-height: calc(100vh - 122px);
            overflow: auto;
            border-radius: 18px;
            padding: 14px;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
        }

        .control-panel::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari and Opera */
        }

        .panel-title {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .panel-title h1 {
            margin: 0 0 5px;
            font-size: 22px;
            line-height: 1.15;
            letter-spacing: 0;
        }

        .panel-title p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .live-pill {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: 1px solid rgba(34, 197, 94, 0.24);
            color: #86efac;
            background: rgba(34, 197, 94, 0.1);
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--green);
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.12);
        }

        .route-form {
            display: grid;
            gap: 10px;
        }

        .search-stack {
            position: relative;
            display: grid;
            gap: 8px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        .field label {
            color: #e5e7eb;
            font-size: 12px;
            font-weight: 700;
        }

        .input-row {
            height: 46px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 13px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(2, 6, 23, 0.62);
            transition: 0.2s ease;
        }

        .input-row:focus-within {
            border-color: rgba(59, 130, 246, 0.72);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }

        .point-badge {
            flex: 0 0 auto;
            width: 26px;
            height: 26px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            color: #fff;
        }

        .point-badge.origin {
            background: var(--blue);
        }

        .point-badge.destination {
            background: var(--amber);
        }

        .input-row input {
            width: 100%;
            min-width: 0;
            color: var(--text);
            background: transparent;
            border: 0;
            outline: 0;
            font-size: 14px;
        }

        .input-row input::placeholder {
            color: #6b7280;
        }

        .suggestions {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            z-index: 40;
            max-height: 260px;
            overflow: auto;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(15, 23, 42, 0.96);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.34);
        }

        .suggestion-item,
        .recent-item {
            width: 100%;
            display: grid;
            grid-template-columns: 50px minmax(0, 1fr);
            gap: 12px;
            padding: 12px;
            color: #fff;
            text-align: left;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .suggestion-item:hover,
        .recent-item:hover {
            background: rgba(37, 99, 235, 0.12);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .place-icon {
            width: 50px;
            height: 50px;
            border-radius: 6px;
            display: grid;
            place-items: center;
            color: #bfdbfe;
            background: rgba(37, 99, 235, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.3);
            flex-shrink: 0;
        }

        .place-icon svg {
            width: 24px;
            height: 24px;
        }

        .place-main,
        .place-meta {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .place-main {
            font-size: 13px;
            font-weight: 700;
        }

        .place-meta {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
        }

        .panel-section {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0;
        }

        .section-header span {
            color: var(--muted);
            font-size: 12px;
        }

        .recent-list,
        .destination-card,
        .steps-list {
            display: grid;
            gap: 8px;
        }

        .empty-note {
            margin: 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .action-button,
        .primary-button {
            min-height: 42px;
            border: 0;
            border-radius: 13px;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .action-button {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .action-button:hover,
        .action-button.active {
            border-color: rgba(59, 130, 246, 0.62);
            background: rgba(37, 99, 235, 0.18);
        }

        .primary-button {
            width: 100%;
            background: var(--blue);
            box-shadow: 0 12px 26px rgba(37, 99, 235, 0.28);
        }

        .primary-button:hover {
            background: var(--blue-soft);
        }

        .primary-button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
            box-shadow: none;
        }

        .status-message {
            min-height: 19px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        #history-section {
            color: #fff;
            cursor: pointer;
            transition: 0.2s ease;
            flex-shrink: 0;
        }

        .close-sidebar:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .route-summary {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: grid;
            gap: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .summary-item {
            border-radius: 14px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .summary-item span {
            display: block;
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 5px;
        }

        .summary-item strong {
            display: block;
            font-size: 20px;
            line-height: 1.15;
        }

        .route-options {
            display: grid;
            gap: 8px;
        }

        .route-option {
            width: 100%;
            text-align: left;
            border-radius: 13px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(2, 6, 23, 0.45);
            color: #fff;
            padding: 11px 12px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .route-option:hover,
        .route-option.active {
            border-color: rgba(59, 130, 246, 0.68);
            background: rgba(37, 99, 235, 0.18);
        }

        .route-option strong,
        .route-option span {
            display: block;
        }

        .route-option strong {
            font-size: 13px;
        }

        .route-option span {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
        }

        .destination-card {
            padding: 12px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.05);
        }

        .destination-card strong,
        .destination-card span {
            display: block;
        }

        .destination-card strong {
            font-size: 14px;
            line-height: 1.35;
        }

        .destination-card span {
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }

        .steps-list {
            counter-reset: route-step;
        }

        .step-item {
            display: grid;
            grid-template-columns: 24px minmax(0, 1fr);
            gap: 9px;
            align-items: start;
            color: #d1d5db;
            font-size: 12px;
            line-height: 1.45;
        }

        .step-item::before {
            counter-increment: route-step;
            content: counter(route-step);
            width: 22px;
            height: 22px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            color: #bfdbfe;
            background: rgba(37, 99, 235, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.24);
            font-size: 11px;
            font-weight: 800;
        }

        .map-tools {
            position: absolute;
            right: 22px;
            bottom: 22px;
            z-index: 20;
            display: grid;
            gap: 10px;
        }

        .tool-button {
            width: 48px;
            height: 48px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(15, 23, 42, 0.88);
            color: #fff;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
            transition: 0.2s ease;
        }

        .tool-button:hover {
            background: rgba(37, 99, 235, 0.92);
            transform: translateY(-1px);
        }

        .zoom-group {
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(15, 23, 42, 0.88);
            border-radius: 24px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
            overflow: hidden;
            width: 48px;
        }

        .zoom-button {
            width: 48px;
            height: 44px;
            border: 0;
            background: transparent;
            color: #fff;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .zoom-button:first-child {
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            height: 46px;
            padding-top: 2px;
        }

        .zoom-button:last-child {
            height: 46px;
            padding-bottom: 2px;
        }

        .zoom-button:hover {
            background: rgba(37, 99, 235, 0.92);
        }

        .zoom-button svg {
            width: 18px;
            height: 18px;
        }

        .tool-button svg,
        .header-button svg,
        .action-button svg,
        .primary-button svg {
            width: 18px;
            height: 18px;
        }

        .leaflet-control-zoom a {
            background: rgba(15, 23, 42, 0.92) !important;
            color: #fff !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        .leaflet-control-attribution {
            background: rgba(15, 23, 42, 0.72) !important;
            color: #d1d5db !important;
        }

        .leaflet-control-attribution a {
            color: #93c5fd !important;
        }

        .marker-pin {
            position: relative;
            width: 30px;
            height: 30px;
            border-radius: 999px 999px 999px 4px;
            transform: rotate(-45deg);
            display: grid;
            place-items: center;
            border: 2px solid #fff;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.42);
        }

        .marker-pin span {
            transform: rotate(45deg);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
        }

        .marker-pin.origin {
            background: var(--blue);
        }

        .marker-pin.destination {
            background: var(--amber);
        }

        .marker-pin.current {
            background: var(--green);
        }

        .location-marker {
            position: relative;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
        }

        .location-marker::before {
            content: "";
            position: absolute;
            inset: 2px;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.16);
            animation: locationPulse 1.9s ease-out infinite;
        }

        .location-core {
            position: relative;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: var(--green);
            border: 3px solid #fff;
            box-shadow: 0 0 22px rgba(34, 197, 94, 0.62);
        }

        .location-heading {
            position: absolute;
            top: -5px;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 12px solid #86efac;
            transform-origin: 50% 24px;
        }

        @keyframes locationPulse {
            0% {
                transform: scale(0.45);
                opacity: 0.85;
            }

            100% {
                transform: scale(1.8);
                opacity: 0;
            }
        }

        @media (min-width: 900px) {
            .header-copy {
                display: block;
            }
        }

        @media (max-width: 760px) {
            body {
                overflow: auto;
            }

            .visitor-shell {
                min-height: 100dvh;
            }

            .visitor-header {
                height: auto;
                min-height: 70px;
                padding: 14px 16px;
                gap: 12px;
            }

            .header-button {
                width: 42px;
                padding: 0;
            }

            .header-button span {
                display: none;
            }

            .map-stage {
                min-height: calc(100dvh - 70px);
            }

            .control-panel {
                position: relative;
                top: auto;
                left: auto;
                width: calc(100% - 24px);
                margin: 12px;
                max-height: none;
            }

            .details-sidebar {
                position: fixed;
                top: 0;
                left: -320px;
                width: 320px;
                height: 100dvh;
                margin: 0;
                border-radius: 0;
            }

            #visitor-map {
                top: 0;
                height: calc(100dvh - 70px);
            }

            .map-tools {
                right: 14px;
                bottom: 14px;
            }

            .panel-title {
                flex-direction: column;
                gap: 10px;
            }

            .suggestions {
                position: static;
                max-height: 220px;
            }

            .quick-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>

<body>
    <div class="visitor-shell">
        <div class="page-glow one"></div>
        <div class="page-glow two"></div>

        <header class="visitor-header glass-panel">
            <a href="{{ route('home') }}" class="brand-link" aria-label="Volver al inicio de Stellar Traffic">
                <img src="{{ asset('images/logo.png') }}" alt="Stellar Traffic Logo">
                <span class="brand-text">
                    <span class="strong">STELLAR</span>
                    <span class="light">TRAFFIC</span>
                </span>
            </a>
            <p class="header-copy">Mapa publico para consultar ubicaciones y planificar rutas.</p>
            <div class="header-actions">
                <a href="{{ route('home') }}" class="header-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Inicio</span>
                </a>
                <a href="{{ route('login') }}" class="header-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    <span>Ingresar</span>
                </a>
            </div>
        </header>

        <main class="map-stage">
            <div id="visitor-map" aria-label="Mapa interactivo para visitantes"></div>

            <section class="control-panel glass-panel" aria-label="Planificador de rutas">
                <form id="route-form" class="route-form">
                    <div class="search-stack">
                        <div class="field">
                            <label for="origin-input">Origen</label>
                            <div class="input-row">
                                <span class="point-badge origin">A</span>
                                <input id="origin-input" type="text" autocomplete="off"
                                    placeholder="Buscar punto de origen">
                            </div>
                        </div>
                        <div id="origin-suggestions" class="suggestions" hidden></div>
                    </div>

                    <div class="search-stack">
                        <div class="field">
                            <label for="destination-input">Destino</label>
                            <div class="input-row">
                                <span class="point-badge destination">B</span>
                                <input id="destination-input" type="text" autocomplete="off"
                                    placeholder="Buscar destino o punto de interes">
                            </div>
                        </div>
                        <div id="destination-suggestions" class="suggestions" hidden></div>
                    </div>

                    <div class="quick-actions">
                        <button id="select-origin" class="action-button active" type="button">
                            Marcar A
                        </button>
                        <button id="select-destination" class="action-button" type="button">
                            Marcar B
                        </button>
                        <button id="clear-button" class="action-button" type="button">
                            Limpiar
                        </button>
                    </div>

                    <button id="sidebar-toggle" class="primary-button" type="button"
                        style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12); color: #fff; margin-bottom: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            style="width: 18px; height: 18px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ver Historial
                    </button>

                    <button id="route-button" class="primary-button" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6l6 6-6 6" />
                        </svg>
                        Obtener rutas
                    </button>
                </form>

                <p id="status-message" class="status-message">Haz clic en el mapa para colocar el punto A.</p>

                <section id="history-section" class="panel-section" hidden aria-label="Lugares recientes">
                    <div class="section-header">
                        <h2>Historial</h2>
                        <span id="recent-count">0 guardados</span>
                    </div>
                    <div id="recent-list" class="recent-list">
                        <p class="empty-note">Tus busquedas y destinos recientes apareceran aqui.</p>
                    </div>
                </section>

                <div id="route-summary" class="route-summary" hidden>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span>Distancia</span>
                            <strong id="distance-value">--</strong>
                        </div>
                        <div class="summary-item">
                            <span>Tiempo estimado</span>
                            <strong id="duration-value">--</strong>
                        </div>
                    </div>
                    <div id="route-options" class="route-options"></div>
                </div>

                <section id="destination-section" class="panel-section" hidden aria-label="Informacion del destino">
                    <div class="section-header">
                        <h2>Informacion del destino</h2>
                    </div>
                    <div id="destination-card" class="destination-card"></div>
                </section>

                <section id="steps-section" class="panel-section" hidden aria-label="Indicaciones basicas">
                    <div class="section-header">
                        <h2>Indicaciones basicas</h2>
                    </div>
                    <div id="steps-list" class="steps-list"></div>
                </section>
            </section>

            <div class="map-tools" aria-label="Herramientas del mapa">
                <button id="recenter-button" class="tool-button" type="button" title="Centrar mapa">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h11v12H4z" />
                    </svg>
                </button>
                <button id="locate-fab" class="tool-button" type="button" title="Usar mi ubicacion">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8a4 4 0 100 8 4 4 0 000-8zm0-5v2m0 14v2m9-9h-2M5 12H3" />
                    </svg>
                </button>
                <div class="zoom-group">
                    <button id="zoom-in" class="zoom-button" type="button" title="Acercar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                    <button id="zoom-out" class="zoom-button" type="button" title="Alejar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const defaultCenter = [13.6929, -89.2182];
            const state = {
                map: null,
                origin: null,
                destination: null,
                currentLocation: null,
                originMarker: null,
                destinationMarker: null,
                currentMarker: null,
                routeLayers: [],
                activeRoutes: [],
                watchId: null,
                lastLocation: null,
                selectedDestinationPlace: null,
                selectedRoute: 0,
                picking: 'origin',
            };

            const els = {
                originInput: document.getElementById('origin-input'),
                destinationInput: document.getElementById('destination-input'),
                form: document.getElementById('route-form'),
                locateButton: document.getElementById('locate-button'),
                locateFab: document.getElementById('locate-fab'),
                selectOrigin: document.getElementById('select-origin'),
                selectDestination: document.getElementById('select-destination'),
                clearButton: document.getElementById('clear-button'),
                routeButton: document.getElementById('route-button'),
                recenterButton: document.getElementById('recenter-button'),
                status: document.getElementById('status-message'),
                summary: document.getElementById('route-summary'),
                distance: document.getElementById('distance-value'),
                duration: document.getElementById('duration-value'),
                routeOptions: document.getElementById('route-options'),
                originSuggestions: document.getElementById('origin-suggestions'),
                destinationSuggestions: document.getElementById('destination-suggestions'),
                sidebarToggle: document.getElementById('sidebar-toggle'),
                historySection: document.getElementById('history-section'),
                recentList: document.getElementById('recent-list'),
                recentCount: document.getElementById('recent-count'),
                destinationSection: document.getElementById('destination-section'),
                destinationCard: document.getElementById('destination-card'),
                stepsSection: document.getElementById('steps-section'),
                stepsList: document.getElementById('steps-list'),
            };

            const RECENTS_KEY = 'stellarTraffic.visitorMap.recents';
            const placeIconSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.438 7-11a7 7 0 10-14 0c0 6.562 7 11 7 11z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10.5h.01" /></svg>';

            const setStatus = (message) => {
                els.status.textContent = message;
            };

            const showHistorySection = () => {
                els.historySection.removeAttribute('hidden');
            };

            const hideHistorySection = () => {
                els.historySection.setAttribute('hidden', '');
            };

            const debounce = (callback, delay = 350) => {
                let timer = null;
                return (...args) => {
                    window.clearTimeout(timer);
                    timer = window.setTimeout(() => callback(...args), delay);
                };
            };

            const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));

            const summarizeAddress = (properties) => {
                const parts = [
                    properties.street,
                    properties.housenumber,
                    properties.city || properties.county,
                    properties.state,
                    properties.country,
                ].filter(Boolean);
                return parts.join(', ') || properties.name || 'Ubicacion sin direccion detallada';
            };

            const normalizePhotonFeature = (feature) => {
                const properties = feature.properties || {};
                const [lng, lat] = feature.geometry.coordinates;
                return {
                    name: properties.name || properties.street || properties.city || 'Resultado sin nombre',
                    category: properties.osm_value || properties.type || properties.osm_key || 'Lugar',
                    address: summarizeAddress(properties),
                    lat,
                    lng,
                };
            };

            const normalizeNominatimResult = (result) => {
                const category = result.type || result.category || 'Lugar';
                const name = result.name || result.display_name?.split(',')[0] || 'Resultado sin nombre';
                const address = result.display_name || `${result.lat}, ${result.lon}`;
                return {
                    name,
                    category,
                    address,
                    lat: Number(result.lat),
                    lng: Number(result.lon),
                };
            };

            const fallbackPlace = (lat, lng, label) => ({
                name: label || `${lat.toFixed(5)}, ${lng.toFixed(5)}`,
                category: 'Ubicacion',
                address: `${lat.toFixed(5)}, ${lng.toFixed(5)}`,
                lat,
                lng,
            });

            const loadRecents = () => {
                try {
                    const raw = localStorage.getItem(RECENTS_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (error) {
                    return [];
                }
            };

            const saveRecents = (items) => {
                localStorage.setItem(RECENTS_KEY, JSON.stringify(items.slice(0, 10)));
            };

            const rememberPlace = (place) => {
                if (!place || !Number.isFinite(place.lat) || !Number.isFinite(place.lng)) return;
                const normalized = {
                    name: place.name,
                    category: place.category || 'Lugar',
                    address: place.address || `${place.lat.toFixed(5)}, ${place.lng.toFixed(5)}`,
                    lat: place.lat,
                    lng: place.lng,
                    accessedAt: new Date().toISOString(),
                };
                const existing = loadRecents().filter((item) => {
                    const sameCoords = Math.abs(item.lat - normalized.lat) < 0.00001 && Math.abs(item.lng - normalized.lng) < 0.00001;
                    return !(sameCoords || item.name === normalized.name);
                });
                saveRecents([normalized, ...existing]);
                renderRecents();
            };

            const renderDestination = (place) => {
                if (!place) {
                    els.destinationSection.hidden = true;
                    els.destinationCard.innerHTML = '';
                    return;
                }
                els.destinationSection.hidden = false;
                els.destinationCard.innerHTML = `<strong>${escapeHtml(place.name)}</strong><span>${escapeHtml(place.category)} - ${escapeHtml(place.address)}</span>`;
            };

            const renderRecents = () => {
                const recents = loadRecents();
                els.recentCount.textContent = `${recents.length} guardados`;
                if (!recents.length) {
                    els.recentList.innerHTML = '<p class="empty-note">Tus busquedas y destinos recientes apareceran aqui.</p>';
                    return;
                }
                els.recentList.innerHTML = '';
                recents.forEach((place) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'recent-item';
                    button.innerHTML = `<span class="place-icon">${placeIconSvg}</span><span><span class="place-main">${escapeHtml(place.name)}</span><span class="place-meta">${escapeHtml(place.category)} - ${escapeHtml(place.address)}</span></span>`;
                    button.addEventListener('click', () => {
                        state.map.setView([place.lat, place.lng], 16);
                        setPoint('destination', place, place);
                        rememberPlace(place);
                    });
                    els.recentList.appendChild(button);
                });
            };

            const formatDistance = (meters) => {
                if (meters < 1000) return `${Math.round(meters)} m`;
                return `${(meters / 1000).toFixed(1)} km`;
            };

            const formatDuration = (seconds) => {
                const minutes = Math.round(seconds / 60);
                if (minutes < 60) return `${minutes} min`;
                const hours = Math.floor(minutes / 60);
                const rest = minutes % 60;
                return rest ? `${hours} h ${rest} min` : `${hours} h`;
            };

            const makeIcon = (type, label) => window.L.divIcon({
                className: '',
                html: `<div class="marker-pin ${type}"><span>${label}</span></div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 30],
            });

            const makeLocationIcon = (heading = 0) => window.L.divIcon({
                className: '',
                html: `<div class="location-marker"><span class="location-heading" style="transform: rotate(${heading}deg)"></span><span class="location-core"></span></div>`,
                iconSize: [38, 38],
                iconAnchor: [19, 19],
            });

            const calculateBearing = (from, to) => {
                if (!from) return 0;
                const toRad = (value) => value * Math.PI / 180;
                const toDeg = (value) => value * 180 / Math.PI;
                const lat1 = toRad(from.lat);
                const lat2 = toRad(to.lat);
                const deltaLng = toRad(to.lng - from.lng);
                const y = Math.sin(deltaLng) * Math.cos(lat2);
                const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(deltaLng);
                return (toDeg(Math.atan2(y, x)) + 360) % 360;
            };

            const setPicking = (mode) => {
                state.picking = mode;
                els.selectOrigin.classList.toggle('active', mode === 'origin');
                els.selectDestination.classList.toggle('active', mode === 'destination');
                setStatus(mode === 'origin'
                    ? 'Haz clic en el mapa para colocar el punto A.'
                    : 'Haz clic en el mapa para colocar el punto B.');
            };

            const reverseLabel = async (lat, lng, fallback) => {
                try {
                    const url = new URL('https://nominatim.openstreetmap.org/reverse');
                    url.search = new URLSearchParams({
                        lat,
                        lon: lng,
                        format: 'json',
                        zoom: 17,
                    });
                    const response = await fetch(url);
                    const data = await response.json();
                    return data.display_name || fallback;
                } catch (error) {
                    return fallback;
                }
            };

            const setPoint = async (type, latlng, place = null) => {
                const coords = { lat: latlng.lat, lng: latlng.lng };
                const fallback = `${coords.lat.toFixed(5)}, ${coords.lng.toFixed(5)}`;
                const displayName = place?.name || place?.label || await reverseLabel(coords.lat, coords.lng, fallback);
                const fullPlace = place?.name
                    ? { ...place, lat: coords.lat, lng: coords.lng }
                    : fallbackPlace(coords.lat, coords.lng, displayName);
                const isOrigin = type === 'origin';
                const markerKey = isOrigin ? 'originMarker' : 'destinationMarker';
                const pointLabel = isOrigin ? 'A' : 'B';

                state[type] = coords;
                if (state[markerKey]) state[markerKey].remove();
                state[markerKey] = window.L.marker([coords.lat, coords.lng], {
                    icon: makeIcon(type, pointLabel),
                }).addTo(state.map).bindPopup(displayName);

                if (isOrigin) {
                    els.originInput.value = displayName;
                    setPicking('destination');
                } else {
                    els.destinationInput.value = displayName;
                    state.selectedDestinationPlace = fullPlace;
                    renderDestination(fullPlace);
                    rememberPlace(fullPlace);
                    setStatus('Destino definido. Puedes obtener rutas sugeridas.');
                }
            };

            const searchPlaces = async (query, limit = 5) => {
                try {
                    const url = new URL('https://photon.komoot.io/api/');
                    url.search = new URLSearchParams({
                        q: query,
                        limit: String(limit),
                        lang: 'es',
                    });
                    const response = await fetch(url);
                    const data = await response.json();
                    const results = (data.features || []).map(normalizePhotonFeature);
                    if (results.length) return results;
                } catch (error) {
                    // Try the fallback below.
                }

                const fallbackUrl = new URL('https://nominatim.openstreetmap.org/search');
                fallbackUrl.search = new URLSearchParams({
                    q: query,
                    format: 'json',
                    addressdetails: '1',
                    limit: String(limit),
                });
                const response = await fetch(fallbackUrl);
                const results = await response.json();
                return (results || []).map(normalizeNominatimResult);
            };

            const geocode = async (query) => {
                const results = await searchPlaces(query, 1);
                if (!results.length) throw new Error('No se encontraron resultados.');
                return results[0];
            };

            const hideSuggestions = () => {
                els.originSuggestions.hidden = true;
                els.destinationSuggestions.hidden = true;
            };

            const renderSuggestions = (target, places) => {
                const container = target === 'origin' ? els.originSuggestions : els.destinationSuggestions;
                container.innerHTML = '';
                if (!places.length) {
                    container.hidden = true;
                    return;
                }
                places.forEach((place) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'suggestion-item';
                    button.innerHTML = `<span class="place-icon">${placeIconSvg}</span><span><span class="place-main">${escapeHtml(place.name)}</span><span class="place-meta">${escapeHtml(place.category)} - ${escapeHtml(place.address)}</span></span>`;
                    button.addEventListener('click', async () => {
                        hideSuggestions();
                        clearRoutes();
                        state.map.setView([place.lat, place.lng], 16);
                        await setPoint(target, place, place);
                        rememberPlace(place);
                    });
                    container.appendChild(button);
                });
                container.hidden = false;
            };

            const resolveInputs = async () => {
                if (!state.origin && els.originInput.value.trim()) {
                    setStatus('Buscando origen...');
                    const result = await geocode(els.originInput.value.trim());
                    await setPoint('origin', result, result);
                }

                if (!state.destination && els.destinationInput.value.trim()) {
                    setStatus('Buscando destino...');
                    const result = await geocode(els.destinationInput.value.trim());
                    await setPoint('destination', result, result);
                }
            };

            const clearRoutes = () => {
                state.routeLayers.forEach((layer) => layer.remove());
                state.routeLayers = [];
                state.activeRoutes = [];
                els.summary.hidden = true;
                els.distance.textContent = '--';
                els.duration.textContent = '--';
                els.routeOptions.innerHTML = '';
                els.stepsSection.hidden = true;
                els.stepsList.innerHTML = '';
            };

            const drawRoutes = (routes) => {
                clearRoutes();
                state.activeRoutes = routes;
                routes.forEach((route, index) => {
                    const coords = route.geometry.coordinates.map(([lng, lat]) => [lat, lng]);
                    const layer = window.L.polyline(coords, {
                        color: index === 0 ? '#3b82f6' : '#94a3b8',
                        weight: index === 0 ? 7 : 5,
                        opacity: index === 0 ? 0.95 : 0.55,
                    }).addTo(state.map);
                    state.routeLayers.push(layer);
                });

                state.selectedRoute = 0;
                selectRoute(routes, 0);
            };

            const selectRoute = (routes, index) => {
                state.selectedRoute = index;
                state.routeLayers.forEach((layer, layerIndex) => {
                    layer.setStyle({
                        color: layerIndex === index ? '#3b82f6' : '#94a3b8',
                        weight: layerIndex === index ? 7 : 5,
                        opacity: layerIndex === index ? 0.95 : 0.55,
                    });
                });

                const route = routes[index];
                els.distance.textContent = formatDistance(route.distance);
                els.duration.textContent = formatDuration(route.duration);
                els.summary.hidden = false;

                els.routeOptions.innerHTML = '';
                routes.forEach((candidate, candidateIndex) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `route-option${candidateIndex === index ? ' active' : ''}`;
                    button.innerHTML = `<strong>Ruta ${candidateIndex + 1}</strong><span>${formatDistance(candidate.distance)} - ${formatDuration(candidate.duration)}</span>`;
                    button.addEventListener('click', () => selectRoute(routes, candidateIndex));
                    els.routeOptions.appendChild(button);
                });

                renderSteps(route);
                state.map.fitBounds(state.routeLayers[index].getBounds(), { padding: [70, 70] });
            };

            const describeManeuver = (step) => {
                const type = step.maneuver?.type || 'continue';
                const modifier = step.maneuver?.modifier || '';
                const road = step.name ? ` por ${step.name}` : '';
                const labels = {
                    depart: 'Inicia el recorrido',
                    arrive: 'Llega al destino',
                    turn: modifier.includes('left') ? 'Gira a la izquierda' : modifier.includes('right') ? 'Gira a la derecha' : 'Gira',
                    continue: 'Continua',
                    merge: 'Incorporate',
                    roundabout: 'Toma la rotonda',
                    fork: 'Mantente en la bifurcacion',
                    end: 'Continua hasta el final de la via',
                };
                return `${labels[type] || 'Continua'}${road} (${formatDistance(step.distance)})`;
            };

            const renderSteps = (route) => {
                const steps = route.legs?.flatMap((leg) => leg.steps || []) || [];
                const usefulSteps = steps.filter((step) => step.distance > 15).slice(0, 5);
                if (!usefulSteps.length) {
                    els.stepsSection.hidden = true;
                    els.stepsList.innerHTML = '';
                    return;
                }
                els.stepsSection.hidden = false;
                els.stepsList.innerHTML = '';
                usefulSteps.forEach((step) => {
                    const item = document.createElement('div');
                    item.className = 'step-item';
                    item.textContent = describeManeuver(step);
                    els.stepsList.appendChild(item);
                });
            };

            const requestRoutes = async () => {
                els.routeButton.disabled = true;
                try {
                    await resolveInputs();

                    if (!state.origin || !state.destination) {
                        setStatus('Define un origen A y un destino B para calcular la ruta.');
                        return;
                    }

                    setStatus('Calculando rutas sugeridas...');
                    const coordinates = `${state.origin.lng},${state.origin.lat};${state.destination.lng},${state.destination.lat}`;
                    const url = `https://router.project-osrm.org/route/v1/driving/${coordinates}?overview=full&geometries=geojson&alternatives=true&steps=true`;
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data.code !== 'Ok' || !data.routes.length) {
                        throw new Error('No fue posible calcular una ruta.');
                    }
                    drawRoutes(data.routes.slice(0, 3));
                    setStatus('Ruta lista. Puedes seleccionar una alternativa o seguir explorando el mapa.');
                } catch (error) {
                    clearRoutes();
                    setStatus('No se pudo calcular la ruta. Selecciona una sugerencia valida o marca A y B en el mapa.');
                } finally {
                    els.routeButton.disabled = false;
                }
            };

            const locateUser = () => {
                if (!navigator.geolocation) {
                    setStatus('Tu navegador no permite geolocalizacion.');
                    return;
                }

                setStatus('Consultando tu ubicacion actual...');
                if (state.watchId) {
                    navigator.geolocation.clearWatch(state.watchId);
                }

                let firstFix = true;
                state.watchId = navigator.geolocation.watchPosition(async (position) => {
                    const coords = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    const heading = Number.isFinite(position.coords.heading)
                        ? position.coords.heading
                        : calculateBearing(state.lastLocation, coords);
                    state.currentLocation = coords;
                    state.lastLocation = coords;

                    if (state.currentMarker) state.currentMarker.remove();
                    state.currentMarker = window.L.marker([coords.lat, coords.lng], {
                        icon: makeLocationIcon(heading),
                    }).addTo(state.map).bindPopup(`Tu ubicacion actual${Math.round(heading) ? ` - rumbo ${Math.round(heading)} grados` : ''}`);

                    if (firstFix) {
                        state.map.setView([coords.lat, coords.lng], 15);
                        setStatus('Seguimiento GPS activo. Tu ubicacion se muestra en el mapa.');
                        firstFix = false;
                    }
                }, () => {
                    setStatus('No se pudo obtener tu ubicacion. Verifica los permisos del navegador.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 15000,
                });
            };

            const clearAll = () => {
                ['originMarker', 'destinationMarker'].forEach((key) => {
                    if (state[key]) state[key].remove();
                    state[key] = null;
                });
                state.origin = null;
                state.destination = null;
                state.selectedDestinationPlace = null;
                els.originInput.value = '';
                els.destinationInput.value = '';
                renderDestination(null);
                clearRoutes();
                setPicking('origin');
            };

            const init = () => {
                if (!window.L) {
                    setStatus('No se pudo cargar el mapa interactivo.');
                    return;
                }

                state.map = window.L.map('visitor-map', {
                    zoomControl: false,
                }).setView(defaultCenter, 13);

                // Auto-geolocalizar al cargar
                locateUser();

                window.L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 20,
                }).addTo(state.map);

                state.map.on('click', (event) => {
                    clearRoutes();
                    setPoint(state.picking, event.latlng);
                });

                els.selectOrigin.addEventListener('click', () => setPicking('origin'));
                els.selectDestination.addEventListener('click', () => setPicking('destination'));
                if (els.locateButton) els.locateButton.addEventListener('click', locateUser);
                els.locateFab.addEventListener('click', locateUser);
                document.getElementById('zoom-in').addEventListener('click', () => state.map.zoomIn());
                document.getElementById('zoom-out').addEventListener('click', () => state.map.zoomOut());
                els.sidebarToggle.addEventListener('click', () => {
                    if (els.historySection.hasAttribute('hidden')) {
                        showHistorySection();
                    } else {
                        hideHistorySection();
                    }
                });
                els.clearButton.addEventListener('click', clearAll);
                els.form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    requestRoutes();
                });
                els.recenterButton.addEventListener('click', () => {
                    const target = state.currentLocation || defaultCenter;
                    state.map.setView([target.lat || target[0], target.lng || target[1]], state.currentLocation ? 15 : 13);
                });

                const handleAutocomplete = debounce(async (target, value) => {
                    if (value.trim().length < 3) {
                        renderSuggestions(target, []);
                        return;
                    }
                    try {
                        const results = await searchPlaces(value.trim(), 6);
                        renderSuggestions(target, results);
                    } catch (error) {
                        renderSuggestions(target, []);
                    }
                });

                els.originInput.addEventListener('input', (event) => {
                    state.origin = null;
                    clearRoutes();
                    handleAutocomplete('origin', event.target.value);
                });
                els.destinationInput.addEventListener('input', (event) => {
                    state.destination = null;
                    state.selectedDestinationPlace = null;
                    renderDestination(null);
                    clearRoutes();
                    handleAutocomplete('destination', event.target.value);
                });

                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.search-stack')) hideSuggestions();
                });

                renderRecents();
            };

            init();
        });
    </script>
</body>

</html>