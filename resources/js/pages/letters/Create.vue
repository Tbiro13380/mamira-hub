<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create, store } from '@/routes/letters';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { X, Image as ImageIcon } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: dashboard().url,
    },
    {
        title: 'Escrever Carta',
        href: create().url,
    },
];

const imagePreview = ref<string | null>(null);
const imageFile = ref<File | null>(null);

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        imageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    imagePreview.value = null;
    imageFile.value = null;
    const input = document.getElementById('image') as HTMLInputElement;
    if (input) {
        input.value = '';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Carta para o Mamira-San" />

        <h1 class="sr-only">Escrever Carta</h1>

        <div class="flex flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto w-full max-w-2xl">
                <div
                    class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-background p-6"
                >
                    <div class="space-y-6">
                        <Heading
                            title="Carta para o Mamira-San"
                            description="Deixe sua mensagem eterna antes da viagem ao Japão"
                        />

                        <Form
                            v-bind="store.form()"
                            :options="{
                                preserveScroll: true,
                            }"
                            :transform="(data) => ({
                                ...data,
                                is_private: data.is_private === '1' || data.is_private === true,
                            })"
                            @success="() => {
                                imagePreview = null;
                                imageFile = null;
                            }"
                            reset-on-success
                            class="space-y-6"
                            v-slot="{ errors, processing, recentlySuccessful }"
                        >
                            <div class="grid gap-2">
                                <Label for="title">Assunto (opcional)</Label>
                                <Input
                                    id="title"
                                    name="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: Obrigado por tudo!"
                                />
                                <InputError :message="errors.title" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="content">Sua Carta</Label>
                                <textarea
                                    id="content"
                                    name="content"
                                    rows="10"
                                    class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 leading-relaxed resize-none"
                                    placeholder="Escreva aqui do fundo do coração..."
                                    required
                                ></textarea>
                                <InputError :message="errors.content" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="image">Imagem (opcional)</Label>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <Input
                                            id="image"
                                            name="image"
                                            type="file"
                                            accept="image/*"
                                            @change="handleImageChange"
                                            class="cursor-pointer"
                                        />
                                    </div>
                                    <p class="text-xs text-muted-foreground">
                                        Formatos aceitos: JPEG, PNG, JPG, GIF, WEBP. Tamanho máximo: 5MB
                                    </p>
                                    <InputError :message="errors.image" />
                                    
                                    <div v-if="imagePreview" class="relative inline-block">
                                        <div class="relative rounded-lg overflow-hidden border border-sidebar-border/70 dark:border-sidebar-border">
                                            <img
                                                :src="imagePreview"
                                                alt="Preview da imagem"
                                                class="max-w-full h-auto max-h-64 object-contain"
                                            />
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                class="absolute top-2 right-2"
                                                @click="removeImage"
                                            >
                                                <X class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-3 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border bg-muted/50 p-4"
                            >
                                <input type="hidden" name="is_private" value="0" />
                                <Checkbox
                                    id="is_private"
                                    name="is_private"
                                    value="1"
                                    class="mt-0.5"
                                />
                                <div class="grid gap-1.5 leading-none">
                                    <Label
                                        for="is_private"
                                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
                                    >
                                        Tornar esta carta privada
                                    </Label>
                                    <p class="text-xs text-muted-foreground">
                                        Apenas o Mamira-San poderá ler esta carta
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <Button
                                    type="submit"
                                    :disabled="processing"
                                    data-test="submit-letter-button"
                                >
                                    Enviar para o Japão 🇯🇵
                                </Button>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p
                                        v-show="recentlySuccessful"
                                        class="text-sm text-muted-foreground"
                                    >
                                        Carta enviada com sucesso!
                                    </p>
                                </Transition>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>