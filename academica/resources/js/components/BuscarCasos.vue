<template>
    <div class="dashboard">
        <Sidebar />
        <main class="main-content">
            <TopHeader title="Buscar Casos" subtitle="Motor de búsqueda de incidentes" />
            <div class="content-wrapper" style="margin-top: 30px; text-align: center; color: var(--text-muted);">
                <i class="ph ph-magnifying-glass" style="font-size: 64px; margin-bottom: 20px;"></i>
                <h2>Búsqueda de Casos</h2>
                <p>Módulo de búsqueda en construcción...</p>
            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from './Sidebar.vue';
import TopHeader from './TopHeader.vue';
</script>
