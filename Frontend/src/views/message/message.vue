<script setup>
import { onMounted, onUnmounted, ref, nextTick } from 'vue';
import { useRoute } from 'vue-router'; 
import axios from 'axios';

const url = import.meta.env.VITE_API_URL_DEV;

// Almacena la lista de todas las conversaciones abiertas
const listaDeConversaciones = ref([]);

// Almacena los mensajes específicos del chat que se está viendo ahora mismo
const mensajesDelChatActual = ref([]);

// Guarda la información de la conversación seleccionada 
const conversacionActiva = ref(null);

// El texto que el usuario está escribiendo en el input
const textoNuevoMensaje = ref("");

// Identificador para el temporizador que actualiza los mensajes automáticamente
const idIntervaloActualizacion = ref(null);

const route = useRoute();


// Obtiene el ID numérico del usuario actual
const obtenerMiIdDeUsuario = () => {
    try {
        const userStr = localStorage.getItem('user');
        if (!userStr) return 0;
        const user = JSON.parse(userStr);
        // Devuelve el id_user asegurándose de que sea un número
        return Number(user.id_user || user.id);
    } catch (e) {
        return 0;
    }
};

// Determina quién envió un mensaje específico
const obtenerIdDelRemitente = (mensaje) => {
    if (mensaje.id_transmitter) return Number(mensaje.id_transmitter);
    if (mensaje.transmitter && mensaje.transmitter.id_user) return Number(mensaje.transmitter.id_user);
    return 0;
};

// Recupera el token de seguridad para las peticiones API
const obtenerTokenAuth = () => localStorage.getItem("token");

// Analiza un objeto de chat y devuelve los datos de la otra persona
// Esto sirve para mostrar el nombre correcto en la lista lateral
const obtenerDatosDelInterlocutor = (chat) => {
    const miId = obtenerMiIdDeUsuario();
    const idRemitente = obtenerIdDelRemitente(chat);
    
    // Extraemos posibles IDs y Nombres dependiendo de la estructura de la respuesta
    const idReceptor = chat.receiver ? chat.receiver.id_user : chat.id_receiver;
    const idTransmisor = chat.transmitter ? chat.transmitter.id_user : chat.id_transmitter;
    
    const nombreReceptor = chat.receiver ? chat.receiver.name : (chat.receiver_name || 'Usuario');
    const nombreTransmisor = chat.transmitter ? chat.transmitter.name : (chat.transmitter_name || 'Usuario');

    // Si yo fui el que envió el último mensaje, el interlocutor es el Receptor.
    // Si no, el interlocutor es el Transmisor.
    if (idRemitente === miId) {
        return { id: Number(idReceptor), name: nombreReceptor };
    } else {
        return { id: Number(idTransmisor), name: nombreTransmisor };
    }
};

// Fuerza el scroll de la ventana de chat hacia abajo para ver el último mensaje
const desplazarScrollAlFinal = () => {
    nextTick(() => {
        const elementoChat = document.querySelector('.msgs-body');
        if (elementoChat) elementoChat.scrollTop = elementoChat.scrollHeight;
    });
};

// Pide al servidor la lista general de chats (Bandeja de entrada)
const cargarBandejaDeEntrada = async () => {
    try {
        const respuesta = await axios.get(`${url}/api/messages/inbox`, {
            headers: { Authorization: `Bearer ${obtenerTokenAuth()}` }
        });
        
        if (respuesta.data.bandeja) {
            listaDeConversaciones.value = respuesta.data.bandeja;
        }
    } catch (error) {
        console.error("Error al cargar la bandeja:", error);
    }
};

// Pide al servidor todos los mensajes de una conversación específica
const cargarHistorialDeMensajes = async (idProducto, idOtroUsuario) => {
    try {
        const respuesta = await axios.get(`${url}/api/messages/conversation`, {
            headers: { Authorization: `Bearer ${obtenerTokenAuth()}` },
            params: {
                id_product: idProducto,
                id_user_chat: idOtroUsuario
            }
        });

        if (respuesta.data.messages) {
            mensajesDelChatActual.value = respuesta.data.messages;
            desplazarScrollAlFinal(); // Bajar el scroll al cargar
        }
    } catch (error) {
        console.error("Error al cargar historial:", error);
    }
};

// Se ejecuta cuando el usuario hace clic en un chat de la lista o viene redirigido de un producto
const abrirConversacion = async (itemChat, esChatNuevoTemporal = false) => {
    const miId = obtenerMiIdDeUsuario();
    let interlocutor;

    // comprobamos si el chat aún no existe en backend, venimos de ver un producto
    if (esChatNuevoTemporal) {
        interlocutor = { id: Number(itemChat.otherUserId), name: itemChat.otherUserName };
    } else {
        // Si es un chat existente en la bandeja
        interlocutor = obtenerDatosDelInterlocutor(itemChat);
    }

    // Evitar abrir chat con uno mismo
    if (interlocutor.id === miId) return; 

    // Establecemos este chat como el activo
    conversacionActiva.value = {
        productId: esChatNuevoTemporal ? itemChat.productId : itemChat.product.id_product,
        productName: esChatNuevoTemporal ? itemChat.productName : itemChat.product.name,
        otherUserId: interlocutor.id,
        otherUserName: interlocutor.name
    };

    // Limpiamos mensajes anteriores y cargamos los nuevos
    mensajesDelChatActual.value = [];
    await cargarHistorialDeMensajes(conversacionActiva.value.productId, conversacionActiva.value.otherUserId);
};

// Envía el mensaje al servidor y actualiza la vista
const enviarMensaje = async () => {
    // Validar que no esté vacío y que haya un chat seleccionado
    if (!textoNuevoMensaje.value.trim() || !conversacionActiva.value) return;

    const datosMensaje = {
        content: textoNuevoMensaje.value,
        id_product: conversacionActiva.value.productId,
        id_receiver: conversacionActiva.value.otherUserId
    };

    try {
        await axios.post(`${url}/api/messages`, datosMensaje, {
            headers: { Authorization: `Bearer ${obtenerTokenAuth()}` }
        });

        // Limpiar el input
        textoNuevoMensaje.value = "";
        
        // Recargar los mensajes para ver el nuestro y actualizar la bandeja lateral
        await cargarHistorialDeMensajes(conversacionActiva.value.productId, conversacionActiva.value.otherUserId);
        await cargarBandejaDeEntrada();

    } catch (error) {
        alert("Error al enviar mensaje");
    }
};

onMounted(async () => {
    //  Cargar la lista de chats al iniciar
    await cargarBandejaDeEntrada();

    // Comprobar si venimos redirigidos de un producto
    if (route.query.prodId) {
        const prodIdParam = Number(route.query.prodId);
        const userIdParam = Number(route.query.userId); 
        const miId = obtenerMiIdDeUsuario();

        if (userIdParam !== miId) {
            // Buscamos si ya existe una conversación con ese usuario para ese producto
            const chatExistente = listaDeConversaciones.value.find(c => {
                const infoInterlocutor = obtenerDatosDelInterlocutor(c);
                return c.product.id_product === prodIdParam && infoInterlocutor.id === userIdParam;
            });

            if (chatExistente) {
                // Si existe, lo abrimos normalmente
                abrirConversacion(chatExistente, false);
            } else {
                // Si no existe, creamos un objeto temporal ("fantasma") para mostrar la interfaz vacía
                const chatFantasma = {
                    productId: prodIdParam,
                    productName: route.query.prodName,
                    otherUserId: userIdParam,
                    otherUserName: route.query.userName
                };
                abrirConversacion(chatFantasma, true);
            }
        }
    }

    // Configurar actualización automática cada 3 segundos (Polling)
    idIntervaloActualizacion.value = setInterval(() => {
        cargarBandejaDeEntrada(); // Refrescar lista lateral
        if (conversacionActiva.value) {
            // Refrescar mensajes del chat abierto
            cargarHistorialDeMensajes(conversacionActiva.value.productId, conversacionActiva.value.otherUserId);
        }
    }, 3000);
});

// Limpiar el intervalo cuando se cierra el componente para evitar fugas de memoria
onUnmounted(() => {
    if (idIntervaloActualizacion.value) clearInterval(idIntervaloActualizacion.value);
});
</script>

<template>
    <div class="chat-container">
        
        <div class="sidebar">
            <div class="header"><h3>Mis Chats</h3></div>
            <ul class="chat-list">
                <li 
                    v-for="(chat, index) in listaDeConversaciones" 
                    :key="chat.id_message || index"
                    @click="abrirConversacion(chat, false)"
                    :class="{ 'active': conversacionActiva && conversacionActiva.productId === chat.product.id_product && conversacionActiva.otherUserId === obtenerDatosDelInterlocutor(chat).id }"
                >
                    <div class="info">
                        <span class="name">{{ obtenerDatosDelInterlocutor(chat).name }}</span>
                        <span class="prod">📦 {{ chat.product.name }}</span>
                    </div>
                </li>

                <li 
                    v-if="conversacionActiva && !listaDeConversaciones.find(c => c.product.id_product === conversacionActiva.productId && obtenerDatosDelInterlocutor(c).id === conversacionActiva.otherUserId)"
                    class="active"
                >
                    <div class="info">
                        <span class="name">{{ conversacionActiva.otherUserName }} (Nuevo)</span>
                        <span class="prod">📦 {{ conversacionActiva.productName }}</span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="main">
            <div v-if="conversacionActiva" class="window">
                <div class="chat-header">
                    <h3>{{ conversacionActiva.otherUserName }}</h3>
                    <span>{{ conversacionActiva.productName }}</span>
                </div>

                <div class="msgs-body">
                    <div v-if="mensajesDelChatActual.length === 0" class="empty">
                        Escribe el primer mensaje...
                    </div>
                    
                    <div 
                        v-for="(msg, index) in mensajesDelChatActual" 
                        :key="msg.id_message || index"
                        class="row"
                        :class="obtenerIdDelRemitente(msg) === obtenerMiIdDeUsuario() ? 'mine' : 'theirs'"
                    >
                        <div class="bubble">
                            <p>{{ msg.content }}</p>
                            <small v-if="msg.shipping_date">{{ new Date(msg.shipping_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</small>
                        </div>
                    </div>
                </div>

                <div class="input-area">
                    <input v-model="textoNuevoMensaje" @keyup.enter="enviarMensaje" placeholder="Escribe..." />
                    <button @click="enviarMensaje">Enviar</button>
                </div>
            </div>

            <div v-else class="no-select">
                <p>Selecciona un chat</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.chat-container { display: flex; height: 90vh; font-family: sans-serif; background: #f0f2f5; }
.sidebar { width: 300px; background: white; border-right: 1px solid #ddd; display: flex; flex-direction: column; }
.header { padding: 15px; background: #eee; border-bottom: 1px solid #ddd; }
.chat-list { list-style: none; padding: 0; margin: 0; overflow-y: auto; flex: 1; }
.chat-list li { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
.chat-list li:hover { background: #f9f9f9; }
.chat-list li.active { background: #e6f7ff; border-left: 4px solid #1890ff; }
.info { display: flex; flex-direction: column; }
.name { font-weight: bold; }
.prod { font-size: 0.85em; color: #666; }

.main { flex: 1; display: flex; flex-direction: column; }
.window { display: flex; flex-direction: column; height: 100%; }
.chat-header { padding: 15px; background: white; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
.msgs-body { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
.row { display: flex; }
.mine { justify-content: flex-end; }
.theirs { justify-content: flex-start; }
.bubble { padding: 10px 15px; border-radius: 10px; max-width: 70%; box-shadow: 0 1px 2px rgba(0,0,0,0.1); word-wrap: break-word; }
.mine .bubble { background: #dcf8c6; }
.theirs .bubble { background: white; }
.bubble p { margin: 0; }
.bubble small { font-size: 0.7em; color: #666; display: block; text-align: right; margin-top: 5px; }

.input-area { padding: 15px; background: #f0f0f0; display: flex; gap: 10px; }
.input-area input { flex: 1; padding: 10px; border-radius: 20px; border: 1px solid #ccc; outline: none; }
.input-area button { padding: 10px 20px; border-radius: 20px; border: none; background: #008069; color: white; cursor: pointer; }
.no-select { display: flex; align-items: center; justify-content: center; height: 100%; color: #888; }
.empty { text-align: center; color: #888; margin-top: 20px; }
</style>