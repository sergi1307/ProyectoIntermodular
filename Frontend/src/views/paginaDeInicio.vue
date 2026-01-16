<script setup lang="ts">
import router from '../routes/routes';

</script>

<template>
  <nav>
    <div id="empresa">
      <img src="../assets/imgs/logoEmpresa.png" alt="Logo ProxiMarkt" width="100px">
      <p>ProxiMarkt</p>
    </div>
    <router-link to="/login" id="iniciarSesion">Iniciar Sesión</router-link>
  </nav>

  <section id="section1">
    <div class="maximaAmplitud">
      <div id="contenido">
        <h2>Productos Frescos de Granjas Locales<br>Directo a tu Hogar</h2>
        <p>Conecta con agricultores y vendedores locales de confianza. Apoya la agricultura sostenible y disfruta de los productos más frescos de tu comunidad.</p>
      </div>
      <div id="sectionButtons">
        <router-link to="/login" id="comenzar">Comenzar</router-link>
        <router-link to="/mapa" id="saberMas">Tiendas Locales</router-link>
      </div>
    </div>
  </section>

  <section id="section2">
    <div id="contenido">
      <h3>¿Por Qué Elegir ProxiMarkt?</h3>
      <p>Estamos construyendo un mercado sostenible que beneficia a todos en tu comunidad</p>
    </div>
    <div id="grid">
      <div>
        <img src="../assets/imgs/logos/LogoUbicacion.png" alt="Logo Ubicación" width="60px">
        <h4>Hiperlocal</h4>
        <p>Encuentra productos de tu vecindario. Reduce tu huella de carbono.</p>
      </div>
      <div>
        <img src="../assets/imgs/logos/LogoSeguridad.png" alt="Logo Ubicación" width="60px">
        <h4>Vendedores de Confianza</h4>
        <p>Agricultores y productores locales verificados en los que puedes confiar.</p>
      </div>
      <div>
        <img src="../assets/imgs/logos/LogoPlanta.png" alt="Logo Ubicación" width="60px">
        <h4>Fresco y Orgánico</h4>
        <p>Productos de granja recién cosechados en su punto óptimo de maduración.</p>
      </div>
      <div>
        <img src="../assets/imgs/logos/LogoComunidad.png" alt="Logo Ubicación" width="60px">
        <h4>Comunidad Primero</h4>
        <p>Apoya la economía local y construye relaciones duraderas.</p>
      </div>
    </div>
  </section>

  <section id="section3">
    <div id="contenido">
      <h3>¿Listo para Comenzar?</h3>
      <p>Únete a nuestra creciente comunidad de consumidores conscientes y productores locales dedicados.</p>
      <router-link to="/login" id="boton">Comenzar Ahora</router-link>
    </div>
  </section>
</template>