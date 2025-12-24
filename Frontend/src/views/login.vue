<script setup>
    import login from '../components/loginForm.vue';
</script>

<template>
    <div id="iniciarSesion">
        <div id="container">
            <div id="izquierda">
                <div id="titulo">
                    <img src="../assets/imgs/logoEmpresa.png" alt="Logo de la empresa" width="90"></img>
                    <h1>ProxiMarkt</h1>
                </div>
                <div id="content">
                    <h2>Únete a tu comunidad de mercado de proximidad</h2>
                    <p>Si eres granjero y quieres compartir tus cultivos o eres un
                        consumidor que buscas productos frescos locales, ProxiMarkt
                    </p>
                    <div class="lista">
                        <p>✅</p>
                        <p>100% Local<br>Ayuda en la economía de tu vecino</p>

                        <p>✅</p>
                        <p>Fresco y Sostenible<br>De la granja a la mesa en horas</p>

                        <p>✅</p>
                        <p>Conexión directa<br>Chatea directamente con los productores</p>
                    </div>
                </div>
            </div>
            <div id="derecha">
                <login />
                <router-link to="/">Volver al inicio</router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
    #iniciarSesion {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; /* Ocupar toda la pantalla */
        background-color: #f5f5f5; /* Fondo gris suave para la página */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 900px; /* Ancho fijo o máx-width para que no se estire demasiado */
        max-width: 90%;
        background-color: white;
        border-radius: 15px; /* Bordes más redondeados */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden; /* Para que la imagen verde respete el borde redondeado */
        min-height: 600px;
    }

    /* --- LADO IZQUIERDO (VERDE) --- */
    #izquierda {
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: #1c5537;
        color: white;
        padding: 40px;
    }

    #titulo {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    #titulo h1 {
        font-size: 1.8rem;
        margin-left: 10px;
        font-weight: bold;
    }

    #content {
        width: 100%;
    }

    #content h2 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        line-height: 1.3;
    }

    #content > p {
        opacity: 0.9;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .lista {
        display: grid;
        grid-template-columns: 30px 1fr;
        row-gap: 20px;
        align-items: start;
    }

    .lista p:nth-child(odd) {
        font-size: 1.2rem;
    }

    .lista p:nth-child(even) {
        margin: 0;
        font-size: 0.9rem;
    }

    #derecha {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 60px;
        position: relative;
    }

    #derecha a {
        margin-top: 20px;
        text-align: center;
        color: #888;
        text-decoration: none;
        font-size: 0.9rem;
    }
    
    #derecha a:hover {
        text-decoration: underline;
    }
</style>