<template>
    <div class="dashboard">
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
                    <li class="nav-item" @click="goTo('/dashboard')">
                        <i class="ph ph-house"></i> Inicio
                    </li>
                    <li class="nav-item active">
                        <i class="ph ph-plus-square"></i> Registrar incidente
                    </li>
                    <li class="nav-item">
                        <i class="ph ph-magnifying-glass"></i> Buscar casos
                    </li>
                    <li class="nav-item" @click="goTo('/ver-mapa')">
                        <i class="ph ph-map-pin"></i> Ver mapa
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <p class="nav-title">SISTEMA</p>
                <ul class="nav-list">
                    <li class="nav-item">
                        <i class="ph ph-file-text"></i> Reportes
                    </li>
                    <li class="nav-item">
                        <i class="ph ph-clock-counter-clockwise"></i> Historial
                    </li>
                    <li class="nav-item">
                        <i class="ph ph-gear"></i> Configuración
                    </li>
                    <li class="nav-item">
                        <i class="ph ph-question"></i> Ayuda
                    </li>
                </ul>
            </div>

            <div class="logout" @click="handleLogout">
                <i class="ph ph-sign-out"></i> Salir de la cuenta
            </div>
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

            <div class="form-view">
                <div class="stepper-container">
                    <div class="car-icon-floating">
                        <i class="ph ph-car"></i>
                    </div>
                    <div class="stepper-background-line"></div>
                    <div class="stepper-active-line"></div>

                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <div class="details-card">
                    <div class="section-header">
                        <i class="ph ph-file-text"></i>
                        <div class="section-header-text">
                            <h3>Detalles del incidente</h3>
                            <p>
                                Proporciona información adicional sobre lo
                                ocurrido.
                            </p>
                        </div>
                    </div>

                    <form
                        id="details-form"
                        style="display: flex; flex-direction: column; gap: 25px"
                    >
                        <div class="form-group">
                            <label>Declaración</label>
                            <div
                                class="textarea-wrapper"
                                :class="{ error: declaracionError }"
                            >
                                <textarea
                                    id="declaracion-input"
                                    class="textarea-control"
                                    placeholder="Describe lo ocurrido..."
                                    maxlength="500"
                                    v-model="formData.declaracion"
                                ></textarea>
                                <span
                                    class="char-counter"
                                    :style="{ color: charCountColor }"
                                    >{{ charCount }}/500</span
                                >
                            </div>
                        </div>

                        <div class="selection-row">
                            <div class="row-label-box">Condición climática</div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.clima === 'soleado',
                                }"
                                @click="selectOption('clima', 'soleado')"
                            >
                                <i class="ph ph-sun"></i><span>Soleado</span>
                            </div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.clima === 'lluvioso',
                                }"
                                @click="selectOption('clima', 'lluvioso')"
                            >
                                <i class="ph ph-cloud-rain"></i
                                ><span>Lluvioso</span>
                            </div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.clima === 'nublado',
                                }"
                                @click="selectOption('clima', 'nublado')"
                            >
                                <i class="ph ph-cloud"></i><span>Nublado</span>
                            </div>
                        </div>

                        <div class="selection-row">
                            <div class="row-label-box">Tipo de vía</div>
                            <div
                                class="option-btn"
                                :class="{ selected: formData.via === 'urbana' }"
                                @click="selectOption('via', 'urbana')"
                            >
                                <i class="ph ph-buildings"></i
                                ><span>Urbana</span>
                            </div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.via === 'carretera',
                                }"
                                @click="selectOption('via', 'carretera')"
                            >
                                <i class="ph ph-road-horizon"></i
                                ><span>Carretera</span>
                            </div>
                            <div
                                class="option-btn"
                                :class="{ selected: formData.via === 'rural' }"
                                @click="selectOption('via', 'rural')"
                            >
                                <i class="ph ph-tree"></i><span>Rural</span>
                            </div>
                        </div>

                        <div class="selection-row">
                            <div class="row-label-box">
                                Estado del pavimento
                            </div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.pavimento === 'asfalto',
                                }"
                                @click="selectOption('pavimento', 'asfalto')"
                            >
                                <i class="ph ph-road"></i><span>Asfalto</span>
                            </div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.pavimento === 'polvo',
                                }"
                                @click="selectOption('pavimento', 'polvo')"
                            >
                                <i class="ph ph-wind"></i><span>Polvo</span>
                            </div>
                            <div
                                class="option-btn"
                                :class="{
                                    selected: formData.pavimento === 'piedra',
                                }"
                                @click="selectOption('pavimento', 'piedra')"
                            >
                                <i class="ph ph-dots-nine"></i
                                ><span>Piedra</span>
                            </div>
                        </div>
                    </form>
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
                        Continuar
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { reactive, computed, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useDatetime } from "../composables/useDatetime.js";
import { useIncidenteStore } from "../composables/useIncidenteStore.js";

const { currentDate, currentTime } = useDatetime();
const { state: incidenteState } = useIncidenteStore();

const router = useRouter();

// Estado reactivo para el formulario
const formData = reactive({
    declaracion: "",
    clima: "",
    via: "",
    pavimento: "",
});

const declaracionError = ref(false);

onMounted(() => {
    // Prioridad: 1) Manual (usuario ya editó) → 2) IA → 3) Vacío
    const savedDecl = localStorage.getItem('incidente_declaracion');
    const savedClima = localStorage.getItem('incidente_clima');
    const savedVia = localStorage.getItem('incidente_via');
    const savedPav = localStorage.getItem('incidente_pavimento');

    if (savedDecl || savedClima || savedVia || savedPav) {
        if (savedDecl) formData.declaracion = savedDecl;
        if (savedClima) formData.clima = savedClima;
        if (savedVia) formData.via = savedVia;
        if (savedPav) formData.pavimento = savedPav;
    } else {
        // Cargar desde la IA
        const iaDataRaw = localStorage.getItem('incidente_ia_data');
        if (iaDataRaw) {
            try {
                const iaData = JSON.parse(iaDataRaw);
                if (iaData.declaracion_involucrados) {
                    formData.declaracion = iaData.declaracion_involucrados;
                } else if (iaData.descripcion) {
                    formData.declaracion = iaData.descripcion;
                }

                // Mapear clima
                if (iaData.condicion_climatica) {
                    const climaStr = iaData.condicion_climatica.toLowerCase();
                    if (climaStr.includes('lluv') || climaStr.includes('mojado')) {
                        formData.clima = 'lluvioso';
                    } else if (climaStr.includes('nub') || climaStr.includes('niebla')) {
                        formData.clima = 'nublado';
                    } else {
                        formData.clima = 'soleado';
                    }
                }

                // Mapear vía
                if (iaData.tipo_via) {
                    const viaStr = iaData.tipo_via.toLowerCase();
                    if (viaStr.includes('carretera') || viaStr.includes('autopista')) {
                        formData.via = 'carretera';
                    } else if (viaStr.includes('rural') || viaStr.includes('tierra')) {
                        formData.via = 'rural';
                    } else {
                        formData.via = 'urbana';
                    }
                }

                // Mapear pavimento
                if (iaData.estado_pavimento) {
                    const pavStr = iaData.estado_pavimento.toLowerCase();
                    if (pavStr.includes('polvo') || pavStr.includes('tierra')) {
                        formData.pavimento = 'polvo';
                    } else if (pavStr.includes('piedra') || pavStr.includes('empedrado')) {
                        formData.pavimento = 'piedra';
                    } else {
                        formData.pavimento = 'asfalto';
                    }
                }
            } catch (e) {
                console.error('Error al cargar datos de IA en declaración', e);
            }
        }
    }
});

// Contador de caracteres
const charCount = computed(() => formData.declaracion.length);
const charCountColor = computed(() => {
    return charCount.value >= 500 ? "var(--critical)" : "var(--text-muted)";
});

// Función genérica para selección exclusiva por fila
const selectOption = (group, value) => {
    formData[group] = value;
};

// Navegación
const handleBack = () => {
    // Guardar avance actual al ir atrás
    localStorage.setItem('incidente_declaracion', formData.declaracion);
    localStorage.setItem('incidente_clima', formData.clima);
    localStorage.setItem('incidente_via', formData.via);
    localStorage.setItem('incidente_pavimento', formData.pavimento);
    router.push({ name: "registrar-incidente-ubicacion" });
};

const handleNext = () => {
    if (formData.declaracion.trim() === "") {
        alert("Por favor, describe lo ocurrido en la declaración.");
        declaracionError.value = true;
        const input = document.getElementById("declaracion-input");
        if (input) input.focus();
        return;
    }

    declaracionError.value = false;

    console.log("Avanzando al siguiente paso con los datos:", formData);
    
    // Guardar en localStorage
    localStorage.setItem('incidente_declaracion', formData.declaracion);
    localStorage.setItem('incidente_clima', formData.clima);
    localStorage.setItem('incidente_via', formData.via);
    localStorage.setItem('incidente_pavimento', formData.pavimento);

    router.push({ name: "registrar-incidente-involucrados" });
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

/* --- BARRA LATERAL --- */
.sidebar {
    width: 260px;
    background-color: var(--bg-sidebar);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    padding: 20px 0;
    flex-shrink: 0;
}
.user-profile {
    display: flex;
    align-items: center;
    padding: 0 20px 20px;
    border-bottom: 1px solid var(--border-color);
    gap: 12px;
}
.avatar {
    width: 40px;
    height: 40px;
    background-color: var(--primary-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    color: white;
}
.user-info h4 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}
.user-info span {
    font-size: 10px;
    color: var(--text-muted);
}
.menu-icon {
    margin-left: auto;
    cursor: pointer;
    color: var(--text-muted);
    font-size: 20px;
}

.nav-section {
    margin-top: 25px;
}
.nav-title {
    font-size: 11px;
    color: var(--text-muted);
    padding: 0 20px;
    margin-bottom: 10px;
    letter-spacing: 1px;
}
.nav-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.nav-item {
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-muted);
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}
.nav-item i {
    font-size: 18px;
}
.nav-item:hover {
    color: var(--text-main);
}
.nav-item.active {
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--text-main);
    border: 1px solid var(--primary-blue);
    border-radius: 8px;
    margin: 0 10px;
    padding: 12px 10px;
}
.logout {
    margin-top: auto;
    padding: 20px;
    color: var(--critical);
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
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
    margin: 0 0 5px 0;
}
.header-titles p {
    color: var(--text-muted);
    font-size: 14px;
    margin: 0;
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

/* --- VISTA DEL FORMULARIO --- */
.form-view {
    flex: 1;
    display: flex;
    flex-direction: column;
    max-width: 900px;
    margin: 0 auto;
    width: 100%;
}

/* Stepper - Paso 3 */
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

/* Línea activa cubriendo hasta el tercer punto (50%) */
.stepper-active-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 50%;
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
    z-index: 4;
}

/* Contenedor Principal */
.details-card {
    border: 1px solid var(--accent-blue);
    border-radius: 16px;
    padding: 40px 50px;
    background-color: transparent;
    margin-bottom: 30px;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 15px;
}
.section-header i {
    font-size: 24px;
    color: var(--text-main);
}
.section-header-text h3 {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
}
.section-header-text p {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
}

/* Área de Texto (Declaración) */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.form-group label {
    font-size: 14px;
    color: var(--text-main);
    margin-bottom: 5px;
}

.textarea-wrapper {
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 15px;
    position: relative;
    transition: 0.3s;
    display: flex;
    flex-direction: column;
}
.textarea-wrapper:focus-within {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}
.textarea-wrapper.error {
    border-color: var(--critical);
}

.textarea-control {
    background: transparent;
    border: none;
    color: var(--text-main);
    font-size: 14px;
    width: 100%;
    min-height: 100px;
    resize: none;
    outline: none;
}
.textarea-control::placeholder {
    color: var(--text-muted);
}

.char-counter {
    align-self: flex-end;
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 10px;
}

/* Cuadrículas de Selección */
.selection-row {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: 20px;
    align-items: stretch;
}

.row-label-box {
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    font-size: 14px;
    color: var(--text-main);
}

.option-btn {
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 15px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.option-btn i {
    font-size: 24px;
    color: var(--text-main);
    transition: 0.3s;
}
.option-btn span {
    font-size: 13px;
    color: var(--text-main);
    text-align: center;
}

.option-btn:hover {
    border-color: var(--accent-blue);
    background-color: rgba(255, 255, 255, 0.02);
}

/* Estado Activo de las Opciones */
.option-btn.selected {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.1);
}
.option-btn.selected i {
    color: var(--accent-blue);
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

/* Responsividad */
@media (max-width: 900px) {
    .stepper-container {
        display: none;
    }
    .selection-row {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    .row-label-box {
        padding: 15px;
        justify-content: center;
        background-color: rgba(255, 255, 255, 0.02);
    }
    .details-card {
        padding: 30px 20px;
    }
}
</style>
