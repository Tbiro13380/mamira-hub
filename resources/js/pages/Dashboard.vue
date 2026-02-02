<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Activity, 
    Image, 
    Mail, 
    Type, 
    Droplet, 
    Users, 
    BookOpen, 
    Heart, 
    MessageSquare, 
    Trophy,
    TrendingUp,
    Clock,
    User,
    ArrowRight,
    Plus
} from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as lettersIndex, create as createLetter } from '@/routes/letters/index';
import { index as photosIndex } from '@/routes/photos';
import { index as activitiesIndex } from '@/routes/activities';
import { index as statsIndex } from '@/routes/stats';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

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

type Photo = {
    id: number;
    path: string;
    caption: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
};

type Letter = {
    id: number;
    title: string | null;
    content: string;
    image: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
    likes_count: number;
    comments_count: number;
};

type Badge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
    earned_at: string;
};

type Props = {
    stats: {
        total_words: number;
        total_tears: number;
        total_letters: number;
        total_users: number;
    };
    user_stats: {
        letters_count: number;
        likes_received: number;
        comments_count: number;
        badges_count: number;
    };
    recent_activities: ActivityItem[];
    recent_photos: Photo[];
    recent_letters: Letter[];
    recent_badges: Badge[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [];

const page = usePage();
const currentUserId = page.props.auth.user?.id;
const isAddingTear = ref(false);
const localTears = ref(props.stats.total_tears);

const formatNumber = (num: number): string => {
    return new Intl.NumberFormat('pt-BR').format(num);
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
    return `há ${diffInDays}d`;
};

const addTear = () => {
    if (isAddingTear.value) return;

    isAddingTear.value = true;
    fetch('/estatisticas/lagrimas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    })
        .then((response) => response.json())
        .then((data) => {
            localTears.value = data.total_tears;
            isAddingTear.value = false;
        })
        .catch(() => {
            isAddingTear.value = false;
        });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Painel" />
        <div class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Estatísticas Globais -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Total de Palavras</p>
                            <p class="text-2xl font-bold">{{ formatNumber(stats.total_words) }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-primary/10">
                            <Type class="h-5 w-5 text-primary" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Lágrimas Derramadas</p>
                            <p class="text-2xl font-bold">{{ formatNumber(localTears) }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-destructive/10">
                            <Droplet class="h-5 w-5 text-destructive" />
                        </div>
                    </div>
                    <Button
                        variant="outline"
                        size="sm"
                        class="mt-2 w-full"
                        @click="addTear"
                        :disabled="isAddingTear"
                    >
                        <Droplet class="h-3 w-3 mr-2" />
                        {{ isAddingTear ? 'Derramando...' : 'Derramar' }}
                    </Button>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Total de Cartas</p>
                            <p class="text-2xl font-bold">{{ formatNumber(stats.total_letters) }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-500/10">
                            <Mail class="h-5 w-5 text-blue-500" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Total de Usuários</p>
                            <p class="text-2xl font-bold">{{ formatNumber(stats.total_users) }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-green-500/10">
                            <Users class="h-5 w-5 text-green-500" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do Usuário -->
            <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Seu Progresso</h2>
                    <Link :href="lettersIndex()" class="text-sm text-primary hover:underline flex items-center gap-1">
                        Ver todas
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 rounded-lg bg-muted/50">
                        <BookOpen class="h-6 w-6 mx-auto mb-2 text-muted-foreground" />
                        <p class="text-2xl font-bold">{{ user_stats.letters_count }}</p>
                        <p class="text-xs text-muted-foreground">Cartas</p>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-muted/50">
                        <Heart class="h-6 w-6 mx-auto mb-2 text-destructive" />
                        <p class="text-2xl font-bold">{{ user_stats.likes_received }}</p>
                        <p class="text-xs text-muted-foreground">Likes Recebidos</p>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-muted/50">
                        <MessageSquare class="h-6 w-6 mx-auto mb-2 text-muted-foreground" />
                        <p class="text-2xl font-bold">{{ user_stats.comments_count }}</p>
                        <p class="text-xs text-muted-foreground">Comentários</p>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-muted/50">
                        <Trophy class="h-6 w-6 mx-auto mb-2 text-yellow-500" />
                        <p class="text-2xl font-bold">{{ user_stats.badges_count }}</p>
                        <p class="text-xs text-muted-foreground">Badges</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Feed de Atividades -->
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <Activity class="h-5 w-5" />
                            Feed de Atividades
                        </h2>
                        <Link :href="activitiesIndex()" class="text-sm text-primary hover:underline flex items-center gap-1">
                            Ver todas
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                    <div v-if="recent_activities.length === 0" class="text-center py-8 text-muted-foreground">
                        <Activity class="h-8 w-8 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">Nenhuma atividade ainda</p>
                    </div>
                    <div v-else class="space-y-3 max-h-96 overflow-y-auto">
                        <div
                            v-for="activity in recent_activities"
                            :key="activity.id"
                            class="flex items-start gap-3 p-3 rounded-lg hover:bg-muted/50 transition-colors"
                        >
                            <div class="text-xl flex-shrink-0">{{ activity.icon || '📢' }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-foreground mb-1">{{ activity.description }}</p>
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    <User class="h-3 w-3" />
                                    <span>{{ activity.user.name }}</span>
                                    <UserBadges v-if="activity.user.selected_badge" :badge="activity.user.selected_badge" />
                                    <span class="mx-1">•</span>
                                    <Clock class="h-3 w-3" />
                                    <span>{{ formatTimeAgo(activity.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimas Cartas -->
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <Mail class="h-5 w-5" />
                            Últimas Cartas
                        </h2>
                        <div class="flex items-center gap-2">
                            <Link :href="createLetter()" class="text-sm text-primary hover:underline flex items-center gap-1">
                                <Plus class="h-4 w-4" />
                                Nova
                            </Link>
                            <Link :href="lettersIndex()" class="text-sm text-primary hover:underline flex items-center gap-1">
                                Ver todas
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                    <div v-if="recent_letters.length === 0" class="text-center py-8 text-muted-foreground">
                        <Mail class="h-8 w-8 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">Nenhuma carta ainda</p>
                    </div>
                    <div v-else class="space-y-3 max-h-96 overflow-y-auto">
                        <Link
                            v-for="letter in recent_letters"
                            :key="letter.id"
                            :href="lettersIndex()"
                            class="block p-3 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-start gap-3">
                                <div v-if="letter.image" class="flex-shrink-0">
                                    <img :src="letter.image" alt="Imagem" class="w-16 h-16 rounded-lg object-cover" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-sm mb-1 line-clamp-1">
                                        {{ letter.title || 'Sem título' }}
                                    </h3>
                                    <p class="text-xs text-muted-foreground mb-2 line-clamp-2">{{ letter.content }}</p>
                                    <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                        <div class="flex items-center gap-1">
                                            <User class="h-3 w-3" />
                                            <span>{{ letter.user.name }}</span>
                                            <UserBadges v-if="letter.user.selected_badge" :badge="letter.user.selected_badge" />
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <Heart class="h-3 w-3" />
                                            <span>{{ letter.likes_count }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <MessageSquare class="h-3 w-3" />
                                            <span>{{ letter.comments_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mural de Fotos e Badges Recentes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Mural de Fotos -->
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <Image class="h-5 w-5" />
                            Mural de Fotos
                        </h2>
                        <div class="flex items-center gap-2">
                            <Link :href="photosIndex()" class="text-sm text-primary hover:underline flex items-center gap-1">
                                Ver todas
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                    <div v-if="recent_photos.length === 0" class="text-center py-8 text-muted-foreground">
                        <Image class="h-8 w-8 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">Nenhuma foto ainda</p>
                    </div>
                    <div v-else class="grid grid-cols-3 gap-2">
                        <Link
                            v-for="photo in recent_photos"
                            :key="photo.id"
                            :href="photosIndex()"
                            class="group relative aspect-square rounded-lg overflow-hidden border border-sidebar-border/70 dark:border-sidebar-border hover:border-primary transition-colors"
                        >
                            <img
                                :src="photo.path"
                                :alt="photo.caption || 'Foto'"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                <p v-if="photo.caption" class="text-white text-xs px-2 text-center line-clamp-2 opacity-0 group-hover:opacity-100">
                                    {{ photo.caption }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Badges Recentes -->
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <Trophy class="h-5 w-5" />
                            Suas Badges Recentes
                        </h2>
                        <Link href="/conquistas" class="text-sm text-primary hover:underline flex items-center gap-1">
                            Ver todas
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                    <div v-if="recent_badges.length === 0" class="text-center py-8 text-muted-foreground">
                        <Trophy class="h-8 w-8 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">Nenhuma badge ainda</p>
                        <p class="text-xs mt-1">Continue interagindo para ganhar badges!</p>
                    </div>
                    <div v-else class="space-y-2 max-h-96 overflow-y-auto">
                        <div
                            v-for="badge in recent_badges"
                            :key="badge.id"
                            class="flex items-center gap-3 p-3 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                            :style="{
                                backgroundColor: badge.color + '10',
                                borderColor: badge.color + '40',
                            }"
                        >
                            <div class="text-2xl">{{ badge.icon || '🏆' }}</div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm" :style="{ color: badge.color }">
                                    {{ badge.name }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Ganha {{ formatTimeAgo(badge.earned_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
