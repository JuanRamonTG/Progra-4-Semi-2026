<template>
    <aside :class="['sidebar', { 'sidebar-collapsed': isCollapsed }]">
        <div class="user-profile">
            <div class="avatar">LZ</div>
            <div class="user-info">
                <h4>Luis Zelaya</h4>
                <span>PANEL DE CONTROL PNC</span>
            </div>
            <i class="ph ph-list menu-icon" @click="toggleSidebar"></i>
        </div>

        <div class="nav-section">
            <p class="nav-title">PRINCIPAL</p>
            <ul class="nav-list" id="main-nav">
                <li
                    :class="[
                        'nav-item',
                        { active: currentRoute === '/dashboard' },
                    ]"
                    @click="goTo('/dashboard')"
                >
                    <i class="ph ph-house"></i>
                    <span class="nav-text">Inicio</span>
                </li>
                <li
                    :class="[
                        'nav-item',
                        {
                            active: currentRoute.includes(
                                'registrar-incidente',
                            ),
                        },
                    ]"
                    @click="goTo('/registrar-incidente')"
                >
                    <i class="ph ph-plus-square"></i>
                    <span class="nav-text">Registrar incidente</span>
                </li>
                <li
                    :class="['nav-item', { active: currentRoute === '/mapa' }]"
                    @click="goTo('/mapa')"
                >
                    <i class="ph ph-map-pin"></i>
                    <span class="nav-text">Ver mapa</span>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <p class="nav-title">SISTEMA</p>
            <ul class="nav-list">
                <li
                    :class="[
                        'nav-item',
                        { active: currentRoute === '/reportes' },
                    ]"
                    @click="goTo('/reportes')"
                >
                    <i class="ph ph-file-text"></i>
                    <span class="nav-text">Reportes</span>
                </li>
                <li
                    :class="[
                        'nav-item',
                        { active: currentRoute === '/historial' },
                    ]"
                    @click="goTo('/historial')"
                >
                    <i class="ph ph-clock-counter-clockwise"></i>
                    <span class="nav-text">Historial</span>
                </li>
                <li
                    :class="[
                        'nav-item',
                        { active: currentRoute === '/configuracion' },
                    ]"
                    @click="goTo('/configuracion')"
                >
                    <i class="ph ph-gear"></i>
                    <span class="nav-text">Configuración</span>
                </li>
                <li
                    :class="['nav-item', { active: currentRoute === '/ayuda' }]"
                    @click="goTo('/ayuda')"
                >
                    <i class="ph ph-question"></i>
                    <span class="nav-text">Ayuda</span>
                </li>
            </ul>
        </div>

        <div class="logout" @click="handleLogout">
            <i class="ph ph-sign-out"></i>
            <span class="logout-text">Salir de la cuenta</span>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";

const router = useRouter();
const route = useRoute();

// Cargar el estado colapsado inicial de localStorage
const isCollapsed = ref(localStorage.getItem("sidebar-collapsed") === "true");

const currentRoute = computed(() => route.path);

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
    localStorage.setItem("sidebar-collapsed", isCollapsed.value);
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
.sidebar {
    width: 260px !important;
    background-color: var(--bg-sidebar, #081738);
    border-right: 1px solid var(--border-color, #1D2C52);
    display: flex;
    flex-direction: column;
    padding: 20px 0;
    flex-shrink: 0;
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-x: hidden;
}

/* Forzar el ancho cuando está colapsado */
.sidebar.sidebar-collapsed {
    width: 80px !important;
}

/* Perfil de usuario */
.user-profile {
    display: flex;
    align-items: center;
    padding: 0 20px 20px;
    border-bottom: 1px solid var(--border-color, #1D2C52);
    gap: 12px;
    transition:
        padding 0.3s ease,
        flex-direction 0.3s ease,
        justify-content 0.3s ease;
}

.avatar {
    width: 40px;
    height: 40px;
    background-color: var(--primary-blue, #2563eb);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    color: white;
    flex-shrink: 0;
}

.user-info h4 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
}

.user-info span {
    font-size: 10px;
    color: var(--text-muted, #8AABBB);
}

.menu-icon {
    margin-left: auto;
    cursor: pointer;
    color: var(--text-muted, #8AABBB);
    font-size: 20px;
    transition: transform 0.3s ease;
}

/* Secciones de navegación */
.nav-section {
    margin-top: 25px;
}

.nav-title {
    font-size: 11px;
    color: var(--text-muted, #8b95a5);
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
    color: var(--text-muted, #8b95a5);
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.nav-item i {
    font-size: 18px;
}

.nav-item:hover {
    color: var(--text-main, #ffffff);
}

.nav-item.active {
    background-color: rgba(51, 107, 250, 0.1);
    color: var(--text-main, #ffffff);
    border: 1px solid var(--accent-blue, #336BFA);
    border-radius: 8px;
    margin: 0 10px;
    padding: 12px 10px;
}

/* Transiciones de opacidad y desplazamiento para textos */
.user-info,
.nav-title,
.nav-text,
.logout-text {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
    opacity: 1;
    transform: translateX(0);
    white-space: nowrap;
}

/* Ocultar elementos de texto al colapsar */
.sidebar-collapsed .user-info,
.sidebar-collapsed .nav-text,
.sidebar-collapsed .logout-text {
    opacity: 0 !important;
    transform: translateX(-10px);
    pointer-events: none;
    display: inline-block;
    width: 0;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

/* Ocultar títulos de sección por completo para no dejar espacio vertical */
.sidebar-collapsed .nav-title {
    opacity: 0 !important;
    height: 0;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

/* Estilo del perfil de usuario en modo colapsado */
.sidebar-collapsed .user-profile {
    padding: 20px 0;
    flex-direction: column;
    justify-content: center;
    gap: 12px;
}

.sidebar-collapsed .menu-icon {
    margin: 0 !important;
}

/* Centrado de los elementos del menú en modo colapsado */
.sidebar-collapsed .nav-item {
    justify-content: center;
    padding: 12px 0;
    margin: 0 !important;
    border-radius: 0;
    border: none;
    background-color: transparent;
}

.sidebar-collapsed .nav-item.active {
    margin: 0 15px !important;
    padding: 12px 0;
    background-color: rgba(51, 107, 250, 0.1) !important;
    border: 1px solid var(--accent-blue, #336BFA) !important;
    border-radius: 8px !important;
}

.sidebar-collapsed .nav-item i {
    font-size: 20px;
}

/* Cerrar sesión */
.logout {
    margin-top: auto;
    padding: 20px;
    color: var(--critical, #dc2626);
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    transition:
        justify-content 0.3s ease,
        padding 0.3s ease;
}

.sidebar-collapsed .logout {
    justify-content: center;
    padding: 20px 0;
}
</style>
