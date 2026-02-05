<script setup>
import { onMounted, onUnmounted, ref, nextTick } from 'vue';
import { useRoute } from 'vue-router'; 
import axios from 'axios';

const bandeja = ref([]);
const mensajesChat = ref([]);
const chatActivo = ref(null);
const nuevoMensaje = ref("");
const route = useRoute();
const intervalId = ref(null);

const getMyId = () => {
    try {
        const userStr = localStorage.getItem('user');
        if (!userStr) return 0;
        const user = JSON.parse(userStr);
        return Number(user.id_user || user.id);
    } catch (e) {
        return 0;
    }
};

const getSenderId = (msg) => {
    if (msg.id_transmitter) return Number(msg.id_transmitter);
    if (msg.transmitter && msg.transmitter.id_user) return Number(msg.transmitter.id_user);
    return 0;
};

const getToken = () => localStorage.getItem("token");

const getPartnerInfo = (chat) => {
    const myId = getMyId();
    const senderId = getSenderId(chat);
    
    const receiverId = chat.receiver ? chat.receiver.id_user : chat.id_receiver;
    const transmitterId = chat.transmitter ? chat.transmitter.id_user : chat.id_transmitter;
    
    const receiverName = chat.receiver ? chat.receiver.name : (chat.receiver_name || 'Usuario');
    const transmitterName = chat.transmitter ? chat.transmitter.name : (chat.transmitter_name || 'Usuario');

    if (senderId === myId) {
        return { id: Number(receiverId), name: receiverName };
    } else {
        return { id: Number(transmitterId), name: transmitterName };
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        const el = document.querySelector('.msgs-body');
        if (el) el.scrollTop = el.scrollHeight;
    });
};

const fetchBandeja = async () => {
    try {
        const res = await axios.get("http://localhost:8080/api/messages/inbox", {
            headers: { Authorization: `Bearer ${getToken()}` }
        });
        
        if (res.data.bandeja) {
            bandeja.value = res.data.bandeja;
        }
    } catch (e) {
        console.error(e);
    }
};

const fetchConversacion = async (prodId, otherUserId) => {
    try {
        const res = await axios.get("http://localhost:8080/api/messages/conversation", {
            headers: { Authorization: `Bearer ${getToken()}` },
            params: {
                id_product: prodId,
                id_user_chat: otherUserId
            }
        });

        if (res.data.messages) {
            mensajesChat.value = res.data.messages;
            scrollToBottom();
        }
    } catch (e) {
        console.error(e);
    }
};

const seleccionarChat = async (item, esFantasma = false) => {
    const myId = getMyId();
    let partner;

    if (esFantasma) {
        partner = { id: Number(item.otherUserId), name: item.otherUserName };
    } else {
        partner = getPartnerInfo(item);
    }

    if (partner.id === myId) return; 

    chatActivo.value = {
        productId: esFantasma ? item.productId : item.product.id_product,
        productName: esFantasma ? item.productName : item.product.name,
        otherUserId: partner.id,
        otherUserName: partner.name
    };

    mensajesChat.value = [];
    await fetchConversacion(chatActivo.value.productId, chatActivo.value.otherUserId);
};

const enviar = async () => {
    if (!nuevoMensaje.value.trim() || !chatActivo.value) return;

    const payload = {
        content: nuevoMensaje.value,
        id_product: chatActivo.value.productId,
        id_receiver: chatActivo.value.otherUserId
    };

    try {
        await axios.post("http://localhost:8080/api/messages", payload, {
            headers: { Authorization: `Bearer ${getToken()}` }
        });

        nuevoMensaje.value = "";
        
        await fetchConversacion(chatActivo.value.productId, chatActivo.value.otherUserId);
        await fetchBandeja();

    } catch (e) {
        alert("Error al enviar mensaje");
    }
};

onMounted(async () => {
    await fetchBandeja();

    if (route.query.prodId) {
        const pId = Number(route.query.prodId);
        const uId = Number(route.query.userId); 
        const myId = getMyId();

        if (uId !== myId) {
            const existe = bandeja.value.find(c => {
                const partner = getPartnerInfo(c);
                return c.product.id_product === pId && partner.id === uId;
            });

            if (existe) {
                seleccionarChat(existe, false);
            } else {
                const fantasma = {
                    productId: pId,
                    productName: route.query.prodName,
                    otherUserId: uId,
                    otherUserName: route.query.userName
                };
                seleccionarChat(fantasma, true);
            }
        }
    }

    intervalId.value = setInterval(() => {
        fetchBandeja();
        if (chatActivo.value) {
            fetchConversacion(chatActivo.value.productId, chatActivo.value.otherUserId);
        }
    }, 3000);
});

onUnmounted(() => {
    if (intervalId.value) clearInterval(intervalId.value);
});
</script>

<template>
    <div class="chat-container">
        
        <div class="sidebar">
            <div class="header"><h3>Mis Chats</h3></div>
            <ul class="chat-list">
                <li 
                    v-for="(chat, index) in bandeja" 
                    :key="chat.id_message || index"
                    @click="seleccionarChat(chat, false)"
                    :class="{ 'active': chatActivo && chatActivo.productId === chat.product.id_product && chatActivo.otherUserId === getPartnerInfo(chat).id }"
                >
                    <div class="info">
                        <span class="name">{{ getPartnerInfo(chat).name }}</span>
                        <span class="prod">📦 {{ chat.product.name }}</span>
                    </div>
                </li>

                <li 
                    v-if="chatActivo && !bandeja.find(c => c.product.id_product === chatActivo.productId && getPartnerInfo(c).id === chatActivo.otherUserId)"
                    class="active"
                >
                    <div class="info">
                        <span class="name">{{ chatActivo.otherUserName }} (Nuevo)</span>
                        <span class="prod">📦 {{ chatActivo.productName }}</span>
                    </div>
                </li>
            </ul>
        </div>

        <div class="main">
            <div v-if="chatActivo" class="window">
                <div class="chat-header">
                    <h3>{{ chatActivo.otherUserName }}</h3>
                    <span>{{ chatActivo.productName }}</span>
                </div>

                <div class="msgs-body">
                    <div v-if="mensajesChat.length === 0" class="empty">
                        Escribe el primer mensaje...
                    </div>
                    
                    <div 
                        v-for="(msg, index) in mensajesChat" 
                        :key="msg.id_message || index"
                        class="row"
                        :class="getSenderId(msg) === getMyId() ? 'mine' : 'theirs'"
                    >
                        <div class="bubble">
                            <p>{{ msg.content }}</p>
                            <small v-if="msg.shipping_date">{{ new Date(msg.shipping_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</small>
                        </div>
                    </div>
                </div>

                <div class="input-area">
                    <input v-model="nuevoMensaje" @keyup.enter="enviar" placeholder="Escribe..." />
                    <button @click="enviar">Enviar</button>
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