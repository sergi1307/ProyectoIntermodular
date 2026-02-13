<script setup>
import axios from 'axios';
import router from '../../routes/routes';

const url = import.meta.env.VITE_API_URL_DEV;

const cerrarSesion = async () => {
  try {
    const token = localStorage.getItem('token');
    await axios.post(`${url}/api/auth/logout`, {}, {
      withCredentials: true,
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log("Cuenta cerrada correctamente");
    router.push('/');
  } catch (error) {
    console.error("Error al cerrar sesión:", error);
  }
}
</script>

<template>
  <nav class="nav">
    <div class="logo-section">
      <img src="../../assets/imgs/logoEmpresa.png" alt="Logo ProxiMarkt" class="logo-img">
      <router-link to="/general" class="logo-text">ProxiMarkt</router-link>
    </div>

    <div class="grupo-iconos">
      <router-link to="/general" class="link-modo">&larr; Volver</router-link>

      <button class="icono-btn">
        <img src="../../assets/icons/campana.png" alt="Notificaciones"/> 
      </button>

      <router-link to="message">
        <button class="icono-btn">
          <img src="../../assets/icons/mensajes.png" alt="Mensajes"/> 
        </button>
      </router-link>

      <button class="icono-btn">
        <img src="../../assets/icons/carrito.png" alt="Carrito" />
      </button>

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
  /* TU FILTRO NARANJA ORIGINAL */
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