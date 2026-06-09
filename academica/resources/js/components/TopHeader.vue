<template>
    <header class="header">
        <div class="header-titles">
            <h1>{{ title }}</h1>
            <p>{{ subtitle }}</p>
        </div>
        <slot name="center"></slot>
        <div class="header-actions">
            <div class="datetime-pill" @click="showCalendarAlert" style="cursor: pointer;">
                <i class="ph ph-calendar-blank"></i>
                <div class="dt-text">
                    <span class="date">{{ currentDate }}</span>
                    <span class="time">{{ currentTime }}</span>
                </div>
            </div>
            <div class="notification" @click="showNotificationAlert">
                <i class="ph ph-bell"></i>
                <span class="badge">1</span>
            </div>
        </div>
    </header>
</template>

<script setup>
import { defineProps } from 'vue';
import { useDatetime } from '../composables/useDatetime.js';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, required: true }
});

const { currentDate, currentTime } = useDatetime();

const showCalendarAlert = () => {
    alert("Calendario del Sistema: Mostrando agenda y turnos del oficial...");
};

const showNotificationAlert = () => {
    alert("Notificaciones:\n1. Tienes un caso pendiente de firma.\n2. Alerta de tráfico en Ruta 5.\n3. Actualización de sistema disponible.");
};
</script>
