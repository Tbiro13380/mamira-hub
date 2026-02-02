<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Trophy, Medal, Award, Crown, User as UserIcon } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as leaderboardIndex } from '@/routes/leaderboard';
import { type BreadcrumbItem } from '@/types';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type LeaderboardUser = {
    id: number;
    name: string;
    score: number;
    rank: number;
    letters_count: number;
    likes_received: number;
    comments_count: number;
    badges_count: number;
    selected_badge?: UserBadge | null;
};

type Props = {
    users: LeaderboardUser[];
    current_user_id: number;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: dashboard().url,
    },
    {
        title: 'Leaderboard',
        href: leaderboardIndex().url,
    },
];

const getRankIcon = (rank: number) => {
    if (rank === 1) return Crown;
    if (rank === 2) return Trophy;
    if (rank === 3) return Medal;
    return Award;
};

const getRankColor = (rank: number) => {
    if (rank === 1) return 'text-yellow-500';
    if (rank === 2) return 'text-gray-400';
    if (rank === 3) return 'text-amber-600';
    return 'text-muted-foreground';
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Leaderboard - Fãs do Mamira" />

        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6">
                    <Heading
                        title="Leaderboard - Fãs do Mamira"
                        description="Os maiores fãs do Mamira-San! Veja quem está no topo"
                    />
                </div>

                <div v-if="users.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <Trophy class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhum fã ainda</h3>
                    <p class="text-muted-foreground">
                        Ainda não há usuários no leaderboard.
                    </p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="user in users"
                        :key="user.id"
                        :class="[
                            'rounded-xl border p-4 transition-all',
                            user.id === current_user_id
                                ? 'border-primary bg-primary/5 shadow-md'
                                : 'border-sidebar-border/70 dark:border-sidebar-border bg-background hover:shadow-sm',
                            user.rank <= 3 ? 'ring-2 ring-offset-2' : '',
                            user.rank === 1 ? 'ring-yellow-500' : '',
                            user.rank === 2 ? 'ring-gray-400' : '',
                            user.rank === 3 ? 'ring-amber-600' : '',
                        ]"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex shrink-0 items-center justify-center w-12">
                                <component
                                    :is="getRankIcon(user.rank)"
                                    :class="['h-6 w-6', getRankColor(user.rank)]"
                                />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        :class="[
                                            'font-semibold text-lg',
                                            user.id === current_user_id ? 'text-primary' : '',
                                        ]"
                                    >
                                        {{ user.rank }}º - {{ user.name }}
                                    </span>
                                    <UserBadges v-if="user.selected_badge" :badge="user.selected_badge" />
                                    <span
                                        v-if="user.id === current_user_id"
                                        class="text-xs px-2 py-0.5 rounded-full bg-primary/20 text-primary"
                                    >
                                        Você
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-muted-foreground flex-wrap">
                                    <div class="flex items-center gap-1">
                                        <Trophy class="h-4 w-4" />
                                        <span class="font-medium text-foreground">{{ user.score }} pontos</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <UserIcon class="h-4 w-4" />
                                        <span>{{ user.letters_count }} carta{{ user.letters_count !== 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span>❤️</span>
                                        <span>{{ user.likes_received }} like{{ user.likes_received !== 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span>💬</span>
                                        <span>{{ user.comments_count }} comentário{{ user.comments_count !== 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Award class="h-4 w-4" />
                                        <span>{{ user.badges_count }} badge{{ user.badges_count !== 1 ? 's' : '' }}</span>
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

