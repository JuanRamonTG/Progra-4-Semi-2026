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
          <h2>Recuperar contraseña</h2>
          <p>Ingresa tu correo para recibir un enlace de restablecimiento</p>
        </div>

        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <form v-else @submit.prevent="submitForm">
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
                autofocus
              />
            </div>
          </div>

          <div v-if="errorMessage" class="error-message">
            <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
              <li>{{ errorMessage }}</li>
            </ul>
          </div>

          <button type="submit" class="submit-btn" :disabled="isLoading">
            {{ isLoading ? 'Enviando enlace...' : 'Enviar enlace de recuperación' }}
          </button>
        </form>

        <div class="divider">
          <span>o</span>
        </div>

        <p class="footer-text">
          ¿Recordaste tu contraseña? <a href="/login">Inicia sesión</a>
        </p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const form = reactive({
  email: '',
});

const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const submitForm = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await axios.post('/password/email', form);
    if (response.data.status) {
      successMessage.value = response.data.status;
    } else {
      successMessage.value = 'Se ha enviado un correo con las instrucciones para restablecer tu contraseña.';
    }
  } catch (error) {
    console.error('Password reset request error:', error);
    const responseData = error.response?.data;
    errorMessage.value = responseData?.message || responseData?.errors?.email?.[0] || 'Ocurrió un error al enviar el enlace.';
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

.error-message {
  margin-bottom: 24px;
  color: #f87171;
  font-size: 0.95rem;
  background: rgba(248, 113, 113, 0.1);
  border: 1px solid rgba(248, 113, 113, 0.25);
  border-radius: 14px;
  padding: 14px 18px;
}

.success-message {
  margin-bottom: 24px;
  color: #34d399;
  font-size: 0.95rem;
  background: rgba(52, 211, 153, 0.1);
  border: 1px solid rgba(52, 211, 153, 0.25);
  border-radius: 14px;
  padding: 14px 18px;
  text-align: center;
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
