<template>
  <div class="bg-glow-1"></div>
  <div class="bg-glow-2"></div>

  <div class="container">
    <!-- Left Section -->
    <section class="left-section">
      <div class="logo-container">
        <img src="/images/logo.png" alt="Stellar Traffic Logo" />
      </div>
      <h1 class="brand-title">Stellar <span>Traffic</span></h1>
      <p class="brand-subtitle">Sistema Inteligente de Seguridad Vial</p>
    </section>

    <!-- Right Section -->
    <section class="right-section">
      <div class="login-card">
        <div class="card-header">
          <h2>Iniciar sesión</h2>
          <p>Ingresa tus credenciales para continuar</p>
        </div>

        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <div class="input-wrapper">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              <input
                type="email"
                id="email"
                v-model="form.email"
                placeholder="usuario@ejemplo.com"
                required
              />
            </div>
          </div>


          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-wrapper">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                v-model="form.password"
                placeholder="••••••••••••••••"
                required
              />
              <svg
                class="eye-icon"
                @click="showPassword = !showPassword"
                :style="{ opacity: showPassword ? '0.5' : '1' }"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </div>
          </div>

          <div class="form-options">
            <label class="remember-me">
              <input type="checkbox" v-model="form.remember" />
              <span>Recuérdame</span>
            </label>
            <a href="/password/forgot" class="forgot-password">¿Olvidaste tu contraseña?</a>
          </div>

          <div v-if="errorMessage" class="error-message">
            <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
              <li>{{ errorMessage }}</li>
            </ul>
          </div>

          <button type="submit" class="submit-btn" :disabled="isLoading">
            {{ isLoading ? 'Iniciando sesión...' : 'Iniciar sesión' }}
          </button>

          <div class="divider">
            <span>o</span>
          </div>

          <p class="footer-text">
            ¿No tienes una cuenta? <a href="/register">Regístrate</a>
          </p>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';

const form = reactive({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

const submitForm = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await axios.post('/login', form);
    if (response.data.redirect) {
      window.location.href = response.data.redirect;
    }
  } catch (error) {
    console.error('Login error:', error);
    const responseData = error.response?.data;
    errorMessage.value = responseData?.message || responseData?.errors?.email?.[0] || responseData?.errors?.password?.[0] || 'Credenciales incorrectas o error de servidor.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.bg-glow-1 {
  position: absolute;
  top: -10%;
  left: -10%;
  width: 40%;
  height: 40%;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
  z-index: -1;
}

.bg-glow-2 {
  position: absolute;
  bottom: -10%;
  right: -10%;
  width: 40%;
  height: 40%;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
  z-index: -1;
}

.container {
  width: 100%;
  max-width: 1200px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 100px;
  padding: 40px;
  align-items: center;
  margin: 0 auto;
}

@media (max-width: 900px) {
  .container {
    grid-template-columns: 1fr;
    text-align: center;
    gap: 40px;
  }
  .left-section {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
}

.left-section {
  animation: fadeIn 1s ease-out;
}

.logo-container {
  width: 280px;
  height: 280px;
  margin-bottom: 24px;
  border-radius: 40px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
}

.logo-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.brand-title {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 8px;
  letter-spacing: -1px;
  color: white;
}

.brand-title span {
  color: #2563eb;
}

.brand-subtitle {
  font-size: 1.2rem;
  color: #94a3b8;
  font-weight: 400;
}

.right-section {
  animation: slideInRight 0.8s ease-out;
}

.login-card {
  background-color: #030a1c;
  border: 1.5px solid rgba(37, 99, 235, 0.2);
  border-radius: 24px;
  padding: 48px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
  position: relative;
  overflow: hidden;
}

.login-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, transparent, #2563eb, transparent);
  opacity: 0.5;
}

.card-header {
  text-align: center;
  margin-bottom: 32px;
}

.card-header h2 {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: white;
}

.card-header p {
  color: #94a3b8;
  font-size: 0.95rem;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.9rem;
  color: white;
  font-weight: 400;
  text-align: left;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper input {
  width: 100%;
  background-color: rgba(5, 15, 35, 0.6);
  border: 1px solid rgba(37, 99, 235, 0.2);
  border-radius: 12px;
  padding: 14px 16px 14px 48px;
  color: white;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.3);
}

.input-wrapper svg {
  position: absolute;
  left: 16px;
  color: #94a3b8;
  width: 20px;
  height: 20px;
  pointer-events: none;
}

.eye-icon {
  position: absolute;
  right: 16px !important;
  left: auto !important;
  cursor: pointer;
  pointer-events: all !important;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  font-size: 0.85rem;
}

.error-message {
  margin-bottom: 24px;
  color: #f87171;
  font-size: 0.95rem;
  background: rgba(248, 113, 113, 0.1);
  border: 1px solid rgba(248, 113, 113, 0.25);
  border-radius: 14px;
  padding: 14px 18px;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  color: white;
}

.remember-me input {
  accent-color: #2563eb;
  width: 16px;
  height: 16px;
}

.forgot-password {
  color: #2563eb;
  text-decoration: none;
  transition: opacity 0.2s;
}

.forgot-password:hover {
  opacity: 0.8;
}

.submit-btn {
  width: 100%;
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 12px;
  padding: 16px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5);
  background-color: #1d4ed8;
}

.secondary-btn {
  display: block;
  width: 100%;
  margin: 18px 0 0;
  text-align: center;
  padding: 14px 0;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: white;
  text-decoration: none;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.04);
  transition: background 0.2s ease, transform 0.2s ease;
}

.secondary-btn:hover {
  background: rgba(255, 255, 255, 0.08);
  transform: translateY(-1px);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.divider {
  display: flex;
  align-items: center;
  margin: 24px 0;
  color: #94a3b8;
  font-size: 0.9rem;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background-color: rgba(37, 99, 235, 0.2);
}

.divider span {
  padding: 0 16px;
}

.footer-text {
  text-align: center;
  font-size: 0.9rem;
  color: #94a3b8;
}

.footer-text a {
  color: #2563eb;
  text-decoration: none;
  font-weight: 600;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>
