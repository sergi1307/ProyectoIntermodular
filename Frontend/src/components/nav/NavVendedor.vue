<script setup>
import axios from 'axios';
import router from '../../routes/routes';

const cerrarSesion = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post(`http://localhost:8080/api/auth/logout`, {}, {
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
  padding: 10px 30px;
  background-color: #143b27;
  border-bottom: 1px solid #e0e0e0;
  height: 80px;
  box-sizing: border-box;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 15px;
}

.logo-img {
  width: 80px;
  height: auto;
}

.logo-text {
  font-size: 24px;
  font-weight: bold;
  color: white;
  text-decoration: none;
  font-family: sans-serif;
}

.grupo-iconos {
  display: flex;
  align-items: center;
  gap: 25px;
}

.link-modo {
  color: white;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  margin-right: 10px;
  transition: color 0.3s;
}

.link-modo:hover {
  color: #e67e22;
  text-decoration: underline;
}

.icono-btn {
  background: none;
  border: none;
  padding: 0;
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
  filter: invert(0.5) sepia(2) saturate(15) hue-rotate(-30deg);
}

.icono-btn:hover img {
  transform: scale(1.2);
  filter: invert(0.4) sepia(1) saturate(15) hue-rotate(-30deg);
}

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
  min-width: 160px;
  box-shadow: 0px 5px 15px rgba(0,0,0,0.15);
  border-radius: 8px;
  padding: 10px 0;
  z-index: 100;
  margin-top: 10px;
}

.subMenu::before {
  content: "";
  position: absolute;
  top: -6px;
  right: 10px;
  width: 12px;
  height: 12px;
  background: white;
  transform: rotate(45deg);
  border-top: 1px solid #eee;
  border-left: 1px solid #eee;
}

.listaDesplegable:hover .subMenu {
  display: block;
}

.subMenu button,
.subMenu a {
  display: block;
  width: 100%;
  padding: 12px 20px;
  text-align: left;
  background: none;
  border: none;
  color: #555;
  font-size: 14px;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s;
}

.subMenu button:hover,
.subMenu a:hover {
  background-color: #f9f9f9;
  color: #e67e22;
}
</style>