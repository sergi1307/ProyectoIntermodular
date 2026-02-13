<script setup>
import { onMounted, onUnmounted, ref, nextTick } from 'vue';
import { useRoute } from 'vue-router'; 
import axios from 'axios';
import { useNotificaciones } from '@/utilidades/useNotificaciones';

const notificacion = useNotificaciones();

import url from '../../config/api';
console.log(url);

const listaDeConversaciones = ref([]);
const mensajesDelChatActual = ref([]);
const conversacionActiva = ref(null);
const textoNuevoMensaje = ref("");
const idIntervaloActualizacion = ref(null);

const route = useRoute();

const obtenerMiIdDeUsuario = () => {
    try {
        const userStr = localStorage.getItem('user');
        if (!userStr) return 0;
        const user = JSON.parse(userStr);
        return Number(user.id_user || user.id);
    } catch (e) {
        return 0;
    }
};

const obtenerIdDelRemitente = (mensaje) => {
    if (mensaje.id_transmitter) return Number(mensaje.id_transmitter);
    if (mensaje.transmitter && mensaje.transmitter.id_user) return Number(mensaje.transmitter.id_user);
    return 0;
};

const obtenerTokenAuth = () => localStorage.getItem("token");

const obtenerDatosDelInterlocutor = (chat) => {
    const miId = obtenerMiIdDeUsuario();
    const idRemitente = obtenerIdDelRemitente(chat);
    const idReceptor = chat.receiver ? chat.receiver.id_user : chat.id_receiver;
    const idTransmisor = chat.transmitter ? chat.transmitter.id_user : chat.id_transmitter;
    
    const nombreReceptor = chat.receiver ? chat.receiver.name : (chat.receiver_name || 'Usuario');
    const nombreTransmisor = chat.transmitter ? chat.transmitter.name : (chat.transmitter_name || 'Usuario');

    if (idRemitente === miId) {
        return { id: Number(idReceptor), name: nombreReceptor };
    } else {
        return { id: Number(idTransmisor), name: nombreTransmisor };
    }
};

const desplazarScrollAlFinal = () => {
    nextTick(() => {
        const elementoChat = document.querySelector('.msgs-body');
        if (elementoChat) elementoChat.scrollTop = elementoChat.scrollHeight;
    });
};

const cerrarChat = () => {
    conversacionActiva.value = null;
    mensajesDelChatActual.value = [];
};

// --- FUNCIÓN CORREGIDA ---
// Si hay hora real, la muestra. Si es medianoche (00:00/01:00), muestra la fecha.
const formatearFechaInteligente = (fecha) => {
    if (!fecha) return '';
    const date = new Date(fecha);
    
    if (isNaN(date.getTime())) return '';

    // Obtenemos las horas y minutos
    const horas = date.getHours();
    const minutos = date.getMinutes();

    // Si es exactamente medianoche (00:00) o la 01:00 (por el cambio horario en España con fechas DATE),
    // asumimos que es un dato antiguo sin hora. Mostramos la FECHA.
    if ((horas === 0 || horas === 1) && minutos === 0) {
        return date.toLocaleDateString(); // Devuelve "10/11/2023"
    }

    // Si tiene una hora distinta, mostramos la HORA.
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const cargarBandejaDeEntrada = async () => {
    try {
        const respuesta = await axios.get(`${url}/api/messages/inbox`, {
            headers: { Authorization: `Bearer ${obtenerTokenAuth()}` }
        });
        if (respuesta.data.bandeja) {
            listaDeConversaciones.value = respuesta.data.bandeja;
        }
    } catch (error) {
        console.error("Error bandeja:", error);
    }
};

const cargarHistorialDeMensajes = async (idProducto, idOtroUsuario) => {
    try {
        const respuesta = await axios.get(`${url}/api/messages/conversation`, {
            headers: { Authorization: `Bearer ${obtenerTokenAuth()}` },
            params: { id_product: idProducto, id_user_chat: idOtroUsuario }
        });

        if (respuesta.data.messages) {
            mensajesDelChatActual.value = respuesta.data.messages;
            desplazarScrollAlFinal();
        }
    } catch (error) {
        console.error("Error historial:", error);
    }
};

const abrirConversacion = async (itemChat, esChatNuevoTemporal = false) => {
    const miId = obtenerMiIdDeUsuario();
    let interlocutor;

    if (esChatNuevoTemporal) {
        interlocutor = { id: Number(itemChat.otherUserId), name: itemChat.otherUserName };
    } else {
        interlocutor = obtenerDatosDelInterlocutor(itemChat);
    }

    if (interlocutor.id === miId) return; 

    conversacionActiva.value = {
        productId: esChatNuevoTemporal ? itemChat.productId : itemChat.product.id_product,
        productName: esChatNuevoTemporal ? itemChat.productName : itemChat.product.name,
        otherUserId: interlocutor.id,
        otherUserName: interlocutor.name
    };

    mensajesDelChatActual.value = [];
    await cargarHistorialDeMensajes(conversacionActiva.value.productId, conversacionActiva.value.otherUserId);
};

const enviarMensaje = async () => {
    if (!textoNuevoMensaje.value.trim() || !conversacionActiva.value) return;

    const datosMensaje = {
        content: textoNuevoMensaje.value,
        id_product: conversacionActiva.value.productId,
        id_receiver: conversacionActiva.value.otherUserId
    };

    try {
        // Envio optimista: Usamos la fecha y hora ACTUAL completa
        const mensajeTemporal = {
            id_message: Date.now(),
            content: textoNuevoMensaje.value,
            id_transmitter: obtenerMiIdDeUsuario(),
            shipping_date: new Date().toISOString() // Hora completa para que salga bien ahora
        };
        mensajesDelChatActual.value.push(mensajeTemporal);
        desplazarScrollAlFinal();

        textoNuevoMensaje.value = ""; 

        await axios.post(`${url}/api/messages`, datosMensaje, {
            headers: { Authorization: `Bearer ${obtenerTokenAuth()}` }
        });
        
        await cargarBandejaDeEntrada();
        // NOTA: No recargamos el historial aquí inmediatamente para que no se borre la hora visualmente

    } catch (error) {
        notificacion.error("Error al enviar mensaje");
    }
};

onMounted(async () => {
    await cargarBandejaDeEntrada();

    if (route.query.prodId) {
        const prodIdParam = Number(route.query.prodId);
        const userIdParam = Number(route.query.userId); 
        const miId = obtenerMiIdDeUsuario();

        if (userIdParam !== miId) {
            const chatExistente = listaDeConversaciones.value.find(c => {
                const infoInterlocutor = obtenerDatosDelInterlocutor(c);
                return c.product.id_product === prodIdParam && infoInterlocutor.id === userIdParam;
            });

            if (chatExistente) {
                abrirConversacion(chatExistente, false);
            } else {
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

    idIntervaloActualizacion.value = setInterval(() => {
        cargarBandejaDeEntrada(); 
        if (conversacionActiva.value) {
            // Solo recargamos si no hay mensajes pendientes recientes para evitar parpadeos
            cargarHistorialDeMensajes(conversacionActiva.value.productId, conversacionActiva.value.otherUserId);
        }
    }, 5000);
});

onUnmounted(() => {
    if (idIntervaloActualizacion.value) clearInterval(idIntervaloActualizacion.value);
});
</script>

<template>
    <div class="chat-container" :class="{ 'chat-activo': conversacionActiva }">
        
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
                    <div class="flecha-der">›</div>
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
                    <button class="btn-volver" @click="cerrarChat">←</button>
                    <div class="info-header">
                        <h3>{{ conversacionActiva.otherUserName }}</h3>
                        <span>{{ conversacionActiva.productName }}</span>
                    </div>
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
                            <small class="msg-time">
                                {{ formatearFechaInteligente(msg.shipping_date || msg.created_at) }}
                            </small>
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
.chat-container {
    display: flex;
    /* Esto hace que el chat ocupe exactamente lo que sobra de la pantalla */
    height: calc(100vh - 80px); 
    width: 100%;
    font-family: 'Inter', sans-serif;
    background: #f0f2f5;
    position: relative;
    overflow: hidden;
    /* Eliminamos cualquier margen que pueda estar empujando */
    margin: 0; 
}

/* En el media query de móvil, ajusta si el Nav mide diferente (ej: 60px) */
@media (max-width: 768px) {
    .chat-container {
        height: calc(100vh - 60px); 
    }
}
.sidebar {
    width: 350px;
    background: white;
    border-right: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    z-index: 2;
    transition: transform 0.3s ease;
}

.header {
    padding: 15px;
    background: #f0f2f5;
    border-bottom: 1px solid #ddd;
}
.header h3 { margin: 0; color: #1c5537; }

.chat-list {
    list-style: none;
    padding: 0;
    margin: 0;
    overflow-y: auto;
    flex: 1;
}

.chat-list li {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s;
}

.chat-list li:hover { background: #f9f9f9; }
.chat-list li.active { background: #e8f5e9; border-left: 4px solid #1c5537; }

.info { display: flex; flex-direction: column; }
.name { font-weight: 600; color: #333; }
.prod { font-size: 0.85em; color: #666; margin-top: 2px; }
.flecha-der { color: #ccc; font-size: 1.2rem; }

.main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background-color: #e5ddd5;
    position: relative;
}

.window { display: flex; flex-direction: column; height: 100%; }

.chat-header {
    padding: 10px 15px;
    background: white;
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: center;
    gap: 15px;
    height: 60px;
    flex-shrink: 0;
}

.btn-volver {
    display: none;
    background: none;
    border: none;
    font-size: 1.8rem;
    color: #1c5537;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.info-header h3 { margin: 0; font-size: 1rem; color: #111; }
.info-header span { font-size: 0.8rem; color: #666; }

.msgs-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background-image: radial-gradient(#ddd 1px, transparent 1px);
    background-size: 20px 20px;
}

.row { display: flex; width: 100%; }
.mine { justify-content: flex-end; }
.theirs { justify-content: flex-start; }

.bubble {
    padding: 8px 12px;
    border-radius: 8px;
    max-width: 75%;
    box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    word-wrap: break-word;
    position: relative;
    font-size: 0.95rem;
    display: flex;
    flex-direction: column;
}

.mine .bubble { background: #dcf8c6; border-top-right-radius: 0; }
.theirs .bubble { background: white; border-top-left-radius: 0; }

.bubble p { margin: 0; line-height: 1.4; }

.msg-time {
    font-size: 0.7em;
    color: #999;
    text-align: right;
    margin-top: 4px;
    align-self: flex-end;
    line-height: 1;
    min-height: 1em;
}

.input-area {
    padding: 10px;
    background: #f0f0f0;
    display: flex;
    gap: 10px;
    align-items: center;
    border-top: 1px solid #ddd;
}

.input-area input {
    flex: 1;
    padding: 12px 15px;
    border-radius: 24px;
    border: 1px solid #ccc;
    outline: none;
}

.input-area button {
    padding: 10px 20px;
    border-radius: 24px;
    border: none;
    background: #1c5537;
    color: white;
    cursor: pointer;
    font-weight: 600;
}

.no-select {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #888;
    background-color: #f8f9fa;
    text-align: center;
}

@media (max-width: 768px) {
    .chat-container {
        height: 85vh;
        display: block;
    }

    .sidebar {
        width: 100%;
        height: 100%;
        display: flex;
    }

    .main {
        display: none !important;
    }

    .no-select {
        display: none;
    }

    .chat-container.chat-activo .sidebar {
        display: none !important;
    }

    .chat-container.chat-activo .main {
        display: flex !important;
        width: 100%;
        height: 100%;
    }

    .btn-volver {
        display: block;
    }

    .bubble {
        max-width: 85%;
    }
}
</style>