<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { HelpCircle, Trophy, Clock, CheckCircle, XCircle, Plus, Award } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type QuizQuestion = {
    id: number;
    question: string;
    options: string[];
    user_answer?: string | null;
    correct_answer?: string;
};

type Quiz = {
    id: number;
    title: string;
    description: string | null;
    week_start_date: string;
    week_end_date: string;
    has_answered: boolean;
    user_result?: {
        correct_count: number;
        total_questions: number;
        is_perfect: boolean;
    };
    creator: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
};

type Props = {
    active_quizzes: Quiz[];
    past_quizzes: Quiz[];
    active_medal: {
        quiz_title: string;
        earned_date: string;
        expires_at: string;
    } | null;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quiz Semanal',
        href: '#',
    },
];

const page = usePage();
const currentUserId = page.props.auth.user?.id;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Quiz Semanal" />
        <div class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6 flex items-center justify-between">
                    <Heading
                        title="Você conhece o Mamira-San?"
                        description="Quiz semanal sobre fatos do Mamira"
                    />
                    <Link href="/quizzes/criar">
                        <Button>
                            <Plus class="h-4 w-4 mr-2" />
                            Criar Quiz
                        </Button>
                    </Link>
                </div>

                <!-- Medalha Ativa -->
                <div v-if="active_medal" class="mb-6 rounded-xl border border-yellow-500/50 bg-yellow-500/10 p-4">
                    <div class="flex items-center gap-3">
                        <Award class="h-8 w-8 text-yellow-500" />
                        <div class="flex-1">
                            <h3 class="font-semibold text-yellow-700 dark:text-yellow-400">Medalha Ativa!</h3>
                            <p class="text-sm text-muted-foreground">
                                Você ganhou a medalha do quiz "{{ active_medal.quiz_title }}"
                            </p>
                            <p class="text-xs text-muted-foreground mt-1">
                                Expira em: {{ new Date(active_medal.expires_at).toLocaleDateString('pt-BR') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quizzes Ativos -->
                <div v-if="active_quizzes.length > 0" class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Quizzes Ativos</h2>
                    <div class="space-y-3">
                        <div
                            v-for="quiz in active_quizzes"
                            :key="quiz.id"
                            @click="router.visit(`/quizzes/${quiz.id}`)"
                            class="p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border bg-background hover:bg-muted/50 transition-colors cursor-pointer"
                        >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold mb-1">{{ quiz.title }}</h3>
                                        <p v-if="quiz.description" class="text-sm text-muted-foreground mb-2 line-clamp-2">
                                            {{ quiz.description }}
                                        </p>
                                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                            <div class="flex items-center gap-1">
                                                <span>Criado por:</span>
                                                <span class="font-semibold">{{ quiz.creator.name }}</span>
                                                <UserBadges v-if="quiz.creator.selected_badge" :badge="quiz.creator.selected_badge" />
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <Clock class="h-3 w-3" />
                                                <span>
                                                    {{ new Date(quiz.week_start_date).toLocaleDateString('pt-BR') }} - 
                                                    {{ new Date(quiz.week_end_date).toLocaleDateString('pt-BR') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <div v-if="quiz.has_answered && quiz.user_result" class="text-sm">
                                            <span class="font-semibold">
                                                {{ quiz.user_result.correct_count }}/{{ quiz.user_result.total_questions }}
                                            </span>
                                            <Trophy
                                                v-if="quiz.user_result.is_perfect"
                                                class="h-4 w-4 inline-block ml-1 text-yellow-500"
                                            />
                                            <p class="text-xs text-muted-foreground mt-1">Respondido</p>
                                        </div>
                                        <div v-else>
                                            <Button size="sm" class="mt-2">
                                                Responder
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Sem Quiz Ativo -->
                <div v-else class="mb-8 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <HelpCircle class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhum quiz ativo no momento</h3>
                    <p class="text-muted-foreground mb-4">
                        Crie um novo quiz ou aguarde a próxima semana!
                    </p>
                    <Link href="/quizzes/criar">
                        <Button>
                            <Plus class="h-4 w-4 mr-2" />
                            Criar Quiz
                        </Button>
                    </Link>
                </div>

                <!-- Quizzes Anteriores -->
                <div v-if="past_quizzes.length > 0">
                    <h2 class="text-lg font-semibold mb-4">Quizzes Anteriores</h2>
                    <div class="space-y-3">
                        <div
                            v-for="quiz in past_quizzes"
                            :key="quiz.id"
                            @click="router.visit(`/quizzes/${quiz.id}`)"
                            class="p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border bg-background hover:bg-muted/50 transition-colors cursor-pointer"
                        >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold mb-1">{{ quiz.title }}</h3>
                                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                            <div class="flex items-center gap-1">
                                                <span>Criado por:</span>
                                                <span class="font-semibold">{{ quiz.creator.name }}</span>
                                                <UserBadges v-if="quiz.creator.selected_badge" :badge="quiz.creator.selected_badge" />
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <Clock class="h-3 w-3" />
                                                <span>
                                                    {{ new Date(quiz.week_start_date).toLocaleDateString('pt-BR') }} - 
                                                    {{ new Date(quiz.week_end_date).toLocaleDateString('pt-BR') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <div v-if="quiz.has_answered && quiz.user_result" class="text-sm">
                                            <span class="font-semibold">
                                                {{ quiz.user_result.correct_count }}/{{ quiz.user_result.total_questions }}
                                            </span>
                                            <Trophy
                                                v-if="quiz.user_result.is_perfect"
                                                class="h-4 w-4 inline-block ml-1 text-yellow-500"
                                            />
                                        </div>
                                        <span v-else class="text-sm text-muted-foreground">Não respondido</span>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

