<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Image, Trophy, Upload, X, Smile, Heart, MessageSquare, Send, User } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type VoteUser = {
    id: number;
    name: string;
    selected_badge?: UserBadge | null;
};

type VotesByEmoji = Record<string, {
    count: number;
    users: VoteUser[];
}>;

type Comment = {
    id: number;
    content: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
};

type Meme = {
    id: number;
    type: 'image' | 'video' | 'audio';
    media_path: string;
    caption: string | null;
    total_votes: number;
    is_winner: boolean;
    user_vote: string | null;
    votes_by_emoji: VotesByEmoji;
    comments_count: number;
    comments: Comment[];
    user: {
        id: number;
        name: string;
        selected_badge?: UserBadge | null;
    };
};

type HallOfFameEntry = {
    id: number;
    meme: {
        id: number;
        type: 'image' | 'video' | 'audio';
        media_path: string;
        caption: string | null;
        user: {
            id: number;
            name: string;
            selected_badge?: UserBadge | null;
        };
    };
    won_date: string;
    votes_count: number;
};

type Props = {
    week_memes: Meme[];
    hall_of_fame: HallOfFameEntry[];
    week_start: string;
    week_end: string;
};

const page = usePage();
const currentUserId = page.props.auth.user?.id;

const props = defineProps<Props>();

const localMemes = ref<Meme[]>(props.week_memes);

// Observar erros da página
watch(() => page.props.errors, (errors) => {
    if (errors && Object.keys(errors).length > 0) {
        if (errors.media) {
            uploadError.value = Array.isArray(errors.media) ? errors.media[0] : errors.media;
        } else if (errors.caption) {
            uploadError.value = Array.isArray(errors.caption) ? errors.caption[0] : errors.caption;
        }
    }
}, { deep: true });

watch(() => props.week_memes, (newMemes) => {
    localMemes.value = newMemes;
}, { deep: true });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mural de Memes Semanais',
        href: '#',
    },
];

const showUploadModal = ref(false);
const memeFile = ref<File | null>(null);
const memePreview = ref<string | null>(null);
const caption = ref('');
const isUploading = ref(false);
const uploadError = ref<string | null>(null);

const emojis = [
    { emoji: '😄', label: 'Feliz' },
    { emoji: '😂', label: 'Rindo' },
    { emoji: '🔥', label: 'Fogo' },
    { emoji: '❤️', label: 'Amor' },
    { emoji: '👍', label: 'Like' },
    { emoji: '🎉', label: 'Celebração' },
    { emoji: '😍', label: 'Apaixonado' },
    { emoji: '🤣', label: 'Muito Rindo' },
    { emoji: '😱', label: 'Surpreso' },
    { emoji: '💯', label: 'Perfeito' },
    { emoji: '👏', label: 'Palmas' },
    { emoji: '🙌', label: 'Celebração' },
];

const showEmojiModal = ref<number | null>(null);
const showVotesModal = ref<{ memeId: number; emoji: string } | null>(null);
const showComments = ref<Record<number, boolean>>({});
const commentInputs = ref<Record<number, string>>({});

const currentVotesUsers = computed(() => {
    if (!showVotesModal.value) return [];
    const meme = localMemes.value.find(m => m.id === showVotesModal.value!.memeId);
    return meme?.votes_by_emoji[showVotesModal.value.emoji]?.users || [];
});

const handleMemeChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    uploadError.value = null;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        memeFile.value = file;
        
        // Criar preview baseado no tipo de arquivo
        if (file.type.startsWith('image/') || file.type.startsWith('video/') || file.type.startsWith('audio/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                memePreview.value = e.target?.result as string;
            };
            reader.onerror = () => {
                uploadError.value = 'Erro ao carregar preview do arquivo.';
                memePreview.value = null;
            };
            reader.readAsDataURL(file);
        } else {
            uploadError.value = 'Tipo de arquivo não suportado.';
            memeFile.value = null;
            memePreview.value = null;
        }
    }
};

const removeMeme = () => {
    memeFile.value = null;
    memePreview.value = null;
    uploadError.value = null;
    const input = document.getElementById('meme') as HTMLInputElement;
    if (input) {
        input.value = '';
    }
};

const uploadMeme = () => {
    if (!memeFile.value) return;

    isUploading.value = true;
    uploadError.value = null;
    const formData = new FormData();
    formData.append('media', memeFile.value);
    if (caption.value) {
        formData.append('caption', caption.value);
    }

    router.post('/memes', formData, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            showUploadModal.value = false;
            memeFile.value = null;
            memePreview.value = null;
            caption.value = '';
            isUploading.value = false;
            uploadError.value = null;
            // Resetar o input file
            const input = document.getElementById('meme') as HTMLInputElement;
            if (input) {
                input.value = '';
            }
        },
        onError: (errors) => {
            isUploading.value = false;
            console.error('Erro no upload:', errors);
            // Capturar erros de validação
            if (errors && typeof errors === 'object') {
                if (errors.media) {
                    uploadError.value = Array.isArray(errors.media) ? errors.media[0] : errors.media;
                } else if (errors.caption) {
                    uploadError.value = Array.isArray(errors.caption) ? errors.caption[0] : errors.caption;
                } else if (errors.message) {
                    uploadError.value = errors.message;
                } else {
                    // Pegar o primeiro erro disponível
                    const errorKeys = Object.keys(errors);
                    if (errorKeys.length > 0) {
                        const firstError = errors[errorKeys[0]];
                        uploadError.value = Array.isArray(firstError) ? firstError[0] : String(firstError);
                    } else {
                        uploadError.value = 'Erro ao fazer upload. Verifique o arquivo e tente novamente.';
                    }
                }
            } else if (typeof errors === 'string') {
                uploadError.value = errors;
            } else {
                uploadError.value = 'Erro ao fazer upload. Verifique o arquivo e tente novamente.';
            }
        },
    });
};

const voteMeme = (meme: Meme, emoji: string) => {
    showEmojiModal.value = null;
    fetch(`/memes/${meme.id}/vote`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ emoji }),
    })
        .then((response) => response.json())
        .then((data) => {
            // Atualizar meme localmente
            const memeIndex = localMemes.value.findIndex((m) => m.id === meme.id);
            if (memeIndex !== -1) {
                localMemes.value[memeIndex].total_votes = data.total_votes;
                localMemes.value[memeIndex].user_vote = data.user_vote;
                localMemes.value[memeIndex].votes_by_emoji = data.votes_by_emoji;
                localMemes.value[memeIndex].is_winner = data.is_winner || false;
            }
            
            // Atualizar is_winner de todos os memes (remover de todos e adicionar ao com mais votos)
            localMemes.value.forEach(m => {
                m.is_winner = false;
            });
            const winner = localMemes.value.sort((a, b) => b.total_votes - a.total_votes)[0];
            if (winner && winner.total_votes > 0) {
                winner.is_winner = true;
            }
        })
        .catch((error) => {
            console.error('Erro ao votar:', error);
        });
};

const toggleComments = (memeId: number) => {
    showComments.value[memeId] = !showComments.value[memeId];
};

const addComment = (meme: Meme) => {
    const content = commentInputs.value[meme.id]?.trim();
    if (!content || content.length < 3) return;

    router.post(`/memes/${meme.id}/comments`, { content }, {
        preserveScroll: true,
        onSuccess: (page) => {
            // Recarregar apenas os memes para atualizar os comentários
            router.reload({ only: ['week_memes'] });
            commentInputs.value[meme.id] = '';
        },
        onError: (errors) => {
            console.error('Erro ao comentar:', errors);
        },
    });
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Mural de Memes Semanais" />
        <div class="flex flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-6xl">
                <div class="mb-6 flex items-center justify-between">
                    <Heading
                        title="The Week Mamira"
                        :description="`Memes da semana (${week_start} - ${week_end})`"
                    />
                    <Button @click="showUploadModal = true">
                        <Upload class="h-4 w-4 mr-2" />
                        Postar Meme
                    </Button>
                </div>

                <!-- Memes da Semana -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Memes da Semana</h2>
                    <div v-if="week_memes.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                        <Image class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                        <h3 class="text-lg font-semibold mb-2">Nenhum meme ainda esta semana</h3>
                        <p class="text-muted-foreground mb-4">
                            Seja o primeiro a postar um meme!
                        </p>
                        <Button @click="showUploadModal = true">
                            <Upload class="h-4 w-4 mr-2" />
                            Postar Meme
                        </Button>
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="meme in localMemes"
                            :key="meme.id"
                            :class="[
                                'rounded-xl border p-4 bg-background',
                                meme.is_winner ? 'border-yellow-500 border-2' : 'border-sidebar-border/70 dark:border-sidebar-border',
                            ]"
                        >
                            <div class="relative mb-3">
                                <img
                                    v-if="meme.type === 'image'"
                                    :src="meme.media_path"
                                    :alt="meme.caption || 'Meme'"
                                    class="w-full h-64 object-cover rounded-lg"
                                />
                                <video
                                    v-else-if="meme.type === 'video'"
                                    :src="meme.media_path"
                                    controls
                                    class="w-full h-64 object-cover rounded-lg"
                                >
                                    Seu navegador não suporta vídeos.
                                </video>
                                <audio
                                    v-else-if="meme.type === 'audio'"
                                    :src="meme.media_path"
                                    controls
                                    class="w-full"
                                >
                                    Seu navegador não suporta áudio.
                                </audio>
                                <div v-if="meme.is_winner" class="absolute top-2 right-2">
                                    <div class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                                        <Trophy class="h-3 w-3" />
                                        Vencedor
                                    </div>
                                </div>
                            </div>
                            <div v-if="meme.caption" class="mb-3">
                                <p class="text-sm text-foreground">{{ meme.caption }}</p>
                            </div>
                            <div class="flex items-center gap-2 mb-3 text-xs text-muted-foreground">
                                <span>{{ meme.user.name }}</span>
                                <UserBadges v-if="meme.user.selected_badge" :badge="meme.user.selected_badge" />
                            </div>
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-semibold">{{ meme.total_votes }} votos</p>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="showEmojiModal = showEmojiModal === meme.id ? null : meme.id"
                                    >
                                        <Smile class="h-4 w-4 mr-2" />
                                        {{ meme.user_vote || 'Votar' }}
                                    </Button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <Button
                                        v-for="(voteData, emoji) in meme.votes_by_emoji"
                                        :key="emoji"
                                        variant="ghost"
                                        size="sm"
                                        @click="showVotesModal = { memeId: meme.id, emoji }"
                                        class="text-lg hover:bg-muted"
                                    >
                                        {{ emoji }}
                                        <span class="ml-1 text-xs">{{ voteData.count }}</span>
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Comentários -->
                            <div class="border-t border-sidebar-border/70 dark:border-sidebar-border pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="toggleComments(meme.id)"
                                    >
                                        <MessageSquare class="h-4 w-4 mr-2" />
                                        {{ meme.comments_count }} comentário{{ meme.comments_count !== 1 ? 's' : '' }}
                                    </Button>
                                </div>
                                
                                <div v-if="showComments[meme.id]" class="space-y-3">
                                    <div v-if="meme.comments.length === 0" class="text-sm text-muted-foreground text-center py-2">
                                        Nenhum comentário ainda
                                    </div>
                                    <div v-else class="space-y-2 max-h-64 overflow-y-auto">
                                        <div
                                            v-for="comment in meme.comments"
                                            :key="comment.id"
                                            class="p-2 rounded-lg bg-muted/50"
                                        >
                                            <div class="flex items-start gap-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-sm font-semibold">{{ comment.user.name }}</span>
                                                        <UserBadges v-if="comment.user.selected_badge" :badge="comment.user.selected_badge" />
                                                        <span class="text-xs text-muted-foreground">{{ formatTimeAgo(comment.created_at) }}</span>
                                                    </div>
                                                    <p class="text-sm text-foreground">{{ comment.content }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <Input
                                            v-model="commentInputs[meme.id]"
                                            placeholder="Escreva um comentário..."
                                            @keyup.enter="addComment(meme)"
                                            class="flex-1"
                                        />
                                        <Button
                                            size="sm"
                                            @click="addComment(meme)"
                                            :disabled="!commentInputs[meme.id]?.trim()"
                                        >
                                            <Send class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hall da Fama -->
                <div>
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Trophy class="h-5 w-5 text-yellow-500" />
                        Hall da Fama
                    </h2>
                    <div v-if="hall_of_fame.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                        <Trophy class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                        <h3 class="text-lg font-semibold mb-2">Nenhum meme no Hall da Fama ainda</h3>
                        <p class="text-muted-foreground">
                            Os memes vencedores aparecerão aqui!
                        </p>
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div
                            v-for="entry in hall_of_fame"
                            :key="entry.id"
                            class="rounded-xl border border-yellow-500/50 bg-background p-4"
                        >
                            <div class="relative mb-3">
                                <img
                                    v-if="entry.meme.type === 'image'"
                                    :src="entry.meme.media_path"
                                    :alt="entry.meme.caption || 'Meme'"
                                    class="w-full h-48 object-cover rounded-lg"
                                />
                                <video
                                    v-else-if="entry.meme.type === 'video'"
                                    :src="entry.meme.media_path"
                                    controls
                                    class="w-full h-48 object-cover rounded-lg"
                                >
                                    Seu navegador não suporta vídeos.
                                </video>
                                <audio
                                    v-else-if="entry.meme.type === 'audio'"
                                    :src="entry.meme.media_path"
                                    controls
                                    class="w-full"
                                >
                                    Seu navegador não suporta áudio.
                                </audio>
                                <div class="absolute top-2 right-2">
                                    <div class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                        🏆
                                    </div>
                                </div>
                            </div>
                            <div v-if="entry.meme.caption" class="mb-2">
                                <p class="text-xs text-foreground line-clamp-2">{{ entry.meme.caption }}</p>
                            </div>
                            <div class="flex items-center gap-2 mb-2 text-xs text-muted-foreground">
                                <span>{{ entry.meme.user.name }}</span>
                                <UserBadges v-if="entry.meme.user.selected_badge" :badge="entry.meme.user.selected_badge" />
                            </div>
                            <div class="text-xs text-muted-foreground">
                                <p>{{ entry.votes_count }} votos</p>
                                <p>{{ new Date(entry.won_date).toLocaleDateString('pt-BR') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <div
            v-if="showUploadModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="showUploadModal = false"
        >
            <div class="bg-background rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Postar Meme</h3>
                    <Button variant="ghost" size="sm" @click="showUploadModal = false">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
                <div class="space-y-4">
                    <div>
                        <Label for="meme">Meme (Imagem, Vídeo ou Áudio)</Label>
                        <Input
                            id="meme"
                            type="file"
                            accept="image/*,video/*,audio/*"
                            @change="handleMemeChange"
                            class="mt-1"
                        />
                        <p class="text-xs text-muted-foreground mt-1">
                            Formatos aceitos: Imagens (JPEG, PNG, JPG, GIF, WEBP), Vídeos (MP4, AVI, MOV, WEBM), Áudios (MP3, WAV, OGG, M4A). Tamanho máximo: 20MB
                        </p>
                        <div v-if="uploadError" class="mt-2 p-2 bg-destructive/10 border border-destructive/20 rounded text-sm text-destructive">
                            {{ uploadError }}
                        </div>
                        <div v-if="memePreview && memeFile" class="mt-4 relative w-full">
                            <div class="relative w-full flex flex-col items-center">
                                <img
                                    v-if="memeFile.type.startsWith('image/')"
                                    :src="memePreview"
                                    alt="Preview"
                                    class="max-w-full h-auto max-h-64 rounded-lg object-contain"
                                />
                                <video
                                    v-else-if="memeFile.type.startsWith('video/')"
                                    :src="memePreview"
                                    controls
                                    class="max-w-full h-auto max-h-64 rounded-lg"
                                >
                                    Seu navegador não suporta vídeos.
                                </video>
                                <div
                                    v-else-if="memeFile.type.startsWith('audio/')"
                                    class="w-full p-4 bg-muted rounded-lg"
                                >
                                    <p class="text-sm text-muted-foreground mb-2">Preview de áudio:</p>
                                    <audio
                                        :src="memePreview"
                                        controls
                                        class="w-full"
                                    >
                                        Seu navegador não suporta áudio.
                                    </audio>
                                </div>
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="sm"
                                    class="absolute top-2 right-2"
                                    @click="removeMeme"
                                >
                                    <X class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <Label for="caption">Legenda (opcional)</Label>
                        <Input
                            id="caption"
                            v-model="caption"
                            type="text"
                            placeholder="Adicione uma legenda..."
                            class="mt-1"
                        />
                    </div>
                    <div class="flex gap-2 justify-end">
                        <Button variant="outline" @click="showUploadModal = false">
                            Cancelar
                        </Button>
                        <Button @click="uploadMeme" :disabled="!memeFile || isUploading">
                            <Upload class="h-4 w-4 mr-2" />
                            {{ isUploading ? 'Postando...' : 'Postar' }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Emojis -->
        <div
            v-if="showEmojiModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="showEmojiModal = null"
        >
            <div class="bg-background rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Escolha um emoji</h3>
                    <Button variant="ghost" size="sm" @click="showEmojiModal = null">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
                <div class="grid grid-cols-4 gap-3">
                    <Button
                        v-for="emojiOption in emojis"
                        :key="emojiOption.emoji"
                        variant="outline"
                        size="lg"
                        @click="voteMeme(localMemes.find(m => m.id === showEmojiModal)!, emojiOption.emoji)"
                        class="text-3xl h-16 hover:bg-primary hover:text-primary-foreground"
                        :title="emojiOption.label"
                    >
                        {{ emojiOption.emoji }}
                    </Button>
                </div>
            </div>
        </div>

        <!-- Modal de Votos -->
        <div
            v-if="showVotesModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            @click.self="showVotesModal = null"
        >
            <div class="bg-background rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ showVotesModal?.emoji }}</span>
                        <h3 class="text-lg font-semibold">
                            Quem votou com {{ showVotesModal?.emoji }}
                        </h3>
                    </div>
                    <Button variant="ghost" size="sm" @click="showVotesModal = null">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
                <div v-if="currentVotesUsers.length > 0" class="space-y-2">
                    <div
                        v-for="user in currentVotesUsers"
                        :key="user.id"
                        class="flex items-center gap-2 p-2 rounded-lg hover:bg-muted"
                    >
                        <User class="h-4 w-4 text-muted-foreground" />
                        <span class="font-medium">{{ user.name }}</span>
                        <UserBadges v-if="user.selected_badge" :badge="user.selected_badge" />
                    </div>
                </div>
                <div v-else class="text-center py-8 text-muted-foreground">
                    Ninguém votou com este emoji ainda
                </div>
            </div>
        </div>
    </AppLayout>
</template>

