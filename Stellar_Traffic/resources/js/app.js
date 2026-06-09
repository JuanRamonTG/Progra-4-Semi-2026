import './bootstrap';
import * as bootstrap from 'bootstrap';
import { createApp } from 'vue';
import router from './router';

// Componentes
import AppMain from './components/App.vue';
import LoginForm from './components/LoginForm.vue';
import RegisterForm from './components/RegisterForm.vue';
import ForgotPasswordForm from './components/ForgotPasswordForm.vue';
import ResetPasswordForm from './components/ResetPasswordForm.vue';
import LandingPublic from './components/LandingPublic.vue';
import CoberturaPublic from './components/CoberturaPublic.vue';
import EmergenciasPublic from './components/EmergenciasPublic.vue';
import AcercaDePublic from './components/AcercaDePublic.vue';

const app = createApp({});

app.component('app-main', AppMain);
app.component('login-form', LoginForm);
app.component('register-form', RegisterForm);
app.component('forgot-password-form', ForgotPasswordForm);
app.component('reset-password-form', ResetPasswordForm);
app.component('landing-public', LandingPublic);
app.component('cobertura-public', CoberturaPublic);
app.component('emergencias-public', EmergenciasPublic);
app.component('acerca-de-public', AcercaDePublic);

app.use(router);
app.mount('#app');
