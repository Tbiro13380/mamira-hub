<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { HelpCircle, Plus, X, Save } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Criar Quiz',
        href: '#',
    },
];

const title = ref('');
const description = ref('');
const questions = ref([
    {
        question: '',
        correct_answer: '',
        options: ['', '', '', ''],
    },
    {
        question: '',
        correct_answer: '',
        options: ['', '', '', ''],
    },
    {
        question: '',
        correct_answer: '',
        options: ['', '', '', ''],
    },
    {
        question: '',
        correct_answer: '',
        options: ['', '', '', ''],
    },
    {
        question: '',
        correct_answer: '',
        options: ['', '', '', ''],
    },
]);

const isSubmitting = ref(false);

const addOption = (questionIndex: number) => {
    if (questions.value[questionIndex].options.length < 6) {
        questions.value[questionIndex].options.push('');
    }
};

const removeOption = (questionIndex: number, optionIndex: number) => {
    if (questions.value[questionIndex].options.length > 2) {
        questions.value[questionIndex].options.splice(optionIndex, 1);
    }
};

const submitQuiz = () => {
    // Validação
    if (!title.value.trim()) {
        alert('Por favor, preencha o título do quiz.');
        return;
    }

    for (let i = 0; i < questions.value.length; i++) {
        const q = questions.value[i];
        if (!q.question.trim()) {
            alert(`Por favor, preencha a pergunta ${i + 1}.`);
            return;
        }
        if (!q.correct_answer.trim()) {
            alert(`Por favor, selecione a resposta correta da pergunta ${i + 1}.`);
            return;
        }
        const validOptions = q.options.filter(opt => opt.trim());
        if (validOptions.length < 2) {
            alert(`A pergunta ${i + 1} precisa de pelo menos 2 opções.`);
            return;
        }
        if (!validOptions.includes(q.correct_answer)) {
            alert(`A resposta correta da pergunta ${i + 1} deve estar entre as opções.`);
            return;
        }
    }

    isSubmitting.value = true;

    const formattedQuestions = questions.value.map((q, index) => ({
        question: q.question.trim(),
        correct_answer: q.correct_answer.trim(),
        options: q.options.filter(opt => opt.trim()),
    }));

    router.post('/quizzes', {
        title: title.value.trim(),
        description: description.value.trim() || null,
        questions: formattedQuestions,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Criar Quiz" />
        <div class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6">
                    <Heading
                        title="Criar Quiz Semanal"
                        description="Crie 5 perguntas sobre fatos do Mamira-San"
                    />
                </div>

                <form @submit.prevent="submitQuiz" class="space-y-6">
                    <!-- Informações do Quiz -->
                    <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                        <h2 class="text-lg font-semibold mb-4">Informações do Quiz</h2>
                        <div class="space-y-4">
                            <div>
                                <Label for="title">Título *</Label>
                                <Input
                                    id="title"
                                    v-model="title"
                                    type="text"
                                    placeholder="Ex: Você conhece o Mamira-San? - Semana 1"
                                    required
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="description">Descrição (opcional)</Label>
                                <Input
                                    id="description"
                                    v-model="description"
                                    type="text"
                                    placeholder="Adicione uma descrição..."
                                    class="mt-1"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Perguntas -->
                    <div class="space-y-6">
                        <div
                            v-for="(question, questionIndex) in questions"
                            :key="questionIndex"
                            class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6"
                        >
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary">
                                    {{ questionIndex + 1 }}
                                </div>
                                <h3 class="text-lg font-semibold">Pergunta {{ questionIndex + 1 }}</h3>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <Label :for="`question-${questionIndex}`">Pergunta *</Label>
                                    <Input
                                        :id="`question-${questionIndex}`"
                                        v-model="question.question"
                                        type="text"
                                        placeholder="Ex: Qual é a comida favorita do Mamira?"
                                        required
                                        class="mt-1"
                                    />
                                </div>

                                <div>
                                    <Label>Opções de Resposta *</Label>
                                    <div class="mt-2 space-y-2">
                                        <div
                                            v-for="(option, optionIndex) in question.options"
                                            :key="optionIndex"
                                            class="flex items-center gap-2"
                                        >
                                            <Input
                                                v-model="question.options[optionIndex]"
                                                type="text"
                                                :placeholder="`Opção ${optionIndex + 1}`"
                                                class="flex-1"
                                            />
                                            <Button
                                                v-if="question.options.length > 2"
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                @click="removeOption(questionIndex, optionIndex)"
                                            >
                                                <X class="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <Button
                                            v-if="question.options.length < 6"
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            @click="addOption(questionIndex)"
                                        >
                                            <Plus class="h-4 w-4 mr-2" />
                                            Adicionar Opção
                                        </Button>
                                    </div>
                                </div>

                                <div>
                                    <Label :for="`correct-${questionIndex}`">Resposta Correta *</Label>
                                    <select
                                        :id="`correct-${questionIndex}`"
                                        v-model="question.correct_answer"
                                        required
                                        class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                    >
                                        <option value="">Selecione a resposta correta</option>
                                        <option
                                            v-for="(option, optionIndex) in question.options"
                                            :key="optionIndex"
                                            :value="option"
                                            :disabled="!option.trim()"
                                        >
                                            {{ option || `Opção ${optionIndex + 1}` }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Submit -->
                    <div class="flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.visit('/quizzes')"
                        >
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="isSubmitting">
                            <Save class="h-4 w-4 mr-2" />
                            {{ isSubmitting ? 'Criando...' : 'Criar Quiz' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

