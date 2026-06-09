import { reactive } from 'vue';

const generateIdCaso = () => {
    return 'AUTO-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
};

// Estado global compartido entre todos los pasos del registro
const state = reactive({
    // Paso 0: Selección de tipo
    tipo_accidente: '',

    // Paso 1: Detalle
    fecha_incidente: new Date().toISOString().split('T')[0],
    hora_aproximada: new Date().toTimeString().split(' ')[0].substring(0, 5),
    id_caso: generateIdCaso(),
    gravedad: '',

    // Paso 2: Ubicación
    direccion: '',
    municipio: '',

    // Paso 3: Declaración
    declaracion: '',
    condicion_climatica: '',
    tipo_via: '',
    estado_pavimento: '',

    // Paso 4: Involucrados
    vehiculos: ['', ''],
    personas: ['', ''],

    // Paso 5: Evidencia (solo metadatos, archivos se manejan localmente)
    archivosCount: 0
});

export function useIncidenteStore() {
    const reset = () => {
        state.tipo_accidente = '';
        state.fecha_incidente = new Date().toISOString().split('T')[0];
        state.hora_aproximada = new Date().toTimeString().split(' ')[0].substring(0, 5);
        state.id_caso = generateIdCaso();
        state.gravedad = '';
        state.direccion = '';
        state.municipio = '';
        state.declaracion = '';
        state.condicion_climatica = '';
        state.tipo_via = '';
        state.estado_pavimento = '';
        state.vehiculos = ['', ''];
        state.personas = ['', ''];
        state.archivosCount = 0;
    };

    return { state, reset };
}
