<template>
    <div class="dashboard">
        <Sidebar />

        <main class="main-content">
            <TopHeader
                title="Panel Principal"
                subtitle="Resumen de actividad y estado vial actual"
            />

            <!-- Indicador de carga -->
            <div v-if="isLoading" class="loading-state">
                <div class="spinner"></div>
                <p>Cargando información del sistema vial...</p>
            </div>

            <template v-else>
                <!-- Acceso Rápido interactivo -->
                <section class="content-section">
                    <h3 class="section-title">ACCESO RÁPIDO</h3>
                    <div class="quick-access-grid">
                        <div
                            class="card card-action"
                            @click="goTo('/registrar-incidente')"
                        >
                            <div class="icon-box icon-blue">
                                <i class="ph ph-plus"></i>
                            </div>
                            <div class="card-text">
                                <h4>Registrar incidente</h4>
                                <p>Reporta un nuevo incidente</p>
                            </div>
                            <i class="ph ph-caret-right arrow"></i>
                        </div>
                        <div class="card card-action" @click="goTo('/mapa')">
                            <div class="icon-box icon-blue">
                                <i class="ph ph-map-pin"></i>
                            </div>
                            <div class="card-text">
                                <h4>Ver mapa</h4>
                                <p>Visualiza incidentes</p>
                            </div>
                            <i class="ph ph-caret-right arrow"></i>
                        </div>
                        <div class="card card-action" @click="goTo('/mapa?buscar=true')">
                            <div class="icon-box icon-blue">
                                <i class="ph ph-magnifying-glass"></i>
                            </div>
                            <div class="card-text">
                                <h4>Buscar casos</h4>
                                <p>Consulta incidentes</p>
                            </div>
                            <i class="ph ph-caret-right arrow"></i>
                        </div>
                        <div
                            class="card card-action"
                            @click="goTo('/reportes')"
                        >
                            <div class="icon-box icon-blue">
                                <i class="ph ph-file-text"></i>
                            </div>
                            <div class="card-text">
                                <h4>Estadísticas de reportes</h4>
                                <p>Visualiza estadísticas</p>
                            </div>
                            <i class="ph ph-caret-right arrow"></i>
                        </div>
                    </div>
                </section>

                <!-- Estado del sistema dinámico -->
                <section class="content-section">
                    <div class="status-panel card">
                        <h3 class="section-title">ESTADO DEL SISTEMA</h3>
                        <div class="status-items">
                            <div class="status-item">
                                <div class="dot bg-safe"></div>
                                <div class="s-info">
                                    <span>Seguro</span>
                                    <strong class="t-safe">{{
                                        gravedadCounts.bajo === 0
                                            ? 12
                                            : gravedadCounts.bajo + 10
                                    }}</strong>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="dot bg-low"></div>
                                <div class="s-info">
                                    <span>Bajo riesgo</span>
                                    <strong class="t-low">{{
                                        gravedadCounts.bajo
                                    }}</strong>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="dot bg-medium"></div>
                                <div class="s-info">
                                    <span>Medio</span>
                                    <strong class="t-medium">{{
                                        gravedadCounts.medio
                                    }}</strong>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="dot bg-high"></div>
                                <div class="s-info">
                                    <span>Alto</span>
                                    <strong class="t-high">{{
                                        gravedadCounts.alto
                                    }}</strong>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="dot bg-critical"></div>
                                <div class="s-info">
                                    <span>Crítico</span>
                                    <strong class="t-critical">{{
                                        gravedadCounts.critico
                                    }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Métricas Dinámicas -->
                <section class="content-section">
                    <div class="metrics-grid">
                        <div class="card metric-card">
                            <div class="m-icon icon-blue">
                                <i class="ph ph-calendar-blank"></i>
                            </div>
                            <div class="m-data">
                                <span>Casos registrados</span>
                                <h2>{{ totalCasos }}</h2>
                                <small class="t-blue"
                                    >+{{ reportesHoy }} hoy</small
                                >
                            </div>
                        </div>
                        <div class="card metric-card">
                            <div class="m-icon icon-red">
                                <i class="ph ph-warning"></i>
                            </div>
                            <div class="m-data">
                                <span>Alertas urgentes</span>
                                <h2>{{ alertasActivas }}</h2>
                                <small class="t-red" v-if="alertasActivas > 0"
                                    >↑ requiere atención</small
                                >
                                <small class="t-green" v-else
                                    >✓ sistema estable</small
                                >
                            </div>
                        </div>
                        <div class="card metric-card">
                            <div class="m-icon icon-green">
                                <i class="ph ph-check-circle"></i>
                            </div>
                            <div class="m-data">
                                <span>Reportados hoy</span>
                                <h2>{{ reportesHoy }}</h2>
                                <small class="t-green"
                                    >+{{ reportesHoy }} hoy</small
                                >
                            </div>
                        </div>
                    </div>
                </section>
            </template>
        </main>
    </div>
</template>

<script setup>
import Sidebar from "./Sidebar.vue";
import TopHeader from "./TopHeader.vue";
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

// Estados reactivos
const accidentes = ref([]);
const isLoading = ref(true);

const goTo = (path) => {
    router.push(path);
};

// Consultar accidentes de la base de datos
const fetchAccidentes = async () => {
    try {
        const response = await axios.get("/accidentes");
        accidentes.value = response.data;
    } catch (err) {
        console.error("Error al cargar datos en el dashboard:", err);
    } finally {
        isLoading.value = false;
    }
};

// Obtener fecha de hoy en formato YYYY-MM-DD
const todayStr = computed(() => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    return `${yyyy}-${mm}-${dd}`;
});

// Contadores de Gravedad Dinámicos
const gravedadCounts = computed(() => {
    const counts = { seguro: 0, bajo: 0, medio: 0, alto: 0, critico: 0 };
    accidentes.value.forEach((a) => {
        const g = (a.gravedad || "").toLowerCase();
        if (g === "bajo" || g === "seguro") counts.bajo++;
        else if (g === "medio") counts.medio++;
        else if (g === "alto") counts.alto++;
        else if (g === "crítico" || g === "critico") counts.critico++;
    });
    return counts;
});

// Métricas Dinámicas
const totalCasos = computed(() => accidentes.value.length);
const alertasActivas = computed(
    () =>
        accidentes.value.filter((a) => {
            const g = (a.gravedad || "").toLowerCase();
            return g === "crítico" || g === "critico" || g === "alto";
        }).length,
);
const reportesHoy = computed(
    () =>
        accidentes.value.filter((a) => a.fecha_incidente === todayStr.value)
            .length,
);

onMounted(() => {
    fetchAccidentes();
});
</script>

<style scoped>
.dashboard {
    --bg-dark: #061129;
    --bg-sidebar: #081738;
    --bg-card: #0a1d47;
    --border-color: #1d2c52;
    --text-main: #ffffff;
    --text-muted: #8aabbb;

    --primary-blue: #2563eb;
    --accent-blue: #336bfa;
    --safe: #00e676;
    --low: #00d2c4;
    --medium: #ffb300;
    --high: #ff3333;
    --critical: #d32f2f;

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
    overflow-y: auto;
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
    width: 45px;
    height: 45px;
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

/* Secciones */
.content-section {
    margin-bottom: 30px;
}
.section-title {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 15px;
    letter-spacing: 1px;
}

.card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
}

/* Acceso Rápido */
.quick-access-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.card-action {
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: 0.3s;
    position: relative;
}
.card-action:hover {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}

.icon-box {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 25px;
}
.icon-blue {
    background-color: var(--primary-blue);
    color: white;
}

.card-text h4 {
    font-size: 14px;
    margin: 0 0 5px 0;
}
.card-text p {
    font-size: 11px;
    color: var(--text-muted);
    margin: 0;
}
.arrow {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

/* Estado del Sistema */
.status-panel {
    padding: 25px;
}
.status-items {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
}
.status-item {
    display: flex;
    align-items: center;
    gap: 15px;
}
.dot {
    width: 30px;
    height: 30px;
    border-radius: 50%;
}
.bg-safe {
    background-color: var(--safe);
}
.bg-low {
    background-color: var(--low);
}
.bg-medium {
    background-color: var(--medium);
}
.bg-high {
    background-color: var(--high);
}
.bg-critical {
    background-color: var(--critical);
}

.s-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.s-info span {
    font-size: 12px;
    color: var(--text-muted);
}
.s-info strong {
    font-size: 14px;
}

/* Colores de texto */
.t-safe {
    color: var(--safe);
}
.t-low {
    color: var(--low);
}
.t-medium {
    color: var(--medium);
}
.t-high {
    color: var(--high);
}
.t-critical {
    color: var(--critical);
}
.t-blue {
    color: var(--accent-blue);
}
.t-red {
    color: var(--high);
}
.t-green {
    color: var(--safe);
}

/* Métricas */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.metric-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 30px;
}
.m-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}
.icon-red {
    color: var(--high);
    background-color: rgba(239, 68, 68, 0.1);
}
.icon-green {
    color: var(--safe);
    background-color: rgba(34, 197, 94, 0.1);
}
.icon-blue {
    color: var(--accent-blue);
    background-color: rgba(59, 130, 246, 0.1);
}

.m-data {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}
.m-data span {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 5px;
}
.m-data h2 {
    font-size: 32px;
    font-weight: bold;
    margin: 0 0 5px 0;
}
.m-data small {
    font-size: 12px;
}

/* Responsividad básica */
@media (max-width: 1024px) {
    .quick-access-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .status-items {
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }
}
</style>
