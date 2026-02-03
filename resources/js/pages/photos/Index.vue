<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Image, Upload, X, Trash2, User } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import UserBadges from '@/components/UserBadges.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

type UserBadge = {
    id: number;
    name: string;
    icon: string | null;
    color: string;
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

type Props = {
    photos: Photo[];
};

const page = usePage();
const currentUserId = page.props.auth.user?.id;

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mural de Fotos',
        href: '#',
    },
];

const showUploadModal = ref(false);
const photoFile = ref<File | null>(null);
const photoPreview = ref<string | null>(null);
const caption = ref('');
const isUploading = ref(false);
const selectedImage = ref<string | null>(null);
const isImageModalOpen = ref(false);

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        photoFile.value = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(photoFile.value);
    }
};

const removePhoto = () => {
    photoFile.value = null;
    photoPreview.value = null;
};

const uploadPhoto = () => {
    if (!photoFile.value) return;

    isUploading.value = true;
    const formData = new FormData();
    formData.append('photo', photoFile.value);
    if (caption.value) {
        formData.append('caption', caption.value);
    }

    router.post('/fotos', formData, {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            photoFile.value = null;
            photoPreview.value = null;
            caption.value = '';
            isUploading.value = false;
        },
        onError: () => {
            isUploading.value = false;
        },
    });
};

const deletePhoto = (photo: Photo) => {
    if (!confirm('Tem certeza que deseja excluir esta foto?')) {
        return;
    }

    router.delete(`/fotos/${photo.id}`, {
        preserveScroll: true,
    });
};

const openImageModal = (imageUrl: string) => {
    selectedImage.value = imageUrl;
    isImageModalOpen.value = true;
};

const closeImageModal = () => {
    isImageModalOpen.value = false;
    selectedImage.value = null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Mural de Fotos" />
        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-6xl">
                <div class="mb-6 flex items-center justify-between">
                    <Heading
                        title="Mural de Fotos"
                        description="Galeria de memórias do Mamira-San"
                    />
                    <Button @click="showUploadModal = true">
                        <Upload class="h-4 w-4 mr-2" />
                        Enviar Foto
                    </Button>
                </div>

                <div v-if="photos.length === 0" class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-12 text-center">
                    <Image class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold mb-2">Nenhuma foto ainda</h3>
                    <p class="text-muted-foreground mb-4">
                        Seja o primeiro a compartilhar uma foto!
                    </p>
                    <Button @click="showUploadModal = true">
                        <Upload class="h-4 w-4 mr-2" />
                        Enviar Foto
                    </Button>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div
                        v-for="photo in photos"
                        :key="photo.id"
                        class="group relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background overflow-hidden"
                    >
                        <img
                            :src="photo.path"
                            :alt="photo.caption || 'Foto'"
                            class="w-full h-64 object-cover cursor-pointer hover:opacity-90 transition-opacity"
                            @click="openImageModal(photo.path)"
                        />
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100 pointer-events-none">
                            <Button
                                v-if="photo.user.id === currentUserId"
                                variant="destructive"
                                size="sm"
                                @click.stop="deletePhoto(photo)"
                                class="pointer-events-auto"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                        <div v-if="photo.caption" class="p-3">
                            <p class="text-sm text-foreground mb-1 whitespace-pre-wrap break-words">{{ photo.caption }}</p>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                <User class="h-3 w-3" />
                                <span>{{ photo.user.name }}</span>
                                <UserBadges v-if="photo.user.selected_badge" :badge="photo.user.selected_badge" />
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
                    <h3 class="text-lg font-semibold">Enviar Foto</h3>
                    <Button variant="ghost" size="sm" @click="showUploadModal = false">
                        <X class="h-4 w-4" />
                    </Button>
                </div>
                <div class="space-y-4">
                    <div>
                        <Label for="photo">Foto</Label>
                        <Input
                            id="photo"
                            type="file"
                            accept="image/*"
                            @change="handlePhotoChange"
                            class="mt-1"
                        />
                        <p class="text-xs text-muted-foreground mt-1">
                            Formatos aceitos: JPEG, PNG, JPG, GIF, WEBP. Tamanho máximo: 5MB
                        </p>
                        <div v-if="photoPreview" class="mt-4 relative inline-block">
                            <img
                                :src="photoPreview"
                                alt="Preview"
                                class="max-w-full h-auto max-h-64 rounded-lg object-contain"
                            />
                            <Button
                                type="button"
                                variant="destructive"
                                size="sm"
                                class="absolute top-2 right-2"
                                @click="removePhoto"
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
                        <Button @click="uploadPhoto" :disabled="!photoFile || isUploading">
                            <Upload class="h-4 w-4 mr-2" />
                            {{ isUploading ? 'Enviando...' : 'Enviar' }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para imagem -->
        <Dialog :open="isImageModalOpen" @update:open="(open) => !open && closeImageModal()">
            <DialogContent class="max-w-7xl w-full p-0 bg-transparent/95 border-0 shadow-2xl">
                <div class="relative w-full h-full flex items-center justify-center p-4">
                    <img
                        v-if="selectedImage"
                        :src="selectedImage"
                        alt="Foto em tamanho completo"
                        class="max-w-full max-h-[90vh] object-contain rounded-lg"
                    />
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

