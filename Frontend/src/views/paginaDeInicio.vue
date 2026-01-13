<template>
    <div class="home-container">
      
      <nav class="navbar">
        <div class="logo">
          🌱 <span>ProxiMarkt</span>
        </div>
        <div class="nav-links">
          <router-link to="/login" class="btn-login">Iniciar Sesión</router-link>
        </div>
      </nav>
  
      <header class="hero-section">
        <div class="overlay"></div> 
        
        <div class="hero-content">
          <h1>De la Huerta a tu Mesa, <br> Sin Intermediarios</h1>
          <p>
            Conecta con los agricultores del barrio. 
            Pilla tomates que saben a tomate y déjate de historias de súper.
          </p>
          
          <div class="hero-buttons">
            <router-link to="/mapa" class="btn btn-primary">Ver Mercado</router-link>
            <button class="btn btn-outline">¿Ké ase esto?</button>
          </div>
        </div>
      </header>
  
      <section class="features-section">
        <h2>¿Por qué mola ProxiMarkt?</h2>
        <p class="subtitle">Básicamente porque ayudas al barrio y comes mejor.</p>
  
        <div class="features-grid">
          
          <div class="feature-card">
            <div class="icon-circle green">📍</div>
            <h3>Al lao' de casa</h3>
            <p>Pilla cosas de tu zona. Menos camiones dando vueltas = menos humo.</p>
          </div>
  
          <div class="feature-card">
            <div class="icon-circle red">🛡️</div>
            <h3>Gente legal</h3>
            <p>Sabes a quién le compras. Nada de movidas raras, todo verificado.</p>
          </div>
  
          <div class="feature-card">
            <div class="icon-circle grey">🌱</div>
            <h3>Fresco fresquísimo</h3>
            <p>Del campo a la bolsa. Nada de cámaras frigoríficas de hace 3 meses.</p>
          </div>
  
          <div class="feature-card">
            <div class="icon-circle brown">🤝</div>
            <h3>Barrio Rico</h3>
            <p>La pasta se queda aquí, pa la gente de tu pueblo o ciudad.</p>
          </div>
        </div>
      </section>
  
      <section class="cta-section">
        <h2>¿Te apuntas o qué?</h2>
        <p>Venga va, únete que somos gente maja.</p>
        <router-link to="/mapa" class="btn btn-light">Dale caña</router-link>
      </section>
  
    </div>
  </template>
  
  <script>
  export default {
    name: 'HomeView'
    // Aquí no meto lógica ni na, es solo visual pa que quede bonito
  }
  </script>