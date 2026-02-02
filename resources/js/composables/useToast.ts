import { usePage } from '@inertiajs/vue3';
import { watch, ref, type Ref } from 'vue';

export type ToastVariant = 'default' | 'success' | 'destructive';

export interface Toast {
    id: string;
    title: string;
    description?: string;
    variant: ToastVariant;
}

const toasts: Ref<Toast[]> = ref([]);
let processedFlashIds = new Set<string>();

export function useToast() {
    const page = usePage();

    const showToast = (title: string, description?: string, variant: ToastVariant = 'default') => {
        const id = Math.random().toString(36).substring(2, 9);
        toasts.value.push({
            id,
            title,
            description,
            variant,
        });

        setTimeout(() => {
            removeToast(id);
        }, 5000);
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const processFlash = (flash: any) => {
        if (!flash) return;

        const messageId = flash.message ? `msg-${flash.message}` : null;
        const errorId = flash.error ? `err-${flash.error}` : null;

        if (messageId && !processedFlashIds.has(messageId)) {
            processedFlashIds.add(messageId);
            showToast('Sucesso', flash.message, 'success');
            setTimeout(() => processedFlashIds.delete(messageId), 6000);
        }

        if (errorId && !processedFlashIds.has(errorId)) {
            processedFlashIds.add(errorId);
            showToast('Erro', flash.error, 'destructive');
            setTimeout(() => processedFlashIds.delete(errorId), 6000);
        }
    };

    const initialFlash = page.props.flash as { message?: string; error?: string } | undefined;
    if (initialFlash) {
        processFlash(initialFlash);
    }

    watch(
        () => page.props.flash,
        (flash, oldFlash) => {
            if (flash && flash !== oldFlash) {
                processFlash(flash);
            }
        },
        { deep: true, immediate: false }
    );

    return {
        toasts,
        showToast,
        removeToast,
    };
}

