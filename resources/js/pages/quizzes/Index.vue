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

type ActiveQuiz = {
    id: number;
    title: string;
    description: string | null;
    week_start_date: string;
    week_end_date: string;
    has_answered: boolean;
    questions: QuizQuestion[];
    creator: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
    user_result?: {
        correct_count: number;
        total_questions: number;
        is_perfect: boolean;
        time_taken_seconds: number;
    };
};

type PastQuiz = {
    id: number;
    title: string;
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
    };
};

type Props = {
    active_quiz: ActiveQuiz | null;
    past_quizzes: PastQuiz[];
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

const answers = ref<Record<number, string>>({});
const startTime = ref<number | null>(null);
const isSubmitting = ref(false);

const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const submitQuiz = () => {
    if (!props.active_quiz || isSubmitting.value) return;

    const timeTaken = startTime.value ? Math.floor((Date.now() - startTime.value) / 1000) : 0;

    isSubmitting.value = true;
    router.post(`/quizzes/${props.active_quiz.id}/submit`, {
        answers: answers.value,
        time_taken_seconds: timeTaken,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

onMounted(() => {
    if (props.active_quiz && !props.active_quiz.has_answered) {
        startTime.value = Date.now();
    }
});
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

                <!-- Quiz Ativo -->
                <div v-if="active_quiz" class="mb-8 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold mb-2">{{ active_quiz.title }}</h2>
                        <p v-if="active_quiz.description" class="text-sm text-muted-foreground mb-2">
                            {{ active_quiz.description }}
                        </p>
                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                            <div class="flex items-center gap-1">
                                <span>Criado por:</span>
                                <span class="font-semibold">{{ active_quiz.creator.name }}</span>
                                <UserBadges v-if="active_quiz.creator.selected_badge" :badge="active_quiz.creator.selected_badge" />
                            </div>
                            <div class="flex items-center gap-1">
                                <Clock class="h-3 w-3" />
                                <span>
                                    {{ new Date(active_quiz.week_start_date).toLocaleDateString('pt-BR') }} - 
                                    {{ new Date(active_quiz.week_end_date).toLocaleDateString('pt-BR') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Resultado (se já respondeu) -->
                    <div v-if="active_quiz.has_answered && active_quiz.user_result" class="mb-6 p-4 rounded-lg bg-muted">
                        <div class="flex items-center gap-3 mb-2">
                            <CheckCircle v-if="active_quiz.user_result.is_perfect" class="h-6 w-6 text-green-500" />
                            <XCircle v-else class="h-6 w-6 text-orange-500" />
                            <div>
                                <h3 class="font-semibold">
                                    {{ active_quiz.user_result.correct_count }} de {{ active_quiz.user_result.total_questions }} corretas
                                </h3>
                                <p v-if="active_quiz.user_result.is_perfect" class="text-sm text-green-600 dark:text-green-400">
                                    🎉 Perfeito! Você acertou todas!
                                </p>
                                <p v-else class="text-sm text-muted-foreground">
                                    Tempo: {{ formatTime(active_quiz.user_result.time_taken_seconds) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Perguntas -->
                    <div class="space-y-6">
                        <div
                            v-for="(question, index) in active_quiz.questions"
                            :key="question.id"
                            class="p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary">
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold mb-3">{{ question.question }}</h3>
                                    <div class="space-y-2">
                                        <label
                                            v-for="option in question.options"
                                            :key="option"
                                            :class="[
                                                'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors',
                                                active_quiz.has_answered
                                                    ? option === question.correct_answer
                                                        ? 'border-green-500 bg-green-500/10'
                                                        : question.user_answer === option && option !== question.correct_answer
                                                            ? 'border-red-500 bg-red-500/10'
                                                            : 'border-sidebar-border/70 dark:border-sidebar-border'
                                                    : answers[question.id] === option
                                                        ? 'border-primary bg-primary/10'
                                                        : 'border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted',
                                            ]"
                                        >
                                            <input
                                                v-if="!active_quiz.has_answered"
                                                type="radio"
                                                :name="`question-${question.id}`"
                                                :value="option"
                                                v-model="answers[question.id]"
                                                class="cursor-pointer"
                                            />
                                            <span class="flex-1">{{ option }}</span>
                                            <CheckCircle
                                                v-if="active_quiz.has_answered && option === question.correct_answer"
                                                class="h-5 w-5 text-green-500"
                                            />
                                            <XCircle
                                                v-if="active_quiz.has_answered && question.user_answer === option && option !== question.correct_answer"
                                                class="h-5 w-5 text-red-500"
                                            />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Submit -->
                    <div v-if="!active_quiz.has_answered" class="mt-6 flex justify-end">
                        <Button
                            @click="submitQuiz"
                            :disabled="Object.keys(answers).length !== active_quiz.questions.length || isSubmitting"
                            size="lg"
                        >
                            {{ isSubmitting ? 'Enviando...' : 'Enviar Respostas' }}
                        </Button>
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
                    <div class="space-y-2">
                        <div
                            v-for="quiz in past_quizzes"
                            :key="quiz.id"
                            class="p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border bg-background flex items-center justify-between"
                        >
                            <div>
                                <h3 class="font-semibold">{{ quiz.title }}</h3>
                                <p class="text-xs text-muted-foreground">
                                    {{ new Date(quiz.week_start_date).toLocaleDateString('pt-BR') }} - 
                                    {{ new Date(quiz.week_end_date).toLocaleDateString('pt-BR') }}
                                </p>
                            </div>
                            <div class="text-right">
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
    </AppLayout>
</template>

