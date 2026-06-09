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
                    <li class="nav-item" @click="goTo('/dashboard')"><i class="ph ph-house"></i> Inicio</li>
                    <li class="nav-item active"><i class="ph ph-plus-square"></i> Registrar incidente</li>
                    <li class="nav-item"><i class="ph ph-magnifying-glass"></i> Buscar casos</li>
                    <li class="nav-item" @click="goTo('/ver-mapa')"><i class="ph ph-map-pin"></i> Ver mapa</li>
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
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <div class="form-card">
                    <div class="section-header">
                        <i class="ph ph-map-pin"></i>
                        <h3>Ubicación del incidente</h3>
                    </div>

                    <form id="location-form" @submit.prevent="handleSubmit">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Dirección</label>
                                <div class="input-wrapper">
                                    <i class="ph ph-map-pin leading-icon"></i>
                                    <input
                                        type="text"
                                        placeholder="Selecciona la dirección"
                                        class="input-control"
                                        required
                                        v-model="formData.direccion"
                                    />
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Distrito</label>
                                <div class="input-wrapper">
                                    <i class="ph ph-buildings leading-icon"></i>
                                    <input
                                        type="text"
                                        placeholder="Selecciona el distrito"
                                        class="input-control"
                                        required
                                        v-model="formData.distrito"
                                    />
                                </div>
                            </div>

                            <div class="map-selector-card" @click="openMap">
                                <i class="ph ph-map-trifold"></i>
                                <h4>Seleccionar en mapa</h4>
                                <p>Haga click aquí para abrir el mapa</p>
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
                        @click="triggerSubmit"
                    >
                        Continuar
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const { state: incidenteState } = useIncidenteStore();
const router = useRouter();

// Estado reactivo para los datos del formulario de ubicación
const formData = reactive({
    direccion: '',
    distrito: ''
});

onMounted(() => {
    // Prioridad: 1) Manual (usuario ya editó) → 2) IA → 3) Vacío
    const direccionGuardada = localStorage.getItem('incidente_direccion');
    const distritoGuardado = localStorage.getItem('incidente_distrito');
    
    if (direccionGuardada || distritoGuardado) {
        if (direccionGuardada) formData.direccion = direccionGuardada;
        if (distritoGuardado) formData.distrito = distritoGuardado;
    } else {
        const iaDataRaw = localStorage.getItem('incidente_ia_data');
        if (iaDataRaw) {
            try {
                const iaData = JSON.parse(iaDataRaw);
                if (iaData.direccion) formData.direccion = iaData.direccion;
                if (iaData.municipio) formData.distrito = iaData.municipio;
            } catch (e) {
                console.error('Error al parsear datos de la IA', e);
            }
        }
    }
});

// Simulación de apertura de mapa
const openMap = () => {
    // Guardar lo actual por si abre el mapa
    localStorage.setItem('incidente_direccion', formData.direccion);
    localStorage.setItem('incidente_distrito', formData.distrito);
    router.push({ name: 'registrar-incidente-mapa' });
};

// Función para disparar la validación nativa de HTML5
const triggerSubmit = () => {
    const form = document.getElementById("location-form");
    if (form.reportValidity()) {
        handleSubmit();
    }
};

const handleSubmit = () => {
    console.log("Formulario válido. Avanzando al Paso 3...", formData);
    
    // Guardar valores en localStorage
    localStorage.setItem('incidente_direccion', formData.direccion);
    localStorage.setItem('incidente_distrito', formData.distrito);

    router.push({ name: 'registrar-incidente-declaracion' });
};

// Volver al paso anterior
const handleBack = () => {
    router.push({ name: "registrar-incidente-detalle" });
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
    margin-bottom: 40px;
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

/* Indicador de Progreso (Stepper) - PASO 2 */
.stepper-container {
    position: relative;
    margin: 40px 0 60px 0;
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

/* Modificado para avanzar al paso 2 (aprox 38% del ancho) */
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

/* Contenedor del Formulario */
.form-card {
    border: 1px solid var(--accent-blue);
    border-radius: 16px;
    padding: 40px 50px;
    background-color: transparent;
    margin-bottom: 40px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
}
.section-header i {
    font-size: 24px;
    color: var(--text-main);
}
.section-header h3 {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.form-group label {
    font-size: 13px;
    color: var(--text-muted);
}

.input-wrapper {
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    height: 55px;
    transition: 0.3s;
}
.input-wrapper:focus-within {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}
.input-wrapper i.leading-icon {
    font-size: 20px;
    color: var(--text-main);
    margin-right: 15px;
}
.input-control {
    background: transparent;
    border: none;
    color: var(--text-main);
    font-size: 14px;
    width: 100%;
    height: 100%;
    outline: none;
}
.input-control::placeholder {
    color: var(--text-muted);
}

/* Botón gigante de mapa */
.map-selector-card {
    grid-column: span 2;
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.map-selector-card:hover {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}
.map-selector-card i {
    font-size: 32px;
    color: var(--text-main);
}
.map-selector-card h4 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}
.map-selector-card p {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0;
}

/* Barra de Acciones Inferior */
.bottom-action-bar {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
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
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    .map-selector-card {
        grid-column: span 1;
        padding: 30px 20px;
    }
    .form-card {
        padding: 30px 20px;
    }
    .stepper-container {
        display: none;
    }
}
</style>
