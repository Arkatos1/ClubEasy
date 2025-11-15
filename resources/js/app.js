import './bootstrap';

import { createApp } from 'vue';
import Calendar from './components/Calendar.vue';

// Vue 3 setup
const app = createApp({});
app.component('Calendar', Calendar);
app.mount('#app');

// Keep Alpine for other components
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
