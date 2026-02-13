<script setup>
import axios from 'axios';
import router from '../../routes/routes';

const url = import.meta.env.VITE_API_URL_DEV;

const cerrarSesion = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post(`${url}/api/auth/logout`, {}, {
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
      <router-link to="/products" class="icono-btn">
        <img src="../../assets/icons/dashboard.png" alt="Dashboard">
      </router-link>

      <button class="icono-btn">
        <img src="../../assets/icons/campana.png" alt="Notificaciones"/> 
      </button>

      <router-link to="message"><button class="icono-btn">
        <img src="../../assets/icons/mensajes.png" alt="Notificaciones"/> 
      </button></router-link>

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