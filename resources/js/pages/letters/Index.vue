<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Lock, Calendar, User, Heart, MessageSquare, Send, Trash2 } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create } from '@/routes/letters/index';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const { getInitials } = useInitials();

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type Comment = {
    id: number;
    content: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        avatar?: string | null;
        selected_badge?: UserBadge | null;
    };
};

type Letter = {
    id: number;
    title: string | null;
    content: string;
    image: string | null;
    is_private: boolean;
    created_at: string;
    user_id: number;
    user: {
        id: number;
        name: string;
        avatar?: string | null;
        selected_badge?: UserBadge | null;
    };
    likes_count: number;
    is_liked: boolean;
    comments_count: number;
    comments: Comment[];
};

type Props = {
    letters: Letter[];
};

const page = usePage();
const currentUserId = page.props.auth.user?.id;

const props = defineProps<Props>();

const localLetters = ref<Letter[]>(props.letters);
const commentInputs = ref<Record<number, string>>({});
const showComments = ref<Record<number, boolean>>({});

const toggleLike = (letter: Letter) => {
    const letterIndex = localLetters.value.findIndex((l) => l.id === letter.id);
    if (letterIndex === -1) return;

    const currentLetter = localLetters.value[letterIndex];
    const wasLiked = currentLetter.is_liked;

    localLetters.value[letterIndex] = {
        ...currentLetter,
        is_liked: !wasLiked,
        likes_count: wasLiked ? currentLetter.likes_count - 1 : currentLetter.likes_count + 1,
    };

    fetch(`/cartas/${letter.id}/like`, {
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
            localLetters.value[letterIndex] = {
                ...currentLetter,
                is_liked: data.liked,
                likes_count: data.likes_count,
            };
        })
        .catch(() => {
            localLetters.value[letterIndex] = currentLetter;
        });
};

const toggleComments = (letterId: number) => {
    showComments.value[letterId] = !showComments.value[letterId];
};

const addComment = (letter: Letter) => {
    const content = commentInputs.value[letter.id]?.trim();
    if (!content || content.length < 3) return;

    const letterIndex = localLetters.value.findIndex((l) => l.id === letter.id);
    if (letterIndex === -1) return;

    const currentLetter = localLetters.value[letterIndex];

    fetch(`/cartas/${letter.id}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ content }),
    })
        .then((response) => response.json())
        .then((data) => {
            localLetters.value[letterIndex] = {
                ...currentLetter,
                comments: [...(currentLetter.comments || []), data.comment],
                comments_count: data.comments_count,
            };
            commentInputs.value[letter.id] = '';
        })
        .catch((error) => {
            console.error('Erro ao adicionar comentário:', error);
        });
};

const deleteLetter = (letter: Letter) => {
    if (!confirm('Tem certeza que deseja excluir esta carta? Esta ação não pode ser desfeita.')) {
        return;
    }

    router.delete(`/cartas/${letter.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            const letterIndex = localLetters.value.findIndex((l) => l.id === letter.id);
            if (letterIndex !== -1) {
                localLetters.value.splice(letterIndex, 1);
            }
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: dashboard().url,
    },
    {
        title: 'Cartas',
        href: '#',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Cartas para o Mamira-San" />

        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <Heading
                            title="Cartas para o Mamira-San"
                            description="Todas as cartas escritas pela comunidade"
                        />
                    </div>
                    <Button as-child class="shrink-0">
                        <Link :href="create().url">
                            <Mail class="mr-2 h-4 w-4" />
                            Escrever Nova Carta
                        </Link>
                    </Button>
                </div>

                <div v-if="letters.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <Mail class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhuma carta ainda</h3>
                    <p class="text-muted-foreground mb-6">
                        Ainda não há cartas publicadas para o Mamira-San.
                    </p>
                    <Button as-child>
                        <Link :href="create().url">
                            Escrever Primeira Carta
                        </Link>
                    </Button>
                </div>

                <div v-else class="grid gap-4">
                    <div
                        v-for="letter in localLetters"
                        :key="letter.id"
                        class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-lg font-semibold">
                                        {{ letter.title || 'Sem título' }}
                                    </h3>
                                    <Lock
                                        v-if="letter.is_private"
                                        class="h-4 w-4 text-muted-foreground"
                                        title="Carta privada"
                                    />
                                </div>
                                <div class="flex items-center gap-4 text-sm text-muted-foreground flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <Avatar class="h-6 w-6">
                                            <AvatarImage v-if="letter.user.avatar" :src="letter.user.avatar" :alt="letter.user.name" />
                                            <AvatarFallback class="rounded-full text-xs">
                                                {{ getInitials(letter.user.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <span>{{ letter.user.name }}</span>
                                        <UserBadges v-if="letter.user.selected_badge" :badge="letter.user.selected_badge" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Calendar class="h-4 w-4" />
                                        <span>
                                            {{ new Date(letter.created_at).toLocaleDateString('pt-BR', {
                                                day: '2-digit',
                                                month: 'long',
                                                year: 'numeric',
                                            }) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <Button
                                v-if="currentUserId && letter.user_id === currentUserId"
                                variant="ghost"
                                size="sm"
                                @click="deleteLetter(letter)"
                                class="text-destructive hover:text-destructive hover:bg-destructive/10 shrink-0"
                                title="Excluir carta"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                        <div v-if="letter.image" class="mb-4">
                            <img
                                :src="letter.image"
                                :alt="letter.title || 'Imagem da carta'"
                                class="w-full rounded-lg object-cover max-h-96"
                            />
                        </div>
                        <p class="text-muted-foreground line-clamp-3 mb-4">
                            {{ letter.content }}
                        </p>
                        <div class="flex items-center gap-4 pt-4 border-t border-sidebar-border/70">
                            <Button
                                variant="ghost"
                                size="sm"
                                @click="toggleLike(letter)"
                                :class="{
                                    'text-red-500 hover:text-red-600': letter.is_liked,
                                    'text-muted-foreground hover:text-foreground': !letter.is_liked,
                                }"
                            >
                                <Heart
                                    :class="[
                                        'h-4 w-4 mr-2',
                                        letter.is_liked ? 'fill-current' : '',
                                    ]"
                                />
                                <span>{{ letter.likes_count }}</span>
                            </Button>
                            <Button
                                variant="ghost"
                                size="sm"
                                @click="toggleComments(letter.id)"
                                class="text-muted-foreground hover:text-foreground"
                            >
                                <MessageSquare class="h-4 w-4 mr-2" />
                                <span>{{ letter.comments_count || 0 }}</span>
                            </Button>
                        </div>

                        <div v-if="showComments[letter.id]" class="mt-4 pt-4 border-t border-sidebar-border/70">
                            <div v-if="letter.comments && letter.comments.length > 0" class="mb-4 space-y-3">
                                <div
                                    v-for="comment in letter.comments"
                                    :key="comment.id"
                                    class="flex gap-3 p-3 rounded-lg bg-muted/50"
                                >
                                    <Avatar class="h-8 w-8 shrink-0">
                                        <AvatarImage v-if="comment.user.avatar" :src="comment.user.avatar" :alt="comment.user.name" />
                                        <AvatarFallback class="rounded-full text-xs">
                                            {{ getInitials(comment.user.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span class="text-sm font-medium">{{ comment.user.name }}</span>
                                            <UserBadges v-if="comment.user.selected_badge" :badge="comment.user.selected_badge" />
                                            <span class="text-xs text-muted-foreground">
                                                {{ new Date(comment.created_at).toLocaleDateString('pt-BR', {
                                                    day: '2-digit',
                                                    month: 'short',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                }) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-foreground">{{ comment.content }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <Input
                                    v-model="commentInputs[letter.id]"
                                    placeholder="Escreva um comentário..."
                                    class="flex-1"
                                    @keyup.enter="addComment(letter)"
                                />
                                <Button
                                    size="sm"
                                    @click="addComment(letter)"
                                    :disabled="!commentInputs[letter.id]?.trim() || commentInputs[letter.id]?.trim().length < 3"
                                >
                                    <Send class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

