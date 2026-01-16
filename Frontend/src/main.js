import { createApp } from 'vue';
import App from './App.vue';
import router from './routes/routes.js';

import './assets/css/style.css'; 

const app = createApp(App);

app.use(router);
app.mount('#app');