<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Trophy, CheckCircle2, Lock, Star } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as badgesIndex } from '@/routes/badges';
import { type BreadcrumbItem } from '@/types';

type Badge = {
    id: number;
    name: string;
    description: string;
    icon: string | null;
    color: string;
    earned: boolean;
    earned_at: string | null;
};

type Props = {
    badges: Badge[];
    selected_badge_id: number | null;
};

const props = defineProps<Props>();

const form = useForm({
    badge_id: props.selected_badge_id,
});

const selectBadge = (badgeId: number | null) => {
    form.badge_id = badgeId;
    form.patch('/conquistas/selecionar', {
        preserveScroll: true,
        onSuccess: () => {
            // Sucesso
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: dashboard().url,
    },
    {
        title: 'Minhas Conquistas',
        href: badgesIndex().url,
    },
];

const earnedCount = props.badges.filter((b) => b.earned).length;
const totalCount = props.badges.length;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Minhas Conquistas" />

        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6">
                    <Heading
                        title="Minhas Conquistas"
                        description="Veja todas as badges disponíveis e suas conquistas"
                    />
                    <div class="mt-4 flex items-center gap-2 text-sm text-muted-foreground">
                        <Trophy class="h-4 w-4" />
                        <span>
                            {{ earnedCount }} de {{ totalCount }} conquistas desbloqueadas
                        </span>
                    </div>
                </div>

                <div v-if="badges.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <Trophy class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhuma badge disponível</h3>
                    <p class="text-muted-foreground">
                        Ainda não há badges configuradas no sistema.
                    </p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="badge in badges"
                        :key="badge.id"
                        :class="[
                            'rounded-xl border p-6 transition-all',
                            badge.earned
                                ? 'border-sidebar-border/70 dark:border-sidebar-border bg-background shadow-sm'
                                : 'border-sidebar-border/30 dark:border-sidebar-border/30 bg-muted/30 opacity-60',
                        ]"
                    >
                        <div class="flex flex-col items-center text-center space-y-3">
                            <div
                                :class="[
                                    'w-16 h-16 rounded-full flex items-center justify-center text-2xl transition-all',
                                    badge.earned
                                        ? 'shadow-md'
                                        : 'grayscale opacity-50',
                                ]"
                                :style="{
                                    backgroundColor: badge.earned ? badge.color + '20' : '#6b728020',
                                    color: badge.earned ? badge.color : '#6b7280',
                                }"
                            >
                                <span v-if="badge.icon">{{ badge.icon }}</span>
                                <Trophy v-else class="h-8 w-8" />
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <h3
                                        :class="[
                                            'font-semibold text-lg',
                                            badge.earned ? 'text-foreground' : 'text-muted-foreground',
                                        ]"
                                    >
                                        {{ badge.name }}
                                    </h3>
                                    <CheckCircle2
                                        v-if="badge.earned"
                                        class="h-5 w-5 text-green-500"
                                    />
                                    <Lock
                                        v-else
                                        class="h-5 w-5 text-muted-foreground"
                                    />
                                    <Star
                                        v-if="badge.earned && selected_badge_id === badge.id"
                                        class="h-5 w-5 text-yellow-500 fill-yellow-500"
                                        title="Badge selecionada"
                                    />
                                </div>
                                <p
                                    :class="[
                                        'text-sm',
                                        badge.earned ? 'text-muted-foreground' : 'text-muted-foreground/70',
                                    ]"
                                >
                                    {{ badge.description }}
                                </p>
                                <p
                                    v-if="badge.earned && badge.earned_at"
                                    class="text-xs text-muted-foreground mt-2"
                                >
                                    Conquistada em
                                    {{ new Date(badge.earned_at).toLocaleDateString('pt-BR', {
                                        day: '2-digit',
                                        month: 'long',
                                        year: 'numeric',
                                    }) }}
                                </p>
                                <div v-if="badge.earned" class="mt-3">
                                    <Button
                                        v-if="selected_badge_id !== badge.id"
                                        size="sm"
                                        variant="outline"
                                        @click="selectBadge(badge.id)"
                                        class="w-full"
                                    >
                                        <Star class="h-4 w-4 mr-2" />
                                        Usar esta badge
                                    </Button>
                                    <Button
                                        v-else
                                        size="sm"
                                        variant="outline"
                                        @click="selectBadge(null)"
                                        class="w-full"
                                    >
                                        Remover badge
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

