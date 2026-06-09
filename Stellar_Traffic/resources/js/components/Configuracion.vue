<template>
    <div class="dashboard">
        <Sidebar />
        <main class="main-content">
            <TopHeader title="Configuración" subtitle="Ajustes del sistema y preferencias de la cuenta" />
            <div class="content-wrapper" style="margin-top: 30px; text-align: center; color: var(--text-muted);">
                <i class="ph ph-gear" style="font-size: 64px; margin-bottom: 20px;"></i>
                <h2>Configuración</h2>
                <p>Módulo de configuración en construcción...</p>
            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue';
import TopHeader from './TopHeader.vue';
</script>
