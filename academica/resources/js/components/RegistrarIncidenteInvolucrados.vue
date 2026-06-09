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
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
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
                        id="involved-form"
                        style="display: flex; flex-direction: column; gap: 35px"
                        @submit.prevent
                    >
                        <div class="dynamic-list-container">
                            <div class="list-header-row">
                                <span class="list-title">Vehículos</span>
                                <span class="add-btn" @click="addVehicle"
                                    >Agregar vehículo +</span
                                >
                            </div>
                            <div class="list-inputs" id="vehicles-list">
                                <div
                                    v-for="(vehiculo, index) in vehiculos"
                                    :key="'vehiculo-' + index"
                                    class="block-card"
                                    :class="{ 'new-input': index >= 2 }"
                                >
                                    <div class="block-card-header">
                                        <span class="block-card-title">Vehículo #{{ index + 1 }}</span>
                                        <button
                                            v-if="vehiculos.length > 0"
                                            class="delete-block-btn"
                                            type="button"
                                            title="Eliminar vehículo"
                                            @click="removeVehicle(index)"
                                        >
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                    <div class="block-card-body">
                                        <div class="input-grid">
                                            <div class="input-field">
                                                <label>Marca / Modelo</label>
                                                <input
                                                    type="text"
                                                    class="outline-input"
                                                    placeholder="Ej. Toyota Corolla"
                                                    v-model="vehiculo.marca"
                                                    :disabled="vehiculo.noIdentificado"
                                                />
                                            </div>
                                            <div class="input-field">
                                                <label>Número de Placa / Matrícula</label>
                                                <input
                                                    type="text"
                                                    class="outline-input"
                                                    placeholder="Ej. P123-456"
                                                    v-model="vehiculo.matricula"
                                                    :disabled="vehiculo.noIdentificado"
                                                />
                                            </div>
                                        </div>
                                        <div class="checkbox-wrapper">
                                            <label class="custom-checkbox">
                                                <input
                                                    type="checkbox"
                                                    v-model="vehiculo.noIdentificado"
                                                    @change="handleVehicleCheckboxChange(index)"
                                                />
                                                <span class="checkmark"></span>
                                                <span class="checkbox-label">Vehículo no identificado / Se dio a la fuga</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dynamic-list-container">
                            <div class="list-header-row">
                                <span class="list-title">Personas</span>
                                <span class="add-btn" @click="addPerson"
                                    >Agregar persona +</span
                                >
                            </div>
                            <div class="list-inputs" id="persons-list">
                                <div
                                    v-for="(persona, index) in personas"
                                    :key="'persona-' + index"
                                    class="block-card"
                                    :class="{ 'new-input': index >= 2 }"
                                >
                                    <div class="block-card-header">
                                        <span class="block-card-title">Persona #{{ index + 1 }}</span>
                                        <button
                                            v-if="personas.length > 0"
                                            class="delete-block-btn"
                                            type="button"
                                            title="Eliminar persona"
                                            @click="removePerson(index)"
                                        >
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </div>
                                    <div class="block-card-body">
                                        <div class="input-grid three-columns">
                                            <div class="input-field">
                                                <label>Nombre Completo</label>
                                                <input
                                                    type="text"
                                                    class="outline-input"
                                                    placeholder="Ej. Juan Pérez"
                                                    v-model="persona.nombre"
                                                    :disabled="persona.noDisponible"
                                                />
                                            </div>
                                            <div class="input-field">
                                                <label>Número de Licencia</label>
                                                <input
                                                    type="text"
                                                    class="outline-input"
                                                    placeholder="Ej. 0318-120590-101-1"
                                                    v-model="persona.licencia"
                                                    :disabled="persona.noDisponible"
                                                />
                                            </div>
                                            <div class="input-field">
                                                <label>Condición <span class="required-star">*</span></label>
                                                <select
                                                    class="outline-select"
                                                    v-model="persona.condicion"
                                                    required
                                                >
                                                    <option value="" disabled selected>Seleccione...</option>
                                                    <option value="Ileso">Ileso</option>
                                                    <option value="Lesionado">Lesionado</option>
                                                    <option value="Fallecido">Fallecido</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="checkbox-wrapper">
                                            <label class="custom-checkbox">
                                                <input
                                                    type="checkbox"
                                                    v-model="persona.noDisponible"
                                                    @change="handlePersonCheckboxChange(index)"
                                                />
                                                <span class="checkmark"></span>
                                                <span class="checkbox-label">Datos de persona no disponibles</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Texto de Ayuda Cohesivo -->
                        <p class="help-text">
                            * Si falta información, asegúrese de capturar las placas y los rostros en el siguiente paso de Evidencia.
                        </p>
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// Arrays reactivos para las listas dinámicas, inicializados con 2 elementos vacíos como en el HTML original
const vehiculos = ref(['', '']);
const personas = ref(['', '']);

onMounted(() => {
    // Prioridad: 1) Manual (usuario ya editó) → 2) IA → 3) Vacío
    const savedVehiculos = localStorage.getItem('incidente_vehiculos');
    const savedPersonas = localStorage.getItem('incidente_personas');
    
    if (savedVehiculos || savedPersonas) {
        // Datos manuales existen (el usuario ya pasó por aquí)
        if (savedVehiculos) {
            try { vehiculos.value = JSON.parse(savedVehiculos); } catch (e) {}
        }
        if (savedPersonas) {
            try { personas.value = JSON.parse(savedPersonas); } catch (e) {}
        }
    } else {
        // Sin datos manuales: cargar desde la IA
        const iaDataRaw = localStorage.getItem('incidente_ia_data');
        if (iaDataRaw) {
            try {
                const iaData = JSON.parse(iaDataRaw);
                if (iaData.vehiculos && Array.isArray(iaData.vehiculos) && iaData.vehiculos.length > 0) {
                    vehiculos.value = [...iaData.vehiculos];
                }
                if (iaData.personas && Array.isArray(iaData.personas) && iaData.personas.length > 0) {
                    personas.value = [...iaData.personas];
                }
            } catch (e) {
                console.error('Error al cargar datos de IA en involucrados', e);
            }
        }
    }

    // Asegurarse de que al menos haya algún campo si los arrays están vacíos
    if (vehiculos.value.length === 0) vehiculos.value = ['', ''];
    if (personas.value.length === 0) personas.value = ['', ''];
});

// Funciones para agregar campos dinámicamente
const addVehicle = () => {
    vehiculos.value.push({ marca: "", matricula: "", noIdentificado: false });
};

const removeVehicle = (index) => {
    vehiculos.value.splice(index, 1);
};

const addPerson = () => {
    personas.value.push({ nombre: "", licencia: "", condicion: "", noDisponible: false });
};

// Navegación
const handleBack = () => {
    // Guardar avance
    localStorage.setItem('incidente_vehiculos', JSON.stringify(vehiculos.value));
    localStorage.setItem('incidente_personas', JSON.stringify(personas.value));
    router.push({ name: 'registrar-incidente-declaracion' });
};

const handleNext = () => {
    // Recolectar datos limpiando campos vacíos
    const cleanedVehicles = vehiculos.value.filter(v => v.trim() !== '');
    const cleanedPersons = personas.value.filter(p => p.trim() !== '');

    console.log("Datos de involucrados recolectados:", { cleanedVehicles, cleanedPersons });

    // Guardar en localStorage
    localStorage.setItem('incidente_vehiculos', JSON.stringify(vehiculos.value));
    localStorage.setItem('incidente_personas', JSON.stringify(personas.value));
    
    router.push({ name: 'registrar-incidente-evidencia' });
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

/* Stepper - Paso 4 */
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

/* Línea activa cubriendo hasta el cuarto punto (aprox 75%) */
.stepper-active-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 75%;
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

/* Listas Dinámicas (Vehículos y Personas) */
.dynamic-list-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.list-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.list-title {
    font-size: 14px;
    color: var(--text-main);
}
.add-btn {
    font-size: 13px;
    color: var(--accent-blue);
    cursor: pointer;
    font-weight: 500;
    transition: color 0.2s;
}
.add-btn:hover {
    color: #60a5fa;
    text-decoration: underline;
}

/* Inputs de lista */
.list-inputs {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.outline-input {
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 0 20px;
    height: 55px;
    width: 100%;
    color: var(--text-main);
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
}
.outline-input::placeholder {
    color: var(--text-muted);
}
.outline-input:focus {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}

/* Animación para nuevos inputs */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.new-input {
    animation: fadeIn 0.3s ease-out;
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

/* Tarjetas de Bloque Estructuradas */
.block-card {
    background-color: rgba(255, 255, 255, 0.015);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: relative;
    transition: all 0.3s ease;
}

.block-card:hover {
    border-color: rgba(51, 107, 250, 0.3);
    background-color: rgba(255, 255, 255, 0.03);
}

.block-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(29, 44, 82, 0.5);
    padding-bottom: 12px;
}

.block-card-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.delete-block-btn {
    background: transparent;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 18px;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.delete-block-btn:hover {
    color: var(--critical);
    transform: scale(1.1);
}

.block-card-body {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Grillas para Inputs */
.input-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.input-grid.three-columns {
    grid-template-columns: 1.5fr 1fr 1fr;
}

.input-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-field label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
}

.required-star {
    color: var(--critical);
}

/* Dropdown select moderno */
.outline-select {
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 0 15px;
    height: 55px;
    width: 100%;
    color: var(--text-main);
    font-size: 14px;
    outline: none;
    cursor: pointer;
    transition: all 0.3s ease;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%238AABBB' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
}

.outline-select:focus {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}

.outline-select:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: rgba(0, 0, 0, 0.2);
}

.outline-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: rgba(0, 0, 0, 0.2);
}

/* Checkbox personalizado sutil */
.checkbox-wrapper {
    margin-top: 5px;
}

.custom-checkbox {
    display: inline-flex;
    align-items: center;
    position: relative;
    cursor: pointer;
    user-select: none;
    gap: 10px;
}

.custom-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: relative;
    height: 18px;
    width: 18px;
    background-color: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    transition: all 0.2s ease;
}

.custom-checkbox:hover input ~ .checkmark {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}

.custom-checkbox input:checked ~ .checkmark {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.custom-checkbox input:checked ~ .checkmark:after {
    display: block;
}

.custom-checkbox .checkmark:after {
    left: 6px;
    top: 2px;
    width: 4px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkbox-label {
    font-size: 13px;
    color: var(--text-muted);
    transition: color 0.2s ease;
}

.custom-checkbox input:checked ~ .checkbox-label {
    color: var(--text-main);
}

/* Texto de Ayuda Cohesivo */
.help-text {
    font-size: 12px;
    color: var(--text-muted);
    font-style: italic;
    margin-top: 10px;
    text-align: center;
    letter-spacing: 0.3px;
}

/* Responsividad */
@media (max-width: 900px) {
    .stepper-container {
        display: none;
    }
    .details-card {
        padding: 30px 20px;
    }
    .input-grid, .input-grid.three-columns {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}
</style>
