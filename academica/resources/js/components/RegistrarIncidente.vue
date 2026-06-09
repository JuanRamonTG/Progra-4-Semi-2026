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

            <div class="selection-view">
                
                <div class="center-header">
                    <div class="icon-circle">
                        <i class="ph ph-car"></i>
                    </div>
                    <h2>Seleccionar tipo de incidente</h2>
                    <p>Selecciona la categoría que mejor describa el incidente</p>
                </div>

                <!-- Sección de Copiloto de IA -->
                <div class="ai-copilot-container">
                    <div class="ai-header">
                        <div class="ai-sparkle"><i class="ph ph-sparkle"></i></div>
                        <div>
                            <h3>Copiloto de Registro Rápido con IA</h3>
                            <p>Describe el incidente con tus propias palabras y la IA pre-llenará todo el formulario</p>
                        </div>
                    </div>
                    <div class="ai-body">
                        <textarea 
                            v-model="aiText" 
                            placeholder="Ej: Choque leve entre un sedán gris y una motocicleta en la Av. España a las 2:30 PM. Pavimento mojado por lluvia. Ambos conductores con lesiones leves y discutiendo..."
                            class="ai-textarea"
                            :disabled="isLoadingAi"
                        ></textarea>
                        <button 
                            class="btn btn-ai" 
                            type="button"
                            @click="processWithAi" 
                            :disabled="isLoadingAi || !aiText.trim()"
                        >
                            <i v-if="isLoadingAi" class="ph ph-circle-notch spin"></i>
                            <i v-else class="ph ph-sparkles"></i>
                            <span>{{ isLoadingAi ? 'Analizando incidente...' : 'Pre-llenar con IA' }}</span>
                        </button>
                    </div>
                    <div v-if="aiError" class="ai-error-message">
                        <i class="ph ph-warning-circle"></i> {{ aiError }}
                    </div>
                </div>

                <div class="or-divider">
                    <span>O continuar de forma manual</span>
                </div>

                <div class="cards-grid">
                    <div 
                        class="option-card selectable-card" 
                        :class="{ 'selected': selectedType === 'victimas' }"
                        @click="selectType('victimas')"
                    >
                        <div class="card-icon-box">
                            <i class="ph ph-users-three"></i>
                        </div>
                        <div class="card-content">
                            <h3>Incidente con<br>victimas o fallecimiento</h3>
                            <div class="short-divider"></div>
                            <p>Incidentes que involucran<br>personas lesionadas o<br>fallecidas.</p>
                        </div>
                    </div>

                    <div 
                        class="option-card selectable-card" 
                        :class="{ 'selected': selectedType === 'materiales' }"
                        @click="selectType('materiales')"
                    >
                        <div class="card-icon-box">
                            <i class="ph ph-car-profile"></i>
                        </div>
                        <div class="card-content">
                            <h3>Incidente con<br>daños materiales</h3>
                            <div class="short-divider"></div>
                            <p>Incidentes que solo causan<br>daños materiales entre<br>vehículos.</p>
                        </div>
                    </div>
                </div>

                <div class="bottom-action-bar">
                    <div class="help-link">
                        <i class="ph ph-question"></i>
                        <span>¿No estás seguro?</span>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-outline" @click="handleCancel">Cancelar</button>
                        <button class="btn btn-primary" :disabled="!selectedType" @click="handleContinue">Continuar</button>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// Estado reactivo para el Copiloto de IA
const aiText = ref('');
const isLoadingAi = ref(false);
const aiError = ref(null);

// Estado reactivo para almacenar el tipo de incidente seleccionado
const selectedType = ref(null);

const processWithAi = async () => {
    if (!aiText.value.trim()) return;
    
    isLoadingAi.value = true;
    aiError.value = null;
    
    try {
        const response = await axios.post('/api/ia/parsear-incidente', {
            descripcion: aiText.value
        });
        
        const data = response.data;
        console.log('Datos extraídos por IA:', data);
        
        // IMPORTANTE: Limpiar TODAS las claves individuales de pasos anteriores
        // para que no sobreescriban los datos frescos de la IA en los componentes
        localStorage.removeItem('incidente_fecha');
        localStorage.removeItem('incidente_hora');
        localStorage.removeItem('incidente_gravedad');
        localStorage.removeItem('incidente_caso_id');
        localStorage.removeItem('incidente_direccion');
        localStorage.removeItem('incidente_distrito');
        localStorage.removeItem('incidente_declaracion');
        localStorage.removeItem('incidente_clima');
        localStorage.removeItem('incidente_via');
        localStorage.removeItem('incidente_pavimento');
        localStorage.removeItem('incidente_vehiculos');
        localStorage.removeItem('incidente_personas');
        localStorage.removeItem('incidente_tipo');
        localStorage.removeItem('incidente_evidencias');
        localStorage.removeItem('currentCaseId');

        // Guardar la respuesta estructurada en localStorage
        localStorage.setItem('incidente_ia_data', JSON.stringify(data));
        
        // Establecer el tipo de incidente según lo que extrajo la IA
        if (data.tipo_accidente) {
            selectedType.value = data.tipo_accidente;
            
            // Avanzar automáticamente tras una pequeña animación/espera
            setTimeout(() => {
                router.push({ name: 'registrar-incidente-detalle', query: { tipo: selectedType.value } });
            }, 600);
        } else {
            aiError.value = "No se pudo clasificar el tipo de accidente.";
        }
    } catch (error) {
        console.error('Error al procesar con IA:', error);
        aiError.value = error.response?.data?.error || "Error al comunicarse con el servicio de Inteligencia Artificial.";
    } finally {
        isLoadingAi.value = false;
    }
};

// Función para actualizar el tipo seleccionado
const selectType = (type) => {
    selectedType.value = type;
};

// Función para el botón Continuar
const handleContinue = () => {
    if (selectedType.value) {
        console.log(`Procesando incidente de tipo: ${selectedType.value}`);
        router.push({
            name: "registrar-incidente-detalle",
            query: { tipo: selectedType.value },
        });
    }
};

// Función para el botón Cancelar
const handleCancel = () => {
    router.push({ name: "dashboard" });
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

/* --- VISTA DE SELECCIÓN DE INCIDENTE --- */
.selection-view {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Cabecera central */
.center-header {
    text-align: center;
    margin-bottom: 40px;
    margin-top: 20px;
}

.icon-circle {
    width: 60px;
    height: 60px;
    background-color: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px auto;
    font-size: 28px;
    color: var(--text-muted);
}

.center-header h2 {
    font-size: 22px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.center-header p {
    color: var(--text-muted);
    font-size: 14px;
    margin: 0;
}

/* Tarjetas de Opciones */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    max-width: 900px;
    margin: 0 auto;
}

.option-card {
    background-color: transparent;
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.option-card:hover {
    border-color: #2b3954;
    background-color: rgba(255, 255, 255, 0.02);
}

/* Estado Activo (Seleccionado) */
.option-card.selected {
    border-color: var(--accent-blue);
    background-color: rgba(37, 99, 235, 0.05);
}

.card-icon-box {
    width: 90px;
    height: 90px;
    background-color: var(--accent-blue);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 45px;
    color: white;
    flex-shrink: 0;
}

.card-content h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 15px 0;
    line-height: 1.3;
}

.short-divider {
    width: 30px;
    height: 2px;
    background-color: var(--border-color);
    margin-bottom: 15px;
}

.option-card.selected .short-divider {
    background-color: var(--accent-blue);
}

.card-content p {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.5;
    margin: 0;
}

/* Barra de Acciones Inferior */
.bottom-action-bar {
    margin-top: auto;
    padding-top: 30px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.help-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-muted);
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.help-link i {
    font-size: 20px;
    color: var(--text-main);
}

.help-link:hover {
    color: var(--text-main);
}

.action-buttons {
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 28px;
    border-radius: 8px;
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

.btn-primary:hover:not(:disabled) {
    background-color: var(--accent-blue);
}

/* Estado deshabilitado del botón continuar */
.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsividad */
@media (max-width: 1024px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }
    .option-card {
        background-color: transparent;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .option-card:hover {
        border-color: #2b3954;
        background-color: rgba(255, 255, 255, 0.02);
    }

    /* Estado Activo (Seleccionado) */
    .option-card.selected {
        border-color: var(--accent-blue);
        background-color: rgba(37, 99, 235, 0.05);
    }

    .card-icon-box {
        width: 90px;
        height: 90px;
        background-color: var(--accent-blue);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 45px;
        color: white;
        flex-shrink: 0;
    }

    .card-content h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 15px 0;
        line-height: 1.3;
    }

    .short-divider {
        width: 30px;
        height: 2px;
        background-color: var(--border-color);
        margin-bottom: 15px;
    }
    
    .option-card.selected .short-divider {
        background-color: var(--accent-blue);
    }

    .card-content p {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
        margin: 0;
    }

    /* Barra de Acciones Inferior */
    .bottom-action-bar {
        margin-top: auto;
        padding-top: 30px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .help-link {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-muted);
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
    }

    .help-link i {
        font-size: 20px;
        color: var(--text-main);
    }

    .help-link:hover {
        color: var(--text-main);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 12px 28px;
        border-radius: 8px;
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

    .btn-primary:hover:not(:disabled) {
        background-color: var(--accent-blue);
    }

    /* Estado deshabilitado del botón continuar */
    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* --- COPILOTO IA --- */
    .ai-copilot-container {
        max-width: 900px;
        margin: 0 auto 30px auto;
        width: 100%;
        background: radial-gradient(100% 100% at 0% 0%, rgba(37, 99, 235, 0.15) 0%, rgba(15, 21, 36, 0) 100%), var(--bg-card);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    .ai-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .ai-sparkle {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 20px;
        color: white;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
    }
    .ai-header h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }
    .ai-header p {
        font-size: 12px;
        color: var(--text-muted);
        margin: 4px 0 0 0;
    }
    .ai-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .ai-textarea {
        width: 100%;
        height: 90px;
        background-color: var(--bg-sidebar);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 12px;
        color: var(--text-main);
        font-family: inherit;
        font-size: 14px;
        resize: none;
        outline: none;
        transition: border-color 0.3s;
    }
    .ai-textarea:focus {
        border-color: var(--accent-blue);
    }
    .btn-ai {
        align-self: flex-end;
        background: linear-gradient(90deg, var(--primary-blue), #6366f1);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-ai:hover:not(:disabled) {
        background: linear-gradient(90deg, var(--accent-blue), #4f46e5);
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
    }
    .btn-ai:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .ai-error-message {
        margin-top: 12px;
        color: #f87171;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .spin {
        animation: spin-animation 1s linear infinite;
    }
    @keyframes spin-animation {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .or-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 900px;
        margin: 0 auto 30px auto;
        width: 100%;
    }
    .or-divider::before, .or-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background-color: var(--border-color);
    }
    .or-divider span {
        padding: 0 15px;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Responsividad */
    @media (max-width: 1024px) {
        .cards-grid { grid-template-columns: 1fr; }
        .option-card { padding: 20px; }
    }
}
</style>
