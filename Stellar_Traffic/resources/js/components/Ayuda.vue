<template>
    <div class="dashboard">
        <Sidebar />

        <main class="main-content">
            <TopHeader
                title="Centro de Ayuda y Soporte"
                subtitle="Guías de usuario, instrucciones de uso y preguntas frecuentes"
            />

            <div class="help-container">
                <!-- Tarjetas de Guías Rápidas -->
                <section class="guides-section">
                    <h3 class="section-title">GUÍAS RÁPIDAS DEL SISTEMA</h3>
                    <div class="guides-grid">
                        <div class="guide-card">
                            <div class="guide-icon icon-blue">
                                <i class="ph ph-plus-circle"></i>
                            </div>
                            <div class="guide-info">
                                <h4>Registrar un Incidente</h4>
                                <p>Reporta accidentes siguiendo el flujo del asistente en 5 pasos:</p>
                                <ol>
                                    <li><strong>Detalles:</strong> Tipo de incidente y gravedad.</li>
                                    <li><strong>Ubicación:</strong> Dirección detallada en San Miguel.</li>
                                    <li><strong>Declaración:</strong> Relato del oficial y descripción.</li>
                                    <li><strong>Involucrados:</strong> Datos de conductores y vehículos.</li>
                                    <li><strong>Evidencia:</strong> Carga de fotografías o archivos del caso.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="guide-card">
                            <div class="guide-icon icon-emerald">
                                <i class="ph ph-map-trifold"></i>
                            </div>
                            <div class="guide-info">
                                <h4>Uso del Mapa de Monitoreo</h4>
                                <p>Visualiza incidentes viales activos en tiempo real:</p>
                                <ul>
                                    <li><strong>Buscador:</strong> Filtra casos al instante por ID, calle o descripción.</li>
                                    <li><strong>Badges de Gravedad:</strong> Segmenta por urgencia (Crítico, Alto, Medio, Bajo).</li>
                                    <li><strong>Marcadores:</strong> Haz clic en un marcador para ver los detalles del caso en su popup.</li>
                                    <li><strong>Foco:</strong> Presiona un incidente de la lista lateral para centrar el mapa.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="guide-card">
                            <div class="guide-icon icon-amber">
                                <i class="ph ph-file-text"></i>
                            </div>
                            <div class="guide-info">
                                <h4>Reportes e Historial</h4>
                                <p>Monitorea métricas clave e información histórica:</p>
                                <ul>
                                    <li><strong>Estadísticas:</strong> Accede a gráficos dinámicos de severidad y tipos de accidentes en "Reportes".</li>
                                    <li><strong>Historial:</strong> Consulta el registro cronológico completo de incidentes reportados.</li>
                                    <li><strong>Búsqueda en Historial:</strong> Localiza casos cerrados o antiguos usando filtros avanzados de texto.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Acordeón de Preguntas Frecuentes (FAQ) -->
                <section class="faq-section">
                    <div class="faq-header">
                        <h3 class="section-title">PREGUNTAS FRECUENTES (FAQ)</h3>
                        <div class="faq-search-wrapper">
                            <i class="ph ph-magnifying-glass search-icon"></i>
                            <input 
                                v-model="faqQuery" 
                                type="text" 
                                placeholder="Buscar preguntas frecuentes..." 
                                class="faq-search-input"
                            />
                            <button v-if="faqQuery" @click="faqQuery = ''" class="clear-search-btn">
                                <i class="ph ph-x"></i>
                            </button>
                        </div>
                    </div>

                    <div class="faq-list" v-if="filteredFaqs.length > 0">
                        <div 
                            v-for="(faq, idx) in filteredFaqs" 
                            :key="idx" 
                            class="faq-item"
                            :class="{ active: activeFaqIndex === idx }"
                        >
                            <div class="faq-question" @click="toggleFaq(idx)">
                                <span>{{ faq.question }}</span>
                                <i class="ph" :class="activeFaqIndex === idx ? 'ph-caret-up' : 'ph-caret-down'"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ faq.answer }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-empty-state" v-else>
                        <i class="ph ph-question"></i>
                        <p>No se encontraron respuestas para tu búsqueda. Intenta con palabras clave como "mapa", "incidente" o "soporte".</p>
                    </div>
                </section>

            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue';
import TopHeader from './TopHeader.vue';
import { ref, computed } from 'vue';

const faqQuery = ref("");
const activeFaqIndex = ref(null);

const faqs = [
    {
        question: "¿Cómo edito o elimino un incidente que ya fue registrado?",
        answer: "Por motivos de integridad de los registros del PNC, una vez que el incidente es confirmado y registrado con éxito, no puede ser modificado o eliminado directamente por el oficial de patrulla. Deberás solicitar la corrección al supervisor del Panel de Control de Guardia enviando el ID del Caso."
    },
    {
        question: "El mapa no carga los marcadores de incidentes, ¿qué debo hacer?",
        answer: "Verifica que tengas una conexión estable a internet. Si el mapa sigue sin cargar, presiona F5 para recargar la aplicación. Si el problema persiste, es probable que la API de OpenStreetMap esté experimentando latencia temporal; las coordenadas se cargarán con un fallback local automáticamente."
    },
    {
        question: "¿Cómo exporto el historial de accidentes a formato PDF o Excel?",
        answer: "Ve a la sección 'Historial' desde el menú lateral. En la esquina superior derecha del panel de datos, encontrarás los botones para exportar el reporte actual filtrado directamente a formato de hoja de cálculo o documento impreso."
    },
    {
        question: "¿Qué significan los colores de los marcadores en el Mapa de Monitoreo?",
        answer: "Los marcadores en el mapa utilizan una escala de criticidad por colores: Rojo representa incidentes Críticos (colisiones graves o vías bloqueadas por completo); Naranja/Rojo denota gravedad Alta; Amarillo representa gravedad Media (tráfico lento o colisiones leves); y Verde indica gravedad Bajo/Seguro (obstáculos menores en la vía)."
    },
    {
        question: "¿La aplicación funciona en dispositivos móviles o tablets de patrulla?",
        answer: "Sí, toda la aplicación cuenta con un diseño responsivo. Los menús laterales y el mapa se adaptan automáticamente a las pantallas táctiles de las tablets de las patrullas policiales para facilitar el registro de campo."
    }
];

const filteredFaqs = computed(() => {
    return faqs.filter(faq => {
        const query = faqQuery.value.toLowerCase().trim();
        return faq.question.toLowerCase().includes(query) || faq.answer.toLowerCase().includes(query);
    });
});

const toggleFaq = (idx) => {
    if (activeFaqIndex.value === idx) {
        activeFaqIndex.value = null;
    } else {
        activeFaqIndex.value = idx;
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
    --safe: #00E676;
    --warning: #FFB300;
    --critical: #FF1744;

    background-color: var(--bg-dark);
    color: var(--text-main);
    height: 100vh;
    display: flex;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.main-content {
    flex: 1;
    padding: 30px 40px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

.help-container {
    margin-top: 25px;
    display: flex;
    flex-direction: column;
    gap: 30px;
    padding-bottom: 40px;
}

/* --- TÍTULO DE SECCIÓN --- */
.section-title {
    font-size: 12px;
    color: var(--text-muted);
    letter-spacing: 1px;
    margin-bottom: 15px;
    text-transform: uppercase;
}

/* --- SECCIÓN DE GUÍAS --- */
.guides-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.guide-card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    gap: 15px;
    transition: all 0.3s ease;
}

.guide-card:hover {
    border-color: var(--accent-blue);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(51, 107, 250, 0.1);
}

.guide-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.icon-blue {
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--accent-blue);
}

.icon-emerald {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--safe);
}

.icon-amber {
    background-color: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.guide-info h4 {
    font-size: 15px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.guide-info p {
    font-size: 12px;
    color: var(--text-muted);
    line-height: 1.5;
    margin: 0 0 10px 0;
}

.guide-info ul, .guide-info ol {
    font-size: 11px;
    color: var(--text-muted);
    padding-left: 15px;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.guide-info li {
    line-height: 1.4;
}

/* --- SECCIÓN DE FAQ --- */
.faq-section {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 25px;
}

.faq-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.faq-header .section-title {
    margin: 0;
}

.faq-search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 320px;
}

.faq-search-wrapper .search-icon {
    position: absolute;
    left: 12px;
    color: var(--text-muted);
    font-size: 15px;
}

.faq-search-input {
    width: 100%;
    background-color: rgba(6, 17, 41, 0.4);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 8px 35px 8px 35px;
    color: var(--text-main);
    font-size: 12px;
    outline: none;
    transition: all 0.2s ease;
}

.faq-search-input:focus {
    border-color: var(--accent-blue);
    background-color: rgba(6, 17, 41, 0.7);
}

.clear-search-btn {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 13px;
    padding: 2px;
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.faq-item {
    border: 1px solid var(--border-color);
    border-radius: 10px;
    overflow: hidden;
    background-color: rgba(255, 255, 255, 0.01);
    transition: all 0.2s ease;
}

.faq-item:hover {
    border-color: rgba(51, 107, 250, 0.3);
    background-color: rgba(255, 255, 255, 0.02);
}

.faq-question {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
    user-select: none;
}

.faq-question i {
    font-size: 14px;
    color: var(--text-muted);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0, 1, 0, 1);
    background-color: rgba(6, 17, 41, 0.2);
    border-top: 0px solid var(--border-color);
}

.faq-item.active .faq-answer {
    max-height: 200px;
    border-top: 1px solid var(--border-color);
    transition: max-height 0.3s cubic-bezier(1, 0, 1, 0);
}

.faq-answer p {
    padding: 16px 20px;
    margin: 0;
    font-size: 12px;
    line-height: 1.6;
    color: var(--text-muted);
}

.faq-empty-state {
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.faq-empty-state i {
    font-size: 32px;
    color: var(--border-color);
}

.faq-empty-state p {
    font-size: 12px;
    max-width: 400px;
    line-height: 1.5;
}

/* --- RESPONSIVIDAD --- */
@media (max-width: 1024px) {
    .guides-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}
</style>