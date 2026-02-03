<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Bell, BellOff, Check, CheckCheck, Clock } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type Notification = {
    id: number;
    type: string;
    message: string;
    read: boolean;
    created_at: string;
    from_user: {
        id: number;
        name: string;
        avatar?: string | null;
        selected_badge?: UserBadge | null;
    } | null;
    data?: {
        letter_id?: number;
        meme_id?: number;
    } | null;
    notifiable_type: string;
    notifiable_id: number;
};

type Props = {
    notifications: Notification[];
    unread_count: number;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Notificações',
        href: '#',
    },
];

const page = usePage();

const markAsRead = (notification: Notification) => {
    if (notification.read) return;

    fetch(`/notificacoes/${notification.id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    })
        .then(() => {
            router.reload({ only: ['notifications', 'unread_count'] });
        })
        .catch((error) => {
            console.error('Erro ao marcar notificação como lida:', error);
        });
};

const markAllAsRead = () => {
    fetch('/notificacoes/read-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    })
        .then(() => {
            router.reload({ only: ['notifications', 'unread_count'] });
        })
        .catch((error) => {
            console.error('Erro ao marcar todas como lidas:', error);
        });
};

const formatTimeAgo = (dateString: string): string => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) {
        return 'agora';
    }

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
        return `há ${diffInMinutes} min`;
    }

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
        return `há ${diffInHours}h`;
    }

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) {
        return `há ${diffInDays}d`;
    }

    return new Date(dateString).toLocaleDateString('pt-BR');
};

const getNotificationLink = (notification: Notification): string => {
    if (notification.data?.letter_id) {
        return '/cartas';
    }
    if (notification.data?.meme_id) {
        return '/memes';
    }
    return '#';
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Notificações" />
        <div class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6 flex items-center justify-between">
                    <Heading
                        title="Notificações"
                        :description="`${unread_count} não lida${unread_count !== 1 ? 's' : ''}`"
                    />
                    <Button
                        v-if="unread_count > 0"
                        variant="outline"
                        @click="markAllAsRead"
                    >
                        <CheckCheck class="h-4 w-4 mr-2" />
                        Marcar todas como lidas
                    </Button>
                </div>

                <div v-if="notifications.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <BellOff class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhuma notificação</h3>
                    <p class="text-muted-foreground">
                        Você não tem notificações no momento.
                    </p>
                </div>

                <div v-else class="space-y-2">
                    <div
                        v-for="notification in notifications"
                        :key="notification.id"
                        :class="[
                            'rounded-lg border p-4 cursor-pointer transition-colors',
                            notification.read
                                ? 'border-sidebar-border/70 dark:border-sidebar-border bg-background'
                                : 'border-primary/50 bg-primary/5 dark:bg-primary/10',
                        ]"
                        @click="markAsRead(notification); router.visit(getNotificationLink(notification))"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <div
                                    v-if="notification.from_user"
                                    class="h-10 w-10 rounded-full bg-muted flex items-center justify-center"
                                >
                                    <span class="text-sm font-semibold">
                                        {{ notification.from_user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <Bell v-else class="h-5 w-5 text-muted-foreground" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <p
                                            :class="[
                                                'text-sm',
                                                notification.read ? 'text-muted-foreground' : 'font-semibold text-foreground',
                                            ]"
                                        >
                                            {{ notification.message }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                v-if="notification.from_user"
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{ notification.from_user.name }}
                                            </span>
                                            <UserBadges
                                                v-if="notification.from_user?.selected_badge"
                                                :badge="notification.from_user.selected_badge"
                                            />
                                            <span class="text-xs text-muted-foreground">•</span>
                                            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                                <Clock class="h-3 w-3" />
                                                {{ formatTimeAgo(notification.created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <Button
                                            v-if="!notification.read"
                                            variant="ghost"
                                            size="sm"
                                            @click.stop="markAsRead(notification)"
                                            class="h-6 w-6 p-0"
                                        >
                                            <Check class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

