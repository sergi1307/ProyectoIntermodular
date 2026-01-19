<template>
  <nav class="nav">
    
    <div class="logo">
      <img src="../assets/imgs/logoEmpresa.png" alt="Logo ProxiMarkt" width="100px">
      <span><router-link to="/general">ProxiMarkt</router-link></span>
    </div>

    <div class="menu">
      
      <button class="comprador">
        <span><router-link to="/general">Cambiar a modo comprador</router-link></span>
      </button>

      <div class="iconos">
        
        <button class="iconos">
          <img src="../assets/icons/campana.png" alt="foto campana"/> </button>

        <button class="iconos">
          <img src="../assets/icons/carrito.png" alt="foto carrito" /></button>

        <button class="iconos">
          <img src="../assets/icons/usuario.png" alt="foto usuario" />
        </button>

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

.logo, .menu, .comprador, .iconos {
  display: flex;
  align-items: center;
  gap: 15px;
}

.comprador {
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