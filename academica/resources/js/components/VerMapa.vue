<template>
    <div class="dashboard">

        
        <!-- BARRA LATERAL (SIDEBAR) -->
        <aside class="sidebar">
            <div class="user-profile">
                <div class="avatar">LZ</div>
                <div class="user-info">
                    <h4>Luis Zelaya</h4>
                    <span>PANEL DE CONTROL PNC</span>
                </div>
                <i class="ph ph-list menu-icon"></i>
            </div>

            <div class="nav-section">
                <p class="nav-title">PRINCIPAL</p>
                <ul class="nav-list" id="main-nav">
                    <li class="nav-item" @click="goTo('/dashboard')"><i class="ph ph-house"></i> Inicio</li>
                    <li class="nav-item" @click="goTo('/registrar-incidente')"><i class="ph ph-plus-square"></i> Registrar incidente</li>
                    <li class="nav-item"><i class="ph ph-magnifying-glass"></i> Buscar casos</li>
                    <li class="nav-item active"><i class="ph ph-map-pin"></i> Ver mapa</li>
                </ul>
            </div>

            <div class="nav-section">
                <p class="nav-title">SISTEMA</p>
                <ul class="nav-list">
                    <li class="nav-item"><i class="ph ph-file-text"></i> Reportes</li>
                    <li class="nav-item"><i class="ph ph-clock-counter-clockwise"></i> Historial</li>
                    <li class="nav-item"><i class="ph ph-gear"></i> Configuración</li>
                    <li class="nav-item"><i class="ph ph-question"></i> Ayuda</li>
                </ul>
            </div>

            <div class="logout" @click="handleLogout">
                <i class="ph ph-sign-out"></i> Salir de la cuenta
            </div>
        </aside>

        <main class="main-content">
            <TopHeader
                title="Monitoreo Vial"
                subtitle="Geolocalización de incidentes y alertas en tiempo real"
            >
                <template #center>
                    <!-- Barra de Navegación/Búsqueda Unificada Compacta -->
                    <div class="search-nav-bar-mini">
                        <div class="search-input-wrapper-mini">
                            <i class="ph ph-magnifying-glass search-icon-mini"></i>
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Buscar por ID, dirección o descripción..." 
                                class="search-input-mini"
                                ref="searchInputRef"
                            />
                            <button v-if="searchQuery" @click="clearSearch" class="clear-search-btn-mini" title="Limpiar búsqueda">
                                <i class="ph ph-x"></i>
                            </button>
                        </div>

                        <div class="filter-badges-mini">
                            <span 
                                class="filter-badge-mini" 
                                :class="{ active: selectedGravedad === '' }"
                                @click="selectedGravedad = ''"
                            >
                                Todos
                            </span>
                            <span 
                                class="filter-badge-mini severity-badge-critico" 
                                :class="{ active: selectedGravedad === 'crítico' }"
                                @click="selectedGravedad = 'crítico'"
                            >
                                Crítico
                            </span>
                            <span 
                                class="filter-badge-mini severity-badge-alto" 
                                :class="{ active: selectedGravedad === 'alto' }"
                                @click="selectedGravedad = 'alto'"
                            >
                                Alto
                            </span>
                            <span 
                                class="filter-badge-mini severity-badge-medio" 
                                :class="{ active: selectedGravedad === 'medio' }"
                                @click="selectedGravedad = 'medio'"
                            >
                                Medio
                            </span>
                            <span 
                                class="filter-badge-mini severity-badge-bajo" 
                                :class="{ active: selectedGravedad === 'bajo' }"
                                @click="selectedGravedad = 'bajo'"
                            >
                                Bajo
                            </span>
                        </div>

                        <div class="results-count-mini">
                            Encontrados: <strong>{{ incidentesFiltrados.length }}</strong>
                        </div>
                    </div>
                </template>
            </TopHeader>

            <div class="map-container">
                <!-- Panel lateral de reportes activos -->
                <div class="map-sidebar-info" v-if="incidentes.length > 0">
                    <div class="info-header">
                        <i class="ph ph-warning-circle"></i>
                        <h3>Reportes Activos</h3>
                    </div>
                    
                    <div class="incidents-list" v-if="incidentesFiltrados.length > 0">
                        <div
                            v-for="incidente in incidentesFiltrados"
                            :key="incidente.id_accidente"
                            class="incident-list-item"
                            :class="[
                                'severity-' +
                                    (
                                        incidente.gravedad || 'Medio'
                                    ).toLowerCase(),
                            ]"
                            @click="focusIncident(incidente)"
                        >
                            <div class="item-header">
                                <span class="case-id">{{
                                    incidente.id_caso || "N/A"
                                }}</span>
                                <span class="badge">{{
                                    incidente.gravedad || "Medio"
                                }}</span>
                            </div>
                            <span class="type-text">{{
                                incidente.tipo_accidente === "victimas"
                                    ? "Con víctimas"
                                    : "Daños materiales"
                            }}</span>
                            <p class="address-text">
                                {{ incidente.direccion || "Sin dirección" }}
                            </p>
                        </div>
                    </div>

                    <!-- Estado de búsqueda vacía -->
                    <div class="empty-search-state" v-else>
                        <i class="ph ph-magnifying-glass"></i>
                        <p>No se encontraron incidentes que coincidan con la búsqueda.</p>
                        <button class="reset-filters-btn" @click="resetFilters">Restablecer filtros</button>
                    </div>
                </div>

                <!-- Contenedor del mapa Leaflet -->
                <div class="map-wrapper-card">
                    <div v-if="mapError" class="map-error-overlay">
                        <i class="ph ph-warning-octagon"></i>
                        <p>{{ mapError }}</p>
                    </div>
                    <div v-else-if="isLoading" class="map-loading-overlay">
                        <div class="spinner"></div>
                        <p>Cargando mapa de monitoreo vial...</p>
                    </div>
                    <div id="global-map"></div>
                </div>

            </div>
        </main>
    </div>
</template>

<script setup>

import { onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// Estados reactivos
const route = useRoute();
const incidentes = ref([]);
const isLoading = ref(true);
const mapError = ref(null);

const searchQuery = ref("");
const selectedGravedad = ref("");
const searchInputRef = ref(null);

let map = null;
let resizeObserver = null;
const markers = {}; // Almacenar marcadores por id_accidente para enfocarlos al hacer click

// Coordenadas predeterminadas centradas en San Miguel, El Salvador
const defaultCenter = [13.4789, -88.1772];

// Función para obtener coordenadas simuladas distribuidas en San Miguel (fallback si Nominatim falla)
const getRandomCoordsInSanMiguel = () => {
    const offsetLat = (Math.random() - 0.5) * 0.025;
    const offsetLng = (Math.random() - 0.5) * 0.025;
    return [defaultCenter[0] + offsetLat, defaultCenter[1] + offsetLng];
};

// Generar icono personalizado palpitante según la gravedad del accidente
const getMarkerIcon = (gravedad) => {
    let color = "#336BFA"; // Azul por defecto (Bajo/Seguro)
    const g = (gravedad || "").toLowerCase();
    if (g === "crítico" || g === "critico")
        color = "#D32F2F"; // Rojo crítico
    else if (g === "alto")
        color = "#FF3333"; // Naranja/Rojo alto
    else if (g === "medio")
        color = "#FFB300"; // Amarillo medio
    else if (g === "bajo" || g === "seguro" || g === "bajo riesgo") color = "#00E676"; // Verde bajo

    return window.L.divIcon({
        className: "custom-map-marker",
        html: `
            <div style="position: relative; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                <div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background-color: ${color}; opacity: 0.25; animation: marker-pulse 1.8s infinite ease-in-out;"></div>
                <div style="position: absolute; width: 12px; height: 12px; border-radius: 50%; background-color: ${color}; border: 2px solid #ffffff; box-shadow: 0 0 8px rgba(0,0,0,0.5);"></div>
            </div>
        `,
        iconSize: [24, 24],
        iconAnchor: [12, 12],
    });
};

// Enfocar e interactuar con el marcador del mapa cuando se hace click en la lista
const focusIncident = (incidente) => {
    const marker = markers[incidente.id_accidente];
    if (marker && map) {
        map.setView(marker.getLatLng(), 17);
        marker.openPopup();
    }
};

// Geocodificar direcciones de texto a coordenadas usando Nominatim de OpenStreetMap
const geocodeAddress = async (direccion, municipio) => {
    const query = `${direccion}, ${municipio}, El Salvador`;
    try {
        const response = await axios.get(
            "https://nominatim.openstreetmap.org/search",
            {
                params: {
                    q: query,
                    format: "json",
                    limit: 1,
                },
            },
        );
        if (response.data && response.data.length > 0) {
            return [
                parseFloat(response.data[0].lat),
                parseFloat(response.data[0].lon),
            ];
        }
    } catch (err) {
        console.warn(
            `Error al geocodificar dirección: ${direccion}. Usando fallback...`,
            err,
        );
    }
    return null;
};

// Filtrar incidentes según texto de búsqueda y gravedad seleccionada
const incidentesFiltrados = computed(() => {
    return incidentes.value.filter((incidente) => {
        const query = searchQuery.value.trim().toLowerCase();
        
        // Filtro por texto
        const matchesQuery = !query || 
            (incidente.id_caso && incidente.id_caso.toLowerCase().includes(query)) ||
            (incidente.direccion && incidente.direccion.toLowerCase().includes(query)) ||
            (incidente.municipio && incidente.municipio.toLowerCase().includes(query)) ||
            (incidente.descripcion && incidente.descripcion.toLowerCase().includes(query)) ||
            (incidente.tipo_accidente && (incidente.tipo_accidente === "victimas" ? "con víctimas con victimas" : "daños materiales daños").includes(query));
            
        // Filtro por gravedad
        const g = (incidente.gravedad || "").toLowerCase();
        const matchesGravedad = !selectedGravedad.value || 
            (selectedGravedad.value === "bajo" && (g === "bajo" || g === "seguro" || g === "bajo riesgo")) ||
            (selectedGravedad.value === "crítico" && (g === "crítico" || g === "critico")) ||
            (g === selectedGravedad.value.toLowerCase());
            
        return matchesQuery && matchesGravedad;
    });
});

// Limpiar la barra de búsqueda
const clearSearch = () => {
    searchQuery.value = "";
    if (searchInputRef.value) {
        searchInputRef.value.focus();
    }
};

// Limpiar todos los filtros y búsqueda
const resetFilters = () => {
    searchQuery.value = "";
    selectedGravedad.value = "";
};

// Consultar incidentes de la BD y resolver sus coordenadas una única vez en memoria
const fetchAndRenderIncidentes = async () => {
    try {
        const response = await axios.get("/accidentes");
        const rawIncidentes = response.data;
        console.log("Incidentes crudos cargados de BD:", rawIncidentes);

        // Procesar incidentes en paralelo para asignarles coordenadas fijas
        const promises = rawIncidentes.map(async (incidente, idx) => {
            let coords = null;

            // Geocodificar solo los primeros 3 para no saturar la API
            if (idx < 3 && incidente.direccion) {
                coords = await geocodeAddress(
                    incidente.direccion,
                    incidente.municipio || "San Miguel",
                );
            }

            // Dispersión simulada si falla
            if (!coords) {
                coords = getRandomCoordsInSanMiguel();
            }

            incidente.coords = coords;
            return incidente;
        });

        incidentes.value = await Promise.all(promises);
        
        // Dibujar los marcadores en el mapa por primera vez
        updateMapMarkers();
        
    } catch (err) {
        console.error("Error al inicializar y renderizar incidentes:", err);
    } finally {
        isLoading.value = false;
    }
};

// Actualizar marcadores de forma reactiva según los filtros actuales
const updateMapMarkers = () => {
    if (!map || !window.L) return;

    // Eliminar del mapa los marcadores que ya no estén en la lista filtrada
    Object.keys(markers).forEach((idAccidente) => {
        const sigueFiltrado = incidentesFiltrados.value.some(
            (incidente) => String(incidente.id_accidente) === String(idAccidente)
        );
        if (!sigueFiltrado) {
            map.removeLayer(markers[idAccidente]);
            delete markers[idAccidente];
        }
    });

    // Agregar marcadores para los incidentes filtrados que no tengan uno dibujado
    incidentesFiltrados.value.forEach((incidente) => {
        const id = incidente.id_accidente;
        
        // Si el marcador ya está dibujado en el mapa, omitir
        if (markers[id]) return;

        const coords = incidente.coords;
        if (coords) {
            const marker = window.L.marker(coords, {
                icon: getMarkerIcon(incidente.gravedad),
            }).addTo(map);

            const popupContent = `
                <div style="color: #ffffff; font-family: 'Segoe UI', sans-serif; min-width: 220px; padding: 5px; background: transparent;">
                    <h4 style="margin: 0 0 8px 0; color: #336BFA; font-size: 13px; border-bottom: 1px solid #1D2C52; padding-bottom: 5px; font-weight: 600;">
                        Caso ID: ${incidente.id_caso || "N/A"}
                    </h4>
                    <div style="font-size: 11px; margin-bottom: 5px;">
                        <strong style="color: #8AABBB;">Tipo:</strong> 
                        <span style="color: #ffffff;">${incidente.tipo_accidente === "victimas" ? "Con víctimas" : "Daños materiales"}</span>
                    </div>
                    <div style="font-size: 11px; margin-bottom: 5px;">
                        <strong style="color: #8AABBB;">Gravedad:</strong> 
                        <span style="color: ${
                            incidente.gravedad === "Crítico" || incidente.gravedad === "critico"
                            ? "#D32F2F"
                            : incidente.gravedad === "Alto"
                              ? "#FF3333"
                              : incidente.gravedad === "Medio"
                                ? "#FFB300"
                                : "#00E676"
                        }; font-weight: 600;">
                            ${incidente.gravedad || "No especificada"}
                        </span>
                    </div>
                    <div style="font-size: 11px; margin-bottom: 5px;">
                        <strong style="color: #8AABBB;">Fecha:</strong> 
                        <span style="color: #ffffff;">${incidente.fecha_incidente || "N/A"}</span>
                    </div>
                    <div style="font-size: 11px; margin-bottom: 5px;">
                        <strong style="color: #8AABBB;">Dirección:</strong> 
                        <span style="color: #ffffff; display: block; margin-top: 2px; line-height: 1.3;">${incidente.direccion || "N/A"}</span>
                    </div>
                    ${
                        incidente.descripcion
                            ? `
                    <div style="font-size: 10px; margin-top: 8px; background: #061129; padding: 8px; border-radius: 6px; border: 1px solid #1D2C52; max-height: 70px; overflow-y: auto;">
                        <strong style="color: #8AABBB; display: block; margin-bottom: 2px;">Descripción:</strong>
                        <span style="color: #d1d5db; line-height: 1.3;">${incidente.descripcion}</span>
                    </div>
                    `
                            : ""
                    }
                </div>
            `;

            marker.bindPopup(popupContent, {
                className: "dark-theme-popup",
                closeButton: false,
            });

            markers[id] = marker;
        }
    });
};

// Observar cambios en los incidentes filtrados para redibujar marcadores
watch(incidentesFiltrados, () => {
    updateMapMarkers();
});

// Inicializar el mapa
const initMap = () => {
    if (!window.L) {
        mapError.value = "Leaflet no cargó correctamente. Reintentando...";
        return;
    }

    try {
        map = window.L.map("global-map").setView(defaultCenter, 14);

        // Capa oscura de CartoDB a juego con el tema de la aplicación
        window.L.tileLayer(
            "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
            {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                subdomains: "abcd",
                maxZoom: 20,
            },
        ).addTo(map);

        // ResizeObserver para recalcular el tamaño del mapa cuando la sidebar cambie de tamaño
        const mapElement = document.getElementById("global-map");
        if (mapElement && "ResizeObserver" in window) {
            resizeObserver = new ResizeObserver(() => {
                if (map) {
                    map.invalidateSize();
                }
            });
            resizeObserver.observe(mapElement);
        }

        // Obtener y pintar los marcadores
        fetchAndRenderIncidentes();
    } catch (err) {
        console.error("Error al inicializar el mapa Leaflet:", err);
        mapError.value = "Error al iniciar el mapa interactivo.";
        isLoading.value = false;
    }
};

onMounted(() => {
    // Retraso para garantizar que el DOM esté montado
    setTimeout(() => {
        initMap();

        // Si se viene del Dashboard para buscar, enfocar el input de búsqueda
        if (route.query.buscar === "true" || route.query.buscar === true) {
            setTimeout(() => {
                if (searchInputRef.value) {
                    searchInputRef.value.focus();
                }
            }, 400);
        }
    }, 150);
});

onUnmounted(() => {
    if (resizeObserver) {
        resizeObserver.disconnect();
    }
    if (map) {
        map.remove();
    }
});
</script>

<style scoped>
.dashboard {
    --bg-dark: #061129;
    --bg-sidebar: #081738;
    --bg-card: #0A1D47;
    --border-color: #1D2C52;
    --text-main: #ffffff;
    --text-muted: #8AABBB;

    --primary-blue: #2563eb;
    --accent-blue: #336BFA;
    --safe: #00E676;
    --warning: #FFB300;
    --critical: #FF1744;

    background-color: var(--bg-dark);
    color: var(--text-main);
    height: 100vh;
    display: flex;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.dashboard * {
    box-sizing: border-box;
}

/* --- CONTENIDO PRINCIPAL --- */
.main-content {
    flex: 1;
    padding: 30px 40px;
    display: flex;
    flex-direction: column;
    overflow-y: hidden; /* Evitar scroll en el contenedor principal */
}

/* Contenedor del mapa y panel */
.map-container {
    flex: 1;
    display: flex;
    gap: 25px;
    margin-top: 25px;
    height: calc(100vh - 150px);
    overflow: hidden;
}

/* --- BARRA DE BÚSQUEDA Y NAVEGACIÓN COMPACTA (MINI) --- */
.search-nav-bar-mini {
    display: flex;
    align-items: center;
    gap: 15px;
    background-color: rgba(10, 29, 71, 0.45);
    border: 1px solid rgba(29, 44, 82, 0.75);
    border-radius: 30px;
    padding: 8px 18px;
    max-width: 780px;
    margin: 0 auto;
    z-index: 10;
}

.search-input-wrapper-mini {
    position: relative;
    display: flex;
    align-items: center;
    width: 330px;
}

.search-icon-mini {
    position: absolute;
    left: 12px;
    color: var(--text-muted);
    font-size: 15px;
    pointer-events: none;
}

.search-input-mini {
    width: 100%;
    background-color: rgba(6, 17, 41, 0.5);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 7px 30px 7px 34px;
    color: var(--text-main);
    font-size: 13px;
    outline: none;
    transition: all 0.2s ease;
}

.search-input-mini:focus {
    border-color: var(--accent-blue);
    background-color: rgba(6, 17, 41, 0.8);
    box-shadow: 0 0 0 2px rgba(51, 107, 250, 0.15);
}

.clear-search-btn-mini {
    position: absolute;
    right: 10px;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3px;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.clear-search-btn-mini:hover {
    background-color: rgba(255, 255, 255, 0.08);
    color: var(--text-main);
}

.filter-badges-mini {
    display: flex;
    align-items: center;
    gap: 7px;
}

.filter-badge-mini {
    font-size: 11px;
    font-weight: 500;
    padding: 5px 12px;
    border-radius: 15px;
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.filter-badge-mini:hover {
    border-color: var(--text-muted);
    color: var(--text-main);
    transform: translateY(-1px);
}

.filter-badge-mini.active {
    background-color: var(--accent-blue);
    border-color: var(--accent-blue);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(51, 107, 250, 0.25);
}

.filter-badge-mini.severity-badge-critico.active {
    background-color: var(--critical);
    border-color: var(--critical);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(255, 23, 68, 0.25);
}

.filter-badge-mini.severity-badge-alto.active {
    background-color: #FF3333;
    border-color: #FF3333;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(255, 51, 51, 0.25);
}

.filter-badge-mini.severity-badge-medio.active {
    background-color: var(--warning);
    border-color: var(--warning);
    color: #061129;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(255, 179, 0, 0.25);
}

.filter-badge-mini.severity-badge-bajo.active {
    background-color: var(--safe);
    border-color: var(--safe);
    color: #061129;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0, 230, 118, 0.25);
}

.results-count-mini {
    font-size: 12px;
    color: var(--text-muted);
    white-space: nowrap;
}

/* --- ESTADO VACÍO DE BÚSQUEDA --- */
.empty-search-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
    gap: 15px;
    color: var(--text-muted);
    flex: 1;
}

.empty-search-state i {
    font-size: 44px;
    color: var(--border-color);
}

.empty-search-state p {
    font-size: 13px;
    line-height: 1.5;
    margin: 0;
}

.reset-filters-btn {
    background-color: rgba(51, 107, 250, 0.1);
    border: 1px solid var(--accent-blue);
    color: var(--text-main);
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.reset-filters-btn:hover {
    background-color: var(--accent-blue);
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(51, 107, 250, 0.2);
}

/* Barra lateral de alertas */
.map-sidebar-info {
    width: 320px;
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    flex-shrink: 0;
}

.info-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
}

.info-header i {
    font-size: 20px;
    color: var(--accent-blue);
}

.info-header h3 {
    font-size: 15px;
    font-weight: 600;
    margin: 0;
}

.incidents-list {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.incident-list-item {
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.incident-list-item:hover {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
    transform: translateY(-2px);
}

.item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.case-id {
    font-size: 12px;
    font-weight: bold;
    color: var(--accent-blue);
}

.badge {
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 4px;
}

.type-text {
    font-size: 12px;
    font-weight: 500;
}

.address-text {
    font-size: 11px;
    color: var(--text-muted);
    line-height: 1.4;
}

/* Colores según gravedad de alertas */
.severity-crítico .badge,
.severity-critico .badge {
    background-color: rgba(220, 38, 38, 0.15);
    color: #ef4444;
    border: 1px solid rgba(220, 38, 38, 0.3);
}
.severity-alto .badge {
    background-color: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
.severity-medio .badge {
    background-color: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.3);
}
.severity-bajo .badge,
.severity-seguro .badge {
    background-color: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

/* Envoltorio Leaflet */
.map-wrapper-card {
    flex: 1;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    position: relative;
    height: 100%;
}

#global-map {
    width: 100%;
    height: 100%;
    z-index: 1;
}

/* Controles Leaflet oscuros */
:deep(.leaflet-bar a) {
    background-color: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border-color) !important;
}

:deep(.leaflet-bar a:hover) {
    background-color: var(--border-color) !important;
}

/* Popups Leaflet oscuros */
:deep(.dark-theme-popup .leaflet-popup-content-wrapper) {
    background-color: var(--bg-card) !important;
    color: var(--text-main) !important;
    border: 1px solid var(--accent-blue) !important;
    border-radius: 12px !important;
    padding: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.7) !important;
}

:deep(.dark-theme-popup .leaflet-popup-tip) {
    background: var(--bg-card) !important;
    border-left: 1px solid var(--accent-blue) !important;
    border-bottom: 1px solid var(--accent-blue) !important;
}

/* Pulso del marcador en CSS */
@keyframes marker-pulse {
    0% {
        transform: scale(0.6);
        opacity: 0.6;
    }
    100% {
        transform: scale(1.6);
        opacity: 0;
    }
}

/* Overlays */
.map-loading-overlay,
.map-error-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--bg-dark);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 15px;
    z-index: 10;
}

.map-error-overlay i {
    font-size: 40px;
    color: var(--critical);
}

.map-error-overlay p {
    color: var(--text-muted);
    font-size: 14px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255, 255, 255, 0.1);
    border-top-color: var(--accent-blue);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsividad */
@media (max-width: 900px) {
    .map-container {
        flex-direction: column-reverse;
    }
    .map-sidebar-info {
        width: 100%;
        height: 250px;
    }
}
</style>

