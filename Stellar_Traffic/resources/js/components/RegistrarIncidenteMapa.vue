<template>
    <div class="dashboard">
        
        <aside class="sidebar">
            <div class="user-profile">
                <div class="avatar">LZ</div>
                <div class="user-info">
                    <h4>Luis Zelaya</h4>
                    <span>PANEL DE CONTROL PNC</span>
                </div>
            </div>

            <transition name="fade">
                <div v-if="showNotification" class="toast-notification">
                    <div class="toast-content">
                        <span class="toast-title">Ubicación Seleccionada</span>
                        <p class="toast-text">{{ notificationMessage }}</p>
                    </div>
                    <button class="toast-close" type="button" @click="showNotification = false">
                        <i class="ph ph-x"></i>
                    </button>
                </div>
            </transition>
        </aside>

        <main class="main-content">
            
            <header class="header">
                <div class="header-titles">
                    <h1>Registro de Incidente</h1>
                    <p>Gestión rápida de incidentes y monitoreo vial</p>
                </div>
                <div class="header-actions">
                    <div class="datetime-pill">
                        <i class="ph ph-calendar-blank"></i>
                        <div class="dt-text">
                            <span class="date">12 Mayo 2026</span>
                            <span class="time">09:23 PM</span>
                        </div>
                    </div>
                    <div class="notification">
                        <i class="ph ph-bell"></i>
                        <span class="badge">2</span>
                    </div>
                </div>
            </header>

            <div class="map-view">
                <div class="stepper-container">
                    <div class="car-icon-floating">
                        <i class="ph ph-car"></i>
                    </div>
                    <div class="stepper-background-line"></div>
                    <div class="stepper-active-line"></div>

                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <div class="map-card">
                    <div class="map-header">
                        <div class="map-title-group">
                            <i class="ph ph-map-pin"></i>
                            <div class="map-title-text">
                                <h3>Selecciona en el mapa</h3>
                                <p>Elige la ubicación exacta del incidente</p>
                            </div>
                        </div>

                        <div class="search-wrapper">
                            <i class="ph ph-magnifying-glass"></i>
                            <input
                                type="text"
                                class="search-input"
                                placeholder="Buscar dirección o lugar"
                                v-model="searchQuery"
                                @keypress.enter="handleSearch"
                            />
                        </div>
                    </div>

                    <div class="leaflet-container-wrapper">
                        <div v-if="mapError" class="map-error-overlay">
                            <i class="ph ph-warning-octagon"></i>
                            <p>{{ mapError }}</p>
                        </div>
                        <div
                            v-else-if="isLoadingMap"
                            class="map-loading-overlay"
                        >
                            <div class="spinner"></div>
                            <p>Cargando mapa vial...</p>
                        </div>
                        <div id="incident-map"></div>
                    </div>

                    <div class="selected-address-bar" v-if="selectedAddress">
                        <i class="ph ph-map-pin-line"></i>
                        <span
                            ><strong>Ubicación seleccionada:</strong>
                            {{ selectedAddress }}</span
                        >
                    </div>
                </div>

                <div class="bottom-action-bar">
                    <button
                        class="btn btn-outline"
                        type="button"
                        @click="handleBack"
                    >
                        Atrás
                    </button>
                    <button
                        class="btn btn-primary"
                        type="button"
                        @click="handleNext"
                    >
                        Guardar
                    </button>
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

// Variables de estado
const searchQuery = ref("");
const isLoadingMap = ref(true);
const mapError = ref(null);
const selectedAddress = ref("");
const selectedDistrict = ref("");
const showNotification = ref(false);
const notificationMessage = ref("");
let map = null;
let currentMarker = null;
let resizeObserver = null;

// Obtener dirección a partir de las coordenadas (Reverse Geocoding)
const fetchAddressFromCoords = (lat, lng) => {
    isLoadingMap.value = true;
    axios
        .get(`https://nominatim.openstreetmap.org/reverse`, {
            params: {
                lat: lat,
                lon: lng,
                format: "json",
                addressdetails: 1,
            },
        })
        .then((response) => {
            if (response.data) {
                const addr = response.data.address || {};
                selectedAddress.value = response.data.display_name;

                // Intentar extraer el distrito o barrio
                selectedDistrict.value =
                    addr.neighbourhood ||
                    addr.suburb ||
                    addr.quarter ||
                    addr.city_district ||
                    addr.town ||
                    addr.city ||
                    "";
                console.log(
                    "Dirección resuelta:",
                    selectedAddress.value,
                    "Distrito:",
                    selectedDistrict.value,
                );

                // Mostrar notificación al seleccionar en el mapa
                notificationMessage.value = "Ubicación lista. Haz clic en 'Guardar' para continuar.";
                showNotification.value = true;
                setTimeout(() => {
                    showNotification.value = false;
                }, 5000);
            }
        })
        .catch((err) => {
            console.error("Error en reverse geocoding:", err);
        })
        .finally(() => {
            isLoadingMap.value = false;
        });
};

// Función para inicializar el mapa
const initMap = () => {
    if (!window.L) {
        mapError.value = "Leaflet no está cargado. Reintentando...";
        return;
    }

    try {
        // Cargar coordenadas previamente guardadas si existen
        const savedLat = localStorage.getItem("incidente_mapa_lat");
        const savedLng = localStorage.getItem("incidente_mapa_lng");
        const savedAddress = localStorage.getItem("incidente_direccion");
        const savedDistrict = localStorage.getItem("incidente_distrito");

        let initialCoords = [9.9281, -84.0907];
        let hasSavedLocation = false;

        if (savedLat && savedLng) {
            initialCoords = [parseFloat(savedLat), parseFloat(savedLng)];
            hasSavedLocation = true;
        }

        // Si ya hay un mapa inicializado, no lo duplicamos
        if (map) {
            isLoadingMap.value = false;
            return;
        }

        // Crear mapa
        map = window.L.map("incident-map").setView(
            initialCoords,
            hasSavedLocation ? 16 : 14,
        );

        // Capa oscura (Dark Theme para coincidir con la UI)
        window.L.tileLayer(
            "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",
            {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                subdomains: "abcd",
                maxZoom: 20,
            },
        ).addTo(map);

        // Configurar iconos por defecto usando URLs absolutas de CDN
        const defaultIcon = window.L.icon({
            iconUrl:
                "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
            iconRetinaUrl:
                "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png",
            shadowUrl:
                "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
        });

        // Si hay una ubicación guardada previa, colocar marcador e inicializar textos
        if (hasSavedLocation) {
            currentMarker = window.L.marker(initialCoords, {
                icon: defaultIcon,
            }).addTo(map);
            selectedAddress.value = savedAddress || "";
            selectedDistrict.value = savedDistrict || "";
        }

        // Configurar marcador si ya se hace click
        map.on("click", function (e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            currentMarker = window.L.marker([lat, lng], {
                icon: defaultIcon,
            }).addTo(map);
            fetchAddressFromCoords(lat, lng);
        });

        // Configurar ResizeObserver para actualizar el mapa cuando su contenedor cambie de tamaño (ej. colapso de sidebar)
        const mapElement = document.getElementById("incident-map");
        if (mapElement && "ResizeObserver" in window) {
            resizeObserver = new ResizeObserver(() => {
                if (map) {
                    map.invalidateSize();
                }
            });
            resizeObserver.observe(mapElement);
        }

        // Forzar actualización del layout del mapa en el siguiente frame
        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
            isLoadingMap.value = false;
        }, 150);
    } catch (err) {
        console.error("Error al inicializar el mapa Leaflet:", err);
        mapError.value =
            "Ocurrió un error al cargar el mapa. Por favor, recarga la página.";
        isLoadingMap.value = false;
    }
};

// Función para cargar dinámicamente los recursos si no están presentes
const loadLeafletResources = () => {
    return new Promise((resolve, reject) => {
        // Si ya está cargado en window, resolver de inmediato
        if (window.L) {
            resolve();
            return;
        }

        // Si ya se está cargando el script en la página, esperar a que termine
        const existingScript = document.querySelector(
            'script[src*="leaflet.js"]',
        );
        if (existingScript) {
            existingScript.addEventListener("load", () => resolve());
            existingScript.addEventListener("error", (e) => reject(e));
            return;
        }

        // Si no existen las etiquetas en el DOM, cargarlas dinámicamente
        // 1. Cargar CSS
        if (!document.querySelector('link[href*="leaflet.css"]')) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
            document.head.appendChild(link);
        }

        // 2. Cargar JS
        const script = document.createElement("script");
        script.src = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js";
        script.async = true;
        script.onload = () => resolve();
        script.onerror = (e) => reject(e);
        document.head.appendChild(script);
    });
};

onMounted(async () => {
    try {
        await loadLeafletResources();
        // Un pequeño retraso para asegurar que el DOM esté completamente pintado
        setTimeout(() => {
            initMap();
        }, 100);
    } catch (err) {
        console.error("No se pudo cargar Leaflet desde CDN:", err);
        mapError.value =
            "Error al descargar recursos del mapa. Verifica tu conexión a internet.";
        isLoadingMap.value = false;
    }
});

onUnmounted(() => {
    if (resizeObserver) {
        resizeObserver.disconnect();
    }
    if (map) {
        map.remove();
    }
});

// Lógica del Buscador con geocodificación gratuita usando OpenStreetMap Nominatim
const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.value && map) {
        isLoadingMap.value = true;
        axios
            .get(`https://nominatim.openstreetmap.org/search`, {
                params: {
                    q: searchQuery.value,
                    format: "json",
                    addressdetails: 1,
                    limit: 1,
                },
            })
            .then((response) => {
                if (response.data && response.data.length > 0) {
                    const result = response.data[0];
                    const lat = parseFloat(result.lat);
                    const lon = parseFloat(result.lon);

                    // Mover mapa a la ubicación encontrada
                    map.setView([lat, lon], 16);

                    // Colocar marcador automático
                    const defaultIcon = window.L.icon({
                        iconUrl:
                            "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
                        iconRetinaUrl:
                            "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png",
                        shadowUrl:
                            "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41],
                    });

                    if (currentMarker) {
                        map.removeLayer(currentMarker);
                    }
                    currentMarker = window.L.marker([lat, lon], {
                        icon: defaultIcon,
                    }).addTo(map);

                    // Actualizar dirección y distrito a partir del resultado
                    selectedAddress.value = result.display_name;
                    const addr = result.address || {};
                    selectedDistrict.value =
                        addr.neighbourhood ||
                        addr.suburb ||
                        addr.quarter ||
                        addr.city_district ||
                        addr.town ||
                        addr.city ||
                        "";
                    console.log(
                        `Búsqueda exitosa. Marcador colocado en: Lat ${lat}, Lng ${lon}`,
                    );

                    // Mostrar notificación
                    notificationMessage.value = "Dirección localizada. Haz clic en 'Guardar' para continuar.";
                    showNotification.value = true;
                    setTimeout(() => {
                        showNotification.value = false;
                    }, 5000);
                } else {
                    alert(
                        "No se encontró la dirección. Intenta con otros términos.",
                    );
                }
            })
            .catch((err) => {
                console.error("Error buscando dirección:", err);
                alert("Error al conectar con el servicio de búsqueda.");
            })
            .finally(() => {
                isLoadingMap.value = false;
            });
    }
};

// Navegación
const handleBack = () => {
    router.push({ name: "registrar-incidente-ubicacion" });
};

const handleNext = () => {
    if (currentMarker) {
        const position = currentMarker.getLatLng();
        console.log("Coordenadas guardadas:", position);

        // Guardar la ubicación en localStorage
        localStorage.setItem("incidente_mapa_lat", position.lat);
        localStorage.setItem("incidente_mapa_lng", position.lng);
        localStorage.setItem("incidente_direccion", selectedAddress.value);
        localStorage.setItem("incidente_distrito", selectedDistrict.value);

        // Redirigir de vuelta al formulario de ubicación, que ahora tendrá cargados los datos
        router.push({ name: "registrar-incidente-ubicacion" });
    } else {
        alert("Por favor, selecciona una ubicación en el mapa haciendo clic.");
    }
};

const goTo = (path) => {
    router.push(path);
};

const handleLogout = async () => {
    try {
        await axios.post("/logout");
        window.location.href = "/login";
    } catch (error) {
        console.error("Error al cerrar sesión:", error);
        window.location.href = "/login";
    }
};
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
    overflow-y: auto;
}

/* Encabezado */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-shrink: 0;
}
.header-titles h1 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 5px;
}
.header-titles p {
    color: var(--text-muted);
    font-size: 14px;
}
.header-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.datetime-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: var(--bg-card);
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}
.datetime-pill i {
    font-size: 20px;
    color: var(--text-muted);
}
.dt-text {
    display: flex;
    flex-direction: column;
    font-size: 12px;
}
.dt-text .date {
    font-weight: 600;
}
.dt-text .time {
    color: var(--text-muted);
    font-size: 11px;
}

.notification {
    background-color: var(--bg-card);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-color);
    position: relative;
    cursor: pointer;
}
.notification i {
    font-size: 20px;
    color: var(--text-muted);
}
.badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background-color: var(--critical);
    color: white;
    font-size: 10px;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
}

/* --- VISTA DEL MAPA --- */
.map-view {
    flex: 1;
    display: flex;
    flex-direction: column;
    max-width: 1000px;
    margin: 0 auto;
    width: 100%;
}

/* Stepper - Paso 2 */
.stepper-container {
    position: relative;
    margin: 30px 0 50px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0 10px;
}
.stepper-background-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 6px;
    background-color: #ffffff;
    transform: translateY(-50%);
    border-radius: 3px;
    z-index: 1;
}
.stepper-active-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 38%;
    height: 6px;
    background-color: var(--accent-blue);
    transform: translateY(-50%);
    border-radius: 3px;
    z-index: 2;
    transition: width 0.4s ease;
}

.step-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #ffffff;
    z-index: 3;
    position: relative;
    transition: 0.4s ease;
}
.step-dot.active {
    background-color: var(--accent-blue);
    box-shadow: 0 0 12px var(--accent-blue);
}
.car-icon-floating {
    position: absolute;
    top: -45px;
    left: 50%;
    transform: translateX(-50%);
    width: 45px;
    height: 45px;
    background-color: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--text-muted);
}

/* Contenedor Principal del Mapa */
.map-card {
    border: 1px solid var(--accent-blue);
    border-radius: 16px;
    padding: 30px;
    background-color: transparent;
    margin-bottom: 30px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.map-title-group {
    display: flex;
    align-items: center;
    gap: 15px;
}
.map-title-group i {
    font-size: 24px;
    color: var(--text-main);
}
.map-title-text h3 {
    font-size: 16px;
    font-weight: 500;
}
.map-title-text p {
    font-size: 12px;
    color: var(--text-muted);
}

/* Buscador integrado */
.search-wrapper {
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    height: 45px;
    width: 350px;
    transition: 0.3s;
}
.search-wrapper:focus-within {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}
.search-wrapper i {
    font-size: 18px;
    color: var(--text-muted);
    margin-right: 15px;
}
.search-input {
    background: transparent;
    border: none;
    color: var(--text-main);
    font-size: 13px;
    width: 100%;
    height: 100%;
    outline: none;
}
.search-input::placeholder {
    color: var(--text-muted);
}

/* Contenedor Leaflet */
.leaflet-container-wrapper {
    width: 100%;
    height: 450px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    position: relative;
}
#incident-map {
    width: 100%;
    height: 100%;
    z-index: 1;
}

/* Ajustes oscuros para controles Leaflet asegurados con :deep para saltar el scope */
:deep(.leaflet-bar a) {
    background-color: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border-color) !important;
}
:deep(.leaflet-bar a:hover) {
    background-color: var(--border-color) !important;
}

/* Barra de Acciones Inferior */
.bottom-action-bar {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 20px;
}
.btn {
    padding: 14px 40px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.2s;
}
.btn-outline {
    background-color: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-main);
}
.btn-outline:hover {
    background-color: rgba(255, 255, 255, 0.05);
}
.btn-primary {
    background-color: var(--primary-blue);
    border: 1px solid var(--primary-blue);
    color: white;
}
.btn-primary:hover {
    background-color: var(--accent-blue);
}

/* Overlays de carga y error */
.map-loading-overlay,
.map-error-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--bg-card);
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

/* Barra de dirección seleccionada */
.selected-address-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: rgba(37, 99, 235, 0.08);
    border: 1px solid var(--accent-blue);
    border-radius: 10px;
    padding: 12px 18px;
    font-size: 13px;
    color: var(--text-main);
    animation: fadeIn 0.3s ease-in-out;
}
.selected-address-bar i {
    font-size: 18px;
    color: var(--accent-blue);
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsividad */
@media (max-width: 900px) {
    .map-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }
    .search-wrapper {
        width: 100%;
    }
    .stepper-container {
        display: none;
    }
}

/* Toast Notification Premium */
.toast-notification {
    position: fixed;
    top: 25px;
    right: 25px;
    background: rgba(10, 29, 71, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(51, 107, 250, 0.4);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    z-index: 9999;
    max-width: 380px;
}

.toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(37, 99, 235, 0.15);
    color: #336BFA;
    font-size: 24px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid rgba(51, 107, 250, 0.3);
}

.toast-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.toast-title {
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
}

.toast-text {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
}

.toast-close {
    background: transparent;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 16px;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.toast-close:hover {
    color: #ffffff;
}

/* Transiciones de Vue */
.toast-fade-enter-active,
.toast-fade-leave-active {
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-fade-enter-from {
    transform: translateX(50px);
    opacity: 0;
}

.toast-fade-leave-to {
    transform: translateX(50px);
    opacity: 0;
}
</style>
