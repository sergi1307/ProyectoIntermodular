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
    
    <div class="logo">
      <img src="../../assets/imgs/logoEmpresa.png" alt="Logo ProxiMarkt" width="100px">
      <span><router-link to="/general">ProxiMarkt</router-link></span>
    </div>

    <div class="menu">
      
      <button class="vendedor">
        <span><router-link to="/products">Cambiar a modo vendedor</router-link> </span>
      </button>

      <div class="iconos">
        
        <button class="iconos">
          <img src="../../assets/icons/campana.png" alt="foto campana"/> </button>

        <button class="iconos"><img src="../../assets/icons/carrito.png" alt="foto carrito" /></button>

        <div class="listaDesplegable">
          <button class="iconos">
            <img src="../../assets/icons/usuario.png" alt="foto usuario" />
          </button>
          <div class="subMenu">
            <button @click="cerrarSesion">Cerrar Sesión</button>
          </div>
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
  padding: 10px 20px;
  border-bottom: 1px solid #ddd;
}

.logo, .menu, .vendedor, .iconos {
  display: flex;
  align-items: center;
  gap: 15px;
}

.vendedor {
  background-color: #E05834;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
}
span{
font-size: 15px;
font-weight: bold;
}
.logo span{
font-size: 25px;
font-weight: bold;
}
.iconos {
  background: none;
  border: none;
  position: relative;
}

.listaDesplegable {
  position: relative;
  display: inline-block;
}

.subMenu {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  min-width: 140px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 4px;
  padding: 10px;
}

.subMenu a {
  color: #333;
  display: block;
  font-size: 14px;
}

.listaDesplegable:hover .subMenu {
  display: block;
}

.logo img{
  width: 100px;
  height: 100px;
  
}
img {
  width: 30px;
  height: 30px;
  display: block;
}
a{
text-decoration: none;
color:white
}
</style>