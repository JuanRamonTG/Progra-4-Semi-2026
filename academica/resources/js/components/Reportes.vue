<template>
    <div class="dashboard">
        <Sidebar />

        <main class="main-content">
            <!-- Encabezado de página -->
            <TopHeader
                title="Estadísticas de reportes"
                subtitle="Análisis interactivo de datos históricos y distribución de incidentes"
            />

            <!-- Barra de Filtros y Acciones -->
            <div class="filters-actions-container" v-if="accidentes.length > 0">
                <div class="filters-bar">
                    <div class="filter-group">
                        <label for="filter-tempo">Temporalidad</label>
                        <select
                            id="filter-tempo"
                            v-model="selectedTemporalidad"
                            class="filter-select"
                        >
                            <option value="all">Todo el historial</option>
                            <option value="today">Hoy</option>
                            <option value="week">Últimos 7 días</option>
                            <option value="month">Último mes</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-mun">Distrito / Municipio</label>
                        <select
                            id="filter-mun"
                            v-model="selectedMunicipio"
                            class="filter-select"
                        >
                            <option value="">Todos los distritos</option>
                            <option
                                v-for="mun in uniqueMunicipios"
                                :key="mun"
                                :value="mun"
                            >
                                {{ mun }}
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-grav">Gravedad</label>
                        <select
                            id="filter-grav"
                            v-model="selectedGravedad"
                            class="filter-select"
                        >
                            <option value="">Todas las gravedades</option>
                            <option value="Crítico">Crítico</option>
                            <option value="Alto">Alto</option>
                            <option value="Medio">Medio</option>
                            <option value="Bajo">Bajo</option>
                        </select>
                    </div>
                </div>

                <div class="actions-bar">
                    <button
                        class="btn btn-secondary"
                        @click="exportToCSV"
                        title="Descargar datos en formato CSV"
                    >
                        <i class="ph ph-file-csv"></i> Exportar CSV
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="exportReport"
                        title="Imprimir informe en PDF / Físico"
                    >
                        <i class="ph ph-printer"></i> Imprimir Reporte
                    </button>
                </div>
            </div>

            <!-- Membrete exclusivo para Impresión Física -->
            <div class="print-header">
                <h2>STELLAR TRAFFIC - INFORME ESTADÍSTICO</h2>
                <p>
                    Generado automáticamente por el Sistema de Control de
                    Tránsito
                </p>
                <div class="print-metadata">
                    <span
                        ><strong>Fecha de Impresión:</strong>
                        {{ formatCurrentDate() }}</span
                    >
                    <span
                        ><strong>Filtros Aplicados:</strong>
                        {{ getAppliedFiltersText() }}</span
                    >
                </div>
            </div>

            <!-- Pantalla de carga -->
            <div v-if="isLoading" class="loading-state">
                <div class="spinner"></div>
                <p>Procesando estadísticas de la base de datos...</p>
            </div>

            <!-- Sin datos en absoluto en la Base de Datos -->
            <div v-else-if="accidentes.length === 0" class="empty-state">
                <i class="ph ph-chart-line-down"></i>
                <h2>No hay suficientes datos</h2>
                <p>
                    Registra incidentes primero para poder generar informes
                    estadísticos.
                </p>
            </div>

            <!-- Si hay datos, pero el filtro los excluye a todos -->
            <div
                v-else-if="filteredAccidentes.length === 0"
                class="empty-state"
            >
                <i class="ph ph-funnel-x"></i>
                <h2>Sin resultados para los filtros aplicados</h2>
                <p>
                    Prueba ajustando los criterios de temporalidad, distrito o
                    gravedad arriba.
                </p>
                <button
                    class="btn btn-secondary"
                    style="margin-top: 15px; margin-inline: auto"
                    @click="resetFilters"
                >
                    Restablecer Filtros
                </button>
            </div>

            <!-- Dashboard de Estadísticas Activo -->
            <div v-else class="statistics-dashboard">
                <!-- KPIs Superiores Interactivos -->
                <div class="kpis-grid">
                    <div class="kpi-card">
                        <div class="kpi-icon icon-blue-glow">
                            <i class="ph ph-files"></i>
                        </div>
                        <div class="kpi-details">
                            <span class="kpi-label">Incidentes Filtrados</span>
                            <h3 class="kpi-value">
                                {{ filteredAccidentes.length }}
                            </h3>
                            <span class="kpi-subtext"
                                >de {{ accidentes.length }} registrados en
                                total</span
                            >
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon icon-red-glow">
                            <i class="ph ph-warning-octagon"></i>
                        </div>
                        <div class="kpi-details">
                            <span class="kpi-label">Índice Crítico / Alto</span>
                            <h3 class="kpi-value text-danger">
                                {{ criticalRatio }}%
                            </h3>
                            <span class="kpi-subtext"
                                >{{ criticalCount }} incidentes de alto
                                riesgo</span
                            >
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon icon-orange-glow">
                            <i class="ph ph-map-pin"></i>
                        </div>
                        <div class="kpi-details">
                            <span class="kpi-label">Zona de Mayor Riesgo</span>
                            <h3 class="kpi-value text-warning">
                                {{ topMunicipioName }}
                            </h3>
                            <span class="kpi-subtext"
                                >{{ topMunicipioCount }} reportes en esta
                                zona</span
                            >
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon icon-green-glow">
                            <i class="ph ph-thermometer"></i>
                        </div>
                        <div class="kpi-details">
                            <span class="kpi-label">Clima Predominante</span>
                            <h3 class="kpi-value text-success">
                                {{ topClimaName }}
                            </h3>
                            <span class="kpi-subtext"
                                >Presente en
                                {{ topClimaCount }} incidentes</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Gráficos del Reporte -->
                <div class="report-grid">
                    <!-- Tarjeta 1: Distribución por Gravedad -->
                    <div class="report-card">
                        <div class="card-title-header">
                            <i class="ph ph-chart-bar"></i>
                            <div class="header-text-wrapper">
                                <h4>Distribución de Gravedad</h4>
                                <p>
                                    Porcentaje de incidentes según el nivel de
                                    urgencia
                                </p>
                            </div>
                        </div>
                        <div class="stat-items-group">
                            <div
                                class="stat-progress-item"
                                v-for="item in gravedadStats"
                                :key="item.name"
                            >
                                <div class="item-info">
                                    <span class="item-name">
                                        <span
                                            class="status-dot-indicator"
                                            :class="item.class"
                                        ></span>
                                        {{ item.name }}
                                    </span>
                                    <span class="item-count">
                                        <strong>{{ item.count }}</strong> cas.
                                        <span class="pct-badge"
                                            >({{ item.percentage }}%)</span
                                        >
                                    </span>
                                </div>
                                <div class="progress-bar-container">
                                    <div
                                        class="progress-bar-fill"
                                        :class="item.class"
                                        :style="{
                                            width: item.percentage + '%',
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Tipos de Incidentes -->
                    <div class="report-card">
                        <div class="card-title-header">
                            <i class="ph ph-chart-pie"></i>
                            <div class="header-text-wrapper">
                                <h4>Tipos de Incidentes</h4>
                                <p>
                                    Contraste entre daños físicos y materiales
                                </p>
                            </div>
                        </div>
                        <div class="types-distribution">
                            <div class="comparison-bar-wrapper">
                                <div class="dual-progress-bar">
                                    <div
                                        class="fill-victimas"
                                        :style="{
                                            width:
                                                typeStats.victimas.percentage +
                                                '%',
                                        }"
                                        v-if="typeStats.victimas.percentage > 0"
                                        title="Con víctimas"
                                    ></div>
                                    <div
                                        class="fill-materiales"
                                        :style="{
                                            width:
                                                typeStats.materiales
                                                    .percentage + '%',
                                        }"
                                        v-if="
                                            typeStats.materiales.percentage > 0
                                        "
                                        title="Daños materiales"
                                    ></div>
                                </div>

                                <div class="bar-legends-row">
                                    <div class="bar-legend">
                                        <span
                                            class="legend-dot bg-victimas"
                                        ></span>
                                        <span class="legend-label"
                                            >Con víctimas</span
                                        >
                                        <span class="legend-val"
                                            >({{
                                                typeStats.victimas.percentage
                                            }}%)</span
                                        >
                                    </div>
                                    <div class="bar-legend">
                                        <span
                                            class="legend-dot bg-materiales"
                                        ></span>
                                        <span class="legend-label"
                                            >Daños materiales</span
                                        >
                                        <span class="legend-val"
                                            >({{
                                                typeStats.materiales.percentage
                                            }}%)</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="grid-2-cols">
                                <div class="mini-data-box text-warning-box">
                                    <h5>Víctimas</h5>
                                    <h3>{{ typeStats.victimas.count }}</h3>
                                    <span>Incidentes graves</span>
                                </div>
                                <div class="mini-data-box text-info-box">
                                    <h5>Daños Mat.</h5>
                                    <h3>{{ typeStats.materiales.count }}</h3>
                                    <span>Solo colisión</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Top Zonas/Municipios con Mayor Incidencia -->
                    <div class="report-card">
                        <div class="card-title-header">
                            <i class="ph ph-map-pin-line"></i>
                            <div class="header-text-wrapper">
                                <h4>Top Distritos / Municipios</h4>
                                <p>Zonas con mayor frecuencia de reportes</p>
                            </div>
                        </div>
                        <div
                            class="stat-items-group"
                            v-if="municipioStats.length > 0"
                        >
                            <div
                                class="stat-progress-item"
                                v-for="(item, idx) in municipioStats"
                                :key="item.name"
                            >
                                <div class="item-info">
                                    <span class="item-name">
                                        <strong
                                            class="ranking-badge"
                                            :class="'rank-' + (idx + 1)"
                                            >#{{ idx + 1 }}</strong
                                        >
                                        {{ item.name }}
                                    </span>
                                    <span class="item-count"
                                        ><strong>{{ item.count }}</strong>
                                        reportes</span
                                    >
                                </div>
                                <div class="progress-bar-container">
                                    <div
                                        class="progress-bar-fill bg-blue-gradient"
                                        :style="{
                                            width: item.percentage + '%',
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="no-data-mini">
                            Sin datos de distritos en la selección
                        </div>
                    </div>

                    <!-- Tarjeta 4: Factores Viales y Ambientales -->
                    <div class="report-card">
                        <div class="card-title-header">
                            <i class="ph ph-cloud-sun"></i>
                            <div class="header-text-wrapper">
                                <h4>Factores Viales y Ambientales</h4>
                                <p>
                                    Condición del entorno al momento del suceso
                                </p>
                            </div>
                        </div>
                        <div class="factors-grid">
                            <div class="factor-column">
                                <span class="factor-title"
                                    >Condición Climática</span
                                >
                                <div class="factor-cards">
                                    <div
                                        v-for="item in climaStats"
                                        :key="item.name"
                                        class="factor-mini-card"
                                    >
                                        <div class="factor-card-left">
                                            <i
                                                class="ph"
                                                :class="getClimaIcon(item.name)"
                                            ></i>
                                            <span class="factor-name">{{
                                                item.name || "No especificado"
                                            }}</span>
                                        </div>
                                        <strong class="factor-count">{{
                                            item.count
                                        }}</strong>
                                    </div>
                                    <div
                                        v-if="climaStats.length === 0"
                                        class="no-data-mini"
                                    >
                                        Sin datos
                                    </div>
                                </div>
                            </div>

                            <div class="factor-column">
                                <span class="factor-title"
                                    >Tipo de Calzada</span
                                >
                                <div class="factor-cards">
                                    <div
                                        v-for="item in viaStats"
                                        :key="item.name"
                                        class="factor-mini-card"
                                    >
                                        <div class="factor-card-left">
                                            <i
                                                class="ph"
                                                :class="getViaIcon(item.name)"
                                            ></i>
                                            <span class="factor-name">{{
                                                item.name || "No especificado"
                                            }}</span>
                                        </div>
                                        <strong class="factor-count">{{
                                            item.count
                                        }}</strong>
                                    </div>
                                    <div
                                        v-if="viaStats.length === 0"
                                        class="no-data-mini"
                                    >
                                        Sin datos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Firma para Reportes Impresos -->
                <div class="print-signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p>Firma del Oficial a Cargo</p>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p>Sello de Autorización</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from "./Sidebar.vue";
import TopHeader from "./TopHeader.vue";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

// Estados reactivos
const accidentes = ref([]);
const isLoading = ref(true);

// Filtros seleccionados
const selectedTemporalidad = ref("all");
const selectedMunicipio = ref("");
const selectedGravedad = ref("");

// Cargar accidentes de la base de datos
const fetchAccidentes = async () => {
    try {
        const response = await axios.get("/accidentes");
        accidentes.value = response.data;
    } catch (err) {
        console.error("Error al cargar datos de reportes:", err);
    } finally {
        isLoading.value = false;
    }
};

// Obtener municipios únicos para el selector
const uniqueMunicipios = computed(() => {
    const list = accidentes.value.map((a) => a.municipio).filter(Boolean);
    return [...new Set(list)].sort();
});

// Filtrar accidentes
const filteredAccidentes = computed(() => {
    return accidentes.value.filter((a) => {
        // 1. Filtro de temporalidad
        if (selectedTemporalidad.value !== "all") {
            const fechaAccidente = new Date(a.created_at);
            const hoy = new Date();

            // Poner horas a cero para comparar solo fechas
            const fechaAccidenteZero = new Date(
                fechaAccidente.getFullYear(),
                fechaAccidente.getMonth(),
                fechaAccidente.getDate(),
            );
            const hoyZero = new Date(
                hoy.getFullYear(),
                hoy.getMonth(),
                hoy.getDate(),
            );

            const diffTime = Math.abs(hoyZero - fechaAccidenteZero);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (selectedTemporalidad.value === "today" && diffDays > 0) {
                // Verificar si es estrictamente hoy
                const esHoy =
                    fechaAccidente.getDate() === hoy.getDate() &&
                    fechaAccidente.getMonth() === hoy.getMonth() &&
                    fechaAccidente.getFullYear() === hoy.getFullYear();
                if (!esHoy) return false;
            } else if (selectedTemporalidad.value === "week" && diffDays > 7) {
                return false;
            } else if (
                selectedTemporalidad.value === "month" &&
                diffDays > 30
            ) {
                return false;
            }
        }

        // 2. Filtro de Municipio
        if (
            selectedMunicipio.value &&
            a.municipio !== selectedMunicipio.value
        ) {
            return false;
        }

        // 3. Filtro de Gravedad
        if (selectedGravedad.value) {
            const grav = a.gravedad || "Medio";
            if (grav.toLowerCase() !== selectedGravedad.value.toLowerCase()) {
                return false;
            }
        }

        return true;
    });
});

// --- CÁLCULO DE KPIs DINÁMICOS ---

const criticalCount = computed(() => {
    return filteredAccidentes.value.filter(
        (a) => a.gravedad === "Crítico" || a.gravedad === "Alto",
    ).length;
});

const criticalRatio = computed(() => {
    const total = filteredAccidentes.value.length;
    if (total === 0) return 0;
    return Math.round((criticalCount.value / total) * 100);
});

const topMunicipioName = computed(() => {
    if (filteredAccidentes.value.length === 0) return "Ninguno";
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        if (a.municipio) counts[a.municipio] = (counts[a.municipio] || 0) + 1;
    });
    const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
    return sorted.length > 0 ? sorted[0][0] : "N/A";
});

const topMunicipioCount = computed(() => {
    if (filteredAccidentes.value.length === 0) return 0;
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        if (a.municipio) counts[a.municipio] = (counts[a.municipio] || 0) + 1;
    });
    const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
    return sorted.length > 0 ? sorted[0][1] : 0;
});

const topClimaName = computed(() => {
    if (filteredAccidentes.value.length === 0) return "Ninguno";
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        const c = a.condicion_climatica || "Despejado";
        counts[c] = (counts[c] || 0) + 1;
    });
    const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
    return sorted.length > 0 ? sorted[0][0] : "Despejado";
});

const topClimaCount = computed(() => {
    if (filteredAccidentes.value.length === 0) return 0;
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        const c = a.condicion_climatica || "Despejado";
        counts[c] = (counts[c] || 0) + 1;
    });
    const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
    return sorted.length > 0 ? sorted[0][1] : 0;
});

// --- CÁLCULO DE GRÁFICOS DINÁMICOS ---

// 1. Estadísticas de gravedad
const gravedadStats = computed(() => {
    const counts = { Crítico: 0, Alto: 0, Medio: 0, Bajo: 0 };
    filteredAccidentes.value.forEach((a) => {
        const g = a.gravedad || "Medio";
        if (counts[g] !== undefined) {
            counts[g]++;
        } else {
            counts["Medio"]++;
        }
    });

    const total = filteredAccidentes.value.length || 1;
    return [
        {
            name: "Crítico",
            count: counts["Crítico"],
            percentage: Math.round((counts["Crítico"] / total) * 100),
            class: "bg-critical",
        },
        {
            name: "Alto",
            count: counts["Alto"],
            percentage: Math.round((counts["Alto"] / total) * 100),
            class: "bg-alto",
        },
        {
            name: "Medio",
            count: counts["Medio"],
            percentage: Math.round((counts["Medio"] / total) * 100),
            class: "bg-medio",
        },
        {
            name: "Bajo / Seguro",
            count: counts["Bajo"],
            percentage: Math.round((counts["Bajo"] / total) * 100),
            class: "bg-bajo",
        },
    ];
});

// 2. Estadísticas de tipo de accidente
const typeStats = computed(() => {
    let victimas = 0;
    let materiales = 0;
    filteredAccidentes.value.forEach((a) => {
        if (a.tipo_accidente === "victimas") victimas++;
        else materiales++;
    });

    const total = filteredAccidentes.value.length || 1;
    return {
        victimas: {
            count: victimas,
            percentage: Math.round((victimas / total) * 100),
        },
        materiales: {
            count: materiales,
            percentage: Math.round((materiales / total) * 100),
        },
    };
});

// 3. Top municipios / distritos
const municipioStats = computed(() => {
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        if (a.municipio) {
            counts[a.municipio] = (counts[a.municipio] || 0) + 1;
        }
    });

    // Ordenar y tomar los top 3
    const sorted = Object.entries(counts)
        .map(([name, count]) => ({ name, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 3);

    // Encontrar el valor más alto para representar el 100% de la barra de progreso
    const maxVal = sorted.length > 0 ? sorted[0].count : 1;

    return sorted.map((item) => ({
        ...item,
        percentage: Math.round((item.count / maxVal) * 100),
    }));
});

// 4. Estadísticas del clima (top 3)
const climaStats = computed(() => {
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        const c = a.condicion_climatica || "Despejado";
        counts[c] = (counts[c] || 0) + 1;
    });
    return Object.entries(counts)
        .map(([name, count]) => ({ name, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 3);
});

// 5. Estadísticas de tipo de vía (top 3)
const viaStats = computed(() => {
    const counts = {};
    filteredAccidentes.value.forEach((a) => {
        const v = a.tipo_via || "Calle urbana";
        counts[v] = (counts[v] || 0) + 1;
    });
    return Object.entries(counts)
        .map(([name, count]) => ({ name, count }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 3);
});

// Iconos de clima representativos
const getClimaIcon = (clima) => {
    const c = (clima || "").toLowerCase();
    if (c.includes("despejado") || c.includes("soleado")) return "ph-sun";
    if (c.includes("lluv") || c.includes("humed")) return "ph-cloud-rain";
    if (c.includes("nub") || c.includes("neblin")) return "ph-cloud";
    if (c.includes("vien") || c.includes("torment")) return "ph-wind";
    return "ph-thermometer-cold";
};

// Iconos de calzada representativos
const getViaIcon = (via) => {
    const v = (via || "").toLowerCase();
    if (v.includes("calle") || v.includes("urban")) return "ph-road-horizon";
    if (v.includes("auto") || v.includes("carret")) return "ph-compass";
    if (v.includes("rural") || v.includes("tierra")) return "ph-tree-palm";
    return "ph-signpost";
};

// Restablecer filtros
const resetFilters = () => {
    selectedTemporalidad.value = "all";
    selectedMunicipio.value = "";
    selectedGravedad.value = "";
};

// Formatear fecha actual
const formatCurrentDate = () => {
    const d = new Date();
    return d.toLocaleString("es-ES", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Obtener filtros aplicados en texto legible
const getAppliedFiltersText = () => {
    const parts = [];
    if (selectedTemporalidad.value !== "all") {
        const label = {
            today: "Hoy",
            week: "Últimos 7 días",
            month: "Último mes",
        }[selectedTemporalidad.value];
        parts.push(`Temporalidad: ${label}`);
    } else {
        parts.push("Temporalidad: Histórico total");
    }

    if (selectedMunicipio.value) {
        parts.push(`Distrito: ${selectedMunicipio.value}`);
    } else {
        parts.push("Distritos: Todos");
    }

    if (selectedGravedad.value) {
        parts.push(`Gravedad: ${selectedGravedad.value}`);
    } else {
        parts.push("Gravedades: Todas");
    }

    return parts.join(" | ");
};

// Exportar a CSV local
const exportToCSV = () => {
    if (filteredAccidentes.value.length === 0) return;

    // Crear cabeceras
    const headers = [
        "ID Accidente",
        "ID Caso",
        "Municipio/Distrito",
        "Dirección",
        "Gravedad",
        "Tipo Accidente",
        "Clima",
        "Calzada",
        "Fecha Registro",
    ];

    // Mapear datos
    const rows = filteredAccidentes.value.map((a) => [
        a.id_accidente,
        a.id_caso || "",
        a.municipio || "",
        `"${(a.direccion || "").replace(/"/g, '""')}"`,
        a.gravedad || "Medio",
        a.tipo_accidente === "victimas" ? "Con víctimas" : "Daños materiales",
        a.condicion_climatica || "Despejado",
        a.tipo_via || "Calle urbana",
        a.created_at,
    ]);

    // Unir contenido
    const csvContent =
        "data:text/csv;charset=utf-8,\uFEFF" +
        [headers.join(","), ...rows.map((e) => e.join(","))].join("\n");

    // Crear elemento de descarga temporal
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);

    const timestamp = new Date().toISOString().slice(0, 10);
    link.setAttribute("download", `reporte_estadistico_${timestamp}.csv`);
    document.body.appendChild(link);

    link.click();
    document.body.removeChild(link);
};

// Lanzar diálogo de impresión
const exportReport = () => {
    window.print();
};

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

/* Barra de Filtros y Acciones */
.filters-actions-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 20px;
    background-color: rgba(19, 26, 44, 0.4);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 20px 25px;
    margin-top: 25px;
    margin-bottom: 20px;
    backdrop-filter: blur(8px);
}

.filters-bar {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    flex: 1;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 180px;
    flex: 1;
}

.filter-group label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #8AABBB !important;
    font-weight: 600;
}

.filter-select {
    background-color: var(--bg-sidebar, #081738);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-main);
    padding: 10px 14px;
    font-size: 13px;
    outline: none;
    cursor: pointer;
    transition: 0.2s;
}

.filter-select:focus {
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
}

.actions-bar {
    display: flex;
    gap: 12px;
}

.btn {
    padding: 11px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background-color: var(--primary-blue);
    border: 1px solid var(--primary-blue);
    color: white;
}

.btn-primary:hover {
    background-color: var(--accent-blue);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

.btn-secondary {
    background-color: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-main);
}

.btn-secondary:hover {
    background-color: rgba(255, 255, 255, 0.05);
    border-color: var(--text-muted);
}

/* Spinner de carga */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 100px 0;
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

/* Membrete Oculto en Web */
.print-header,
.print-signature-section {
    display: none;
}

/* KPIs Grid (Glassmorphism) */
.kpis-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.kpi-card {
    background-color: var(--bg-card) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 16px;
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    backdrop-filter: blur(12px);
}

.kpi-card:hover {
    transform: translateY(-4px);
    border-color: rgba(59, 130, 246, 0.25);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.icon-blue-glow {
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--accent-blue);
}

.icon-red-glow {
    background-color: rgba(239, 44, 44, 0.1);
    color: var(--critical);
}

.icon-orange-glow {
    background-color: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.icon-green-glow {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--safe);
}

.kpi-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.kpi-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #8AABBB !important;
    font-weight: 600;
}

.kpi-value {
    font-size: 22px;
    font-weight: 700;
    margin: 4px 0 2px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.kpi-subtext {
    font-size: 11px;
    color: #8AABBB !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.text-danger {
    color: var(--critical) !important;
}
.text-warning {
    color: var(--warning) !important;
}
.text-success {
    color: var(--safe) !important;
}

/* Grid de Reportes */
.report-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

.report-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 30px;
    display: flex;
    flex-direction: column;
    gap: 25px;
    transition: 0.2s;
}

.report-card:hover {
    border-color: rgba(255, 255, 255, 0.08);
}

.card-title-header {
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 18px;
}

.card-title-header i {
    font-size: 24px;
    color: var(--accent-blue);
}

.header-text-wrapper {
    display: flex;
    flex-direction: column;
}

.card-title-header h4 {
    font-size: 15px;
    font-weight: 600;
    margin: 0;
}

.header-text-wrapper p {
    font-size: 11px;
    color: #8AABBB !important;
    margin: 2px 0 0 0;
}

/* Barras de Progreso e Items */
.stat-items-group {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.stat-progress-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.item-info {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    align-items: center;
}

.item-name {
    color: var(--text-main);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-dot-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.item-count {
    color: #8AABBB !important;
}

.pct-badge {
    color: #ffffff !important;
    font-weight: 600;
    margin-left: 4px;
}

.progress-bar-container {
    width: 100%;
    height: 8px;
    background-color: rgba(255, 255, 255, 0.04);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Colores de barras estilizados */
.bg-critical {
    background: linear-gradient(90deg, #b91c1c, #ef4444);
    box-shadow: 0 0 8px rgba(239, 68, 68, 0.2);
}
.bg-alto {
    background: linear-gradient(90deg, #d97706, #f59e0b);
    box-shadow: 0 0 8px rgba(245, 158, 11, 0.2);
}
.bg-medio {
    background: linear-gradient(90deg, #eab308, #fef08a);
    box-shadow: 0 0 8px rgba(234, 179, 8, 0.2);
}
.bg-bajo {
    background: linear-gradient(90deg, #059669, #10b981);
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.2);
}
.bg-blue-gradient {
    background: linear-gradient(90deg, #1d4ed8, #3b82f6);
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.2);
}

/* Badges de Ranking */
.ranking-badge {
    padding: 2px 7px;
    border-radius: 5px;
    font-size: 10px;
    font-weight: 700;
}

.rank-1 {
    background-color: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
.rank-2 {
    background-color: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.3);
}
.rank-3 {
    background-color: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

/* Distribución por Tipo de Accidentes */
.types-distribution {
    display: flex;
    flex-direction: column;
    gap: 22px;
}

.comparison-bar-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.dual-progress-bar {
    width: 100%;
    height: 12px;
    background-color: rgba(255, 255, 255, 0.04);
    border-radius: 6px;
    overflow: hidden;
    display: flex;
}

.fill-victimas {
    height: 100%;
    background: linear-gradient(90deg, #d97706, #f59e0b);
    transition: width 0.8s ease;
}

.fill-materiales {
    height: 100%;
    background: linear-gradient(90deg, #1d4ed8, #3b82f6);
    transition: width 0.8s ease;
}

.bar-legends-row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.bar-legend {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.bg-victimas {
    background-color: var(--warning);
}
.bg-materiales {
    background-color: var(--accent-blue);
}

.legend-label {
    color: #8AABBB !important;
}
.legend-val {
    color: var(--text-main);
    font-weight: 600;
}

.mini-data-box {
    background-color: rgba(11, 16, 30, 0.4);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 18px 15px;
    text-align: center;
    transition: 0.2s;
}

.mini-data-box:hover {
    background-color: rgba(11, 16, 30, 0.7);
    border-color: rgba(255, 255, 255, 0.08);
}

.mini-data-box h5 {
    font-size: 11px;
    color: #8AABBB !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 6px 0;
}

.mini-data-box h3 {
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 4px 0;
}

.mini-data-box span {
    font-size: 10px;
    color: #8AABBB !important;
}

.text-warning-box h3 {
    color: var(--warning);
}
.text-info-box h3 {
    color: var(--accent-blue);
}

/* Grid de Factores */
.factors-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.factor-column {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.factor-title {
    font-size: 12px;
    font-weight: 600;
    color: #8AABBB !important;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 8px;
}

.factor-cards {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.factor-mini-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: rgba(11, 16, 30, 0.3);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    transition: 0.2s;
}

.factor-mini-card:hover {
    border-color: rgba(255, 255, 255, 0.08);
    background-color: rgba(11, 16, 30, 0.5);
}

.factor-card-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.factor-card-left i {
    font-size: 16px;
    color: var(--accent-blue);
}

.factor-name {
    color: var(--text-main);
    font-weight: 500;
}

.factor-count {
    background-color: rgba(255, 255, 255, 0.05);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 11px;
    color: #8AABBB !important;
}

.no-data-mini {
    text-align: center;
    color: #8AABBB !important;
    font-size: 13px;
    padding: 20px 0;
}

/* Estado vacío */
.empty-state {
    text-align: center;
    padding: 80px 0;
    color: #8AABBB !important;
    background-color: rgba(19, 26, 44, 0.3);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    margin-top: 25px;
}

.empty-state i {
    font-size: 56px;
    margin-bottom: 18px;
    color: var(--border-color);
    display: inline-block;
}

.empty-state h2 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-main);
    margin: 0 0 6px 0;
}

.empty-state p {
    font-size: 13px;
    margin: 0;
}

/* --- ESTILOS DE IMPRESIÓN (window.print) --- */
@media print {
    .sidebar,
    .filters-actions-container,
    header,
    .header-actions,
    .main-content > :first-child {
        display: none !important;
    }

    .main-content {
        padding: 0 !important;
        background-color: white !important;
        color: black !important;
        overflow: visible !important;
    }

    .dashboard {
        background-color: white !important;
        color: black !important;
        height: auto !important;
        display: block !important;
    }

    /* Mostrar encabezado de impresión */
    .print-header {
        display: block !important;
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
        margin-bottom: 30px;
        text-align: center;
    }

    .print-header h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: black !important;
    }

    .print-header p {
        font-size: 12px;
        color: #555 !important;
        margin: 0 0 15px 0;
    }

    .print-metadata {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        border-top: 1px dashed #ccc;
        padding-top: 10px;
        color: #444 !important;
    }

    /* Adaptar KPIs a la impresión */
    .kpis-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px !important;
        margin-bottom: 30px !important;
        page-break-inside: avoid;
    }

    .kpi-card {
        background-color: #f9f9f9 !important;
        border: 1px solid #ddd !important;
        color: black !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .kpi-label,
    .kpi-subtext {
        color: #555 !important;
    }

    .kpi-icon {
        background-color: #eaeaea !important;
        color: black !important;
    }

    /* Adaptar Reportes a la Impresión */
    .report-grid {
        grid-template-columns: 1fr !important;
        gap: 25px !important;
        margin-bottom: 30px !important;
    }

    .report-card {
        background-color: white !important;
        border: 1px solid #ddd !important;
        color: black !important;
        page-break-inside: avoid;
        box-shadow: none !important;
        padding: 20px !important;
    }

    .card-title-header {
        border-bottom: 1px solid #ddd !important;
        padding-bottom: 10px !important;
    }

    .card-title-header h4 {
        color: black !important;
    }

    .card-title-header p,
    .header-text-wrapper p {
        color: #555 !important;
    }

    .card-title-header i {
        color: black !important;
    }

    .item-name {
        color: black !important;
    }

    .item-count {
        color: #444 !important;
    }

    .mini-data-box {
        background-color: #f9f9f9 !important;
        border: 1px solid #ddd !important;
        color: black !important;
    }

    .mini-data-box h5,
    .mini-data-box span {
        color: #555 !important;
    }

    .text-warning-box h3,
    .text-info-box h3 {
        color: black !important;
    }

    .factor-mini-card {
        background-color: #f9f9f9 !important;
        border: 1px solid #ddd !important;
        color: black !important;
    }

    .factor-title {
        color: black !important;
        border-bottom: 1px solid #ddd !important;
    }

    .factor-name {
        color: black !important;
    }

    .factor-card-left i {
        color: black !important;
    }

    .progress-bar-container,
    .dual-progress-bar {
        background-color: #eaeaea !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .progress-bar-fill,
    .fill-victimas,
    .fill-materiales {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Mostrar firmas al imprimir */
    .print-signature-section {
        display: flex !important;
        justify-content: space-around;
        margin-top: 50px;
        page-break-inside: avoid;
    }

    .signature-box {
        text-align: center;
        width: 200px;
    }

    .signature-line {
        border-bottom: 1px solid black;
        height: 40px;
        margin-bottom: 10px;
    }

    .signature-box p {
        font-size: 12px;
        color: black !important;
        margin: 0;
    }
}

/* Responsividad */
@media (max-width: 1024px) {
    .kpis-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .report-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .kpis-grid {
        grid-template-columns: 1fr;
    }

    .filters-actions-container {
        flex-direction: column;
        align-items: stretch;
    }

    .actions-bar {
        justify-content: flex-end;
    }

    .factors-grid {
        grid-template-columns: 1fr;
    }
}
</style>
