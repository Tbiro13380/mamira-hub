<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Activity, Clock, User } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type ActivityItem = {
    id: number;
    type: string;
    description: string;
    icon: string | null;
    metadata: Record<string, any> | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
};

type Props = {
    activities: ActivityItem[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Feed de Atividades',
        href: '#',
    },
];

const formatTimeAgo = (dateString: string): string => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) {
        return 'agora';
    }

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
        return `há ${diffInMinutes} minuto${diffInMinutes > 1 ? 's' : ''}`;
    }

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
        return `há ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`;
    }

    const diffInDays = Math.floor(diffInHours / 24);
    return `há ${diffInDays} dia${diffInDays > 1 ? 's' : ''}`;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Feed de Atividades" />
        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6">
                    <Heading
                        title="Feed de Atividades"
                        description="O pulso da rede - veja o que está acontecendo agora!"
                    />
                </div>

                <div v-if="activities.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <Activity class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhuma atividade ainda</h3>
                    <p class="text-muted-foreground">
                        Quando os usuários começarem a interagir, as atividades aparecerão aqui!
                    </p>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="activity in activities"
                        :key="activity.id"
                        class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-4 flex items-start gap-4"
                    >
                        <div class="flex-shrink-0 text-2xl">
                            {{ activity.icon || '📢' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-foreground mb-2">
                                {{ activity.description }}
                            </p>
                            <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                <div class="flex items-center gap-1">
                                    <User class="h-3 w-3" />
                                    <span>{{ activity.user.name }}</span>
                                    <UserBadges v-if="activity.user.selected_badge" :badge="activity.user.selected_badge" />
                                </div>
                                <div class="flex items-center gap-1">
                                    <Clock class="h-3 w-3" />
                                    <span>{{ formatTimeAgo(activity.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

