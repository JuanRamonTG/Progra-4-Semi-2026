<template>
    <section class="max-w-7xl mx-auto px-6 lg:px-12 py-12">
        <!-- Header Section -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl lg:text-5xl font-bold tracking-tight mb-4">
                Sobre <span class="text-gradient">Stellar Traffic</span>
            </h1>
            <p class="text-lg text-gray-300 leading-relaxed">
                Conoce nuestra misión, visión e infraestructura tecnológica orientada a salvar vidas y optimizar la movilidad urbana en tiempo real.
            </p>
        </div>

        <!-- Mission & Vision Row -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <!-- Mission Card -->
            <div class="glass-panel p-8 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all duration-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Nuestra Misión</h3>
                <p class="text-gray-300 leading-relaxed">
                    Facilitar una plataforma de seguridad vial moderna, inteligente e integrada que conecte a ciudadanos, conductores e instituciones en tiempo real, con el fin de disminuir accidentes de tránsito y optimizar la respuesta ante emergencias.
                </p>
            </div>

            <!-- Vision Card -->
            <div class="glass-panel p-8 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-all duration-500"></div>
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Nuestra Visión</h3>
                <p class="text-gray-300 leading-relaxed">
                    Ser el sistema líder y referente en tecnología de seguridad vial y gestión inteligente de movilidad en Latinoamérica, promoviendo una cultura de conducción responsable y una coordinación institucional inmediata e inteligente.
                </p>
            </div>
        </div>

        <!-- Tech Values / Core Pillars -->
        <div class="mb-20">
            <h3 class="text-2xl font-bold text-white text-center mb-12">Pilares de Nuestra Tecnología</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Pillar 1 -->
                <div class="text-center p-6 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-white">Análisis de Datos</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Recopilamos información sobre flujos y velocidades de vehículos para identificar zonas de alto riesgo de accidentes.
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="text-center p-6 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-white">Respuesta Inmediata</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Puentes directos de comunicación e incidentes automatizados para una coordinación ágil de rescatistas y policías.
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="text-center p-6 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400 mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-white">Comunidad Colaborativa</h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Permitimos a conductores comunes ser partícipes en la seguridad vial mediante reportes directos en el mapa.
                    </p>
                </div>
            </div>
        </div>

        <!-- Integrated Institutions Profiles -->
        <div class="space-y-8 mb-8">
            <h3 class="text-2xl font-bold text-white text-center">Instituciones Vinculadas al Ecosistema</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Institution 1 -->
                <div class="glass-panel p-6 rounded-2xl border border-white/[0.03] space-y-3">
                    <span class="text-xs text-blue-400 font-semibold uppercase">PNC</span>
                    <h4 class="text-lg font-bold text-white">Policía Nacional Civil</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Despliegue operativo y control de accidentes en el sitio. Reciben alertas de incidentes viales graves de forma automatizada.
                    </p>
                </div>

                <!-- Institution 2 -->
                <div class="glass-panel p-6 rounded-2xl border border-white/[0.03] space-y-3">
                    <span class="text-xs text-emerald-400 font-semibold uppercase">VMT</span>
                    <h4 class="text-lg font-bold text-white">Viceministerio de Transporte</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Regulación de carriles, control de velocidades en autopistas, y mantenimiento preventivo de la señalización vial.
                    </p>
                </div>

                <!-- Institution 3 -->
                <div class="glass-panel p-6 rounded-2xl border border-white/[0.03] space-y-3">
                    <span class="text-xs text-purple-400 font-semibold uppercase">CONASEVI</span>
                    <h4 class="text-lg font-bold text-white">Consejo de Seguridad Vial</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Promoción de campañas de educación vial basadas en los datos de accidentalidad reportados en la plataforma.
                    </p>
                </div>

                <!-- Institution 4 -->
                <div class="glass-panel p-6 rounded-2xl border border-white/[0.03] space-y-3">
                    <span class="text-xs text-red-400 font-semibold uppercase">Cruz Roja</span>
                    <h4 class="text-lg font-bold text-white">Cuerpos de Socorro</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Despliegue de paramédicos y ambulancias. Cuentan con un mapa en tiempo real para optimizar las rutas de auxilio médico.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'AcercaDePublic'
}
</script>
