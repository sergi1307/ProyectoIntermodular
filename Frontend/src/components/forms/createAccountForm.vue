<script setup>
    // Importem les llibreríes necessàries
    import { ref } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    
    const router = useRouter();

    // Declarem les variables reactives que rebrem del formulari
    const name = ref('');
    const email = ref('');
    const password = ref('');
    const phone = ref('');

    // Definim un emit per a que quan apretem al botó ens canvie el component de CreateAccount a loginForm
    defineEmits(['cambiar-a-login']);

    // Funció per a enviar les dades al backend
    const enviarDatos = async () => {

        // Si algún valor d'estos es null que done una alerta per a que els plene tots
        if (!name.value || !email.value || !password.value || !phone.value) {
            alert("Por favor, rellena todos los campos.");
            return;
        }

        // Muntem el JSON per a enviar-lo al backend en la estructura correcta
        const datos = {
            name: name.value,
            email: email.value,
            phone: phone.value,
            registration_date: '2026-01-08',
            password: password.value
        };

        console.log("Enviando datos al backend:", datos);

        try {
            // Fem la petició axios al backend enviant les dades necessàries per a fer el registre
            const response = await axios.post('http://localhost:8080/api/auth/register', datos, {
                withCredentials: true
            });
            console.log("Respuesta del servidor:", response.data);

            if (response.data.status == 'true') {
                localStorage.setItem('token', response.data.token);

                if(response.data.user) {
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                    console.log("Usuari guardat en LocalStorage:", response.data.user);
                } else {
                    console.warn("El backend no ha tornat l'usuari");
                }

                router.push('/general');
            }
        } catch(error) {
            console.error("Error detallado:", error);

            if (error.response) {
                // El servidor respondió, pero con un código de error (4xx, 5xx)
                console.log("Datos del error:", error.response.data);
                alert(error.response.data.message || "Error en el servidor: " + error.response.status);
            } else if (error.request) {
                // La petición se hizo pero NO hubo respuesta (Servidor apagado o CORS)
                alert("No se recibió respuesta del servidor. Comprueba que el backend esté encendido (Puerto 8000).");
            } else {
                // Error al configurar la petición
                alert("Error al enviar la petición: " + error.message);
            }
        }
    }
</script>

<template>
    <div class="form-container">
        <h2>Crear una Nueva Cuenta</h2>
        <p>Únete a nuestro mercado sostenible</p>
        <!-- Cuan apretem al botó de submit, captura el procés HTML per a que faja la petició de Axios -->
        <form @submit.prevent="enviarDatos">
            <label for="name">Nombre Completo</label><br>
            <input type="text" placeholder="Paco" v-model="name"> <br><br>

            <label for="email">Correo Electrónico</label><br>
            <input type="email" placeholder="ejemplo@ejemplo.com" v-model="email"> <br><br>

            <label for="phone">Teléfono</label><br>
            <input type="text" placeholder="12345678" v-model="phone"> <br><br>

            <label for="password">Contraseña</label><br>
            <input type="password" placeholder="••••••••" v-model="password"> <br><br>

            <button type="submit">Crear Cuenta</button>

            <!-- Si apreten a este botó canvia el component de Register a Login -->
            <p>Ya tiene una cuenta? <a href="#" @click.prevent="$emit('cambiar-a-login')">Iniciar Sesión</a></p>
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
    input[type="text"],
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