<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { type BreadcrumbItem } from '@/types';
import { useInitials } from '@/composables/useInitials';
import { ref } from 'vue';
import { X, Upload } from 'lucide-vue-next';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configurações de perfil',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;
const { getInitials } = useInitials();

const avatarPreview = ref<string | null>(user.avatar || null);
const avatarFile = ref<File | null>(null);

const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        avatarFile.value = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(avatarFile.value);
    }
};

const removeAvatar = () => {
    avatarFile.value = null;
    avatarPreview.value = user.avatar || null;
    const input = document.getElementById('avatar') as HTMLInputElement;
    if (input) {
        input.value = '';
    }
};

const formErrors = ref<Record<string, string>>({});
const isProcessing = ref(false);
const recentlySuccessful = ref(false);

const submitForm = () => {
    formErrors.value = {};
    isProcessing.value = true;
    recentlySuccessful.value = false;
    
    const formData = new FormData();
    
    // Adicionar dados do formulário
    const nameInput = document.getElementById('name') as HTMLInputElement;
    const emailInput = document.getElementById('email') as HTMLInputElement;
    
    formData.append('name', nameInput.value);
    formData.append('email', emailInput.value);
    
    // Adicionar avatar se houver
    if (avatarFile.value) {
        formData.append('avatar', avatarFile.value);
    }
    
    // Enviar usando router do Inertia (PATCH)
    router.patch(ProfileController.update.form().action, formData, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (page) => {
            isProcessing.value = false;
            recentlySuccessful.value = true;
            // Atualizar preview com a nova URL do avatar
            if (avatarFile.value) {
                avatarFile.value = null;
            }
            // Atualizar preview com o avatar do usuário atualizado
            if (page.props.auth?.user?.avatar) {
                avatarPreview.value = page.props.auth.user.avatar;
            }
            setTimeout(() => {
                recentlySuccessful.value = false;
            }, 2000);
        },
        onError: (errors) => {
            isProcessing.value = false;
            formErrors.value = errors as Record<string, string>;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Configurações de perfil" />

        <h1 class="sr-only">Profile Settings</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Informações de perfil"
                    description="Atualize seu nome e endereço de email"
                />

                <form
                    @submit.prevent="submitForm"
                    class="space-y-6"
                >
                    <!-- Avatar Upload -->
                    <div class="grid gap-2">
                        <Label for="avatar">Foto de Perfil</Label>
                        <div class="flex items-center gap-4">
                            <Avatar class="h-20 w-20 overflow-hidden rounded-full">
                                <AvatarImage v-if="avatarPreview" :src="avatarPreview" :alt="user.name" />
                                <AvatarFallback class="rounded-full text-lg">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="flex-1">
                                <Input
                                    id="avatar"
                                    type="file"
                                    accept="image/*"
                                    @change="handleAvatarChange"
                                    class="cursor-pointer"
                                />
                                <p class="text-xs text-muted-foreground mt-1">
                                    Formatos aceitos: JPEG, PNG, JPG, GIF, WEBP. Tamanho máximo: 2MB
                                </p>
                                <InputError class="mt-2" :message="formErrors.avatar" />
                            </div>
                            <Button
                                v-if="avatarFile || (avatarPreview && avatarPreview !== user.avatar)"
                                type="button"
                                variant="outline"
                                size="sm"
                                @click="removeAvatar"
                            >
                                <X class="h-4 w-4 mr-2" />
                                Remover
                            </Button>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Nome completo"
                        />
                        <InputError class="mt-2" :message="formErrors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Endereço de email</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="username"
                            placeholder="Endereço de email"
                        />
                        <InputError class="mt-2" :message="formErrors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Seu endereço de email não está verificado.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Clique aqui para reenviar o email de verificação.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            Um novo link de verificação foi enviado para seu endereço de email
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            type="submit"
                            :disabled="isProcessing"
                            data-test="update-profile-button"
                            >{{ isProcessing ? 'Salvando...' : 'Salvar' }}</Button
                        >
                        
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-green-600"
                            >
                                Salvo!
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
