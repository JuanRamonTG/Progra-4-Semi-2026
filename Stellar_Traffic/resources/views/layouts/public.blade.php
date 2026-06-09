<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Stellar Trafic - Gestión eficiente del tránsito')</title>
    
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
    @yield('styles')
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden">
    <div id="app" class="min-h-screen flex flex-col justify-between">
        <!-- Background Glow Effects -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] rounded-full bg-indigo-500/10 blur-[100px] pointer-events-none"></div>

        <!-- Header -->
        <header class="w-full z-50 px-6 py-4 lg:px-12 glass-panel border-b-0 sticky top-0">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Stellar Trafic Logo" class="h-10 w-10 object-contain rounded-xl shadow-lg shadow-blue-500/20">
                        <div class="leading-tight">
                            <span class="block text-xl font-bold tracking-widest text-white">STELLAR</span>
                            <span class="block text-xl font-light tracking-widest text-gray-300">TRAFFIC</span>
                        </div>
                    </a>
                </div>
                
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="{{ route('home') }}" class="transition-colors duration-200 {{ Route::currentRouteName() == 'home' ? 'text-blue-400 font-semibold border-b-2 border-blue-500 pb-1' : 'text-gray-300 hover:text-white' }}">Inicio</a>
                    <a href="{{ route('cobertura') }}" class="transition-colors duration-200 {{ Route::currentRouteName() == 'cobertura' ? 'text-blue-400 font-semibold border-b-2 border-blue-500 pb-1' : 'text-gray-300 hover:text-white' }}">Cobertura</a>
                    <a href="{{ route('emergencias') }}" class="transition-colors duration-200 {{ Route::currentRouteName() == 'emergencias' ? 'text-blue-400 font-semibold border-b-2 border-blue-500 pb-1' : 'text-gray-300 hover:text-white' }}">Emergencias</a>
                    <a href="{{ route('acerca-de') }}" class="transition-colors duration-200 {{ Route::currentRouteName() == 'acerca-de' ? 'text-blue-400 font-semibold border-b-2 border-blue-500 pb-1' : 'text-gray-300 hover:text-white' }}">Acerca de</a>
                </nav>
                
                <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium py-2 px-6 rounded-full transition-all duration-300 shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(59,130,246,0.6)] hover:-translate-y-0.5">
                    Iniciar sesión
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow z-10">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-[#080d1a] border-t border-white/[0.05] pt-16 pb-8 z-10 relative mt-auto">
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
                            <li><a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                            <li><a href="{{ route('cobertura') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Cobertura</a></li>
                            <li><a href="{{ route('emergencias') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Emergencias</a></li>
                            <li><a href="{{ route('acerca-de') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Acerca de</a></li>
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
    </div>
    @yield('scripts')
</body>
</html>
