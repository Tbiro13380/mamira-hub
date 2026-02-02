<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Image, Trophy, Upload, X, Smile, Heart, Flame, ThumbsUp, PartyPopper } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
};

type Meme = {
    id: number;
    type: 'image' | 'video';
    media_path: string;
    caption: string | null;
    total_votes: number;
    is_winner: boolean;
    user_vote: string | null;
    votes_by_emoji: Record<string, number>;
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
        type: 'image' | 'video';
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
    today_memes: Meme[];
    hall_of_fame: HallOfFameEntry[];
    today_date: string;
};

const page = usePage();
const currentUserId = page.props.auth.user?.id;

const props = defineProps<Props>();

const localMemes = ref<Meme[]>(props.today_memes);

watch(() => props.today_memes, (newMemes) => {
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

const emojis = [
    { emoji: '😄', label: 'Feliz' },
    { emoji: '😂', label: 'Rindo' },
    { emoji: '🔥', label: 'Fogo' },
    { emoji: '❤️', label: 'Amor' },
    { emoji: '👍', label: 'Like' },
    { emoji: '🎉', label: 'Celebração' },
];

const handleMemeChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        memeFile.value = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            memePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(memeFile.value);
    }
};

const removeMeme = () => {
    memeFile.value = null;
    memePreview.value = null;
};

const uploadMeme = () => {
    if (!memeFile.value) return;

    isUploading.value = true;
    const formData = new FormData();
    formData.append('media', memeFile.value);
    if (caption.value) {
        formData.append('caption', caption.value);
    }

    router.post('/memes', formData, {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            memeFile.value = null;
            memePreview.value = null;
            caption.value = '';
            isUploading.value = false;
        },
        onError: () => {
            isUploading.value = false;
        },
    });
};

const voteMeme = (meme: Meme, emoji: string) => {
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
            }
        })
        .catch((error) => {
            console.error('Erro ao votar:', error);
        });
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
                        :description="`Memes de hoje (${today_date})`"
                    />
                    <Button @click="showUploadModal = true">
                        <Upload class="h-4 w-4 mr-2" />
                        Postar Meme
                    </Button>
                </div>

                <!-- Memes de Hoje -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Memes de Hoje</h2>
                    <div v-if="today_memes.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                        <Image class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                        <h3 class="text-lg font-semibold mb-2">Nenhum meme ainda hoje</h3>
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
                                    v-else
                                    :src="meme.media_path"
                                    controls
                                    class="w-full h-64 object-cover rounded-lg"
                                >
                                    Seu navegador não suporta vídeos.
                                </video>
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
                                <p class="text-sm font-semibold mb-2">{{ meme.total_votes }} votos</p>
                                <div class="flex flex-wrap gap-2">
                                    <Button
                                        v-for="emojiOption in emojis"
                                        :key="emojiOption.emoji"
                                        :variant="meme.user_vote === emojiOption.emoji ? 'default' : 'outline'"
                                        size="sm"
                                        @click="voteMeme(meme, emojiOption.emoji)"
                                        class="text-lg"
                                    >
                                        {{ emojiOption.emoji }}
                                        <span v-if="meme.votes_by_emoji[emojiOption.emoji]" class="ml-1 text-xs">
                                            {{ meme.votes_by_emoji[emojiOption.emoji] }}
                                        </span>
                                    </Button>
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
                                    v-else
                                    :src="entry.meme.media_path"
                                    controls
                                    class="w-full h-48 object-cover rounded-lg"
                                >
                                    Seu navegador não suporta vídeos.
                                </video>
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
                        <Label for="meme">Meme (Imagem ou Vídeo)</Label>
                        <Input
                            id="meme"
                            type="file"
                            accept="image/*,video/*"
                            @change="handleMemeChange"
                            class="mt-1"
                        />
                        <p class="text-xs text-muted-foreground mt-1">
                            Formatos aceitos: JPEG, PNG, JPG, GIF, WEBP, MP4, AVI, MOV, WEBM. Tamanho máximo: 20MB
                        </p>
                        <div v-if="memePreview" class="mt-4 relative inline-block">
                            <img
                                :src="memePreview"
                                alt="Preview"
                                class="max-w-full h-auto max-h-64 rounded-lg object-contain"
                            />
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
    </AppLayout>
</template>

