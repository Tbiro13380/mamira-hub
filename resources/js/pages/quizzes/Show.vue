<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { HelpCircle, Clock, CheckCircle, XCircle, Award } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

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
    is_active: boolean;
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

type Props = {
    quiz: Quiz;
};

const props = defineProps<Props>();

const breadcrumbs = computed(() => [
    {
        title: 'Quiz Semanal',
        href: '/quizzes',
    },
    {
        title: props.quiz?.title || 'Quiz',
        href: '#',
    },
]);

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
    if (!props.quiz || isSubmitting.value) return;

    const timeTaken = startTime.value ? Math.floor((Date.now() - startTime.value) / 1000) : 0;

    isSubmitting.value = true;
    router.post(`/quizzes/${props.quiz.id}/submit`, {
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
    console.log('Quiz data:', props.quiz);
    if (props.quiz && !props.quiz.has_answered) {
        startTime.value = Date.now();
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="quiz?.title || 'Quiz'" />
        <div v-if="!quiz" class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl text-center p-12">
                <HelpCircle class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                <h3 class="text-lg font-semibold mb-2">Carregando quiz...</h3>
            </div>
        </div>
        <div v-else class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6">
                    <Heading
                        :title="quiz.title"
                        :description="quiz.description || 'Quiz semanal sobre fatos do Mamira'"
                    />
                </div>

                <div class="mb-6 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="mb-4">
                        <div class="flex items-center gap-4 text-xs text-muted-foreground mb-4">
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

                    <!-- Resultado (se já respondeu) -->
                    <div v-if="quiz.has_answered && quiz.user_result" class="mb-6 p-4 rounded-lg bg-muted">
                        <div class="flex items-center gap-3 mb-2">
                            <CheckCircle v-if="quiz.user_result.is_perfect" class="h-6 w-6 text-green-500" />
                            <XCircle v-else class="h-6 w-6 text-orange-500" />
                            <div>
                                <h3 class="font-semibold">
                                    {{ quiz.user_result.correct_count }} de {{ quiz.user_result.total_questions }} corretas
                                </h3>
                                <p v-if="quiz.user_result.is_perfect" class="text-sm text-green-600 dark:text-green-400">
                                    🎉 Perfeito! Você acertou todas!
                                </p>
                                <p v-else class="text-sm text-muted-foreground">
                                    Tempo: {{ formatTime(quiz.user_result.time_taken_seconds) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Perguntas -->
                    <div v-if="!quiz.questions || quiz.questions.length === 0" class="p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border bg-muted">
                        <p class="text-sm text-muted-foreground text-center">Nenhuma pergunta disponível.</p>
                    </div>
                    <div v-else class="space-y-6">
                        <div
                            v-for="(question, index) in quiz.questions"
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
                                                quiz.has_answered
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
                                                v-if="!quiz.has_answered"
                                                type="radio"
                                                :name="`question-${question.id}`"
                                                :value="option"
                                                v-model="answers[question.id]"
                                                class="cursor-pointer"
                                            />
                                            <span class="flex-1">{{ option }}</span>
                                            <CheckCircle
                                                v-if="quiz.has_answered && option === question.correct_answer"
                                                class="h-5 w-5 text-green-500"
                                            />
                                            <XCircle
                                                v-if="quiz.has_answered && question.user_answer === option && option !== question.correct_answer"
                                                class="h-5 w-5 text-red-500"
                                            />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Submit -->
                    <div v-if="!quiz.has_answered" class="mt-6 flex justify-end">
                        <div v-if="!quiz.is_active" class="p-4 rounded-lg bg-muted border border-sidebar-border/70">
                            <p class="text-sm text-muted-foreground">
                                Este quiz não está mais ativo. Você não pode mais respondê-lo.
                            </p>
                        </div>
                        <Button
                            v-else
                            @click="submitQuiz"
                            :disabled="Object.keys(answers).length !== quiz.questions.length || isSubmitting"
                            size="lg"
                        >
                            {{ isSubmitting ? 'Enviando...' : 'Enviar Respostas' }}
                        </Button>
                    </div>
                </div>

                <div class="flex justify-center">
                    <Button variant="outline" @click="router.visit('/quizzes')">
                        Voltar para Lista de Quizzes
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

