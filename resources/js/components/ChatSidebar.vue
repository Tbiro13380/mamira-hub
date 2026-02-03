<script setup lang="ts">
import { MessageSquare, X, Send } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import UserBadges from '@/components/UserBadges.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';

const page = usePage();
const currentUser = page.props.auth.user;

const isOpen = ref(false);
const messages = ref<any[]>([]);
const messageInput = ref('');
const messagesContainer = ref<HTMLElement | null>(null);
const isSending = ref(false);

// Função auxiliar para garantir que messages.value é sempre um array
const ensureMessagesArray = () => {
    if (!Array.isArray(messages.value)) {
        console.warn('⚠️ messages.value não é um array, reinicializando...');
        messages.value = [];
    }
};

const loadMessages = async () => {
    try {
        const response = await fetch('/chat', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
            if (data.messages && Array.isArray(data.messages)) {
                messages.value = data.messages;
                scrollToBottom();
            } else {
                console.warn('⚠️ Resposta não contém array de mensagens:', data);
            }
    } catch (error) {
        console.error('Erro ao carregar mensagens:', error);
    }
};

const sendMessage = async () => {
    const message = messageInput.value.trim();
    if (!message || !currentUser || isSending.value) return;

    isSending.value = true;
    const messageToSend = message;
    messageInput.value = ''; // Limpar input imediatamente para evitar dupla submissão

    try {
        const response = await fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message: messageToSend }),
        });

        if (response.ok) {
            const data = await response.json();
            console.log('📤 Resposta do servidor:', data);
            // Adicionar a mensagem localmente imediatamente (para feedback instantâneo)
            // O Echo também vai receber via broadcast, mas a verificação de duplicata vai evitar duplicação
            if (data.message) {
                console.log('💬 Adicionando mensagem localmente:', data.message);
                ensureMessagesArray();
                // Verificar duplicata antes de adicionar
                const messageExists = messages.value.some((m: any) => m.id === data.message.id);
                if (!messageExists) {
                    messages.value.push(data.message);
                    scrollToBottom();
                } else {
                    console.log('⚠️ Mensagem já existe localmente');
                }
            } else {
                console.warn('⚠️ Resposta não contém mensagem:', data);
            }
        } else {
            console.error('❌ Erro ao enviar mensagem:', response.status, response.statusText);
            // Restaurar mensagem em caso de erro
            messageInput.value = messageToSend;
        }
    } catch (error) {
        console.error('Erro ao enviar mensagem:', error);
        // Restaurar mensagem em caso de erro
        messageInput.value = messageToSend;
    } finally {
        isSending.value = false;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const formatTime = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
    if (currentUser) {
        loadMessages();

        // Escutar mensagens em tempo real
        if (window.Echo && window.Echo !== null) {
            const channel = window.Echo.channel('chat');
            
            channel.listen('.MessageSent', (e: any) => {
                ensureMessagesArray();
                // Verificar se a mensagem já existe para evitar duplicação
                // Comparar por ID e também por conteúdo + timestamp para garantir
                const messageExists = messages.value.some((m: any) => {
                    return m.id === e.id || 
                           (m.user_id === e.user_id && 
                            m.message === e.message && 
                            Math.abs(new Date(m.created_at).getTime() - new Date(e.created_at).getTime()) < 1000);
                });
                if (!messageExists) {
                    if (e.selected_badge && typeof e.selected_badge === 'object') {
                        messages.value.push(e);
                    } else {
                        // Se a badge não estiver no formato correto, adicionar sem badge
                        messages.value.push({
                            ...e,
                            selected_badge: null,
                        });
                    }
                    scrollToBottom();
                } else {
                    
                }
            });
        } else {
            
        }
    }
});

onUnmounted(() => {
    if (window.Echo && window.Echo !== null) {
        window.Echo.leave('chat');
    }
});
</script>

<template>
    <div v-if="currentUser" class="fixed bottom-4 right-4 z-50">
        <!-- Botão para abrir/fechar chat -->
        <Button
            v-if="!isOpen"
            @click="isOpen = true"
            size="lg"
            class="h-14 w-14 rounded-full shadow-lg"
        >
            <MessageSquare class="h-6 w-6" />
        </Button>

        <!-- Chat Sidebar -->
        <div
            v-else
            class="flex h-[600px] w-96 flex-col rounded-lg border border-sidebar-border/70 bg-background shadow-xl"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-sidebar-border/70 p-4">
                <h3 class="font-semibold">Chat em Tempo Real</h3>
                <Button
                    variant="ghost"
                    size="icon"
                    @click="isOpen = false"
                    class="h-8 w-8"
                >
                    <X class="h-4 w-4" />
                </Button>
            </div>

            <!-- Messages Container -->
            <div
                ref="messagesContainer"
                class="flex-1 overflow-y-auto p-4"
            >
                <div
                    v-for="message in messages"
                    :key="message.id"
                    :class="[
                        'flex gap-2 mb-4',
                        message.user_id === currentUser.id ? 'flex-row-reverse' : 'flex-row',
                    ]"
                >
                    <Avatar class="h-10 w-10 flex-shrink-0 mt-1">
                        <AvatarImage
                            v-if="message.user_avatar"
                            :src="message.user_avatar"
                            :alt="message.user_name"
                        />
                        <AvatarFallback class="text-sm font-semibold">
                            {{ getInitials(message.user_name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div
                        :class="[
                            'flex flex-col max-w-[75%]',
                            message.user_id === currentUser.id ? 'items-end' : 'items-start',
                        ]"
                    >
                        <!-- Nome e badge (apenas para mensagens de outros usuários) -->
                        <div
                            v-if="message.user_id !== currentUser.id"
                            class="mb-1 flex items-center gap-2 flex-wrap"
                        >
                            <span class="text-xs font-semibold text-foreground">{{ message.user_name }}</span>
                            <UserBadges
                                v-if="message.selected_badge"
                                :badge="message.selected_badge"
                            />
                        </div>
                        
                        <!-- Mensagem -->
                        <div
                            :class="[
                                'rounded-2xl px-4 py-2.5 shadow-sm',
                                message.user_id === currentUser.id
                                    ? 'bg-primary text-primary-foreground rounded-br-md'
                                    : 'bg-muted rounded-bl-md',
                            ]"
                        >
                            <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ message.message }}</p>
                        </div>
                        
                        <!-- Timestamp -->
                        <div
                            :class="[
                                'mt-1 text-xs',
                                message.user_id === currentUser.id
                                    ? 'text-muted-foreground/60 text-right'
                                    : 'text-muted-foreground/60',
                            ]"
                        >
                            {{ formatTime(message.created_at) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t border-sidebar-border/70 p-4">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <Input
                        v-model="messageInput"
                        placeholder="Digite sua mensagem..."
                        class="flex-1"
                        :disabled="isSending"
                        @keydown.enter.exact.prevent="sendMessage"
                    />
                    <Button type="submit" size="icon" :disabled="isSending || !messageInput.trim()">
                        <Send class="h-4 w-4" />
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>

