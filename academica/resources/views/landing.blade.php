<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stellar Trafic - Gestión eficiente del tránsito</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b1121;
            color: #ffffff;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .text-gradient {
            background: linear-gradient(90deg, #ffffff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden">
    
    <!-- Background Glow Effects -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] rounded-full bg-indigo-500/10 blur-[100px] pointer-events-none"></div>

    <!-- Header -->
    <header class="w-full z-50 px-6 py-4 lg:px-12 glass-panel border-b-0 sticky top-0">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Stellar Trafic Logo" class="h-10 w-10 object-contain rounded-xl shadow-lg shadow-blue-500/20">
                <div class="leading-tight">
                    <span class="block text-xl font-bold tracking-widest text-white">STELLAR</span>
                    <span class="block text-xl font-light tracking-widest text-gray-300">TRAFIC</span>
                </div>
            </div>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-300">
                <a href="#" class="hover:text-white transition-colors duration-200">Inicio</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Cobertura</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Emergencias</a>
                <a href="#" class="hover:text-white transition-colors duration-200">Acerca de</a>
            </nav>
            
            <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium py-2 px-6 rounded-full transition-all duration-300 shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(59,130,246,0.6)] hover:-translate-y-0.5">
                Iniciar sesión
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow z-10">
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <h1 class="text-5xl lg:text-6xl font-bold leading-tight tracking-tight">
                    Gestión eficiente del <br />
                    <span class="text-gradient">tránsito y la seguridad vial</span>
                </h1>
                <p class="text-lg text-gray-300 leading-relaxed max-w-xl">
                    Stellar Trafic es una aplicación diseñada para gestionar y monitorear el tránsito vehicular de forma eficiente. Permite visualizar mapas en tiempo real, registrar información importante y mejorar el control y la seguridad vial mediante una interfaz moderna e intuitiva.
                </p>
                <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full border border-gray-600 text-white font-medium hover:bg-white/5 transition-all duration-300 group">
                    Conoce más
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            
            <a href="{{ route('visitor.map') }}" class="relative group block focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-4 focus:ring-offset-[#0b1121] rounded-[2rem]" aria-label="Abrir mapa interactivo para visitantes">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[2rem] blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                <img src="{{ asset('images/map_mockup.png') }}" alt="Abrir mapa interactivo de Stellar Traffic" class="relative rounded-[2rem] border border-gray-700/50 shadow-2xl object-cover w-full h-[400px] lg:h-[500px] transition-transform duration-300 group-hover:-translate-y-1">
                <div class="absolute bottom-5 left-5 right-5 glass-panel rounded-2xl px-5 py-4 flex items-center justify-between gap-4">
                    <div>
                        <span class="block text-sm font-semibold text-white">Explorar mapa</span>
                        <span class="block text-xs text-gray-400">Ubicaciones, rutas y tiempos estimados</span>
                    </div>
                    <span class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center shadow-[0_0_20px_rgba(37,99,235,0.45)] group-hover:bg-blue-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </a>
        </section>

        <!-- Instituciones Integradas -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-12">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">Instituciones Integradas</h3>
            <div class="glass-panel rounded-3xl p-8 flex flex-wrap lg:flex-nowrap items-center justify-between gap-6">
                <div class="text-center w-1/2 lg:w-auto">
                    <div class="text-lg font-bold text-white mb-1">PNC</div>
                    <div class="text-sm text-gray-400">Policía Nacional Civil</div>
                </div>
                <div class="hidden lg:block w-px h-12 bg-gray-700/50"></div>
                <div class="text-center w-1/2 lg:w-auto">
                    <div class="text-lg font-bold text-white mb-1">VMT</div>
                    <div class="text-sm text-gray-400">Viceministerio de Transporte</div>
                </div>
                <div class="hidden lg:block w-px h-12 bg-gray-700/50"></div>
                <div class="text-center w-1/2 lg:w-auto">
                    <div class="text-lg font-bold text-white mb-1">CONASEVI</div>
                    <div class="text-sm text-gray-400">Consejo Nacional de Seguridad Vial</div>
                </div>
                <div class="hidden lg:block w-px h-12 bg-gray-700/50"></div>
                <div class="text-center w-1/2 lg:w-auto">
                    <div class="text-lg font-bold text-white mb-1">Cuerpos de socorro</div>
                    <div class="text-sm text-gray-400">Socorristas</div>
                </div>
                <div class="hidden lg:block w-px h-12 bg-gray-700/50"></div>
                <div class="text-center w-full lg:w-auto mt-4 lg:mt-0">
                    <div class="text-lg font-bold text-white mb-1">Conductores</div>
                    <div class="text-sm text-gray-400">Comunidad Vial</div>
                </div>
            </div>
        </section>

        <!-- Todo lo que necesitas -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-24">
            <div class="mb-16">
                <h2 class="text-2xl lg:text-3xl font-bold mb-4">Todo lo que necesitas <br /> en un solo lugar</h2>
                <div class="w-12 h-1 bg-blue-500 rounded-full"></div>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-colors border border-transparent hover:border-white/[0.05]">
                    <div class="w-10 h-10 mb-4 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 group-hover:scale-110 group-hover:bg-blue-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Navega seguro</h4>
                    <p class="text-sm text-gray-400">Rutas y tráfico en tiempo real.</p>
                </div>
                <!-- Card 2 -->
                <div class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-colors border border-transparent hover:border-white/[0.05]">
                    <div class="w-10 h-10 mb-4 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 group-hover:scale-110 group-hover:bg-red-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Alertas al instante</h4>
                    <p class="text-sm text-gray-400">Incidentes y emergencias en tu ruta.</p>
                </div>
                <!-- Card 3 -->
                <div class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-colors border border-transparent hover:border-white/[0.05]">
                    <div class="w-10 h-10 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400 group-hover:scale-110 group-hover:bg-purple-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Coordinación</h4>
                    <p class="text-sm text-gray-400">Respuesta conectada y más rápida.</p>
                </div>
                <!-- Card 4 -->
                <div class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-colors border border-transparent hover:border-white/[0.05]">
                    <div class="w-10 h-10 mb-4 rounded-full bg-green-500/10 flex items-center justify-center text-green-400 group-hover:scale-110 group-hover:bg-green-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Más seguro</h4>
                    <p class="text-sm text-gray-400">Tecnología y datos para prevenir y salvar vidas.</p>
                </div>
            </div>
        </section>

        <!-- Estadisticas -->
        <section class="max-w-5xl mx-auto px-6 lg:px-12 py-12 mb-16">
             <div class="glass-panel rounded-3xl p-10 flex flex-wrap md:flex-nowrap items-center justify-center md:justify-around gap-10">
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <div>
                        <div class="text-xl font-bold text-white">24/7</div>
                        <div class="text-sm text-gray-400">Monitoreo continuo</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <div>
                        <div class="text-xl font-bold text-white">+1200</div>
                        <div class="text-sm text-gray-400">Incidentes atendidos <br/> diariamente</div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="text-xl font-bold text-white">38%</div>
                        <div class="text-sm text-gray-400">Reducción en tiempos <br/> de respuesta</div>
                    </div>
                </div>
             </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[#080d1a] border-t border-white/[0.05] pt-16 pb-8 z-10 relative">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid md:grid-cols-3 gap-12 mb-12">
                <!-- Brand -->
                <div class="col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Stellar Trafic Logo" class="h-12 w-12 object-contain rounded-xl bg-blue-900/30 p-1">
                        <div>
                            <span class="block text-xl font-bold tracking-widest text-white">STELLAR</span>
                            <span class="block text-xl font-light tracking-widest text-gray-300">TRAFFIC</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Plataforma de movilidad y respuesta vial</p>
                    <p class="text-sm text-gray-500 leading-relaxed">Sistema para gestión integrada de la movilidad, seguridad vial y respuesta a emergencias.</p>
                </div>
                
                <!-- Links -->
                <div class="col-span-1 md:pl-12">
                    <h4 class="text-white font-semibold mb-6">Enlaces</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Instituciones</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Cobertura</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Emergencias</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Acerca de</a></li>
                    </ul>
                </div>
                
                <!-- Social -->
                <div class="col-span-1">
                    <h4 class="text-white font-semibold mb-6">Síguenos</h4>
                    <div class="flex items-center gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-white/[0.05] hover:bg-white/[0.1] flex items-center justify-center transition-colors">
                            <span class="text-gray-300 font-bold">X</span>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/[0.05] hover:bg-white/[0.1] flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.203 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/[0.05] hover:bg-white/[0.1] flex items-center justify-center transition-colors">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center text-xs text-gray-600">
                &copy; {{ date('Y') }} Stellar Traffic. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>
