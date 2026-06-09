<template>
    <div class="dashboard">
        <Sidebar />

        <main class="main-content">
            <TopHeader
                title="Historial de Reportes"
                subtitle="Consulta y auditoría de incidentes registrados"
            />

            <!-- Estadísticas rápidas superiores -->
            <div class="metrics-summary" v-if="accidentes.length > 0">
                <div class="metric-mini-card">
                    <span class="m-label">Total Reportes</span>
                    <strong class="m-value">{{ accidentes.length }}</strong>
                </div>
                <div class="metric-mini-card">
                    <span class="m-label">Críticos/Altos</span>
                    <strong class="m-value text-danger">{{
                        criticosCount
                    }}</strong>
                </div>
                <div class="metric-mini-card">
                    <span class="m-label">Daños Materiales</span>
                    <strong class="m-value text-info">{{
                        materialesCount
                    }}</strong>
                </div>
                <div class="metric-mini-card">
                    <span class="m-label">Con Víctimas</span>
                    <strong class="m-value text-warning">{{
                        victimasCount
                    }}</strong>
                </div>
            </div>

            <!-- Filtros e input de búsqueda -->
            <div class="filters-row" v-if="accidentes.length > 0">
                <div class="search-box">
                    <i class="ph ph-magnifying-glass"></i>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Buscar por ID, dirección o descripción..."
                    />
                </div>
                <div class="filter-groups">
                    <select v-model="filterGravedad" class="filter-select">
                        <option value="">Todas las gravedades</option>
                        <option value="Crítico">Crítico</option>
                        <option value="Alto">Alto</option>
                        <option value="Medio">Medio</option>
                        <option value="Bajo">Bajo</option>
                    </select>
                    <select v-model="filterTipo" class="filter-select">
                        <option value="">Todos los tipos</option>
                        <option value="victimas">Con víctimas</option>
                        <option value="materiales">Daños materiales</option>
                    </select>
                </div>
            </div>

            <!-- Spinner de carga -->
            <div v-if="isLoading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando historial de incidentes...</p>
            </div>

            <!-- Listado de reportes (orden descendente) -->
            <div v-else-if="filteredAccidentes.length > 0" class="history-list">
                <div
                    v-for="incidente in filteredAccidentes"
                    :key="incidente.id_accidente"
                    class="history-card"
                    :class="{ expanded: expandedId === incidente.id_accidente }"
                >
                    <div
                        class="card-header-main"
                        @click="toggleExpand(incidente.id_accidente)"
                    >
                        <div class="card-title-block">
                            <div
                                class="severity-indicator"
                                :class="
                                    'bg-' +
                                    (
                                        incidente.gravedad || 'Medio'
                                    ).toLowerCase()
                                "
                            ></div>
                            <div class="title-info">
                                <span class="case-id">{{
                                    incidente.id_caso || "N/A"
                                }}</span>
                                <span
                                    class="badge"
                                    :class="
                                        'badge-' +
                                        (
                                            incidente.gravedad || 'Medio'
                                        ).toLowerCase()
                                    "
                                    >{{ incidente.gravedad || "Medio" }}</span
                                >
                            </div>
                        </div>

                        <div class="card-quick-meta">
                            <div class="meta-item">
                                <i
                                    :class="
                                        incidente.tipo_accidente === 'victimas'
                                            ? 'ph ph-users-three'
                                            : 'ph ph-car-profile'
                                    "
                                ></i>
                                <span>{{
                                    incidente.tipo_accidente === "victimas"
                                        ? "Con víctimas"
                                        : "Daños materiales"
                                }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="ph ph-calendar-blank"></i>
                                <span>{{ incidente.fecha_incidente }}</span>
                            </div>
                            <i class="ph ph-caret-down expand-arrow"></i>
                        </div>
                    </div>

                    <!-- Panel de detalles colapsable -->
                    <div
                        class="card-details-panel"
                        v-if="expandedId === incidente.id_accidente"
                    >
                        <div class="details-grid">
                            <div class="detail-block">
                                <span class="detail-label"
                                    ><i class="ph ph-clock"></i> Hora
                                    Aproximada</span
                                >
                                <span class="detail-val">{{
                                    incidente.hora_aproximada ||
                                    "No especificada"
                                }}</span>
                            </div>
                            <div class="detail-block">
                                <span class="detail-label"
                                    ><i class="ph ph-map-pin"></i>
                                    Dirección</span
                                >
                                <span class="detail-val">{{
                                    incidente.direccion || "Sin dirección"
                                }}</span>
                            </div>
                            <div class="detail-block">
                                <span class="detail-label"
                                    ><i class="ph ph-buildings"></i>
                                    Distrito</span
                                >
                                <span class="detail-val">{{
                                    incidente.municipio || "Sin distrito"
                                }}</span>
                            </div>
                            <div
                                class="detail-block"
                                v-if="
                                    incidente.condicion_climatica ||
                                    incidente.tipo_via
                                "
                            >
                                <span class="detail-label"
                                    ><i class="ph ph-cloud-sun"></i> Clima /
                                    Vía</span
                                >
                                <span class="detail-val">
                                    {{ incidente.condicion_climatica || "N/A" }}
                                    - {{ incidente.tipo_via || "N/A" }}
                                    <span v-if="incidente.estado_pavimento"
                                        >({{
                                            incidente.estado_pavimento
                                        }})</span
                                    >
                                </span>
                            </div>
                        </div>

                        <div
                            class="description-section"
                            v-if="incidente.descripcion"
                        >
                            <span class="detail-label"
                                ><i class="ph ph-article"></i> Descripción /
                                Declaración</span
                            >
                            <p class="description-text">
                                {{ incidente.descripcion }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado sin resultados -->
            <div v-else class="empty-state">
                <i class="ph ph-clock-counter-clockwise"></i>
                <h2>No se encontraron reportes</h2>
                <p v-if="accidentes.length > 0">
                    Prueba ajustando los filtros de búsqueda.
                </p>
                <p v-else>
                    Aún no has registrado ningún incidente en el sistema.
                </p>
            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from "./Sidebar.vue";
import TopHeader from "./TopHeader.vue";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

// Variables de estado
const accidentes = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");
const filterGravedad = ref("");
const filterTipo = ref("");
const expandedId = ref(null); // ID de la tarjeta expandida actualmente

// Alternar colapso de detalles
const toggleExpand = (id) => {
    if (expandedId.value === id) {
        expandedId.value = null;
    } else {
        expandedId.value = id;
    }
};

// Cargar accidentes del backend
const fetchAccidentes = async () => {
    try {
        const response = await axios.get("/accidentes");
        accidentes.value = response.data;
    } catch (err) {
        console.error("Error al cargar el historial:", err);
    } finally {
        isLoading.value = false;
    }
};

// Estadísticas para las mini-tarjetas
const criticosCount = computed(
    () =>
        accidentes.value.filter(
            (a) => a.gravedad === "Crítico" || a.gravedad === "Alto",
        ).length,
);
const materialesCount = computed(
    () =>
        accidentes.value.filter((a) => a.tipo_accidente === "materiales")
            .length,
);
const victimasCount = computed(
    () =>
        accidentes.value.filter((a) => a.tipo_accidente === "victimas").length,
);

// Filtrado, búsqueda y ordenación de la lista (del último al primero)
const filteredAccidentes = computed(() => {
    // Clonar la lista y ordenar por ID de accidente de forma descendente (los más nuevos arriba)
    let list = [...accidentes.value].sort(
        (a, b) => b.id_accidente - a.id_accidente,
    );

    // Filtrar por consulta de texto (ID caso, dirección, municipio o descripción)
    if (searchQuery.value.trim() !== "") {
        const query = searchQuery.value.toLowerCase();
        list = list.filter(
            (a) =>
                (a.id_caso && a.id_caso.toLowerCase().includes(query)) ||
                (a.direccion && a.direccion.toLowerCase().includes(query)) ||
                (a.descripcion &&
                    a.descripcion.toLowerCase().includes(query)) ||
                (a.municipio && a.municipio.toLowerCase().includes(query)),
        );
    }

    // Filtrar por gravedad
    if (filterGravedad.value !== "") {
        list = list.filter((a) => a.gravedad === filterGravedad.value);
    }

    // Filtrar por tipo
    if (filterTipo.value !== "") {
        list = list.filter((a) => a.tipo_accidente === filterTipo.value);
    }

    return list;
});

onMounted(() => {
    fetchAccidentes();
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

.main-content {
    flex: 1;
    padding: 30px 40px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

/* Métricas superiores */
.metrics-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 25px;
}

.metric-mini-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 15px 20px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.m-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.m-value {
    font-size: 22px;
    font-weight: bold;
}

.text-danger {
    color: #f87171;
}
.text-info {
    color: var(--accent-blue);
}
.text-warning {
    color: var(--warning);
}

/* Filtros y buscador */
.filters-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-top: 25px;
    margin-bottom: 20px;
}

.search-box {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    padding: 0 15px;
    height: 45px;
    flex: 1;
    max-width: 450px;
    transition: 0.3s;
}

.search-box:focus-within {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.03);
}

.search-box i {
    font-size: 18px;
    color: var(--text-muted);
    margin-right: 12px;
}

.search-box input {
    background: transparent;
    border: none;
    color: var(--text-main);
    font-size: 13px;
    outline: none;
    width: 100%;
}

.search-box input::placeholder {
    color: var(--text-muted);
}

.filter-groups {
    display: flex;
    gap: 15px;
}

.filter-select {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-main);
    border-radius: 10px;
    padding: 0 15px;
    height: 45px;
    font-size: 13px;
    outline: none;
    cursor: pointer;
    transition: 0.3s;
}

.filter-select:focus {
    border-color: var(--accent-blue);
}

/* Spinner */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 0;
    gap: 15px;
    color: var(--text-muted);
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

/* Lista de historial */
.history-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 10px;
}

.history-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.history-card:hover {
    border-color: var(--accent-blue);
}

.card-header-main {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    cursor: pointer;
    user-select: none;
}

.card-title-block {
    display: flex;
    align-items: center;
    gap: 15px;
}

.severity-indicator {
    width: 4px;
    height: 35px;
    border-radius: 2px;
}

.bg-crítico,
.bg-critico {
    background-color: var(--critical);
}
.bg-alto {
    background-color: #ef4444;
}
.bg-medio {
    background-color: var(--warning);
}
.bg-bajo,
.bg-seguro {
    background-color: var(--safe);
}

.title-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.case-id {
    font-size: 14px;
    font-weight: bold;
    color: var(--accent-blue);
}

.badge {
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 4px;
    width: fit-content;
}

.badge-crítico,
.badge-critico {
    background-color: rgba(220, 38, 38, 0.15);
    color: #ef4444;
    border: 1px solid rgba(220, 38, 38, 0.3);
}
.badge-alto {
    background-color: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
.badge-medio {
    background-color: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.3);
}
.badge-bajo,
.badge-seguro {
    background-color: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.card-quick-meta {
    display: flex;
    align-items: center;
    gap: 30px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text-muted);
}

.meta-item i {
    font-size: 18px;
    color: var(--text-main);
}

.expand-arrow {
    font-size: 18px;
    color: var(--text-muted);
    transition: transform 0.3s ease;
}

.expanded .expand-arrow {
    transform: rotate(180deg);
}

/* Panel de detalles expandible */
.card-details-panel {
    background-color: rgba(0, 0, 0, 0.12);
    border-top: 1px solid var(--border-color);
    padding: 25px;
    animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.detail-block {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.detail-label {
    font-size: 11px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
}

.detail-label i {
    font-size: 14px;
    color: var(--accent-blue);
}

.detail-val {
    font-size: 13px;
    color: var(--text-main);
    line-height: 1.4;
}

.description-section {
    border-top: 1px solid var(--border-color);
    padding-top: 15px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.description-text {
    font-size: 13px;
    color: #d1d5db;
    line-height: 1.5;
    background-color: var(--bg-dark);
    padding: 15px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    margin: 0;
}

/* Estado vacío */
.empty-state {
    text-align: center;
    padding: 80px 0;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    color: var(--border-color);
}

.empty-state h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-main);
    margin: 0 0 8px 0;
}

.empty-state p {
    font-size: 13px;
    margin: 0;
}

/* Responsividad */
@media (max-width: 1024px) {
    .metrics-summary {
        grid-template-columns: repeat(2, 1fr);
    }
    .details-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    .search-box {
        max-width: 100%;
    }
    .card-quick-meta {
        display: none;
    } /* Ocultar meta rápida en mobile */
    .details-grid {
        grid-template-columns: 1fr;
    }
}
</style>
