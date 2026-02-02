<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BarChart3, Droplet, Type, TrendingUp } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

type Props = {
    total_words: number;
    total_tears: number;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Estatísticas',
        href: '#',
    },
];

const localTears = ref(props.total_tears);
const isAddingTear = ref(false);

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

const formatNumber = (num: number): string => {
    return new Intl.NumberFormat('pt-BR').format(num);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Estatísticas" />
        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6">
                    <Heading
                        title="Contador de Impacto"
                        description="Estatísticas globais da comunidade"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total de Palavras -->
                    <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-lg bg-primary/10">
                                    <Type class="h-6 w-6 text-primary" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground">Total de Palavras</h3>
                                    <p class="text-3xl font-bold text-foreground mt-1">
                                        {{ formatNumber(total_words) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Palavras escritas para o Mamira-San
                        </p>
                    </div>

                    <!-- Lágrimas Derramadas -->
                    <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-lg bg-destructive/10">
                                    <Droplet class="h-6 w-6 text-destructive" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground">Lágrimas Derramadas</h3>
                                    <p class="text-3xl font-bold text-foreground mt-1">
                                        {{ formatNumber(localTears) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-muted-foreground">
                                Cliques infinitos pela piada
                            </p>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="addTear"
                                :disabled="isAddingTear"
                            >
                                <Droplet class="h-4 w-4 mr-2" />
                                {{ isAddingTear ? 'Derramando...' : 'Derramar Lágrima' }}
                            </Button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <TrendingUp class="h-5 w-5 text-muted-foreground" />
                        <h3 class="text-lg font-semibold">Sobre as Estatísticas</h3>
                    </div>
                    <div class="space-y-2 text-sm text-muted-foreground">
                        <p>
                            <strong class="text-foreground">Total de Palavras:</strong> Soma de todas as palavras escritas em todas as cartas enviadas para o Mamira-San.
                        </p>
                        <p>
                            <strong class="text-foreground">Lágrimas Derramadas:</strong> Um contador divertido que todos podem clicar infinitamente. Cada clique adiciona uma lágrima ao contador global!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

