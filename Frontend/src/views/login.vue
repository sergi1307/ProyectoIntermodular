<template>
    <div id="app">
        <div id="container">
            <div id="izquierda">
                <div id="titulo">
                    <img src="../assets/img/Logo Empresa.png" alt="Logo de la empresa" width="90"></img>
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
                
            </div>
        </div>
    </div>

    <router-link to="/">Volver al inicio</router-link>
</template>

<style scoped>
    #app {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 80%;
        border-radius: 5px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
    }

    #izquierda {
        flex-direction: column;
        background-color: #1c5537;
        border-radius: 5px 0 0 5px;
    }

    #content {
        margin: 0 auto;
        width: 80%;
        color: white;
        text-align: justify;
    }

    #titulo {
        display: flex;
        flex-direction: row;
        justify-content: center;
        color: white;
    }

    .lista {
        display: grid;
        grid-template-columns: 30px 1fr;
    }
</style>