<template>
    <section class="max-w-7xl mx-auto px-6 lg:px-12 py-12">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl lg:text-5xl font-bold tracking-tight mb-4">
                Cobertura en <span class="text-gradient">Tiempo Real</span>
            </h1>
            <p class="text-lg text-gray-300 leading-relaxed">
                Visualiza los sectores y carreteras con monitoreo activo de tránsito. Nuestra red integra cámaras inteligentes y sensores de velocidad para una movilidad segura.
            </p>
        </div>

        <!-- Buscador de Cobertura -->
        <div class="glass-panel rounded-3xl p-6 mb-12 max-w-4xl mx-auto">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <div class="flex-grow w-full relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" v-model="searchQuery" @keyup.enter="searchZone" placeholder="Ingresa tu zona o departamento (ej. San José, Escazú...)" class="w-full pl-12 pr-4 py-3 bg-slate-900/50 border border-gray-700/50 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-sm">
                </div>
                <button @click="searchZone" class="w-full md:w-auto bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-8 rounded-2xl transition-all duration-300 shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                    Buscar cobertura
                </button>
            </div>
            <div v-if="searchResult" class="mt-4 text-sm font-medium" :class="searchResultClass">
                {{ searchResult }}
            </div>
        </div>

        <div class="grid lg:grid-cols-5 gap-8 items-start mb-16">
            <!-- Sectores List -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xl font-bold text-white mb-2">Sectores Monitoreados</h3>
                
                <div v-for="zone in zones" :key="zone.id" 
                     @click="focusOnZone(zone)"
                     class="glass-panel p-5 rounded-2xl hover:bg-white/[0.02] transition-all duration-300 border border-white/[0.05] cursor-pointer"
                     :class="{'border-blue-500/50 bg-white/[0.01]': activeZoneId === zone.id}">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold text-white">{{ zone.name }}</h4>
                        <span class="px-2.5 py-0.5 text-xs rounded-full font-medium"
                              :class="zone.status === 'Óptimo' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400'">
                            {{ zone.status }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">{{ zone.subdesc }}</p>
                    <div class="flex gap-4 text-xs text-gray-300">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> {{ zone.cameras }} Cámaras
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span> {{ zone.sensors }} Sensores
                        </span>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="lg:col-span-3">
                <div id="map" class="h-[500px] rounded-3xl border border-gray-700/50 shadow-2xl relative">
                    <!-- Loading overlay if map not loaded yet -->
                    <div v-if="!mapLoaded" class="absolute inset-0 bg-slate-950/80 rounded-3xl flex items-center justify-center text-gray-400 font-medium z-50">
                        <span class="animate-pulse">Cargando mapa interactivo...</span>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script>
export default {
    name: 'CoberturaPublic',
    data() {
        return {
            map: null,
            mapLoaded: false,
            searchQuery: '',
            searchResult: '',
            searchResultClass: '',
            activeZoneId: 'centro',
            mapMarkers: {},
            zones: [
                { id: "centro", name: "San José Centro", coords: [9.9333, -84.0833], status: "Óptimo", subdesc: "Zona comercial y casco urbano principal.", cameras: 45, sensors: 12, desc: "45 cámaras de seguridad vial y 12 sensores de velocidad en línea." },
                { id: "escazu", name: "Escazú", coords: [9.9304, -84.1481], status: "Óptimo", subdesc: "Ruta de alta velocidad y centros comerciales.", cameras: 20, sensors: 6, desc: "20 cámaras e infraestructura de velocidad activa y monitoreada." },
                { id: "pedro", name: "San Pedro / Curridabat", coords: [9.9317, -84.0520], status: "Mantenimiento", subdesc: "Zona universitaria y de alto flujo residencial.", cameras: 30, sensors: 8, desc: "Monitoreo vial activo con mantenimiento en sensores de carril." },
                { id: "sabana", name: "La Sabana", coords: [9.9350, -84.1025], status: "Óptimo", subdesc: "Punto de control estratégico y rutas periféricas.", cameras: 18, sensors: 4, desc: "18 cámaras de alta definición y 4 sensores de flujo vehicular." }
            ]
        }
    },
    mounted() {
        this.loadLeaflet();
    },
    methods: {
        loadLeaflet() {
            if (window.L) {
                this.initMap();
            } else {
                // Dynamically append stylesheet
                if (!document.getElementById('leaflet-css')) {
                    const link = document.createElement('link');
                    link.id = 'leaflet-css';
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(link);
                }
                
                // Dynamically append script
                if (!document.getElementById('leaflet-js')) {
                    const script = document.createElement('script');
                    script.id = 'leaflet-js';
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = () => {
                        this.initMap();
                    };
                    document.head.appendChild(script);
                } else {
                    const interval = setInterval(() => {
                        if (window.L) {
                            clearInterval(interval);
                            this.initMap();
                        }
                    }, 100);
                }
            }
        },
        initMap() {
            const L = window.L;
            this.map = L.map('map', {
                scrollWheelZoom: false
            }).setView([9.9333, -84.0833], 13);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(this.map);

            this.zones.forEach((zone) => {
                const color = zone.status === "Óptimo" ? '#10B981' : '#F59E0B';
                
                // Circle overlay
                L.circle(zone.coords, {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.15,
                    radius: 700
                }).addTo(this.map);

                // Marker
                const marker = L.marker(zone.coords).addTo(this.map);
                
                const popupContent = `
                    <div style="font-family: 'Inter', sans-serif;">
                        <h4 style="margin: 0 0 5px 0; font-weight:700; color: #fff;">${zone.name}</h4>
                        <span style="font-size: 11px; padding: 2px 6px; border-radius: 4px; background: ${color}22; color: ${color}; font-weight: 600;">Estado: ${zone.status}</span>
                        <p style="margin: 8px 0 0 0; font-size:12px; color: #94a3b8; line-height: 1.4;">${zone.desc}</p>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                this.mapMarkers[zone.id] = { marker, coords: zone.coords };
            });

            this.mapLoaded = true;
        },
        focusOnZone(zone) {
            this.activeZoneId = zone.id;
            if (this.map) {
                this.map.setView(zone.coords, 15, {
                    animate: true,
                    duration: 1.2
                });
                // Open popup
                if (this.mapMarkers[zone.id]) {
                    this.mapMarkers[zone.id].marker.openPopup();
                }
            }
        },
        searchZone() {
            const query = this.searchQuery.toLowerCase().trim();
            if (!query) {
                this.searchResult = '';
                return;
            }

            let foundZone = null;
            
            if (query.includes('centro') || query.includes('sanjose') || query.includes('san josé')) {
                foundZone = this.zones.find(z => z.id === 'centro');
            } else if (query.includes('escazu') || query.includes('escazú')) {
                foundZone = this.zones.find(z => z.id === 'escazu');
            } else if (query.includes('pedro') || query.includes('curridabat')) {
                foundZone = this.zones.find(z => z.id === 'pedro');
            } else if (query.includes('sabana')) {
                foundZone = this.zones.find(z => z.id === 'sabana');
            }

            if (foundZone) {
                this.searchResult = `✓ Cobertura Activa en ${foundZone.name}: El sector cuenta con soporte de monitoreo ${foundZone.status.toLowerCase()}. Enfocando mapa...`;
                this.searchResultClass = foundZone.status === 'Óptimo' ? 'text-emerald-400' : 'text-amber-400';
                this.focusOnZone(foundZone);
            } else {
                this.searchResult = '✗ Sin cobertura directa actualmente: Estaremos expandiendo nuestro servicio a este sector próximamente.';
                this.searchResultClass = 'text-red-400';
            }
        }
    }
}
</script>

<style>
/* Custom Leaflet Dark Popup Styles */
.leaflet-popup-content-wrapper {
    background: rgba(15, 23, 42, 0.95) !important;
    backdrop-filter: blur(10px);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
}
.leaflet-popup-tip {
    background: rgba(15, 23, 42, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>
