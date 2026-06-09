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
          <h2>Crea tu cuenta</h2>
          <p>Regístrate para acceder al sistema inteligencia vial</p>
        </div>

        <div v-if="errorMessage" class="error-message">
          <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
            <li>{{ errorMessage }}</li>
          </ul>
        </div>

        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="name">Nombre Completo</label>
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
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <input
                type="text"
                id="name"
                v-model="form.name"
                placeholder="Ej. Luis Zelaya"
                required
              />
            </div>
          </div>


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

          <div class="form-group">
            <label for="password_confirmation">Confirmar contraseña</label>
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
                :type="showPasswordConfirm ? 'text' : 'password'"
                id="password_confirmation"
                v-model="form.password_confirmation"
                placeholder="••••••••••••••••"
                required
              />
              <svg
                class="eye-icon"
                @click="showPasswordConfirm = !showPasswordConfirm"
                :style="{ opacity: showPasswordConfirm ? '0.5' : '1' }"
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

          <label class="terms-checkbox">
            <input type="checkbox" v-model="form.terms" required />
            <span>
              Acepto los <a href="#">términos y condiciones</a> y la
              <a href="#">política de privacidad</a>
            </span>
          </label>

          <button type="submit" class="submit-btn" :disabled="isLoading">
            {{ isLoading ? 'Creando cuenta...' : 'Crear Cuenta' }}
          </button>

          <div class="divider">
            <span>o</span>
          </div>

          <p class="footer-text">
            ¿Ya tienes una cuenta? <a href="/login">Inicia sesión</a>
          </p>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

const submitForm = async () => {
  if (!form.terms) {
    errorMessage.value = 'Debes aceptar los términos y condiciones.';
    return;
  }

  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await axios.post('/register', form);
    if (response.data.redirect) {
      window.location.href = response.data.redirect;
    }
  } catch (error) {
    console.error('Registration error:', error);
    const responseData = error.response?.data;
    
    // Si hay errores de validación específicos, sacamos el primero
    if (responseData?.errors) {
        const firstErrorKey = Object.keys(responseData.errors)[0];
        errorMessage.value = responseData.errors[firstErrorKey][0];
    } else {
        errorMessage.value = responseData?.message || 'Ocurrió un error al crear la cuenta.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
:root {
  --bg-color: #02060f;
  --card-bg: #030a1c;
  --accent-blue: #2563eb;
  --accent-glow: rgba(37, 99, 235, 0.3);
  --text-white: #ffffff;
  --text-gray: #94a3b8;
  --border-color: rgba(37, 99, 235, 0.2);
  --input-bg: rgba(5, 15, 35, 0.6);
}

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
  grid-template-columns: 1fr 1.2fr;
  gap: 80px;
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
  padding: 40px;
  width: 100%;
  max-width: 600px;
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
  margin-bottom: 28px;
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

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}

@media (max-width: 600px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.85rem;
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
  padding: 12px 16px 12px 44px;
  color: white;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.3);
}

.input-wrapper svg {
  position: absolute;
  left: 14px;
  color: #94a3b8;
  width: 18px;
  height: 18px;
  pointer-events: none;
}

.eye-icon {
  position: absolute;
  right: 14px !important;
  left: auto !important;
  cursor: pointer;
  pointer-events: all !important;
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

.terms-checkbox {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 20px 0 24px 0;
  font-size: 0.8rem;
  color: #94a3b8;
  cursor: pointer;
}

.terms-checkbox input {
  accent-color: #2563eb;
  width: 16px;
  height: 16px;
}

.terms-checkbox a {
  color: #2563eb;
  text-decoration: none;
}

.submit-btn {
  width: 100%;
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 12px;
  padding: 14px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
  margin-bottom: 20px;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5);
  background-color: #1d4ed8;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.divider {
  display: flex;
  align-items: center;
  margin: 20px 0;
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
