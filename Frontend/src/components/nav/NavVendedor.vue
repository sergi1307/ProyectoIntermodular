<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import router from "../../routes/routes";
import url from "../../config/api";
import { useNotificaciones } from '@/utilidades/useNotificaciones';

const token = localStorage.getItem("token");

const notificaciones = ref();
let intervaloNotificaciones = null;
const notificacion = useNotificaciones();

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

const cerrarSesion = async () => {
  try {
    if (token) {
      await axios.post(
        `${url}/api/auth/logout`,
        {},
        {
          withCredentials: true,
          headers: {
            Authorization: `Bearer ${token}`,
          },
        },
      );
    }
    notificacion.exito("Sesión cerrada correctamente"); 
    console.log("Cuenta cerrada correctamente");
    localStorage.removeItem("token");
    router.push("/");
  } catch (error) {
    console.error("Error al cerrar sesión:", error);
    router.push("/");
  }
};

onMounted(() => {
  intervaloNotificaciones = setInterval(obtenerNotificaciones, 3000);
});
</script>

<template>
  <nav class="nav">
    <div class="logo-section">
      <img
        src="../../assets/imgs/logoEmpresa.png"
        alt="Logo ProxiMarkt"
        class="logo-img"
      />
      <router-link to="/general" class="logo-text">ProxiMarkt</router-link>
    </div>

    <div class="grupo-iconos">
      <router-link to="/general" class="link-modo">&larr; Volver</router-link>

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
      <router-link to="message"
        ><button class="icono-btn">
          <img src="../../assets/icons/mensajes.png" alt="Mensajes" /></button
      ></router-link>
      <router-link><button>Logs</button></router-link>

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
/* --- NAV PRINCIPAL --- */
.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #143b27; /* Verde ProxiMarkt */
  border-bottom: 2px solid #0f2e1e;
  
  /* STICKY PARA QUE NO SE MUEVA */
  position: sticky;
  top: 0;
  width: 100%;
  z-index: 10000;
  
  /* CAMBIO CLAVE: Quitamos height fijo y usamos padding. 
     Esto asegura que el fondo verde envuelva TODO el contenido. */
  padding: 15px 30px; 
  box-sizing: border-box;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 15px;
  flex-shrink: 0;
}

.logo-img {
  width: 65px;
  height: auto;
}

.logo-text {
  font-size: 24px;
  font-weight: bold;
  color: white;
  text-decoration: none;
  font-family: sans-serif;
}

/* --- GRUPO DE ICONOS --- */
.grupo-iconos {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-shrink: 0;
  /* Permite que si la pantalla es muy pequeña, 
     los iconos bajen de línea pero sigan dentro del fondo verde */
  flex-wrap: wrap; 
  justify-content: flex-end;
}

.link-modo {
  color: white;
  text-decoration: none;
  font-size: 15px;
  font-weight: 600;
  white-space: nowrap;
}

.link-modo:hover {
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
  transition:
    transform 0.3s ease,
    color 0.3s ease;
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
  transition:
    transform 0.3s ease,
    color 0.3s ease;
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

.icono-btn {
  background: none;
  border: none;
  padding: 8px; /* Espacio para el filtro de color */
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icono-btn img {
  width: 28px;
  height: 28px;
  display: block;
  transition:
    transform 0.3s ease,
    filter 0.3s ease;
  filter: invert(0.5) sepia(2) saturate(15) hue-rotate(-30deg);
}

/* --- SUBMENÚ --- */
.listaDesplegable {
  position: relative;
}

.subMenu {
  display: none;
  position: absolute;
  top: 110%; /* Un poco más abajo del nav */
  right: 0;
  background-color: white;
  min-width: 180px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  border-radius: 8px;
  padding: 10px 0;
  z-index: 10001;
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

/* --- MEDIA QUERIES PARA MÓVIL --- */
@media (max-width: 768px) {
  .nav {
    padding: 12px 15px; /* Menos espacio lateral en tablet */
  }

  .logo-text {
    font-size: 1.1rem;
  }

  .logo-img {
    width: 50px;
  }
}

@media (max-width: 480px) {
  .nav {
    padding: 15px 10px; /* Más padding vertical para asegurar el verde */
    justify-content: center; /* Centramos logo arriba */
    flex-wrap: wrap;
    gap: 10px;
  }

  .logo-section {
    width: 100%;
    justify-content: center;
    margin-bottom: 5px;
  }

  .grupo-iconos {
    width: 100%;
    justify-content: space-between; /* Volver a un lado e iconos al otro */
    gap: 10px;
  }

  .link-modo {
    display: block !important;
    font-size: 14px;
  }
}
</style>
