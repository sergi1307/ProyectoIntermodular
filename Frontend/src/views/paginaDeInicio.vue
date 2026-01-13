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
    // Dejo esto aquí para cumplir con el estándar y que las DevTools reconozcan el componente.
    // Aunque ahora mismo sea solo visual, es buena práctica mantener la estructura.
  }
  </script>
  
  <style scoped>
  /* Uso scoped para que estos estilos no me rompan otros componentes */
  
  /* --- ESTILOS GENERALES --- */
  .home-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }
  
  /* --- NAVBAR --- */
  .navbar {
    /* Position absolute para que se monte encima de la imagen del Hero */
    position: absolute; 
    top: 0;
    width: 100%;
    padding: 20px 40px;
    /* Flexbox para separar logo (izq) y login (der) rápidamente */
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 10;
  }
  
  .logo {
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3); /* Sombrita para legibilidad */
  }
  
  .btn-login {
    background-color: white;
    color: #1b4d3e;
    padding: 8px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
  }
  
  .btn-login:hover {
    background-color: #f0f0f0;
  }
  
  /* --- HERO SECTION --- */
  .hero-section {
    position: relative;
    height: 90vh; /* Ocupa casi toda la altura de la pantalla */
    background-image: url('https://images.unsplash.com/photo-1596450514967-f4192b02a945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
    background-size: cover;
    background-position: center;
    /* Flex para centrar verticalmente el contenido */
    display: flex;
    align-items: center;
    padding-left: 80px; 
  }
  
  /* Capa oscura (Overlay) */
  .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* Gradiente negro semitransparente para que resalte el texto blanco */
    background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
    z-index: 1;
  }
  
  .hero-content {
    position: relative;
    z-index: 2; /* Por encima del overlay */
    color: white;
    max-width: 600px;
  }
  
  .hero-content h1 {
    font-size: 3.5rem;
    line-height: 1.1;
    margin-bottom: 20px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
  }
  
  .hero-content p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.95;
  }
  
  .hero-buttons {
    display: flex;
    gap: 15px;
  }
  
  /* --- BOTONES REUTILIZABLES --- */
  .btn {
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    border: none;
    display: inline-block;
    transition: transform 0.2s;
  }
  
  .btn:active {
    transform: scale(0.98); /* Efecto de click */
  }
  
  .btn-primary {
    background-color: #2d8a5e; 
    color: white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
  }
  
  .btn-primary:hover {
    background-color: #246e4b;
  }
  
  .btn-outline {
    background-color: transparent;
    border: 2px solid white;
    color: white;
  }
  
  .btn-outline:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  .btn-light {
    background-color: white;
    color: #1b4d3e;
  }
  
  /* --- SECCIÓN GRID --- */
  .features-section {
    padding: 80px 20px;
    text-align: center;
    background-color: #fff;
  }
  
  .features-section h2 {
    color: #1b4d3e;
    font-size: 2.2rem;
    margin-bottom: 10px;
  }
  
  .subtitle {
    color: #666;
    margin-bottom: 50px;
    font-size: 1.1rem;
  }
  
  /* GRID LAYOUT: Lo importante aquí */
  .features-grid {
    display: grid;
    /* repeat(auto-fit...) hace que sea responsive sin usar media queries manuales */
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .feature-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    transition: transform 0.3s;
  }
  
  .feature-card:hover {
    transform: translateY(-5px); /* Animación suave al pasar el ratón */
  }
  
  .icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 30px;
    margin-bottom: 20px;
  }
  
  /* Clases de utilidad para colores */
  .green { background-color: #e8f5e9; color: #2e7d32; }
  .red { background-color: #fbe9e7; color: #c62828; }
  .grey { background-color: #eceff1; color: #455a64; }
  .brown { background-color: #efebe9; color: #5d4037; }
  
  .feature-card h3 {
    color: #1b4d3e;
    margin-bottom: 15px;
    font-size: 1.3rem;
  }
  
  .feature-card p {
    color: #555;
    font-size: 0.95rem;
    line-height: 1.6;
  }
  
  /* --- CTA FINAL --- */
  .cta-section {
    background-color: #1b4d3e;
    color: white;
    text-align: center;
    padding: 80px 20px;
  }
  
  .cta-section h2 {
    font-size: 2.5rem;
    margin-bottom: 15px;
  }
  
  .cta-section p {
    margin-bottom: 30px;
    font-size: 1.2rem;
    opacity: 0.9;
  }
  </style>