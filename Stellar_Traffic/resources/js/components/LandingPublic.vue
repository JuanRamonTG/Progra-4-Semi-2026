<template>
    <div>
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
                <a :href="routeCobertura" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full border border-gray-600 text-white font-medium hover:bg-white/5 transition-all duration-300 group">
                    Conoce más
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[2rem] blur opacity-30"></div>
                <img :src="mapMockupUrl" alt="Map Visualization" class="relative rounded-[2rem] border border-gray-700/50 shadow-2xl object-cover w-full h-[400px] lg:h-[500px]">
            </div>
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
                <a :href="routeCobertura" class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-all duration-300 border border-transparent hover:border-white/[0.05] block">
                    <div class="w-10 h-10 mb-4 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 group-hover:scale-110 group-hover:bg-blue-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Navega seguro</h4>
                    <p class="text-sm text-gray-400">Rutas y tráfico en tiempo real.</p>
                </a>
                <!-- Card 2 -->
                <a :href="routeEmergencias" class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-all duration-300 border border-transparent hover:border-white/[0.05] block">
                    <div class="w-10 h-10 mb-4 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 group-hover:scale-110 group-hover:bg-red-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Alertas al instante</h4>
                    <p class="text-sm text-gray-400">Incidentes y emergencias en tu ruta.</p>
                </a>
                <!-- Card 3 -->
                <a :href="routeEmergencias" class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-all duration-300 border border-transparent hover:border-white/[0.05] block">
                    <div class="w-10 h-10 mb-4 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400 group-hover:scale-110 group-hover:bg-purple-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Coordinación</h4>
                    <p class="text-sm text-gray-400">Respuesta conectada y más rápida.</p>
                </a>
                <!-- Card 4 -->
                <a :href="routeAcercaDe" class="group p-6 rounded-2xl hover:bg-white/[0.02] transition-all duration-300 border border-transparent hover:border-white/[0.05] block">
                    <div class="w-10 h-10 mb-4 rounded-full bg-green-500/10 flex items-center justify-center text-green-400 group-hover:scale-110 group-hover:bg-green-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-2">Más seguro</h4>
                    <p class="text-sm text-gray-400">Tecnología y datos para prevenir y salvar vidas.</p>
                </a>
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
    </div>
</template>

<script>
export default {
    name: 'LandingPublic',
    props: {
        routeCobertura: {
            type: String,
            required: true
        },
        routeEmergencias: {
            type: String,
            required: true
        },
        routeAcercaDe: {
            type: String,
            required: true
        },
        mapMockupUrl: {
            type: String,
            required: true
        }
    }
}
</script>
