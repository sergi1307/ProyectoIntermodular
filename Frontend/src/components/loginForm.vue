<script setup>
    // Importem les llibreríes necessàries
    import { ref } from 'vue';
    import axios from 'axios';
    
    // Declarem les variables reactives que rebrem del formulari
    const email = ref('');
    const password = ref('');
    const recordarme = ref(false);

    // Definim un emit per a que quan apretem al botó ens canvie el component de loginForm a createAccount
    defineEmits(['cambiar-a-registro']);

    // Funció per a enviar les dades al backend
    const enviarDatos = () => {

        // Si algún valor d'estos es null que done una alerta per a que els plene tots
        if (!email.value || !password.value) {
            alert("Por favor, rellena todos los campos.");
            return;
        }

        // Muntem el JSON per a enviar-lo al backend en la estructura correcta
        const datos = {
            email: email.value,
            password: password.value
        };

        console.log("Enviando datos al backend:", datos);

        // Fem la petició axios al backend enviant les dades necessàries per a fer el login
        axios.post('http://localhost:8080/api/login', datos);
    }
</script>

<template>
    <div class="form-container">
        <h2>Bienvenido de Nuevo</h2>
        <p>Inicia sesión para entrar a Proximarkt</p>
        <!-- Cuan apretem al botó de submit, captura el procés HTML per a que faja la petició de Axios -->
        <form @submit.prevent="enviarDatos">
            <label for="email">Correo Electrónico</label><br>
            <input type="email" placeholder="ejemplo@ejemplo.com" v-model="email"> <br><br>

            <label for="password">Contraseña</label><br>
            <input type="password" placeholder="••••••••" v-model="password"> <br><br>

            <div id="remember_forgot">
                <div>
                    <input type="checkbox" v-model="recordarme">Remember me
                </div>
                <button>Ha olvidado la contraseña?</button>
            </div> <br>

            <button type="submit">Iniciar Sesión</button>

            <!-- Si apreten a este botó canvia el component de Login a Register -->
            <p>No tienes una cuenta aún? <a href="#" @click.prevent="$emit('cambiar-a-registro')">Crear Cuenta</a></p>
        </form>
    </div>
</template>

<style scoped>
    .form-container {
        width: 100%;
        color: #333;
    }

    h2 {
        color: #1c5537;
        font-weight: bold;
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    p {
        color: #666;
        margin-bottom: 30px;
        font-size: 0.95rem;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #555;
        display: inline-block;
    }

    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }

    input:focus {
        outline: none;
        border-color: #1c5537;
    }

    #remember_forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #555;
    }
    
    #remember_forgot input[type="checkbox"] {
        accent-color: #1c5537;
        margin-right: 5px;
    }

    #remember_forgot button {
        background: none;
        border: none;
        color: #1c5537;
        cursor: pointer;
        font-weight: 600;
        padding: 0;
        font-weight: 0.8em;
    }

    button[type="submit"] {
        background-color: #1c5537;
        color: white;
        padding: 18px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s;
        margin-bottom: 10px;
    }

    button[type="submit"]:hover {
        background-color: #14402a;
    }

    .form-container > form > p:last-child {
        text-align: center;
        margin-bottom: 0;
    }

    a {
        color: #1c5537;
        font-weight: bold;
        text-decoration: none;
    }
</style>