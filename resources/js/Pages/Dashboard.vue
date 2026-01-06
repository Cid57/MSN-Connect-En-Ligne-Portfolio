<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const user = computed(() => page.props.auth.user);

const contacts = ref([]);
const channels = ref([]);
const messages = ref([]);
const selectedChannel = ref(null);
const newMessage = ref('');
const loading = ref(false);
const statuses = ref([]);
const onlineUsers = ref([]);

// Charger les données initiales
onMounted(async () => {
    await Promise.all([
        loadStatuses(),
        loadChannels(),
        loadOnlineUsers()
    ]);

    // Heartbeat toutes les 2 minutes
    setInterval(() => {
        sendHeartbeat();
        loadOnlineUsers();
    }, 120000);
});

// Charger les statuts disponibles
const loadStatuses = async () => {
    try {
        const response = await axios.get('/api/statuses');
        statuses.value = response.data;
    } catch (error) {
        console.error('Erreur chargement statuts:', error);
    }
};

// Charger les utilisateurs en ligne
const loadOnlineUsers = async () => {
    try {
        const response = await axios.get('/api/statuses/online-users');
        onlineUsers.value = response.data;
    } catch (error) {
        console.error('Erreur chargement utilisateurs:', error);
    }
};

// Charger les conversations
const loadChannels = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/channels');
        channels.value = response.data;
    } catch (error) {
        console.error('Erreur chargement channels:', error);
    } finally {
        loading.value = false;
    }
};

// Sélectionner une conversation
const selectChannel = async (channel) => {
    selectedChannel.value = channel;
    await loadMessages(channel.id);
};

// Charger les messages d'une conversation
const loadMessages = async (channelId) => {
    try {
        const response = await axios.get(`/api/channels/${channelId}/messages`);
        messages.value = response.data.data.reverse();

        // Marquer comme lu
        await axios.post(`/api/channels/${channelId}/messages/mark-as-read`);
    } catch (error) {
        console.error('Erreur chargement messages:', error);
    }
};

// Envoyer un message
const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedChannel.value) return;

    try {
        const response = await axios.post(
            `/api/channels/${selectedChannel.value.id}/messages`,
            { content: newMessage.value }
        );

        messages.value.push(response.data);
        newMessage.value = '';

        // Scroll vers le bas
        setTimeout(() => {
            const messagesContainer = document.getElementById('messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }, 100);
    } catch (error) {
        console.error('Erreur envoi message:', error);
    }
};

// Créer une nouvelle conversation
const startConversation = async (userId) => {
    try {
        const response = await axios.post('/api/channels', {
            is_group: false,
            member_ids: [userId]
        });

        channels.value.push(response.data);
        selectChannel(response.data);
    } catch (error) {
        console.error('Erreur création conversation:', error);
    }
};

// Envoyer un heartbeat
const sendHeartbeat = async () => {
    try {
        await axios.post('/api/statuses/heartbeat');
    } catch (error) {
        console.error('Erreur heartbeat:', error);
    }
};

// Obtenir la couleur du statut
const getStatusColor = (status) => {
    if (!status) return 'gray';
    const colors = {
        green: 'bg-green-500',
        yellow: 'bg-yellow-500',
        red: 'bg-red-500',
        gray: 'bg-gray-500'
    };
    return colors[status.color] || 'bg-gray-500';
};

// Formater la date
const formatDate = (date) => {
    return new Date(date).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="MSN Connect - Messagerie" />

    <div class="flex h-screen bg-gradient-to-br from-blue-50 to-purple-50">
        <!-- Barre latérale gauche - Liste des contacts/conversations -->
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
            <!-- En-tête utilisateur -->
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-purple-500">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-xl font-bold text-blue-600">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="flex-1 text-white">
                        <div class="font-semibold">{{ user.name }}</div>
                        <div class="text-xs opacity-90">En ligne</div>
                    </div>
                </div>
            </div>

            <!-- Utilisateurs en ligne -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Utilisateurs en ligne ({{ onlineUsers.length }})
                    </h3>

                    <div v-if="onlineUsers.length === 0" class="text-center text-gray-400 py-8">
                        <p class="text-sm">Aucun utilisateur en ligne</p>
                    </div>

                    <div v-else class="space-y-2">
                        <button
                            v-for="contact in onlineUsers"
                            :key="contact.id"
                            @click="startConversation(contact.id)"
                            class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 transition-colors duration-150"
                        >
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white font-semibold">
                                    {{ contact.name.charAt(0).toUpperCase() }}
                                </div>
                                <div :class="['absolute bottom-0 right-0 w-3 h-3 border-2 border-white rounded-full', getStatusColor(contact.status)]"></div>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900">{{ contact.name }}</div>
                                <div class="text-xs text-gray-500 truncate">
                                    {{ contact.status_message || contact.status?.name }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Conversations récentes -->
                <div class="p-4 border-t border-gray-200" v-if="channels.length > 0">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        Conversations récentes
                    </h3>

                    <div class="space-y-2">
                        <button
                            v-for="channel in channels"
                            :key="channel.id"
                            @click="selectChannel(channel)"
                            :class="[
                                'w-full flex items-center space-x-3 p-3 rounded-lg transition-colors duration-150',
                                selectedChannel?.id === channel.id
                                    ? 'bg-blue-100 border-l-4 border-blue-500'
                                    : 'hover:bg-gray-50'
                            ]"
                        >
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-teal-400 flex items-center justify-center text-white font-semibold">
                                {{ channel.name?.charAt(0).toUpperCase() || 'C' }}
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900">{{ channel.name || 'Conversation' }}</div>
                                <div class="text-xs text-gray-500 truncate">
                                    {{ channel.last_message?.content || 'Aucun message' }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone de conversation principale -->
        <div class="flex-1 flex flex-col">
            <!-- En-tête de la conversation -->
            <div v-if="selectedChannel" class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white font-semibold">
                        {{ selectedChannel.name?.charAt(0).toUpperCase() || 'C' }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ selectedChannel.name || 'Conversation' }}</div>
                        <div class="text-xs text-gray-500">{{ selectedChannel.members?.length || 0 }} membres</div>
                    </div>
                </div>
            </div>

            <!-- Zone de messages -->
            <div v-if="selectedChannel" id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                <div v-if="messages.length === 0" class="text-center text-gray-400 py-12">
                    <p>Aucun message. Commencez la conversation!</p>
                </div>

                <div
                    v-for="message in messages"
                    :key="message.id"
                    :class="[
                        'flex',
                        message.user_id === user.id ? 'justify-end' : 'justify-start'
                    ]"
                >
                    <div :class="[
                        'max-w-md rounded-2xl px-4 py-2 shadow-sm',
                        message.user_id === user.id
                            ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white'
                            : 'bg-white text-gray-900 border border-gray-200'
                    ]">
                        <div v-if="message.user_id !== user.id" class="text-xs font-semibold mb-1">
                            {{ message.user?.name }}
                        </div>
                        <div>{{ message.content }}</div>
                        <div :class="[
                            'text-xs mt-1',
                            message.user_id === user.id ? 'text-blue-100' : 'text-gray-500'
                        ]">
                            {{ formatDate(message.created_at) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page d'accueil sans conversation sélectionnée -->
            <div v-else class="flex-1 flex items-center justify-center text-gray-400">
                <div class="text-center">
                    <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Bienvenue sur MSN Connect</h3>
                    <p class="text-gray-500">Sélectionnez un contact pour commencer à discuter</p>
                </div>
            </div>

            <!-- Zone de saisie -->
            <div v-if="selectedChannel" class="h-20 bg-white border-t border-gray-200 px-6 flex items-center space-x-4">
                <input
                    v-model="newMessage"
                    @keyup.enter="sendMessage"
                    type="text"
                    placeholder="Tapez votre message..."
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <button
                    @click="sendMessage"
                    :disabled="!newMessage.trim()"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold hover:from-blue-600 hover:to-purple-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    Envoyer
                </button>
            </div>
        </div>
    </div>
</template>
