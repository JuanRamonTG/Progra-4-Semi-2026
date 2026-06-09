<template>
  <div class="stellar-home">
    <nav class="navbar">
      <div class="logo-container">
        <img src="/images/logo.png" alt="Stellar Traffic Logo" class="logo-img" />
        <div class="logo-divider"></div>
        <span class="logo-text">STELLAR<br>TRAFIC</span>
      </div>
      <ul class="nav-links">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#cobertura">Cobertura</a></li>
        <li><a href="#emergencias">Emergencias</a></li>
        <li><a href="#acerca">Acerca de</a></li>
      </ul>
      <button class="btn-primary" @click="goToLogin">Iniciar sesión</button>
    </nav>

    <header class="hero" id="inicio">
      <div class="hero-content">
        <h1>Gestión eficiente del<br>tránsito y la seguridad<br>vial</h1>
        <p>
          Stellar Traffic es una aplicación diseñada para gestionar y monitorear el tránsito vehicular de forma eficiente. Permite visualizar mapas en tiempo real, registrar información importante y mejorar el control y la seguridad vial mediante una interfaz moderna e intuitiva.
        </p>
        <button class="btn-outline">Conoce más <span class="arrow">›</span></button>
      </div>
      <div class="hero-map">
        <!-- Mapa interactivo real de Leaflet con imagen de preview de fondo -->
        <div id="hero-map-leaflet" class="map-leaflet-container"></div>
      </div>
    </header>

    <section class="institutions">
      <h3 class="section-subtitle">INSTITUCIONES INTEGRADAS</h3>
      <div class="card-container flex-row">
        <div class="inst-item">
          <h4>PNC</h4>
          <p>Policía Nacional Civil</p>
        </div>
        <div class="divider"></div>
        <div class="inst-item">
          <h4>VMT</h4>
          <p>Viceministerio de Transporte</p>
        </div>
        <div class="divider"></div>
        <div class="inst-item">
          <h4>CONASEVI</h4>
          <p>Consejo Nacional de Seguridad Vial</p>
        </div>
        <div class="divider"></div>
        <div class="inst-item">
          <h4>Cuerpos de socorro</h4>
          <p>Socorristas</p>
        </div>
        <div class="divider"></div>
        <div class="inst-item">
          <h4>Conductores</h4>
          <p>Comunidad Vial</p>
        </div>
      </div>
    </section>

    <section class="features">
      <div class="features-header">
        <h2>Todo lo que necesitas<br>en un solo lugar</h2>
        <div class="header-line"></div>
      </div>
      <div class="features-grid">
        <div class="feature-item">
          <div class="icon-container">
            <i class="ph ph-map-pin"></i>
          </div>
          <h4>Navega seguro</h4>
          <p>Rutas y tráfico en tiempo real.</p>
        </div>
        <div class="feature-item">
          <div class="icon-container">
            <i class="ph ph-warning"></i>
          </div>
          <h4>Alertas al instante</h4>
          <p>Incidentes y emergencias en tu ruta.</p>
        </div>
        <div class="feature-item">
          <div class="icon-container">
            <i class="ph ph-users"></i>
          </div>
          <h4>Coordinación</h4>
          <p>Respuesta conectada y más rápida</p>
        </div>
        <div class="feature-item">
          <div class="icon-container">
            <i class="ph ph-shield"></i>
          </div>
          <h4>Más seguro</h4>
          <p>Tecnología y datos para prevenir y salvar vidas.</p>
        </div>
      </div>
    </section>

    <section class="impact">
      <h3 class="section-subtitle">INDICADORES DE IMPACTO</h3>
      <div class="card-container flex-row space-evenly">
        <div class="impact-item">
          <i class="ph-fill ph-shield impact-icon"></i>
          <div class="text">
            <h4>24/7</h4>
            <p>Monitoreo continuo</p>
          </div>
        </div>
        <div class="impact-item">
          <i class="ph-fill ph-car impact-icon"></i>
          <div class="text">
            <h4>+1200</h4>
            <p>Incidentes atendidos diariamente</p>
          </div>
        </div>
        <div class="impact-item">
          <i class="ph ph-clock impact-icon"></i>
          <div class="text">
            <h4>38%</h4>
            <p>Reducción en tiempos de respuesta</p>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer">
      <div class="footer-brand">
        <img src="/images/logo.png" alt="Stellar Traffic Logo" class="footer-logo-img" />
        <div>
          <h4>STELLAR TRAFFIC</h4>
          <p class="subtitle">PLATAFORMA DE MOVILIDAD Y RESPUESTA VIAL</p>
          <p class="desc">Sistema para gestión integrada de la movilidad, seguridad vial y respuesta a emergencias.</p>
        </div>
      </div>
      <div class="footer-links">
        <h4>Enlaces</h4>
        <ul>
          <li><a href="#">Instituciones</a></li>
          <li><a href="#">Cobertura</a></li>
          <li><a href="#">Emergencias</a></li>
          <li><a href="#">Acerca de</a></li>
        </ul>
      </div>
      <div class="footer-social">
        <h4>Síguenos</h4>
        <div class="social-icons">
          <i class="ph-fill ph-facebook-logo"></i>
          <i class="ph-fill ph-instagram-logo"></i>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';

const goToLogin = () => {
  window.location.href = '/login';
};

let map = null;

onMounted(() => {
  if (window.L) {
    // Inicializar el mapa de Leaflet interactivo sobre el contenedor
    map = window.L.map('hero-map-leaflet', {
      zoomControl: true,
      dragging: true,
      scrollWheelZoom: false,
      attributionControl: false
    }).setView([9.9358, -84.096], 14); // Coordenadas centradas en San José (cerca de La Sabana)

    // Cargar tiles en modo oscuro a juego con el diseño
    window.L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      maxZoom: 20
    }).addTo(map);

    // Ajustar la posición de los controles de zoom a la derecha (como en Figma)
    map.zoomControl.setPosition('topright');

    // Marcador personalizado (estilo navegación/GPS)
    const customIcon = window.L.divIcon({
      className: 'custom-map-marker',
      html: `
        <div class="marker-pulse"></div>
        <div class="marker-pin">
          <i class="ph-fill ph-navigation-arrow" style="transform: rotate(45deg); display: inline-block;"></i>
        </div>
      `,
      iconSize: [40, 40],
      iconAnchor: [20, 20]
    });

    // Agregar un pin en el centro
    window.L.marker([9.9358, -84.096], { icon: customIcon }).addTo(map);
  }
});

onUnmounted(() => {
  if (map) {
    map.remove();
  }
});
</script>

<style scoped>
/* Variables de color basadas en la imagen */
:root {
  --bg-dark: #0A0F1D;
  --bg-card: #0F172A;
  --text-main: #FFFFFF;
  --text-muted: #9CA3AF;
  --primary-blue: #1D4ED8;
  --primary-hover: #2563EB;
  --border-color: rgba(255, 255, 255, 0.1);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.stellar-home {
  background-color: #0A0F1D;
  color: #FFFFFF;
  font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  min-height: 100vh;
  overflow-y: auto; 
}

/* === Navbar === */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 5%;
  background-color: transparent;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo-img {
  width: 45px;
  height: 45px;
  object-fit: contain;
}

.logo-divider {
  width: 1px;
  height: 35px;
  background-color: rgba(255, 255, 255, 0.3);
}

.logo-text {
  font-weight: 700;
  letter-spacing: 1px;
  font-size: 1.1rem;
  line-height: 1.1;
  color: #FFFFFF;
}

.nav-links {
  display: flex;
  gap: 2rem;
  list-style: none;
}

.nav-links a {
  color: #9CA3AF;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s;
}

.nav-links a:hover {
  color: #FFFFFF;
}

.btn-primary {
  background-color: #1D4ED8;
  color: white;
  border: none;
  padding: 0.6rem 1.5rem;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.3s;
}

.btn-primary:hover {
  background-color: #2563EB;
}

/* === Hero Section === */
.hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  padding: 4rem 5%;
  align-items: center;
}

.hero-content h1 {
  font-size: 3rem;
  line-height: 1.2;
  margin-bottom: 1.5rem;
}

.hero-content p {
  color: #9CA3AF;
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 90%;
}

.btn-outline {
  background: transparent;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 0.8rem 1.5rem;
  border-radius: 25px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s;
}

.btn-outline:hover {
  background: rgba(255,255,255,0.05);
}

.hero-map {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

/* Contenedor Leaflet interactivo */
.map-leaflet-container {
  width: 100%;
  height: 400px;
  border-radius: 20px;
  border: 2px solid #1D4ED8;
  box-shadow: 0 0 25px rgba(29, 78, 216, 0.35);
  background-image: url('/images/map_hero.png');
  background-size: cover;
  background-position: center;
  z-index: 10;
}

/* Estilos personalizados para el marcador del mapa */
:deep(.custom-map-marker) {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

:deep(.marker-pin) {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #1D4ED8;
  border: 2.5px solid #FFFFFF;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-size: 14px;
  box-shadow: 0 0 10px rgba(29, 78, 216, 0.6);
  z-index: 2;
}

:deep(.marker-pulse) {
  position: absolute;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(29, 78, 216, 0.4);
  animation: pulse 2s infinite;
  z-index: 1;
}

@keyframes pulse {
  0% {
    transform: scale(0.6);
    opacity: 1;
  }
  100% {
    transform: scale(1.3);
    opacity: 0;
  }
}

/* === Reusable Cards & Sections === */
.section-subtitle {
  text-align: center;
  font-size: 0.8rem;
  color: #9CA3AF;
  letter-spacing: 2px;
  margin-bottom: 1.5rem;
}

.card-container {
  background-color: transparent;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 2rem;
  margin: 0 5% 4rem;
}

.flex-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.space-evenly {
  justify-content: space-evenly;
}

.divider {
  width: 1px;
  height: 50px;
  background-color: rgba(255, 255, 255, 0.1);
}

/* === Instituciones === */
.inst-item {
  text-align: center;
  flex: 1;
}

.inst-item h4 {
  font-size: 1.2rem;
  margin-bottom: 0.3rem;
}

.inst-item p {
  font-size: 0.8rem;
  color: #9CA3AF;
}

/* === Características === */
.features {
  padding: 2rem 5% 4rem;
}

.features-header {
  margin-bottom: 3rem;
}

.features-header h2 {
  font-size: 2.2rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.header-line {
  width: 60px;
  height: 3px;
  background-color: #1D4ED8;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.feature-item {
  text-align: center;
  padding: 1.5rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

/* Divisorias verticales entre características */
.feature-item:not(:last-child) {
  border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.icon-container {
  font-size: 2.2rem;
  color: #FFFFFF;
  margin-bottom: 1.2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 60px;
  width: 60px;
  border-radius: 50%;
  background: rgba(29, 78, 216, 0.1);
  border: 1px solid rgba(29, 78, 216, 0.2);
}

.feature-item h4 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.6rem;
  color: #FFFFFF;
}

.feature-item p {
  font-size: 0.9rem;
  color: #9CA3AF;
  line-height: 1.4;
  max-width: 90%;
}

/* === Indicadores === */
.impact-item {
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.impact-icon {
  font-size: 2.5rem;
  color: #FFFFFF;
}

.impact-item h4 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #FFFFFF;
}

.impact-item p {
  font-size: 0.85rem;
  color: #9CA3AF;
}

/* === Footer === */
.footer {
  background-color: #080D18;
  padding: 4rem 5% 2rem;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-brand {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
}

.footer-logo-img {
  width: 55px;
  height: 55px;
  object-fit: contain;
}

.footer-brand .subtitle {
  font-size: 0.7rem;
  color: #9CA3AF;
  letter-spacing: 1px;
  margin: 0.5rem 0;
}

.footer-brand .desc {
  font-size: 0.9rem;
  color: #9CA3AF;
  max-width: 80%;
}

.footer-links ul {
  list-style: none;
  margin-top: 1rem;
}

.footer-links li {
  margin-bottom: 0.8rem;
}

.footer-links a {
  color: #9CA3AF;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-links a:hover {
  color: #FFFFFF;
}

.footer-social h4 {
  margin-bottom: 1rem;
}

.social-icons {
  display: flex;
  gap: 1.5rem;
  font-size: 1.8rem;
  color: #9CA3AF;
  cursor: pointer;
}

.social-icons i:hover {
  color: #FFFFFF;
  transition: color 0.3s;
}

/* === Responsive (Móviles y Tablets) === */
@media (max-width: 992px) {
  .hero {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .hero-content p {
    margin: 0 auto 2rem;
  }
  
  .btn-outline {
    margin: 0 auto;
  }
  
  .features-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
  
  .feature-item:not(:last-child) {
    border-right: none;
  }
}

@media (max-width: 768px) {
  .nav-links, .btn-primary {
    display: none;
  }
  
  .flex-row {
    flex-direction: column;
    gap: 2rem;
  }
  
  .divider {
    width: 50%;
    height: 1px;
  }
  
  .feature-item {
    border-right: none !important;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
  }
  
  .footer {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .footer-brand {
    flex-direction: column;
    align-items: center;
  }
  
  .footer-brand .desc {
    margin: 0 auto;
  }
  
  .social-icons {
    justify-content: center;
  }
}
</style>
