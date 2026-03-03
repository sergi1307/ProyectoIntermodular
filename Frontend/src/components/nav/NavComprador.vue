<script setup>
import axios from 'axios';
import router from '../../routes/routes';
import { ref, onMounted } from 'vue';
import NotificationBell from '../notifications/NotificationBell.vue';
import { useNotificaciones } from '@/utilidades/useNotificaciones';

import url from '../../config/api';

const token = localStorage.getItem("token");
const notificaciones = ref();
let intervaloNotificaciones = null;
const notificacion = useNotificaciones();

const cerrarSesion = async () => {
  try {
    const response = await axios.post(`${url}/api/auth/logout`, {}, {
      withCredentials: true,
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    notificacion.exito("Sesión cerrada correctamente"); 
    console.log("Cuenta cerrada correctamente");
    router.push('/');
  } catch (error) {
    console.error("Error al cerrar sesión:", error);
  }
}

const obtenerNotificaciones = async () => {
  try {
    const response = await axios.get(`${url}/api/notifications/unread-count`, {
      headers: { 
        Authorization:  `Bearer ${token}` 
      }
    });

    notificaciones.value = response.data.count;
  } catch (error) {
    console.log('Error:', error);
  }
}

onMounted(() => {
  intervaloNotificaciones = setInterval(obtenerNotificaciones, 3000);
});
</script>

<template>
  <nav class="nav">
    <div class="logo-section">
      <img src="../../assets/imgs/logoEmpresa.png" alt="Logo ProxiMarkt" class="logo-img">
      <router-link to="/general" class="logo-text">ProxiMarkt</router-link>
    </div>

    <div class="grupo-iconos">
      <router-link to="/products" class="icono-btn">
        <img src="../../assets/icons/dashboard.png" alt="Dashboard">
      </router-link>

      <router-link to="/mis-notificaciones" class="notificacion-wrapper">
        <button class="icono-btn">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            class="svg-icon"
          >
            <path
              fill-rule="evenodd"
              d="M5.25 9a6.75 6.75 0 0113.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 01-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 11-7.48 0 24.585 24.585 0 01-4.831-1.244.75.75 0 01-.298-1.205A8.217 8.217 0 005.25 9.75V9zm4.502 8.9a2.25 2.25 0 104.496 0 25.057 25.057 0 01-4.496 0z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
        <span v-if="notificaciones > 0" class="badge">{{
          notificaciones
        }}</span>
      </router-link>
      <router-link to="MostrarLogs"><button>Logs</button></router-link>
      <router-link to="message"><button class="icono-btn">
        <img src="../../assets/icons/mensajes.png" alt="Notificaciones"/> 
      </button></router-link>

      <div class="listaDesplegable">
        <button class="icono-btn">
          <img src="../../assets/icons/usuario.png" alt="Usuario" />
        </button>
        
        <div class="subMenu">
          
          <button @click="cerrarSesion">Cerrar Sesión</button>
          <router-link to="/mis-compras">Mis Compras</router-link>
          <router-link to="/mis-tiendas">Mis Tiendas</router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #143b27;
  border-bottom: 1px solid #0f2e1e;
  
  /* STICKY: Se queda fijo arriba y ocupa su espacio real */
  position: sticky;
  top: 0;
  width: 100%;
  z-index: 10000;
  
  /* Ajuste de altura para que el verde cubra todo */
  min-height: 90px; 
  padding: 10px 30px;
  box-sizing: border-box;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 15px;
}

.logo-img {
  width: 70px;
  height: auto;
}

.logo-text {
  font-size: 24px;
  font-weight: bold;
  color: white;
  text-decoration: none;
  font-family: sans-serif;
  white-space: nowrap;
}

.grupo-iconos {
  display: flex;
  align-items: center;
  gap: 20px;
  /* Permite que si hay muchos iconos bajen un poco sin salirse de lo verde */
  flex-wrap: wrap; 
  justify-content: flex-end;
}

.icono-btn {
  background: none;
  border: none;
  padding: 8px; /* Más área verde detrás del icono */
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icono-btn img {
  width: 28px;
  height: 28px;
  display: block;
  transition: transform 0.3s ease, filter 0.3s ease;
  /* Filtro naranja/marrón original */
  filter: invert(0.5) sepia(2) saturate(15) hue-rotate(-30deg);
}

.icono-btn:hover img {
  transform: scale(1.15);
  filter: invert(0.4) sepia(1) saturate(15) hue-rotate(-30deg);
}

/* --- MENÚ DESPLEGABLE --- */
.listaDesplegable {
  position: relative;
  display: flex;
  align-items: center;
}

.subMenu {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  min-width: 170px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  border-radius: 8px;
  padding: 10px 0;
  z-index: 10001;
  margin-top: 10px;
}

.subMenu::before {
  content: "";
  position: absolute;
  top: -6px;
  right: 18px;
  width: 12px;
  height: 12px;
  background: white;
  transform: rotate(45deg);
}

.listaDesplegable:hover .subMenu {
  display: block;
}

.subMenu button, .subMenu a {
  display: block;
  width: 100%;
  padding: 12px 20px;
  text-align: left;
  background: none;
  border: none;
  color: #333;
  font-size: 14px;
  text-decoration: none;
  cursor: pointer;
}

.subMenu button:hover, .subMenu a:hover {
  background-color: #f5f5f5;
  color: #e67e22;
}

.notificacion-wrapper {
  position: relative; 
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.svg-icon {
  width: 28px;
  height: 28px;
  color: #e84930;
  transition: transform 0.3s ease, color 0.3s ease;
}

.icono-btn:hover .svg-icon {
  transform: scale(1.2);
  filter: invert(0.4) sepia(1) saturate(15) hue-rotate(-30deg);
}

.badge {
  position: absolute;
  top: -5px;
  left: -8px;
  background-color: #e74c3c;
  color: white;
  font-size: 11px;
  font-weight: bold;
  height: 18px;
  width: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #143b27;
  pointer-events: none;
  z-index: 10;
}

/* --- MEDIA QUERIES --- */
@media (max-width: 768px) {
  .nav {
    padding: 10px 15px;
    min-height: 80px;
  }

  .logo-img {
    width: 50px;
  }

  .logo-text {
    font-size: 1.2rem;
  }

  .grupo-iconos {
    gap: 10px;
  }

  .icono-btn img {
    width: 24px;
    height: 24px;
  }
}

@media (max-width: 480px) {
  .nav {
    /* En móviles muy pequeños, el nav crece para que los iconos no se salgan */
    padding: 15px 10px;
    flex-wrap: wrap;
    justify-content: center; /* Centra todo si no caben en una línea */
    gap: 10px;
  }

  .logo-section {
    width: 100%; /* El logo ocupa arriba solo */
    justify-content: center;
    margin-bottom: 5px;
  }

  .grupo-iconos {
    width: 100%;
    justify-content: space-around; /* Reparte los iconos en el ancho */
  }
}
</style>