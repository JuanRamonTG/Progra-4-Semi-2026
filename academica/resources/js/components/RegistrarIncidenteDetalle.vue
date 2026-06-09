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
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <div class="form-card">
                    <form
                        class="form-grid"
                        id="incident-form"
                        @submit.prevent="handleSubmit"
                    >
                        <div class="form-group">
                            <label>Fecha de incidente</label>
                            <div class="input-wrapper">
                                <i
                                    class="ph ph-calendar-blank leading-icon"
                                ></i>
                                <input
                                    type="text"
                                    placeholder="DD/MM/AA"
                                    @focus="focusDate"
                                    @blur="blurDate"
                                    class="input-control"
                                    required
                                    v-model="formData.fecha"
                                />
                                <i class="ph ph-caret-down trailing-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Hora aproximada</label>
                            <div class="input-wrapper">
                                <i class="ph ph-clock leading-icon"></i>
                                <input
                                    type="text"
                                    placeholder="HH: MM"
                                    @focus="focusTime"
                                    @blur="blurTime"
                                    class="input-control"
                                    required
                                    v-model="formData.hora"
                                />
                                <i class="ph ph-caret-down trailing-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>ID de caso</label>
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    class="input-control readonly-input"
                                    readonly
                                    v-model="formData.casoId"
                                />
                                <i class="ph ph-caret-down trailing-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Gravedad</label>
                            <div class="input-wrapper">
                                <div
                                    class="color-dot"
                                    :style="{ backgroundColor: dotColor }"
                                ></div>
                                <select
                                    class="input-control"
                                    required
                                    v-model="formData.gravedad"
                                >
                                    <option value="" disabled selected hidden>
                                        Seleccionar
                                    </option>
                                    <option value="Leve">Leve</option>
                                    <option value="Grave">Grave</option>
                                </select>
                                <i class="ph ph-caret-down trailing-icon"></i>
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
import { reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// Estado reactivo para los datos del formulario
const formData = reactive({
    fecha: '',
    hora: '',
    casoId: '',
    gravedad: ''
});

// Función para generar un ID de caso automático
const generateCaseID = () => {
    const year = new Date().getFullYear();
    const randomNum = Math.floor(1000 + Math.random() * 9000);
    return `AUTO-${year}-${randomNum}`;
};

// Generar el ID al montar el componente
onMounted(() => {
    formData.casoId = generateCaseID();
    
    // Prioridad de datos: 1) Manual (si el usuario ya editó este paso) → 2) IA → 3) Vacío
    const savedFecha = localStorage.getItem('incidente_fecha');
    const savedHora = localStorage.getItem('incidente_hora');
    const savedGravedad = localStorage.getItem('incidente_gravedad');
    
    // Si hay datos manuales, usarlos (significa que el usuario ya pasó por aquí)
    if (savedFecha || savedHora || savedGravedad) {
        if (savedFecha) formData.fecha = savedFecha;
        if (savedHora) formData.hora = savedHora;
        if (savedGravedad) formData.gravedad = savedGravedad;
    } else {
        // Si no hay datos manuales, intentar cargar desde la IA
        const iaDataRaw = localStorage.getItem('incidente_ia_data');
        if (iaDataRaw) {
            try {
                const iaData = JSON.parse(iaDataRaw);
                if (iaData.fecha_incidente) formData.fecha = iaData.fecha_incidente;
                if (iaData.hora_aproximada) formData.hora = iaData.hora_aproximada;
                if (iaData.gravedad) formData.gravedad = iaData.gravedad;
            } catch (e) {
                console.error('Error al parsear datos de la IA', e);
            }
        }
    }
});

// Computed property para el color del punto de gravedad
const dotColor = computed(() => {
    if (formData.gravedad === "Leve") return "var(--warning)";
    if (formData.gravedad === "Grave") return "var(--critical)";
    return "var(--safe)";
});

// Funciones para manejar los inputs de fecha y hora
const focusDate = (e) => (e.target.type = "date");
const blurDate = (e) => {
    if (!e.target.value) e.target.type = "text";
};

const focusTime = (e) => (e.target.type = "time");
const blurTime = (e) => {
    if (!e.target.value) e.target.type = "text";
};

// Función que emite el evento de enviar (disparado desde el botón Continuar)
const triggerSubmit = () => {
    const form = document.getElementById("incident-form");
    // Valida nativamente el formulario HTML antes de enviarlo
    if (form.reportValidity()) {
        handleSubmit();
    }
};

const handleSubmit = () => {
    console.log("Formulario listo para enviarse al siguiente paso.", formData);
    
    // Guardar en localStorage para persistencia y para confirmación
    localStorage.setItem('incidente_fecha', formData.fecha);
    localStorage.setItem('incidente_hora', formData.hora);
    localStorage.setItem('incidente_gravedad', formData.gravedad);
    localStorage.setItem('incidente_caso_id', formData.casoId);

    // Cuando exista la siguiente vista (ej. Ubicación):
    router.push({ name: 'registrar-incidente-ubicacion' });
};

// Volver atrás
const handleBack = () => {
    router.push({ name: "registrar-incidente" });
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

/* Indicador de Progreso (Stepper) */
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

.stepper-active-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 15%; /* Avanza hasta el primer punto */
    height: 6px;
    background-color: var(--accent-blue);
    transform: translateY(-50%);
    border-radius: 3px;
    z-index: 2;
}

.step-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #ffffff;
    z-index: 3;
    position: relative;
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
    padding: 50px;
    background-color: transparent;
    margin-bottom: 40px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.form-group label {
    font-size: 13px;
    color: var(--text-muted);
}

.input-wrapper {
    background-color: var(--bg-sidebar);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 0 20px;
    height: 55px;
    position: relative;
    transition: 0.3s;
}

.input-wrapper:focus-within {
    border-color: var(--accent-blue);
}

.input-wrapper i.leading-icon {
    font-size: 20px;
    color: var(--text-main);
    margin-right: 15px;
}

.input-wrapper .color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 15px;
    transition: background-color 0.3s;
}

.input-control {
    background: transparent;
    border: none;
    color: var(--text-main);
    font-size: 15px;
    width: 100%;
    height: 100%;
    outline: none;
    appearance: none;
}

.input-control::placeholder {
    color: var(--text-main);
}

::-webkit-calendar-picker-indicator {
    filter: invert(1);
    opacity: 0;
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    cursor: pointer;
}

.input-wrapper i.trailing-icon {
    font-size: 16px;
    color: var(--text-muted);
    margin-left: auto;
    pointer-events: none;
}

.readonly-input {
    color: var(--text-main);
    cursor: default;
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
    background-color: var(--bg-sidebar);
    border: 1px solid var(--border-color);
    color: var(--text-main);
}

.btn-outline:hover {
    background-color: var(--border-color);
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
        gap: 20px;
    }
    .form-card {
        padding: 30px 20px;
    }
    .stepper-container {
        display: none;
    }
}
</style>
