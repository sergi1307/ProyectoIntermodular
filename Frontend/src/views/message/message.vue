<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const miIdUsuario = 1; 

const message = ref([]);
const intervalId = ref(null);
const chatSeleccionado = ref(null);
const nuevoMensaje = ref("");
const route = useRoute(); 
const obtenerMensaje = async () => {
    try {
        const response = await axios.get("http://localhost:8080/api/messages/inbox", {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        message.value = response.data;
    } catch (error) {
        console.error("Error cargando mensajes:", error);
    }
};

const enviarMensaje = async () => {
    if (!nuevoMensaje.value) return;

    const datosParaEnviar = {
        content: nuevoMensaje.value,
        productId: chatSeleccionado.value.product.id_product,
        receiverId: chatSeleccionado.value.transmitter.id_user
    };

    try {
        await axios.post("http://localhost:8080/api/messages", datosParaEnviar, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        nuevoMensaje.value = "";
        await obtenerMensaje(); 

    } catch (error) {
        console.error("Error enviando:", error);
        alert("Error al enviar. Revisa la consola.");
    }
};

const mensajesContacto = computed(() => {
    if (!chatSeleccionado.value) return [];
    
    if (!chatSeleccionado.value.id_message && !message.value.length) return [];

    return message.value.filter(msg => {
        return msg.product.id_product == chatSeleccionado.value.product.id_product && 
               (msg.transmitter.id_user == chatSeleccionado.value.transmitter.id_user || 
                msg.transmitter.id_user == miIdUsuario); 
    });
});

const chats = computed(() => {
    const contactos = {};
    if (!message.value) return [];
    
    for (const msg of message.value){
        let otroUsuarioId = msg.transmitter.id_user === miIdUsuario ? msg.transmitter.id_user : msg.transmitter.id_user;

        const clave = `chat-${msg.product.id_product}-${msg.transmitter.id_user}`;
        contactos[clave] = msg;
    }
    return Object.values(contactos);
});

onMounted(async () => {
    await obtenerMensaje();

    if (route.query.prodId) {
        const pId = Number(route.query.prodId);
        const uId = Number(route.query.userId);

        const chatExistente = message.value.find(m => 
            m.product.id_product === pId && m.transmitter.id_user === uId
        );

        if (chatExistente) {
            chatSeleccionado.value = chatExistente;
        } else {
            chatSeleccionado.value = {
                id_message: null, 
                product: { id_product: pId, name: route.query.prodName },
                transmitter: { id_user: uId, name: route.query.userName }
            };
        }
    }
    intervalId.value = setInterval(obtenerMensaje, 3000);
});

onUnmounted(() => {
    if (intervalId.value) clearInterval(intervalId.value);
});
</script>

<template>
    <div style="display: flex; gap: 0; height: 90vh; font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5;">
        
        <div style="width: 30%; border-right: 1px solid #ddd; background: white; display: flex; flex-direction: column;">
            <div style="padding: 20px; background: #eee; border-bottom: 1px solid #ddd;">
                <h2 style="margin: 0; color: #333;">💬 Mensajes</h2>
            </div>
            
            <ul style="list-style: none; padding: 0; margin: 0; overflow-y: auto;">
                <li 
                    v-for="chat in chats" 
                    :key="chat.id_message" 
                    @click="chatSeleccionado = chat"
                    style="cursor: pointer; padding: 15px; border-bottom: 1px solid #f1f1f1; transition: 0.2s;"
                    onmouseover="this.style.background='#f9f9f9'"
                    onmouseout="this.style.background='white'"
                >
                    <div style="font-weight: bold; font-size: 1.1em; color: #111;">{{chat.transmitter.name}}</div>
                    <div style="color: #666; font-size: 0.9em; margin-top: 4px;">📦 {{chat.product.name}}</div>
                </li>
            </ul>
        </div>

        <div style="width: 70%; display: flex; flex-direction: column; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-repeat: repeat;">
            
            <div v-if="chatSeleccionado" style="display: flex; flex-direction: column; height: 100%;">
                
                <div style="padding: 10px 20px; background: #f0f2f5; border-bottom: 1px solid #ccc; display: flex; align-items: center;">
                    <div>
                        <h3 style="margin: 0; color: #333;">{{ chatSeleccionado.transmitter.name }}</h3>
                        <span style="font-size: 0.85em; color: #666;">Interesado en: <strong>{{ chatSeleccionado.product.name }}</strong></span>
                    </div>
                </div>

                <div style="flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column;">
                    <div v-if="mensajesContacto.length === 0" style="text-align: center; color: #555; background: rgba(255,255,255,0.8); padding: 10px; border-radius: 10px; align-self: center; margin-top: 20px;">
                        Has iniciado una nueva conversación.<br>¡Saluda! 👋
                    </div>

                    <div 
                        v-for="msg in mensajesContacto" 
                        :key="msg.id_message"
                        :style="{
                            alignSelf: msg.transmitter.id_user === miIdUsuario ? 'flex-end' : 'flex-start',
                            backgroundColor: msg.transmitter.id_user === miIdUsuario ? '#dcf8c6' : 'white',
                            padding: '10px 15px',
                            margin: '5px',
                            borderRadius: '8px',
                            maxWidth: '65%',
                            boxShadow: '0 1px 1px rgba(0,0,0,0.1)',
                            position: 'relative'
                        }"
                    >
                        <div style="word-wrap: break-word;">{{ msg.content }}</div>
                    </div>
                </div>

                <div style="padding: 15px; background: #f0f2f5; display: flex; gap: 10px; align-items: center;">
                    <input 
                        v-model="nuevoMensaje" 
                        type="text" 
                        placeholder="Escribe un mensaje aquí..."
                        style="flex-grow: 1; padding: 12px; border-radius: 20px; border: 1px solid #ccc; outline: none;"
                        @keyup.enter="enviarMensaje"
                    >
                    <button 
                        @click="enviarMensaje"
                        style="padding: 10px 20px; background: #008069; color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; font-size: 1.1em;"
                    >
                        ➤
                    </button>
                </div>
            </div>

            <div v-else style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #555; background: #f0f2f5;">
                <div style="font-size: 4em;">💬</div>
                <h2>Tus Mensajes</h2>
                <p>Selecciona una conversación para leerla.</p>
            </div>

        </div>
    </div>
</template>